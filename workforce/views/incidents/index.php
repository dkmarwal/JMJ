<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Incident Command & SOS Emergency Queue</h2>
            <p class="text-xs text-slate-400">Real-time emergency broadcast listener and physical breach management</p>
        </div>
    </div>

    <!-- Active SOS Panic Alerts (Emergency Banner) -->
    <?php if (!empty($sosAlerts)): ?>
        <div class="bg-red-950/40 border-2 border-red-500/50 rounded-2xl p-6 shadow-2xl space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-red-500 animate-ping"></span>
                <h3 class="text-sm font-bold text-red-400 uppercase tracking-wider">Critical Emergency SOS Panic Broadcasts</h3>
            </div>

            <div class="space-y-3">
                <?php foreach ($sosAlerts as $sos): ?>
                    <div class="p-4 rounded-xl bg-slate-950 border border-red-500/30 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-lg font-bold">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white"><?= wf_e($sos['sos_code']) ?> &bull; <span class="text-red-400"><?= wf_e($sos['first_name'] . ' ' . $sos['last_name']) ?></span></div>
                                <div class="text-[11px] text-slate-400"><?= wf_e($sos['site_name']) ?> &bull; Triggered: <?= wf_format_time($sos['trigger_time']) ?> &bull; Mobile: <?= wf_e($sos['phone']) ?></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="tel:<?= wf_e($sos['phone']) ?>" class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold text-xs transition">
                                <i class="fa-solid fa-phone mr-1"></i> Call Guard
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Incident Tickets Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4">Operational Incident Log</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Incident #</th>
                        <th class="pb-3">Site Location</th>
                        <th class="pb-3">Severity</th>
                        <th class="pb-3">Type</th>
                        <th class="pb-3">Reported By</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($incidents)): ?>
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No active incident reports logged. All sites secure.</td></tr>
                    <?php else: ?>
                        <?php foreach ($incidents as $inc): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2 font-mono font-bold text-white"><?= wf_e($inc['incident_number']) ?></td>
                                <td class="py-3.5 text-slate-300"><?= wf_e($inc['site_name']) ?></td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $inc['severity'] === 'CRITICAL' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400' ?>">
                                        <?= wf_e($inc['severity']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 uppercase text-slate-400 text-[10px]"><?= wf_e($inc['incident_type']) ?></td>
                                <td class="py-3.5 text-slate-300"><?= wf_e($inc['reporter_name']) ?></td>
                                <td class="py-3.5 font-bold uppercase text-emerald-400 text-[10px]"><?= wf_e($inc['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
