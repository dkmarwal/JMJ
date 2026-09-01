<?php
/**
 * JMJ Enterprises Solutions - Staff & RBAC Users Management
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$pageTitle = 'Staff & Role-Based Access Control';
$db = \Core\Database::getInstance();

// Handle Create / Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Token expired.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 2);
        $password = $_POST['password'] ?? '';
        $editId = !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;

        if (!empty($name) && !empty($email)) {
            if ($editId) {
                $userData = [
                    'role_id' => $roleId,
                    'name'    => $name,
                    'email'   => $email
                ];
                if (!empty($password)) {
                    $userData['password'] = password_hash($password, PASSWORD_BCRYPT);
                }
                $db->update('users', $userData, 'id = :id', ['id' => $editId]);
                \Services\AuditService::log("Updated staff user #{$editId}", 'user', $editId, 'UPDATE');
                \Core\Session::setFlash('success', 'User profile updated.');
            } else {
                if (empty($password)) {
                    $password = 'Admin@123456';
                }
                $newId = $db->insert('users', [
                    'role_id'  => $roleId,
                    'name'     => $name,
                    'email'    => $email,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'status'   => 'active'
                ]);
                \Services\AuditService::log("Created staff user #{$newId} ({$email})", 'user', (int)$newId, 'CREATE');
                \Core\Session::setFlash('success', 'Staff user account created.');
            }
        }
        redirect('admin/users.php');
    }
}

// Handle Archive
if (isset($_GET['archive_id'])) {
    $archiveId = (int)$_GET['archive_id'];
    if ($archiveId === (int)$currentUser['id']) {
        \Core\Session::setFlash('error', 'You cannot archive your own active session account.');
    } else {
        $db->update('users', [
            'is_archived' => 1,
            'archived_at' => date('Y-m-d H:i:s'),
            'archived_by' => $currentUser['id']
        ], 'id = :id', ['id' => $archiveId]);
        \Services\AuditService::log("Archived user #{$archiveId}", 'user', $archiveId, 'ARCHIVE');
        \Core\Session::setFlash('success', 'User moved to Archive Vault.');
    }
    redirect('admin/users.php');
}

$editUser = null;
if (isset($_GET['edit'])) {
    $editUser = $db->fetch("SELECT * FROM users WHERE id = :id AND is_archived = 0", ['id' => (int)$_GET['edit']]);
}

$users = $db->fetchAll(
    "SELECT u.*, r.label as role_name 
     FROM users u 
     JOIN roles r ON u.role_id = r.id 
     WHERE u.is_archived = 0 
     ORDER BY u.id ASC"
);

$roles = $db->fetchAll("SELECT * FROM roles ORDER BY id ASC");

include __DIR__ . '/partials/header.php';
?>

<div class="grid lg:grid-cols-12 gap-8">
    
    <!-- Left 4 Columns: Form -->
    <div class="lg:col-span-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2">
                <?= $editUser ? 'Edit Staff Account' : 'Register New Staff Member' ?>
            </h3>

            <form action="<?= url('admin/users.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <?php if ($editUser): ?>
                    <input type="hidden" name="edit_id" value="<?= $editUser['id'] ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Full Name *</label>
                    <input type="text" name="name" required value="<?= e($editUser['name'] ?? '') ?>" placeholder="e.g. Sanu Kumar" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email Address *</label>
                    <input type="email" name="email" required value="<?= e($editUser['email'] ?? '') ?>" placeholder="sanu@jmjenterprises.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Access Role *</label>
                    <select name="role_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:outline-none">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($editUser['role_id'] ?? 2) == $r['id'] ? 'selected' : '' ?>><?= e($r['label']) ?> (<?= e($r['name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1"><?= $editUser ? 'Change Password (Leave blank to keep current)' : 'Account Password *' ?></label>
                    <input type="password" name="password" <?= $editUser ? '' : 'required' ?> placeholder="••••••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <?php if ($editUser): ?>
                        <a href="<?= url('admin/users.php') ?>" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="flex-1 bg-[#090F1C] hover:bg-[#254E70] text-white font-black py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-md">
                        <?= $editUser ? 'Update Staff Member' : 'Register Account' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right 8 Columns: Staff Table -->
    <div class="lg:col-span-8">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">
                Authorized Personnel (<?= count($users) ?>)
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase text-[10px] font-bold text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Name & Email</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-6">
                                    <span class="font-bold text-slate-900 block"><?= e($u['name']) ?></span>
                                    <span class="text-slate-400 text-[11px]"><?= e($u['email']) ?></span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-700">
                                    <?= e($u['role_name']) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">
                                        <?= e($u['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right space-x-2">
                                    <a href="<?= url('admin/users.php?edit=' . $u['id']) ?>" class="p-2 rounded-lg text-amber-600 hover:text-amber-800" title="Edit"><i class="fas fa-pen-to-square"></i></a>
                                    <?php if ($u['id'] !== $currentUser['id']): ?>
                                        <a href="<?= url('admin/users.php?archive_id=' . $u['id']) ?>" class="confirm-action p-2 rounded-lg text-red-500 hover:text-red-700" data-confirm="Move this user account to Archive Vault?" title="Archive"><i class="fas fa-box-archive"></i></a>
                                    <?php endif; ?>
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
