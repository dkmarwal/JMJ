<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-lg font-bold text-white tracking-tight">Emergency Reliever Matching & Dispatch</h2>
        <p class="text-xs text-slate-400">Identify verified standby staff to backfill absent guards and prevent SLA penalties</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-users-rays text-emerald-400"></i>
            <span>Available Standby Workforce (<?= count($relievers) ?> candidates found)</span>
        </h3>

        <?php if (empty($relievers)): ?>
            <p class="text-xs text-slate-500 py-6 text-center">All registered workforce personnel are currently assigned on active rosters today.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($relievers as $cand): ?>
                    <form action="<?= wf_url('shifts/dispatch-reliever') ?>" method="POST" class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between hover:border-slate-700 transition">
                        <?= wf_csrf_field() ?>
                        <input type="hidden" name="site_id" value="<?= $site['id'] ?? 1 ?>">
                        <input type="hidden" name="shift_id" value="<?= $shiftId ?? 1 ?>">
                        <input type="hidden" name="reliever_id" value="<?= $cand['id'] ?>">
                        <input type="hidden" name="absent_employee_id" value="1">

                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center font-bold text-sm">
                                <?= strtoupper(substr($cand['first_name'], 0, 1) . substr($cand['last_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white"><?= wf_e($cand['first_name'] . ' ' . $cand['last_name']) ?> <span class="text-slate-500 font-mono font-normal">(<?= wf_e($cand['employee_code']) ?>)</span></div>
                                <div class="text-[11px] text-slate-400"><?= wf_e($cand['category_name']) ?> &bull; <?= wf_e($cand['branch_name'] ?? 'HQ') ?> &bull; Phone: <?= wf_e($cand['phone']) ?></div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right hidden sm:block">
                                <span class="text-[10px] uppercase font-bold text-emerald-400">Match Score: <?= $cand['match_score'] ?>%</span>
                                <div class="text-[10px] text-slate-500">Standby Available</div>
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition shadow-lg shadow-blue-600/20">
                                Dispatch to Site
                            </button>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
