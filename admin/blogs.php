<?php
/**
 * JMJ Enterprises Solutions - Admin Blog Management
 * Hawks Infotech Blog Desk Blueprint
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Blog Posts Desk';
$db = \Core\Database::getInstance();

// Handle Soft Delete to Archive Vault
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('blog_posts', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);

    \Services\AuditService::log("Archived blog post #{$archiveId} to Archive Vault", 'blog', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'Article moved to Archive Vault.');
    redirect('admin/blogs.php');
}

// Filters & Pagination
$status = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$where = "WHERE p.is_archived = 0";
$params = [];

if ($status !== 'all' && in_array($status, ['published', 'draft', 'pending_review', 'scheduled'])) {
    $where .= " AND p.status = :status";
    $params['status'] = $status;
}

if (!empty($search)) {
    $where .= " AND (p.title LIKE :s OR p.short_description LIKE :s)";
    $params['s'] = '%' . $search . '%';
}

if ($categoryId) {
    $where .= " AND p.category_id = :cid";
    $params['cid'] = $categoryId;
}

$total = (int)$db->fetchColumn(
    "SELECT COUNT(*) FROM blog_posts p JOIN blog_categories c ON p.category_id = c.id {$where}",
    $params
);
$totalPages = (int)ceil($total / $limit);

$posts = $db->fetchAll(
    "SELECT p.*, c.name as category_name, u.name as author_name 
     FROM blog_posts p 
     JOIN blog_categories c ON p.category_id = c.id 
     JOIN users u ON p.author_id = u.id 
     {$where} 
     ORDER BY p.id DESC LIMIT {$limit} OFFSET {$offset}",
    $params
);

$categories = $db->fetchAll("SELECT * FROM blog_categories WHERE is_archived = 0 ORDER BY name ASC");

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-slate-900">Manage Blog Publications</h2>
            <p class="text-xs text-slate-500">Draft, schedule, publish, and review articles with version history.</p>
        </div>
        <a href="<?= url('admin/blog-editor.php') ?>" class="bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
            <i class="fas fa-plus mr-1.5"></i> Write New Post
        </a>
    </div>

    <!-- Status Tabs & Filter Strip -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center space-x-1 overflow-x-auto w-full md:w-auto text-xs font-bold">
            <a href="<?= url('admin/blogs.php?status=all') ?>" class="px-4 py-2 rounded-xl transition <?= $status === 'all' ? 'bg-[#090F1C] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">All</a>
            <a href="<?= url('admin/blogs.php?status=published') ?>" class="px-4 py-2 rounded-xl transition <?= $status === 'published' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Published</a>
            <a href="<?= url('admin/blogs.php?status=draft') ?>" class="px-4 py-2 rounded-xl transition <?= $status === 'draft' ? 'bg-amber-500 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Drafts</a>
            <a href="<?= url('admin/blogs.php?status=scheduled') ?>" class="px-4 py-2 rounded-xl transition <?= $status === 'scheduled' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Scheduled</a>
        </div>

        <!-- Search Form -->
        <form action="<?= url('admin/blogs.php') ?>" method="GET" class="flex items-center space-x-2 w-full md:w-auto">
            <input type="hidden" name="status" value="<?= e($status) ?>">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search posts..." class="w-full md:w-64 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-[#F39C12]">
            <button type="submit" class="bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Article Details</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Author</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Views</th>
                        <th class="py-3.5 px-4">Publish Date</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">No blog posts found matching your criteria.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $p): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 flex items-center space-x-3.5">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                        <img src="<?= upload_url($p['featured_image']) ?>" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="max-w-md">
                                        <a href="<?= url('admin/blog-editor.php?id=' . $p['id']) ?>" class="font-bold text-slate-900 hover:text-amber-600 text-sm block leading-snug line-clamp-1">
                                            <?= e($p['title']) ?>
                                        </a>
                                        <span class="text-[10px] text-slate-400 block mt-0.5"><?= (int)$p['reading_time'] ?> min read • <?= e($p['slug']) ?></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-700">
                                    <?= e($p['category_name']) ?>
                                </td>
                                <td class="py-4 px-4 text-slate-500">
                                    <?= e($p['author_name']) ?>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase <?= $p['status'] === 'published' ? 'bg-emerald-100 text-emerald-800' : ($p['status'] === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') ?>">
                                        <?= e($p['status']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-800">
                                    <?= (int)$p['views'] ?>
                                </td>
                                <td class="py-4 px-4 text-slate-500 text-[11px]">
                                    <?= format_date($p['publish_at'] ?: $p['created_at']) ?>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <?php if ($p['status'] === 'published'): ?>
                                        <a href="<?= url('blog/' . $p['slug']) ?>" target="_blank" class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Preview Live"><i class="fas fa-eye"></i></a>
                                    <?php endif; ?>
                                    <a href="<?= url('admin/blog-editor.php?id=' . $p['id']) ?>" class="p-2 rounded-lg text-amber-600 hover:text-amber-800 hover:bg-amber-50 transition" title="Edit Article"><i class="fas fa-pen-to-square"></i></a>
                                    <a href="<?= url('admin/blogs.php?archive_id=' . $p['id']) ?>" class="confirm-action p-2 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition" data-confirm="Move this article to Archive Vault?" title="Archive Post"><i class="fas fa-box-archive"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="p-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
                <span>Showing page <?= $page ?> of <?= $totalPages ?> (<?= $total ?> total posts)</span>
                <div class="flex items-center space-x-1">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="<?= url('admin/blogs.php?page=' . $p . '&status=' . $status . ($search ? '&search=' . urlencode($search) : '')) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition <?= $p === $page ? 'bg-slate-900 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
