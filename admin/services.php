<?php
/**
 * JMJ Enterprises Solutions - Services CMS Catalog
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Services CMS Catalog';
$db = \Core\Database::getInstance();

// Handle Archive
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('services', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);
    \Services\AuditService::log("Archived service #{$archiveId}", 'service', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'Service moved to Archive Vault.');
    redirect('admin/services.php');
}

$categorySlug = $_GET['category'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$where = "WHERE s.is_archived = 0";
$params = [];

if ($categorySlug !== 'all') {
    $where .= " AND c.slug = :cslug";
    $params['cslug'] = $categorySlug;
}

if (!empty($search)) {
    $where .= " AND (s.name LIKE :s OR s.short_summary LIKE :s)";
    $params['s'] = '%' . $search . '%';
}

$services = $db->fetchAll(
    "SELECT s.*, c.name as category_name, c.slug as category_slug 
     FROM services s 
     JOIN service_categories c ON s.category_id = c.id 
     {$where} 
     ORDER BY s.category_id ASC, s.display_order ASC, s.name ASC",
    $params
);

$categories = $db->fetchAll("SELECT * FROM service_categories ORDER BY name ASC");

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-slate-900">Services Catalog (<?= count($services) ?> Active)</h2>
            <p class="text-xs text-slate-500">Manage all 12 Security and 14 Cleaning service offerings, features, FAQs, and SEO.</p>
        </div>
        <a href="<?= url('admin/service-editor.php') ?>" class="bg-[#254E70] hover:bg-blue-900 text-white font-black px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
            <i class="fas fa-plus mr-1.5"></i> Add New Service
        </a>
    </div>

    <!-- Category Filter Tabs -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center space-x-1 overflow-x-auto w-full md:w-auto text-xs font-bold">
            <a href="<?= url('admin/services.php?category=all') ?>" class="px-4 py-2 rounded-xl transition <?= $categorySlug === 'all' ? 'bg-[#090F1C] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">All Categories</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('admin/services.php?category=' . $cat['slug']) ?>" class="px-4 py-2 rounded-xl transition <?= $categorySlug === $cat['slug'] ? 'bg-[#254E70] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form action="<?= url('admin/services.php') ?>" method="GET" class="flex items-center space-x-2 w-full md:w-auto">
            <input type="hidden" name="category" value="<?= e($categorySlug) ?>">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search services..." class="w-full md:w-64 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-[#254E70]">
            <button type="submit" class="bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Service Name & Slug</th>
                        <th class="py-3.5 px-4">Division</th>
                        <th class="py-3.5 px-4">Icon</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php foreach ($services as $s): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 flex items-center space-x-3.5">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                    <img src="<?= upload_url($s['hero_image']) ?>" alt="" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <a href="<?= url('admin/service-editor.php?id=' . $s['id']) ?>" class="font-bold text-slate-900 hover:text-[#254E70] text-sm block">
                                        <?= e($s['name']) ?>
                                    </a>
                                    <span class="text-[10px] text-slate-400 font-mono"><?= e($s['slug']) ?></span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-700">
                                <?= e($s['category_name']) ?>
                            </td>
                            <td class="py-4 px-4 text-center text-base text-[#254E70]">
                                <i class="<?= e($s['icon'] ?: 'fas fa-shield') ?>"></i>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase <?= $s['status'] === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                    <?= e($s['status']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="<?= url($s['category_slug'] . '/' . $s['slug']) ?>" target="_blank" class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100" title="View Public"><i class="fas fa-eye"></i></a>
                                <a href="<?= url('admin/service-editor.php?id=' . $s['id']) ?>" class="p-2 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50" title="Edit"><i class="fas fa-pen-to-square"></i></a>
                                <a href="<?= url('admin/services.php?archive_id=' . $s['id']) ?>" class="confirm-action p-2 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50" data-confirm="Move this service to Archive Vault?" title="Archive"><i class="fas fa-box-archive"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
