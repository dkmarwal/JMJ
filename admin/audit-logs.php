<?php
/**
 * JMJ Enterprises Solutions - Security Audit Logs Viewer
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Security Audit Logs';
$db = \Core\Database::getInstance();

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

$total = (int)$db->fetchColumn("SELECT COUNT(*) FROM audit_logs");
$totalPages = (int)ceil($total / $limit);

$logs = $db->fetchAll(
    "SELECT a.*, u.name as user_name, u.email as user_email 
     FROM audit_logs a 
     LEFT JOIN users u ON a.user_id = u.id 
     ORDER BY a.id DESC LIMIT {$limit} OFFSET {$offset}"
);

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex justify-between items-center">
        <div>
            <h2 class="text-xl font-black text-slate-900">Security Audit Trail (<?= $total ?> Events)</h2>
            <p class="text-xs text-slate-500">Immutable chronological record of administrative actions and content alterations.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Timestamp</th>
                        <th class="py-3 px-4">Operator</th>
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-6">Event Description</th>
                        <th class="py-3 px-4 font-mono">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-6 font-mono text-[11px] text-slate-500">
                                <?= format_date($log['created_at'], 'd M Y, h:i A') ?>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <?= e($log['user_name'] ?: 'System / Guest') ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-700">
                                    <?= e($log['action']) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-800">
                                <?= e($log['description']) ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400">
                                <?= e($log['ip_address']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="p-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
                <span>Page <?= $page ?> of <?= $totalPages ?></span>
                <div class="flex items-center space-x-1">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="<?= url('admin/audit-logs.php?page=' . $p) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition <?= $p === $page ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
