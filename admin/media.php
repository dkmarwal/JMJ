<?php
/**
 * JMJ Enterprises Solutions - Admin Media Library
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Media Asset Library';
$db = \Core\Database::getInstance();

// Handle Direct Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $folder = $_POST['folder'] ?? 'uploads';
        if (!empty($_FILES['file']['name'])) {
            $uploadResult = \Services\MediaService::handleUpload($_FILES['file'], $folder);
            if ($uploadResult['success']) {
                \Services\AuditService::log("Uploaded media file: {$uploadResult['name']}", 'media', (int)$uploadResult['id'], 'UPLOAD');
                \Core\Session::setFlash('success', 'File uploaded successfully to Media Library.');
            } else {
                \Core\Session::setFlash('error', $uploadResult['error'] ?? 'Upload failed.');
            }
        }
        redirect('admin/media.php');
    }
}

// Handle Archive
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('media', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);
    \Services\AuditService::log("Archived media item #{$archiveId}", 'media', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'File moved to Archive Vault.');
    redirect('admin/media.php');
}

$search = trim($_GET['search'] ?? '');
$folderFilter = $_GET['folder'] ?? 'all';

$where = "WHERE is_archived = 0";
$params = [];

if ($folderFilter !== 'all') {
    $where .= " AND folder = :f";
    $params['f'] = $folderFilter;
}

if (!empty($search)) {
    $where .= " AND original_name LIKE :s";
    $params['s'] = '%' . $search . '%';
}

$mediaFiles = $db->fetchAll("SELECT * FROM media {$where} ORDER BY id DESC", $params);

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <!-- Top Bar with Upload Panel -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900">Media Asset Library (<?= count($mediaFiles) ?> Files)</h2>
            <p class="text-xs text-slate-500">Central repository for photography, certificates, and logos.</p>
        </div>

        <form action="<?= url('admin/media.php') ?>" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
            <?= csrf_field() ?>
            <select name="folder" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none">
                <option value="blog">Blog Folder</option>
                <option value="services">Services Folder</option>
                <option value="gallery">Gallery Folder</option>
                <option value="testimonials">Testimonials</option>
                <option value="uploads">General Uploads</option>
            </select>
            <input type="file" name="file" required accept="image/*,.pdf,.doc,.docx" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white">
            <button type="submit" class="bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition shadow-md">
                <i class="fas fa-cloud-arrow-up mr-1"></i> Upload
            </button>
        </form>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
        <div class="flex items-center space-x-2 text-xs font-bold">
            <a href="<?= url('admin/media.php?folder=all') ?>" class="px-3 py-1.5 rounded-lg transition <?= $folderFilter === 'all' ? 'bg-[#090F1C] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">All Folders</a>
            <a href="<?= url('admin/media.php?folder=blog') ?>" class="px-3 py-1.5 rounded-lg transition <?= $folderFilter === 'blog' ? 'bg-[#254E70] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Blog</a>
            <a href="<?= url('admin/media.php?folder=services') ?>" class="px-3 py-1.5 rounded-lg transition <?= $folderFilter === 'services' ? 'bg-[#254E70] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Services</a>
            <a href="<?= url('admin/media.php?folder=gallery') ?>" class="px-3 py-1.5 rounded-lg transition <?= $folderFilter === 'gallery' ? 'bg-[#254E70] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Gallery</a>
        </div>

        <form action="<?= url('admin/media.php') ?>" method="GET" class="flex items-center space-x-2">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search filename..." class="w-48 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-[#F39C12]">
            <button type="submit" class="bg-slate-900 text-white px-3 py-1.5 rounded-xl text-xs"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php foreach ($mediaFiles as $m): ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-2 space-y-2 group hover:border-[#F39C12] transition">
                <div class="h-28 rounded-xl overflow-hidden bg-slate-100 relative">
                    <?php if (str_starts_with((string)$m['mime_type'], 'image/')): ?>
                        <img src="<?= upload_url($m['file_path']) ?>" alt="<?= e($m['alt_text'] ?: $m['original_name']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-3xl text-slate-400">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-1">
                    <span class="text-[11px] font-bold text-slate-800 block truncate" title="<?= e($m['original_name']) ?>">
                        <?= e($m['original_name']) ?>
                    </span>
                    <span class="text-[10px] text-slate-400 block"><?= format_file_size((int)$m['file_size']) ?> • <?= e($m['folder']) ?></span>
                </div>

                <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-xs">
                    <button type="button" class="copy-media-url text-slate-500 hover:text-amber-600 text-[10px] font-bold" data-url="<?= upload_url($m['file_path']) ?>" title="Copy URL">
                        <i class="fas fa-copy mr-0.5"></i> URL
                    </button>
                    <a href="<?= url('admin/media.php?archive_id=' . $m['id']) ?>" class="confirm-action text-red-500 hover:text-red-700 text-[10px]" data-confirm="Move this file to Archive Vault?" title="Archive"><i class="fas fa-box-archive"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
