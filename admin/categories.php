<?php
/**
 * JMJ Enterprises Solutions - Blog Categories Management
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Blog Categories';
$db = \Core\Database::getInstance();

// Handle Add / Edit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $slug = slugify($_POST['slug'] ?: $name);
        $description = trim($_POST['description'] ?? '');
        $editId = !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;

        if (!empty($name)) {
            if ($editId) {
                $db->update('blog_categories', [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description
                ], 'id = :id', ['id' => $editId]);
                \Services\AuditService::log("Updated blog category: {$name}", 'category', $editId, 'UPDATE');
                \Core\Session::setFlash('success', 'Category updated successfully.');
            } else {
                $newId = $db->insert('blog_categories', [
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description
                ]);
                \Services\AuditService::log("Created blog category: {$name}", 'category', (int)$newId, 'CREATE');
                \Core\Session::setFlash('success', 'Category created successfully.');
            }
        }
        redirect('admin/categories.php');
    }
}

// Handle Soft Delete
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('blog_categories', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);
    \Services\AuditService::log("Archived blog category #{$archiveId}", 'category', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'Category moved to Archive Vault.');
    redirect('admin/categories.php');
}

$editCategory = null;
if (isset($_GET['edit'])) {
    $editCategory = $db->fetch("SELECT * FROM blog_categories WHERE id = :id AND is_archived = 0", ['id' => (int)$_GET['edit']]);
}

$categories = $db->fetchAll(
    "SELECT c.*, COUNT(p.id) as post_count 
     FROM blog_categories c 
     LEFT JOIN blog_posts p ON c.id = p.category_id AND p.is_archived = 0 
     WHERE c.is_archived = 0 
     GROUP BY c.id 
     ORDER BY c.name ASC"
);

include __DIR__ . '/partials/header.php';
?>

<div class="grid lg:grid-cols-12 gap-8">
    
    <!-- Left 4 Columns: Create / Edit Form -->
    <div class="lg:col-span-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2">
                <?= $editCategory ? 'Edit Category' : 'Create New Category' ?>
            </h3>

            <form action="<?= url('admin/categories.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <?php if ($editCategory): ?>
                    <input type="hidden" name="edit_id" value="<?= $editCategory['id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Category Name *</label>
                    <input type="text" id="slug-source-title" name="name" required value="<?= e($editCategory['name'] ?? '') ?>" placeholder="e.g. Threat Intelligence" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">URL Slug</label>
                    <input type="text" id="slug-target-input" name="slug" value="<?= e($editCategory['slug'] ?? '') ?>" placeholder="threat-intelligence" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="3" placeholder="Category purpose and scope..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]"><?= e($editCategory['description'] ?? '') ?></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <?php if ($editCategory): ?>
                        <a href="<?= url('admin/categories.php') ?>" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="flex-1 bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-md">
                        <?= $editCategory ? 'Update Category' : 'Save Category' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right 8 Columns: Categories Table -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-base font-black text-slate-900">Blog Taxonomy (<?= count($categories) ?>)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Name & Slug</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4 text-center">Articles</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php foreach ($categories as $c): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-6">
                                    <span class="font-bold text-slate-900 block"><?= e($c['name']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-mono"><?= e($c['slug']) ?></span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate">
                                    <?= e($c['description'] ?: '—') ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full text-[10px] font-bold">
                                        <?= (int)$c['post_count'] ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right space-x-2">
                                    <a href="<?= url('admin/categories.php?edit=' . $c['id']) ?>" class="p-2 rounded-lg text-amber-600 hover:text-amber-800 hover:bg-amber-50" title="Edit"><i class="fas fa-pen-to-square"></i></a>
                                    <a href="<?= url('admin/categories.php?archive_id=' . $c['id']) ?>" class="confirm-action p-2 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50" data-confirm="Move this category to Archive Vault?" title="Archive"><i class="fas fa-box-archive"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
