<?php
/**
 * JMJ Enterprises Solutions - Flash Toast Notification Partial
 */
$flashes = \Core\Session::getFlashes();
if (!empty($flashes)): ?>
    <div class="fixed bottom-6 right-6 z-50 space-y-3">
        <?php foreach ($flashes as $flash): ?>
            <div class="flex items-center p-4 rounded-xl shadow-2xl text-sm font-semibold text-white <?= $flash['type'] === 'success' ? 'bg-slate-900 border-l-4 border-amber-500' : 'bg-red-900 border-l-4 border-red-500' ?> transition-all duration-300">
                <i class="fas <?= $flash['type'] === 'success' ? 'fa-circle-check text-amber-400' : 'fa-circle-exclamation text-red-400' ?> text-lg mr-3"></i>
                <div><?= e($flash['message']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
