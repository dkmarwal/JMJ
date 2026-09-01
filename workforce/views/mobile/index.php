<!-- Staff Identity & Post Card -->
<div class="bg-gradient-to-br from-slate-900 to-slate-800 border border-slate-800 rounded-3xl p-5 shadow-2xl relative overflow-hidden">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-600/30 border border-emerald-500/40 flex items-center justify-center text-emerald-400 font-bold text-lg">
            <?= strtoupper(substr($user['name'], 0, 2)) ?>
        </div>
        <div>
            <h2 class="text-sm font-bold text-white"><?= wf_e($user['name']) ?></h2>
            <p class="text-[11px] text-emerald-400 font-medium"><?= wf_e($employee['designation'] ?? 'Field Staff') ?> &bull; <?= wf_e($employee['employee_code'] ?? 'EMP-001') ?></p>
        </div>
    </div>

    <!-- Assigned Duty Complex -->
    <?php if ($deployment): ?>
        <div class="bg-slate-950/70 rounded-2xl p-3.5 border border-slate-800/80 space-y-1 text-xs">
            <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Assigned Site Post:</div>
            <div class="font-bold text-white"><?= wf_e($deployment['site_name']) ?></div>
            <div class="text-[11px] text-slate-400"><?= wf_e($deployment['client_name']) ?> &bull; <span class="text-emerald-400 font-mono"><?= wf_format_time($deployment['start_time']) ?> - <?= wf_format_time($deployment['end_time']) ?></span></div>
        </div>
    <?php endif; ?>
</div>

<!-- Today's Attendance State -->
<div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Shift Duty Status</span>
        <?php if ($todayAttendance): ?>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <?= wf_e($todayAttendance['status']) ?>
            </span>
        <?php else: ?>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30">
                Not Checked In
            </span>
        <?php endif; ?>
    </div>

    <?php if ($todayAttendance): ?>
        <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 flex justify-between items-center text-xs">
            <div>
                <span class="text-slate-500 block text-[10px]">Check-In Logged:</span>
                <span class="font-mono font-bold text-emerald-400"><?= wf_format_time($todayAttendance['check_in_time']) ?></span>
            </div>
            <div class="text-right">
                <span class="text-slate-500 block text-[10px]">Verification:</span>
                <span class="font-bold text-white"><?= (int)$todayAttendance['verification_score'] ?>% Validated</span>
            </div>
        </div>

        <?php if (!$todayAttendance['check_out_time']): ?>
            <button type="button" onclick="performCheckOut()" class="w-full py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-red-400 border border-red-500/30 font-bold text-xs transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Conclude Shift (Check-Out)</span>
            </button>
        <?php else: ?>
            <div class="text-center text-xs text-slate-400 py-1">
                Shift completed at <span class="font-mono text-white"><?= wf_format_time($todayAttendance['check_out_time']) ?></span> (Total: <?= round(((int)$todayAttendance['total_work_minutes']) / 60, 2) ?> hrs)
            </div>
        <?php endif; ?>
    <?php else: ?>
        <a href="<?= wf_url('mobile/check-in') ?>" class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs tracking-wide shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-fingerprint text-sm"></i>
            <span>Start Duty &bull; 4-Layer Verification Check-In</span>
        </a>
    <?php endif; ?>
</div>

<!-- Patrol & Field Actions Grid -->
<div class="grid grid-cols-2 gap-3">
    <a href="<?= wf_url('mobile/patrol') ?>" class="bg-slate-900 border border-slate-800 p-4 rounded-2xl shadow-xl flex flex-col justify-between h-28 hover:border-emerald-500 transition">
        <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm">
            <i class="fa-solid fa-route"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-white">Guard Patrol Tour</div>
            <div class="text-[10px] text-slate-400">Scan QR Checkpoints</div>
        </div>
    </a>

    <a href="#" onclick="triggerSOSPanic(); return false;" class="bg-slate-900 border border-red-500/30 p-4 rounded-2xl shadow-xl flex flex-col justify-between h-28 hover:bg-red-500/10 transition">
        <div class="w-8 h-8 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-sm">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <div class="text-xs font-bold text-red-400">Emergency Panic</div>
            <div class="text-[10px] text-slate-400">Dispatch Hotline</div>
        </div>
    </a>
</div>

<script>
function performCheckOut() {
    if (confirm('Conclude shift duty and submit check-out?')) {
        fetch('<?= wf_url('api/attendance/check-out') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                employee_id: <?= (int)($user['employee_id'] ?? 1) ?>
            })
        }).then(r => r.json()).then(res => {
            alert(res.message);
            window.location.reload();
        });
    }
}
</script>
