<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Field Officer Site Audits</h2>
            <p class="text-xs text-slate-400">Surprise site inspections, turnout quality scoring, and register compliance</p>
        </div>
        <a href="<?= wf_url('audits/create') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
            <i class="fa-solid fa-clipboard-check"></i>
            <span>Conduct New Audit</span>
        </a>
    </div>

    <!-- Audits Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Audit #</th>
                        <th class="pb-3">Site Location</th>
                        <th class="pb-3">Auditor (Field Officer)</th>
                        <th class="pb-3">Date & Time</th>
                        <th class="pb-3">Guards Present</th>
                        <th class="pb-3">Compliance Rating</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($audits)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">No field audits logged yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($audits as $sa): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2 font-mono font-bold text-white"><?= wf_e($sa['audit_number']) ?></td>
                                <td class="py-3.5 text-slate-200"><?= wf_e($sa['site_name']) ?></td>
                                <td class="py-3.5 text-slate-300"><?= wf_e($sa['auditor_name']) ?></td>
                                <td class="py-3.5 text-slate-400"><?= wf_format_date($sa['audit_date'], 'd M Y, h:i A') ?></td>
                                <td class="py-3.5 font-bold text-white"><?= $sa['guards_present'] ?> Guards</td>
                                <td class="py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-14 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= (int)$sa['total_compliance_score'] ?>%"></div>
                                        </div>
                                        <span class="font-bold text-emerald-400"><?= (int)$sa['total_compliance_score'] ?>%</span>
                                    </div>
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <?= wf_e($sa['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
