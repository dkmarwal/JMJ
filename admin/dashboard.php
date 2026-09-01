<?php
/**
 * JMJ Enterprises Solutions - Admin Executive Dashboard
 * Inspired by Hawks Infotech Blog Desk
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Executive Operations Dashboard';
$db = \Core\Database::getInstance();

// Metric Counts
$totalBlogs = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_posts WHERE is_archived = 0");
$liveBlogs = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_posts WHERE status = 'published' AND is_archived = 0");
$draftBlogs = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_posts WHERE status IN ('draft', 'pending_review', 'scheduled') AND is_archived = 0");
$totalCategories = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_categories WHERE is_archived = 0");

$totalServices = (int)$db->fetchColumn("SELECT COUNT(*) FROM services WHERE is_archived = 0");
$totalEnquiries = (int)$db->fetchColumn("SELECT COUNT(*) FROM enquiries WHERE is_archived = 0");
$newEnquiries = (int)$db->fetchColumn("SELECT COUNT(*) FROM enquiries WHERE status = 'new' AND is_archived = 0");
$totalMedia = (int)$db->fetchColumn("SELECT COUNT(*) FROM media WHERE is_archived = 0");

// Recent Blog Posts
$recentBlogs = $db->fetchAll(
    "SELECT p.*, c.name as category_name 
     FROM blog_posts p 
     JOIN blog_categories c ON p.category_id = c.id 
     WHERE p.is_archived = 0 
     ORDER BY p.id DESC LIMIT 5"
);

// Recent Inbound Leads
$recentEnquiries = $db->fetchAll(
    "SELECT * FROM enquiries WHERE is_archived = 0 ORDER BY id DESC LIMIT 5"
);

include __DIR__ . '/partials/header.php';
?>

<!-- Metric Cards Grid (Hawks Infotech Blog Desk Pattern) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    
    <!-- Total Posts -->
    <div class="metric-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Articles</span>
            <span class="text-3xl font-black text-slate-900 mt-1 block"><?= $totalBlogs ?></span>
            <span class="text-[11px] text-slate-500 mt-1 block">In database repository</span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#F39C12] flex items-center justify-center text-xl font-bold">
            <i class="fas fa-newspaper"></i>
        </div>
    </div>

    <!-- Live on Site -->
    <div class="metric-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Live on Site</span>
            <span class="text-3xl font-black text-emerald-600 mt-1 block"><?= $liveBlogs ?></span>
            <span class="text-[11px] text-emerald-600 mt-1 block font-semibold"><i class="fas fa-circle text-[8px] mr-1"></i> Publicly Indexable</span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
            <i class="fas fa-globe"></i>
        </div>
    </div>

    <!-- Drafts / Pending -->
    <div class="metric-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Drafts & Scheduled</span>
            <span class="text-3xl font-black text-amber-600 mt-1 block"><?= $draftBlogs ?></span>
            <span class="text-[11px] text-amber-600 mt-1 block font-semibold">Editorial queue</span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
            <i class="fas fa-pen-ruler"></i>
        </div>
    </div>

    <!-- Inbound Leads -->
    <div class="metric-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">New Leads / Quotes</span>
            <span class="text-3xl font-black text-blue-600 mt-1 block"><?= $newEnquiries ?></span>
            <span class="text-[11px] text-slate-500 mt-1 block">Total Enquiries: <?= $totalEnquiries ?></span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
            <i class="fas fa-headset"></i>
        </div>
    </div>

</div>

<!-- Quick Action Panel -->
<div class="bg-[#090F1C] text-white p-6 rounded-3xl shadow-xl mb-8 flex flex-col md:flex-row justify-between items-center gap-4 border border-slate-800">
    <div class="space-y-1 text-center md:text-left">
        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#F39C12] block">Fast Desk Operations</span>
        <h3 class="text-lg font-black text-white">Editorial & Operational Shortcuts</h3>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <a href="<?= url('admin/blog-editor.php') ?>" class="bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-extrabold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
            <i class="fas fa-pen-nib mr-1.5"></i> New Article
        </a>
        <a href="<?= url('admin/service-editor.php') ?>" class="bg-[#254E70] hover:bg-blue-900 text-white font-extrabold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
            <i class="fas fa-plus-circle mr-1.5"></i> New Service
        </a>
        <a href="<?= url('admin/media.php') ?>" class="bg-slate-800 hover:bg-slate-700 text-white font-extrabold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
            <i class="fas fa-upload mr-1.5"></i> Media Library
        </a>
        <a href="<?= url('admin/archive.php') ?>" class="bg-slate-800 hover:bg-amber-900/60 text-amber-400 font-extrabold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
            <i class="fas fa-vault mr-1.5"></i> Archive Vault
        </a>
    </div>
</div>

<!-- Main Dual Column Layout -->
<div class="grid lg:grid-cols-12 gap-8">
    
    <!-- Left 7 Columns: Recent Blog Articles -->
    <div class="lg:col-span-7 space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <h3 class="font-black text-base text-slate-900 flex items-center">
                    <i class="fas fa-newspaper text-[#F39C12] mr-2"></i> Recent Articles
                </h3>
                <a href="<?= url('admin/blogs.php') ?>" class="text-xs font-bold text-[#254E70] hover:underline">
                    Manage All (<?= $totalBlogs ?>) &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[10px] uppercase font-bold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-2.5">Title</th>
                            <th class="py-2.5">Category</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php foreach ($recentBlogs as $blog): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 font-bold text-slate-900 max-w-xs truncate">
                                    <?= e($blog['title']) ?>
                                </td>
                                <td class="py-3 text-slate-500">
                                    <?= e($blog['category_name']) ?>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?= $blog['status'] === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= e($blog['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 text-right space-x-2">
                                    <a href="<?= url('blog/' . $blog['slug']) ?>" target="_blank" class="text-slate-400 hover:text-slate-700" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="<?= url('admin/blog-editor.php?id=' . $blog['id']) ?>" class="text-amber-600 hover:text-amber-800 font-bold" title="Edit"><i class="fas fa-pen-to-square"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right 5 Columns: Inbound Enquiries -->
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <h3 class="font-black text-base text-slate-900 flex items-center">
                    <i class="fas fa-envelope text-blue-600 mr-2"></i> Recent Inbound Leads
                </h3>
                <a href="<?= url('admin/enquiries.php') ?>" class="text-xs font-bold text-blue-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            <div class="space-y-3">
                <?php if (empty($recentEnquiries)): ?>
                    <p class="text-xs text-slate-400 text-center py-6">No inbound enquiries received yet.</p>
                <?php else: ?>
                    <?php foreach ($recentEnquiries as $enq): ?>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <div class="flex justify-between items-center">
                                <h4 class="font-bold text-xs text-slate-900"><?= e($enq['name']) ?></h4>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $enq['status'] === 'new' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700' ?>">
                                    <?= e($enq['status']) ?>
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-500 block truncate"><?= e($enq['service_required'] ?: 'General Quote') ?> • <?= e($enq['location'] ?: 'Delhi') ?></span>
                            <div class="flex justify-between items-center text-[10px] text-slate-400 pt-1">
                                <span><i class="fas fa-phone mr-1"></i> <?= e($enq['phone']) ?></span>
                                <span><?= format_date($enq['created_at']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
