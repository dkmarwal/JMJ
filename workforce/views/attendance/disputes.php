<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Staff Attendance Dispute Management</h2>
            <p class="text-xs text-slate-400">Review, investigate and adjudicate workforce check-in and overtime discrepancies</p>
        </div>
        <a href="<?= wf_url('attendance') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Live Attendance</span>
        </a>
    </div>

    <!-- Disputes Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4 flex items-center gap-2">
            <i class="fa-solid fa-scale-unbalanced-flip text-amber-400"></i>
            <span>Pending & Resolved Staff Claims (<?= count($disputes) ?>)</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Staff Member</th>
                        <th class="pb-3">Site Complex</th>
                        <th class="pb-3">Dispute Date & Shift</th>
                        <th class="pb-3">Claim Reason</th>
                        <th class="pb-3">Resolution Status</th>
                        <th class="pb-3 pr-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($disputes)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 font-medium">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xl block mb-2"></i>
                                No open attendance disputes recorded. All workforce records reconciled.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($disputes as $d): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2">
                                    <div class="font-bold text-white"><?= wf_e($d['first_name'] . ' ' . $d['last_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= wf_e($d['employee_code']) ?> &bull; <?= wf_e($d['phone']) ?></div>
                                </td>
                                <td class="py-3.5 text-slate-200"><?= wf_e($d['site_name']) ?></td>
                                <td class="py-3.5 text-slate-300">
                                    <div><?= wf_format_date($d['dispute_date']) ?></div>
                                    <div class="text-[10px] text-slate-500"><?= wf_e($d['shift_name']) ?></div>
                                </td>
                                <td class="py-3.5 text-slate-300 max-w-xs">
                                    <p class="truncate" title="<?= wf_e($d['reason']) ?>"><?= wf_e($d['reason']) ?></p>
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $d['status'] === 'approved' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($d['status'] === 'rejected' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30') ?>">
                                        <?= wf_e($d['status'] ?? 'pending') ?>
                                    </span>
                                </td>
                                <td class="py-3.5 pr-2 text-right space-x-2">
                                    <button type="button" onclick="openDisputeModal(<?= $d['id'] ?>, '<?= wf_e($d['first_name'] . ' ' . $d['last_name']) ?>', '<?= wf_e(addslashes($d['reason'])) ?>')" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-semibold text-[11px]">
                                        Review Claim
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dispute Review Modal -->
<div id="disputeModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="text-sm font-bold text-white mb-1">Adjudicate Attendance Dispute</h3>
        <p id="disputeStaffName" class="text-xs text-emerald-400 font-semibold mb-3"></p>
        
        <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-xs text-slate-300 mb-4">
            <span class="text-slate-500 block text-[10px] uppercase font-bold mb-1">Claim Justification:</span>
            <p id="disputeClaimText" class="leading-relaxed"></p>
        </div>

        <form action="<?= wf_url('attendance/override') ?>" method="POST" class="space-y-4">
            <?= wf_csrf_field() ?>
            <input type="hidden" id="disputeAttId" name="attendance_id" value="1">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Decision Action</label>
                <select name="status" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    <option value="VERIFIED">Approve Claim & Mark Verified (Full Day)</option>
                    <option value="CHECKED_OUT">Approve Overtime / Adjustment</option>
                    <option value="REJECTED">Reject Claim (Uphold Flag)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Resolution Notes *</label>
                <textarea name="override_reason" required rows="2" placeholder="State operational verdict / supervisor endorsement..." class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeDisputeModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20">Submit Resolution</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDisputeModal(id, name, reason) {
    document.getElementById('disputeAttId').value = id;
    document.getElementById('disputeStaffName').innerText = 'Staff: ' + name;
    document.getElementById('disputeClaimText').innerText = reason || 'No claim description provided.';
    document.getElementById('disputeModal').classList.remove('hidden');
}
function closeDisputeModal() {
    document.getElementById('disputeModal').classList.add('hidden');
}
</script>
