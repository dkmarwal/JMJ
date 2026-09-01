<?php
/**
 * JMJ Enterprises Solutions - Blog Tags Management
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Blog Tags';
$db = \Core\Database::getInstance();

// Handle Create Tag
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $slug = slugify($_POST['slug'] ?: $name);

        if (!empty($name)) {
            $newId = $db->insert('blog_tags', ['name' => $name, 'slug' => $slug]);
            \Services\AuditService::log("Created blog tag: {$name}", 'tag', (int)$newId, 'CREATE');
            \Core\Session::setFlash('success', 'Tag created successfully.');
        }
        redirect('admin/tags.php');
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delId = (int)$_GET['delete_id'];
    $db->delete('blog_post_tags', 'tag_id = :id', ['id' => $delId]);
    $db->delete('blog_tags', 'id = :id', ['id' => $delId]);
    \Services\AuditService::log("Deleted blog tag #{$delId}", 'tag', $delId, 'DELETE');
    \Core\Session::setFlash('success', 'Tag removed.');
    redirect('admin/tags.php');
}

$tags = $db->fetchAll(
    "SELECT t.*, COUNT(pt.post_id) as post_count 
     FROM blog_tags t 
     LEFT JOIN blog_post_tags pt ON t.id = pt.tag_id 
     GROUP BY t.id 
     ORDER BY t.name ASC"
);

include __DIR__ . '/partials/header.php';
?>

<div class="grid lg:grid-cols-12 gap-8">
    
    <!-- Left 4 Columns: Create Tag Form -->
    <div class="lg:col-span-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2">
                Add New Tag
            </h3>

            <form action="<?= url('admin/tags.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Tag Name *</label>
                    <input type="text" id="slug-source-title" name="name" required placeholder="e.g. psara-act" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">URL Slug</label>
                    <input type="text" id="slug-target-input" name="slug" placeholder="psara-act" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <button type="submit" class="w-full bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-md">
                    Add Topic Tag
                </button>
            </form>
        </div>
    </div>

    <!-- Right 8 Columns: Tags Cloud Table -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-base font-black text-slate-900">Topic Tags (<?= count($tags) ?>)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Tag Name</th>
                            <th class="py-3 px-4">Slug</th>
                            <th class="py-3 px-4 text-center">Associated Articles</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php foreach ($tags as $t): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    #<?= e($t['name']) ?>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-400 text-[11px]">
                                    <?= e($t['slug']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full text-[10px] font-bold">
                                        <?= (int)$t['post_count'] ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <a href="<?= url('admin/tags.php?delete_id=' . $t['id']) ?>" class="confirm-action p-2 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50" data-confirm="Permanently remove this tag?" title="Delete"><i class="fas fa-trash"></i></a>
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
