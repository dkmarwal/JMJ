<?php
/**
 * JMJ Enterprises Solutions - Global FAQs Manager
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'FAQs Content Engine';
$db = \Core\Database::getInstance();

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $category = $_POST['category'] ?? 'General';
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $editId = !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;

        if (!empty($question) && !empty($answer)) {
            if ($editId) {
                $db->update('faqs', [
                    'category' => $category,
                    'question' => $question,
                    'answer'   => $answer
                ], 'id = :id', ['id' => $editId]);
                \Services\AuditService::log("Updated FAQ #{$editId}", 'faq', $editId, 'UPDATE');
                \Core\Session::setFlash('success', 'FAQ updated.');
            } else {
                $newId = $db->insert('faqs', [
                    'category' => $category,
                    'question' => $question,
                    'answer'   => $answer
                ]);
                \Services\AuditService::log("Created FAQ #{$newId}", 'faq', (int)$newId, 'CREATE');
                \Core\Session::setFlash('success', 'FAQ added.');
            }
        }
        redirect('admin/faqs.php');
    }
}

// Handle Archive
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('faqs', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);
    \Services\AuditService::log("Archived FAQ #{$archiveId}", 'faq', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'FAQ moved to Archive Vault.');
    redirect('admin/faqs.php');
}

$editFaq = null;
if (isset($_GET['edit'])) {
    $editFaq = $db->fetch("SELECT * FROM faqs WHERE id = :id AND is_archived = 0", ['id' => (int)$_GET['edit']]);
}

$faqs = $db->fetchAll("SELECT * FROM faqs WHERE is_archived = 0 ORDER BY category ASC, display_order ASC");

include __DIR__ . '/partials/header.php';
?>

<div class="grid lg:grid-cols-12 gap-8">
    
    <!-- Left 4 Columns: Form -->
    <div class="lg:col-span-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2">
                <?= $editFaq ? 'Edit FAQ Item' : 'Add FAQ Question' ?>
            </h3>

            <form action="<?= url('admin/faqs.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <?php if ($editFaq): ?>
                    <input type="hidden" name="edit_id" value="<?= $editFaq['id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Section Category *</label>
                    <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:outline-none">
                        <option value="Security Services" <?= ($editFaq['category'] ?? '') === 'Security Services' ? 'selected' : '' ?>>Security Services</option>
                        <option value="Cleaning Services" <?= ($editFaq['category'] ?? '') === 'Cleaning Services' ? 'selected' : '' ?>>Cleaning Services</option>
                        <option value="Compliance & Standards" <?= ($editFaq['category'] ?? '') === 'Compliance & Standards' ? 'selected' : '' ?>>Compliance & Standards</option>
                        <option value="Billing & Contracts" <?= ($editFaq['category'] ?? '') === 'Billing & Contracts' ? 'selected' : '' ?>>Billing & Contracts</option>
                        <option value="General" <?= ($editFaq['category'] ?? '') === 'General' ? 'selected' : '' ?>>General</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Question Text *</label>
                    <input type="text" name="question" required value="<?= e($editFaq['question'] ?? '') ?>" placeholder="e.g. What background vetting do your security guards undergo?" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Answer Body *</label>
                    <textarea name="answer" rows="4" required placeholder="Detailed official answer..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs leading-relaxed focus:outline-none focus:border-[#F39C12]"><?= e($editFaq['answer'] ?? '') ?></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <?php if ($editFaq): ?>
                        <a href="<?= url('admin/faqs.php') ?>" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="flex-1 bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-md">
                        <?= $editFaq ? 'Update FAQ' : 'Save FAQ' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right 8 Columns: FAQs Table -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">
                Global FAQs Repository (<?= count($faqs) ?>)
            </h3>

            <div class="space-y-3">
                <?php foreach ($faqs as $f): ?>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#254E70] bg-white px-2.5 py-0.5 rounded border border-slate-200"><?= e($f['category']) ?></span>
                            <div class="space-x-2 text-xs">
                                <a href="<?= url('admin/faqs.php?edit=' . $f['id']) ?>" class="text-amber-600 hover:text-amber-800" title="Edit"><i class="fas fa-pen-to-square"></i></a>
                                <a href="<?= url('admin/faqs.php?archive_id=' . $f['id']) ?>" class="confirm-action text-red-500 hover:text-red-700" data-confirm="Move this FAQ to Archive Vault?" title="Archive"><i class="fas fa-box-archive"></i></a>
                            </div>
                        </div>
                        <h4 class="font-bold text-xs sm:text-sm text-slate-900"><?= e($f['question']) ?></h4>
                        <p class="text-xs text-slate-600 leading-relaxed"><?= e($f['answer']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
