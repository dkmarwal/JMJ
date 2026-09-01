<?php
/**
 * JMJ Enterprises Solutions - Global Website Settings Manager
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Global Corporate Settings';
$db = \Core\Database::getInstance();

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $settings = $_POST['settings'] ?? [];
        foreach ($settings as $key => $val) {
            \Services\SettingService::set($key, (string)$val);
        }
        \Services\AuditService::log("Updated global website settings", 'settings', 0, 'UPDATE');
        \Core\Session::setFlash('success', 'Corporate settings updated successfully.');
        redirect('admin/settings.php');
    }
}

$allSettings = $db->fetchAll("SELECT * FROM settings ORDER BY setting_group ASC, key_name ASC");
$grouped = [];
foreach ($allSettings as $s) {
    $grouped[$s['setting_group']][] = $s;
}

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-black text-slate-900">Enterprise Settings & Dynamic Parameters</h2>
        <p class="text-xs text-slate-500">Configure corporate address, dispatch phone numbers, social links, and statistics displayed across the site.</p>
    </div>

    <form action="<?= url('admin/settings.php') ?>" method="POST" class="space-y-8">
        <?= csrf_field() ?>

        <?php foreach ($grouped as $groupName => $items): ?>
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2 text-[#254E70]">
                    <?= e(ucfirst($groupName)) ?> Configurations
                </h3>

                <div class="grid sm:grid-cols-2 gap-4">
                    <?php foreach ($items as $item): ?>
                        <div class="space-y-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500"><?= e(str_replace('_', ' ', $item['key_name'])) ?></label>
                            <?php if (($item['field_type'] ?? 'text') === 'textarea'): ?>
                                <textarea name="settings[<?= e($item['key_name']) ?>]" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#F39C12]"><?= e($item['key_value']) ?></textarea>
                            <?php else: ?>
                                <input type="<?= ($item['field_type'] ?? 'text') === 'number' ? 'number' : 'text' ?>" name="settings[<?= e($item['key_name']) ?>]" value="<?= e($item['key_value']) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#F39C12]">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="flex justify-end">
            <button type="submit" class="bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black py-3.5 px-8 rounded-2xl text-xs uppercase tracking-widest transition shadow-lg">
                Save All Global Settings
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
