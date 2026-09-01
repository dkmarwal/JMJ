<?php
/**
 * JMJ Enterprises Solutions - Global SEO & Metadata Manager
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Global SEO & Metadata Manager';
$db = \Core\Database::getInstance();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $metaId = (int)($_POST['meta_id'] ?? 0);
        if ($metaId) {
            $db->update('seo_metadata', [
                'meta_title'       => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? ''),
                'meta_keywords'    => trim($_POST['meta_keywords'] ?? ''),
                'canonical_url'    => trim($_POST['canonical_url'] ?? ''),
                'robots'           => trim($_POST['robots'] ?? 'index, follow')
            ], 'id = :id', ['id' => $metaId]);

            \Services\AuditService::log("Updated SEO metadata #{$metaId}", 'seo', $metaId, 'UPDATE');
            \Core\Session::setFlash('success', 'SEO metadata parameters updated.');
        }
        redirect('admin/seo.php');
    }
}

$routes = $db->fetchAll("SELECT * FROM seo_metadata ORDER BY id ASC");

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-black text-slate-900">SEO & Structured Meta Configurations</h2>
        <p class="text-xs text-slate-500">Fine-tune search engine snippets, canonical tags, and OpenGraph parameters for core landing routes.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <?php foreach ($routes as $r): ?>
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#F39C12]">Route Pattern</span>
                        <h3 class="text-base font-black text-slate-900 font-mono">/<?= e($r['page_route']) ?></h3>
                    </div>
                </div>

                <form action="<?= url('admin/seo.php') ?>" method="POST" class="space-y-3">
                    <?= csrf_field() ?>
                    <input type="hidden" name="meta_id" value="<?= $r['id'] ?>">

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="<?= e($r['meta_title']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-[#F39C12]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-[#F39C12]"><?= e($r['meta_description']) ?></textarea>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Keywords</label>
                            <input type="text" name="meta_keywords" value="<?= e($r['meta_keywords']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Robots Directive</label>
                            <input type="text" name="robots" value="<?= e($r['robots'] ?? 'index, follow') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#090F1C] hover:bg-[#254E70] text-white font-bold py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition">
                        Update <?= e($r['page_route']) ?> SEO
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
