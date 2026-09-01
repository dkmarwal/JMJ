<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Workforce Payroll & Salary Processing</h2>
            <p class="text-xs text-slate-400">100% verified attendance-driven wage calculations with PF, ESIC & OT automation</p>
        </div>
    </div>

    <!-- Monthly Calculation Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white mb-4">Calculate New Payroll Period</h3>
        <form action="<?= wf_url('payroll/calculate') ?>" method="POST" class="flex flex-wrap items-end gap-4">
            <?= wf_csrf_field() ?>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Month</label>
                <select name="month" class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= (date('n') == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Year</label>
                <input type="number" name="year" value="<?= date('Y') ?>" class="w-24 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
            </div>
            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-calculator"></i>
                <span>Run Payroll Engine</span>
            </button>
        </form>
    </div>

    <!-- Calculated Payroll Periods -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white mb-4">Processed Monthly Batches</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Period</th>
                        <th class="pb-3">Workforce Count</th>
                        <th class="pb-3">Gross Earnings</th>
                        <th class="pb-3">Net Disbursable</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 pr-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($periods)): ?>
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No payroll periods processed yet. Click 'Run Payroll Engine' above.</td></tr>
                    <?php else: ?>
                        <?php foreach ($periods as $p): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2 font-bold text-white"><?= date('F Y', mktime(0, 0, 0, (int)$p['month'], 1, (int)$p['year'])) ?></td>
                                <td class="py-3.5 text-slate-300"><?= $p['staff_count'] ?> Employees</td>
                                <td class="py-3.5 font-mono text-slate-300"><?= wf_format_currency($p['total_gross']) ?></td>
                                <td class="py-3.5 font-mono font-bold text-emerald-400"><?= wf_format_currency($p['total_net']) ?></td>
                                <td class="py-3.5 uppercase font-bold text-[10px] text-emerald-400"><?= wf_e($p['status']) ?></td>
                                <td class="py-3.5 pr-2 text-right">
                                    <a href="<?= wf_url('payroll/period?id=' . $p['id']) ?>" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-semibold text-[11px]">View Salary Sheet &rarr;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
