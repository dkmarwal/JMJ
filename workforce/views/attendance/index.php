<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Live Attendance & 4-Layer Verifications</h2>
            <p class="text-xs text-slate-400">GPS Geofence + Dynamic QR + Selfie Liveness + Risk Scored Telemetry</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= wf_url('reports/export-attendance') ?>" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
                <i class="fa-solid fa-file-csv text-emerald-400"></i>
                <span>Export CSV</span>
            </a>
            <a href="<?= wf_url('attendance/muster') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-table-list"></i>
                <span>Muster Roll Matrix</span>
            </a>
        </div>
    </div>

    <!-- Filters Strip -->
    <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl flex flex-wrap items-center justify-between gap-4">
        <form action="<?= wf_url('attendance') ?>" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Date</label>
                <input type="date" name="date" value="<?= wf_e($selectedDate) ?>" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Client Site</label>
                <select name="site_id" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    <option value="">All Deployed Sites</option>
                    <?php foreach ($sites as $st): ?>
                        <option value="<?= $st['id'] ?>" <?= ($selectedSite == $st['id']) ? 'selected' : '' ?>><?= wf_e($st['site_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">Filter</button>
            </div>
        </form>
        <div class="text-xs text-slate-400">Total Check-Ins: <strong class="text-white"><?= count($records) ?></strong></div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Staff Details</th>
                        <th class="pb-3">Site & Shift</th>
                        <th class="pb-3">Check-In / Out</th>
                        <th class="pb-3">4-Layer Telemetry</th>
                        <th class="pb-3">Verification Score</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 pr-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($records)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">No verified attendance records logged for this filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $row): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2">
                                    <div class="font-bold text-white"><?= wf_e($row['first_name'] . ' ' . $row['last_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= wf_e($row['employee_code']) ?> &bull; <?= wf_e($row['designation']) ?></div>
                                </td>
                                <td class="py-3.5">
                                    <div class="text-slate-200 font-medium"><?= wf_e($row['site_name']) ?></div>
                                    <div class="text-[10px] text-slate-400"><?= wf_e($row['shift_name']) ?></div>
                                </td>
                                <td class="py-3.5 font-mono text-slate-300">
                                    <div class="text-emerald-400">IN: <?= wf_format_time($row['check_in_time']) ?></div>
                                    <div class="text-slate-400">OUT: <?= $row['check_out_time'] ? wf_format_time($row['check_out_time']) : '<span class="text-amber-400">On Duty</span>' ?></div>
                                </td>
                                <td class="py-3.5 space-y-1">
                                    <div class="flex items-center gap-1.5 text-[10px]">
                                        <span class="text-slate-500">Geo:</span>
                                        <span class="px-1.5 py-0.2 rounded font-bold uppercase <?= $row['geofence_status'] === 'PASS' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' ?>">
                                            <?= $row['geofence_status'] ?? 'PASS' ?> (<?= round((float)($row['geofence_distance_meters'] ?? 0), 1) ?>m)
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px]">
                                        <span class="text-slate-500">QR:</span>
                                        <span class="px-1.5 py-0.2 rounded font-bold uppercase <?= $row['qr_status'] === 'VALID' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400' ?>">
                                            <?= $row['qr_status'] ?? 'N/A' ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-14 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= (int)$row['verification_score'] ?>%"></div>
                                        </div>
                                        <span class="font-mono font-bold text-emerald-400"><?= (int)$row['verification_score'] ?>%</span>
                                    </div>
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <?= wf_e($row['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 pr-2 text-right">
                                    <button type="button" onclick="openOverrideModal(<?= $row['id'] ?>, '<?= wf_e($row['first_name'] . ' ' . $row['last_name']) ?>')" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-[11px]">
                                        Override
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

<!-- Manual Override Modal -->
<div id="overrideModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="text-sm font-bold text-white mb-1">Manual Attendance Adjustment</h3>
        <p id="modalStaffName" class="text-xs text-emerald-400 font-semibold mb-4"></p>
        
        <form action="<?= wf_url('attendance/override') ?>" method="POST" class="space-y-4">
            <?= wf_csrf_field() ?>
            <input type="hidden" id="overrideAttId" name="attendance_id" value="">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Adjustment Status</label>
                <select name="status" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    <option value="VERIFIED">VERIFIED (Present & Approved)</option>
                    <option value="CHECKED_OUT">CHECKED_OUT (Duty Concluded)</option>
                    <option value="REJECTED">REJECTED (Invalid / Discarded)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Mandatory Justification Reason *</label>
                <textarea name="override_reason" required rows="3" placeholder="State operational reason (e.g. Approved mobile camera hardware fault / Supervisor physical verification)..." class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeOverrideModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20">Apply Adjustment</button>
            </div>
        </form>
    </div>
</div>

<script>
function openOverrideModal(id, staffName) {
    document.getElementById('overrideAttId').value = id;
    document.getElementById('modalStaffName').innerText = 'Staff: ' + staffName + ' (Attendance #' + id + ')';
    document.getElementById('overrideModal').classList.remove('hidden');
}
function closeOverrideModal() {
    document.getElementById('overrideModal').classList.add('hidden');
}
</script>
