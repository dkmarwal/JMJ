<?php
/**
 * JMJ Enterprises Solutions - Archive & Recovery Vault
 * Inspired by Hawks Infotech Unified Archive System
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Archive & Recovery Vault';
$db = \Core\Database::getInstance();

$tab = $_GET['tab'] ?? 'blogs';

// Handle Actions: Restore & Permanent Purge
if (isset($_GET['action']) && isset($_GET['entity']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $entity = $_GET['entity'];
    $id = (int)$_GET['id'];

    $tableMap = [
        'blogs'        => 'blog_posts',
        'services'     => 'services',
        'categories'   => 'blog_categories',
        'gallery'      => 'gallery',
        'testimonials' => 'testimonials',
        'faqs'         => 'faqs',
        'enquiries'    => 'enquiries',
        'users'        => 'users',
        'media'        => 'media'
    ];

    if (isset($tableMap[$entity])) {
        $table = $tableMap[$entity];

        if ($action === 'restore') {
            $db->update($table, [
                'is_archived' => 0,
                'archived_at' => null,
                'archived_by' => null
            ], 'id = :id', ['id' => $id]);

            \Services\AuditService::log("Restored {$entity} #{$id} from Archive Vault", $entity, $id, 'RESTORE');
            \Core\Session::setFlash('success', "Item #{$id} restored to active repository.");
        } elseif ($action === 'purge') {
            $db->delete($table, 'id = :id', ['id' => $id]);
            \Services\AuditService::log("Permanently purged {$entity} #{$id}", $entity, $id, 'PURGE');
            \Core\Session::setFlash('success', "Item #{$id} permanently deleted.");
        }
    }
    redirect('admin/archive.php?tab=' . $entity);
}

// Counts for each tab
$blogCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_posts WHERE is_archived = 1");
$serviceCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM services WHERE is_archived = 1");
$catCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_categories WHERE is_archived = 1");
$galleryCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM gallery WHERE is_archived = 1");
$testCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM testimonials WHERE is_archived = 1");
$faqCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM faqs WHERE is_archived = 1");
$enqCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM enquiries WHERE is_archived = 1");
$userCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM users WHERE is_archived = 1");
$mediaCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM media WHERE is_archived = 1");

$items = [];
switch ($tab) {
    case 'services':
        $items = $db->fetchAll("SELECT id, name as title, slug, archived_at FROM services WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'categories':
        $items = $db->fetchAll("SELECT id, name as title, slug, archived_at FROM blog_categories WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'gallery':
        $items = $db->fetchAll("SELECT id, title, image_path as extra, archived_at FROM gallery WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'testimonials':
        $items = $db->fetchAll("SELECT id, client_name as title, company as extra, archived_at FROM testimonials WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'faqs':
        $items = $db->fetchAll("SELECT id, question as title, category as extra, archived_at FROM faqs WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'enquiries':
        $items = $db->fetchAll("SELECT id, name as title, email as extra, archived_at FROM enquiries WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'users':
        $items = $db->fetchAll("SELECT id, name as title, email as extra, archived_at FROM users WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'media':
        $items = $db->fetchAll("SELECT id, original_name as title, file_path as extra, archived_at FROM media WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
    case 'blogs':
    default:
        $tab = 'blogs';
        $items = $db->fetchAll("SELECT id, title, slug, archived_at FROM blog_posts WHERE is_archived = 1 ORDER BY archived_at DESC");
        break;
}

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#F39C12] flex items-center justify-center text-lg font-bold">
                <i class="fas fa-vault"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900">Archive & Recovery Vault</h2>
                <p class="text-xs text-slate-500">Safely inspect, restore, or permanently purge soft-deleted records across all system modules.</p>
            </div>
        </div>
    </div>

    <!-- Vault Category Tabs -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-1 overflow-x-auto text-xs font-bold scrollbar-none">
        <a href="<?= url('admin/archive.php?tab=blogs') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'blogs' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Articles (<?= $blogCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=services') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'services' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Services (<?= $serviceCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=categories') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'categories' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Categories (<?= $catCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=gallery') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'gallery' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Gallery (<?= $galleryCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=testimonials') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'testimonials' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Reviews (<?= $testCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=faqs') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'faqs' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            FAQs (<?= $faqCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=enquiries') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'enquiries' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Leads (<?= $enqCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=users') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'users' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Users (<?= $userCount ?>)
        </a>
        <a href="<?= url('admin/archive.php?tab=media') ?>" class="px-4 py-2 rounded-xl transition <?= $tab === 'media' ? 'bg-[#090F1C] text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' ?>">
            Media (<?= $mediaCount ?>)
        </a>
    </div>

    <!-- Vault Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Item Title / Descriptor</th>
                        <th class="py-3 px-4">Archived Timestamp</th>
                        <th class="py-3 px-6 text-right">Vault Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-400">
                                <i class="fas fa-box-open text-3xl text-slate-300 block mb-2"></i>
                                No archived <?= e($tab) ?> in recovery vault.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-6">
                                    <span class="font-bold text-slate-900 block"><?= e($item['title']) ?></span>
                                    <?php if (!empty($item['extra'])): ?>
                                        <span class="text-[10px] text-slate-400 block"><?= e($item['extra']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">
                                    <?= format_date($item['archived_at'], 'd M Y, h:i A') ?>
                                </td>
                                <td class="py-3.5 px-6 text-right space-x-2">
                                    <a href="<?= url('admin/archive.php?action=restore&entity=' . $tab . '&id=' . $item['id']) ?>" class="confirm-action px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200 text-xs font-bold transition inline-flex items-center" data-confirm="Restore this item back to active database?">
                                        <i class="fas fa-rotate-left mr-1"></i> Restore
                                    </a>
                                    <a href="<?= url('admin/archive.php?action=purge&entity=' . $tab . '&id=' . $item['id']) ?>" class="confirm-action px-3 py-1.5 rounded-lg bg-red-100 text-red-800 hover:bg-red-200 text-xs font-bold transition inline-flex items-center" data-confirm="PERMANENT PURGE: This cannot be undone. Are you sure?">
                                        <i class="fas fa-trash mr-1"></i> Purge
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
