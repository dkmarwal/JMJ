<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Operational Analytics & Reports</h2>
            <p class="text-xs text-slate-400">Attendance trend analytics, verification score metrics, and statutory CSV exports</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= wf_url('reports/export-attendance') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span>Download Master Attendance CSV</span>
            </a>
        </div>
    </div>

    <!-- Attendance Performance Trend -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4">14-Day Attendance Fulfillment & Score Log</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Date</th>
                        <th class="pb-3">Present Staff</th>
                        <th class="pb-3">Flagged for Review</th>
                        <th class="pb-3">Average Verification Score</th>
                        <th class="pb-3">Compliance Health</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    <?php if (empty($attSummary)): ?>
                        <tr><td colspan="5" class="py-6 text-center text-slate-500 font-sans">No trend data available for this range.</td></tr>
                    <?php else: ?>
                        <?php foreach ($attSummary as $sum): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2 font-bold text-white"><?= wf_format_date($sum['attendance_date']) ?></td>
                                <td class="py-3.5 text-emerald-400 font-bold"><?= $sum['present_count'] ?> Staff</td>
                                <td class="py-3.5 text-amber-400"><?= $sum['review_count'] ?> Flagged</td>
                                <td class="py-3.5 text-white"><?= round((float)$sum['avg_score'], 1) ?>%</td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold font-sans uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Optimal
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
