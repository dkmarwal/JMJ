<?php
/**
 * JMJ Enterprises Solutions - Admin Gallery Showcase Manager
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Gallery Showcase Portfolio';
$db = \Core\Database::getInstance();

// Handle Add Item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $title = trim($_POST['title'] ?? '');
        $catId = (int)($_POST['category_id'] ?? 1);
        $caption = trim($_POST['caption'] ?? '');
        $imagePath = 'uploads/gallery/default.jpg';

        if (!empty($_FILES['image']['name'])) {
            $uploadResult = \Services\MediaService::handleUpload($_FILES['image'], 'gallery');
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            }
        }

        if (!empty($title)) {
            $newId = $db->insert('gallery', [
                'category_id' => $catId,
                'title'       => $title,
                'caption'     => $caption,
                'image_path'  => $imagePath
            ]);
            \Services\AuditService::log("Added gallery item: {$title}", 'gallery', (int)$newId, 'CREATE');
            \Core\Session::setFlash('success', 'Gallery item uploaded successfully.');
        }
        redirect('admin/gallery.php');
    }
}

// Handle Archive
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('gallery', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);
    \Services\AuditService::log("Archived gallery item #{$archiveId}", 'gallery', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'Image moved to Archive Vault.');
    redirect('admin/gallery.php');
}

$items = $db->fetchAll(
    "SELECT g.*, c.name as category_name 
     FROM gallery g 
     JOIN gallery_categories c ON g.category_id = c.id 
     WHERE g.is_archived = 0 
     ORDER BY g.id DESC"
);

$categories = $db->fetchAll("SELECT * FROM gallery_categories ORDER BY name ASC");

include __DIR__ . '/partials/header.php';
?>

<div class="grid lg:grid-cols-12 gap-8">
    
    <!-- Left 4 Columns: Upload New Item -->
    <div class="lg:col-span-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2">
                Upload Gallery Photo
            </h3>

            <form action="<?= url('admin/gallery.php') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Photo Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Manned Guard Roster Briefing" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Showcase Category *</label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:outline-none">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Image File *</label>
                    <input type="file" name="image" required accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Caption / Description</label>
                    <textarea name="caption" rows="2" placeholder="Brief context about this operational snapshot..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-[#F39C12]"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#090F1C] hover:bg-[#254E70] text-white font-black py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-md">
                    Upload to Showcase
                </button>
            </form>
        </div>
    </div>

    <!-- Right 8 Columns: Gallery Grid -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-6">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">
                Live Portfolio Items (<?= count($items) ?>)
            </h3>

            <div class="grid sm:grid-cols-3 gap-4">
                <?php foreach ($items as $item): ?>
                    <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 group relative">
                        <div class="h-40 overflow-hidden">
                            <img src="<?= upload_url($item['image_path']) ?>" alt="<?= e($item['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div class="p-3 space-y-1">
                            <span class="text-[9px] font-extrabold uppercase tracking-wider text-[#F39C12] block"><?= e($item['category_name']) ?></span>
                            <h4 class="text-xs font-bold text-slate-900 truncate"><?= e($item['title']) ?></h4>
                        </div>
                        <a href="<?= url('admin/gallery.php?archive_id=' . $item['id']) ?>" class="confirm-action absolute top-2 right-2 w-7 h-7 bg-red-600/90 text-white rounded-lg flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition" data-confirm="Move this image to Archive Vault?" title="Archive">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
