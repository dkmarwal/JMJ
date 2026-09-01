<?php
/**
 * JMJ Enterprises Solutions - Testimonials Manager
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Testimonials & Reviews';
$db = \Core\Database::getInstance();

// Handle Create / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $clientName = trim($_POST['client_name'] ?? '');
        $designation = trim($_POST['designation'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $testimonial = trim($_POST['testimonial'] ?? '');
        $isApproved = isset($_POST['is_approved']) ? 1 : 0;
        $photo = 'uploads/testimonials/default.jpg';

        if (!empty($_FILES['photo']['name'])) {
            $uploadResult = \Services\MediaService::handleUpload($_FILES['photo'], 'testimonials');
            if ($uploadResult['success']) {
                $photo = $uploadResult['path'];
            }
        }

        if (!empty($clientName) && !empty($testimonial)) {
            $newId = $db->insert('testimonials', [
                'client_name' => $clientName,
                'designation' => $designation,
                'company'     => $company,
                'rating'      => $rating,
                'testimonial' => $testimonial,
                'photo'       => $photo,
                'is_approved' => $isApproved
            ]);
            \Services\AuditService::log("Added testimonial for {$clientName}", 'testimonial', (int)$newId, 'CREATE');
            \Core\Session::setFlash('success', 'Testimonial saved.');
        }
        redirect('admin/testimonials.php');
    }
}

// Handle Archive
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('testimonials', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);
    \Services\AuditService::log("Archived testimonial #{$archiveId}", 'testimonial', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'Testimonial moved to Archive Vault.');
    redirect('admin/testimonials.php');
}

$testimonials = $db->fetchAll("SELECT * FROM testimonials WHERE is_archived = 0 ORDER BY id DESC");

include __DIR__ . '/partials/header.php';
?>

<div class="grid lg:grid-cols-12 gap-8">
    
    <!-- Left 4 Columns: Form -->
    <div class="lg:col-span-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2">
                Add Client Testimonial
            </h3>

            <form action="<?= url('admin/testimonials.php') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Client Name *</label>
                    <input type="text" name="client_name" required placeholder="e.g. Vikramaditya Rao" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div class="grid sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Designation</label>
                        <input type="text" name="designation" placeholder="VP Operations" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#F39C12]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Company</label>
                        <input type="text" name="company" placeholder="Apex Logistics" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#F39C12]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Rating</label>
                    <select name="rating" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none">
                        <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                        <option value="4">⭐⭐⭐⭐ 4 Stars</option>
                        <option value="3">⭐⭐⭐ 3 Stars</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Testimonial Quote *</label>
                    <textarea name="testimonial" rows="3" required placeholder="Client feedback text..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-[#F39C12]"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Client Avatar / Photo</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white">
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_approved" value="1" checked class="rounded text-[#F39C12] focus:ring-0">
                        <span>Publish immediately to website</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#090F1C] hover:bg-[#254E70] text-white font-black py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-md">
                    Save Testimonial
                </button>
            </form>
        </div>
    </div>

    <!-- Right 8 Columns: Testimonials Grid -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">
                Client Testimonials (<?= count($testimonials) ?>)
            </h3>

            <div class="grid sm:grid-cols-2 gap-4">
                <?php foreach ($testimonials as $t): ?>
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative group flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex text-amber-500 text-xs">
                                <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                                    <i class="fas fa-star mr-0.5"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-xs text-slate-600 italic leading-relaxed">"<?= e($t['testimonial']) ?>"</p>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-200/80">
                            <div class="flex items-center space-x-2.5">
                                <img src="<?= upload_url($t['photo']) ?>" alt="" class="w-8 h-8 rounded-full object-cover border border-slate-300">
                                <div>
                                    <h4 class="font-bold text-xs text-slate-900"><?= e($t['client_name']) ?></h4>
                                    <span class="text-[10px] text-slate-400 block"><?= e($t['designation']) ?>, <?= e($t['company']) ?></span>
                                </div>
                            </div>
                            <a href="<?= url('admin/testimonials.php?archive_id=' . $t['id']) ?>" class="confirm-action text-red-500 hover:text-red-700 text-xs" data-confirm="Move this testimonial to Archive Vault?" title="Archive"><i class="fas fa-box-archive"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
