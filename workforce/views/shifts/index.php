<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Shift Schedules & Configurations</h2>
            <p class="text-xs text-slate-400">Operating hours, grace periods, and night shift parameters</p>
        </div>
        <a href="<?= wf_url('shifts/roster') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
            <i class="fa-solid fa-calendar-days"></i>
            <span>Open Operational Roster</span>
        </a>
    </div>

    <!-- Shifts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($shifts as $sh): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $sh['is_night_shift'] ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30' ?>">
                            <?= $sh['is_night_shift'] ? 'Night Patrol Shift' : 'Day Guard Shift' ?>
                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold"><?= wf_format_time($sh['start_time']) ?> &mdash; <?= wf_format_time($sh['end_time']) ?></span>
                    </div>

                    <h3 class="text-sm font-bold text-white mb-1"><?= wf_e($sh['name']) ?></h3>
                    <p class="text-xs text-slate-400 mb-4"><?= wf_e($sh['site_name']) ?> (<?= wf_e($sh['client_name']) ?>)</p>

                    <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-1.5 text-xs text-slate-300">
                        <div class="flex justify-between"><span class="text-slate-500">Required Guards:</span> <strong class="text-white"><?= $sh['required_guards'] ?></strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Required Cleaners:</span> <span><?= $sh['required_cleaners'] ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Grace Period:</span> <span class="font-mono text-amber-400"><?= $sh['grace_period_mins'] ?> mins</span></div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Deployed Staff: <strong class="text-white"><?= $sh['deployed_count'] ?></strong></span>
                    <a href="<?= wf_url('shifts/roster?site_id=' . $sh['site_id']) ?>" class="text-emerald-400 hover:text-emerald-300 font-semibold">View Roster &rarr;</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
