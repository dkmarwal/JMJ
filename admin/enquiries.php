<?php
/**
 * JMJ Enterprises Solutions - Enquiries & Leads CRM
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Leads & Enquiries CRM';
$db = \Core\Database::getInstance();

// Handle Status Update & Notes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enquiry_id'])) {
    if (\Core\Csrf::validate()) {
        $enqId = (int)$_POST['enquiry_id'];
        $status = $_POST['status'] ?? 'new';
        $adminNotes = trim($_POST['admin_notes'] ?? '');

        $db->update('enquiries', [
            'status'      => $status,
            'admin_notes' => $adminNotes
        ], 'id = :id', ['id' => $enqId]);

        \Services\AuditService::log("Updated enquiry #{$enqId} status to {$status}", 'enquiry', $enqId, 'UPDATE');
        \Core\Session::setFlash('success', 'Lead record updated.');
    }
    redirect('admin/enquiries.php');
}

// Handle Soft Delete to Archive Vault
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    $db->update('enquiries', [
        'is_archived' => 1,
        'archived_at' => date('Y-m-d H:i:s'),
        'archived_by' => $currentUser['id']
    ], 'id = :id', ['id' => $archiveId]);
    \Services\AuditService::log("Archived lead #{$archiveId} to Archive Vault", 'enquiry', $archiveId, 'ARCHIVE');
    \Core\Session::setFlash('success', 'Lead moved to Archive Vault.');
    redirect('admin/enquiries.php');
}

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=jmj_leads_' . date('Ymd_His') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Type', 'Name', 'Company', 'Email', 'Phone', 'Service Required', 'Location', 'Message', 'Status', 'Date']);
    $allRows = $db->fetchAll("SELECT * FROM enquiries WHERE is_archived = 0 ORDER BY id DESC");
    foreach ($allRows as $r) {
        fputcsv($output, [
            $r['id'], $r['type'], $r['name'], $r['company'], $r['email'],
            $r['phone'], $r['service_required'], $r['location'], $r['message'],
            $r['status'], $r['created_at']
        ]);
    }
    fclose($output);
    exit;
}

$statusFilter = $_GET['status'] ?? 'all';
$typeFilter = $_GET['type'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$where = "WHERE is_archived = 0";
$params = [];

if ($statusFilter !== 'all') {
    $where .= " AND status = :st";
    $params['st'] = $statusFilter;
}

if ($typeFilter !== 'all') {
    $where .= " AND type = :tp";
    $params['tp'] = $typeFilter;
}

if (!empty($search)) {
    $where .= " AND (name LIKE :s OR email LIKE :s OR phone LIKE :s OR company LIKE :s)";
    $params['s'] = '%' . $search . '%';
}

$enquiries = $db->fetchAll("SELECT * FROM enquiries {$where} ORDER BY id DESC", $params);

include __DIR__ . '/partials/header.php';
?>

<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-black text-slate-900">Lead Pipeline & Enquiries (<?= count($enquiries) ?>)</h2>
            <p class="text-xs text-slate-500">Track inbound quote requests, site surveys, and corporate contact messages.</p>
        </div>
        <a href="<?= url('admin/enquiries.php?export=csv') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition flex items-center shadow-md">
            <i class="fas fa-file-csv mr-2"></i> Export to CSV
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center space-x-1 text-xs font-bold overflow-x-auto w-full md:w-auto">
            <a href="<?= url('admin/enquiries.php?status=all') ?>" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'all' ? 'bg-[#090F1C] text-white' : 'text-slate-600 hover:bg-slate-100' ?>">All</a>
            <a href="<?= url('admin/enquiries.php?status=new') ?>" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'new' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">New</a>
            <a href="<?= url('admin/enquiries.php?status=contacted') ?>" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'contacted' ? 'bg-amber-500 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Contacted</a>
            <a href="<?= url('admin/enquiries.php?status=converted') ?>" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'converted' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Converted</a>
            <a href="<?= url('admin/enquiries.php?status=closed') ?>" class="px-3 py-1.5 rounded-lg transition <?= $statusFilter === 'closed' ? 'bg-slate-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">Closed</a>
        </div>

        <form action="<?= url('admin/enquiries.php') ?>" method="GET" class="flex items-center space-x-2 w-full md:w-auto">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search name, phone, email..." class="w-full md:w-64 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-[#F39C12]">
            <button type="submit" class="bg-slate-900 text-white px-3 py-2 rounded-xl text-xs"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!-- Enquiries Grid / Accordion List -->
    <div class="space-y-4">
        <?php if (empty($enquiries)): ?>
            <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center text-slate-400 text-xs">
                No inbound leads found matching criteria.
            </div>
        <?php else: ?>
            <?php foreach ($enquiries as $e): ?>
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase <?= $e['status'] === 'new' ? 'bg-blue-100 text-blue-800' : ($e['status'] === 'converted' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') ?>">
                                <?= e($e['status']) ?>
                            </span>
                            <span class="text-xs font-extrabold text-[#F39C12] uppercase tracking-wider">[<?= e($e['type']) ?>]</span>
                            <h3 class="text-base font-black text-slate-900"><?= e($e['name']) ?></h3>
                            <?php if (!empty($e['company'])): ?>
                                <span class="text-xs text-slate-500 font-semibold">(<?= e($e['company']) ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center space-x-3 text-xs text-slate-400">
                            <span><i class="fas fa-clock mr-1"></i> <?= format_date($e['created_at']) ?></span>
                            <a href="<?= url('admin/enquiries.php?archive_id=' . $e['id']) ?>" class="confirm-action text-red-500 hover:text-red-700" data-confirm="Move this lead to Archive Vault?" title="Archive"><i class="fas fa-box-archive"></i></a>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Contact Channels</span>
                            <span class="font-bold text-slate-800 block mt-0.5"><i class="fas fa-envelope text-slate-400 mr-1"></i> <?= e($e['email']) ?></span>
                            <span class="font-bold text-slate-800 block mt-0.5"><i class="fas fa-phone text-slate-400 mr-1"></i> <a href="tel:<?= e($e['phone']) ?>" class="text-blue-600 underline"><?= e($e['phone']) ?></a></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Requirement & City</span>
                            <span class="font-bold text-slate-800 block mt-0.5"><?= e($e['service_required'] ?: 'General Quote') ?></span>
                            <span class="text-slate-500 block mt-0.5"><i class="fas fa-location-dot text-slate-400 mr-1"></i> <?= e($e['location'] ?: 'Not Specified') ?></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Message / Scope</span>
                            <p class="text-slate-600 italic line-clamp-2 mt-0.5"><?= e($e['message'] ?: 'No message text provided.') ?></p>
                        </div>
                    </div>

                    <!-- Status & Internal Notes Form -->
                    <form action="<?= url('admin/enquiries.php') ?>" method="POST" class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">

                        <div class="w-full sm:w-44">
                            <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-800 focus:outline-none">
                                <option value="new" <?= $e['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                <option value="contacted" <?= $e['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                <option value="in_progress" <?= $e['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="converted" <?= $e['status'] === 'converted' ? 'selected' : '' ?>>Converted</option>
                                <option value="closed" <?= $e['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>

                        <div class="flex-1 w-full">
                            <input type="text" name="admin_notes" value="<?= e($e['admin_notes'] ?? '') ?>" placeholder="Add internal notes (e.g. Called client, quote sent on email)..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:outline-none">
                        </div>

                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-1.5 rounded-xl text-xs transition shrink-0">
                            Update Record
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
