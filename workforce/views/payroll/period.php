<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Salary Statement &bull; <?= date('F Y', mktime(0, 0, 0, (int)$period['month'], 1, (int)$period['year'])) ?></h2>
            <p class="text-xs text-slate-400">Verified attendance breakdown, allowances, PF, ESIC and net transfer amount</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
                <i class="fa-solid fa-print mr-1.5"></i> Print Bank Advice
            </button>
        </div>
    </div>

    <!-- Salary Sheet Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                    <th class="pb-3 pl-2">Employee</th>
                    <th class="pb-3 text-center">Days (P / L / A)</th>
                    <th class="pb-3 text-right">Base Earned</th>
                    <th class="pb-3 text-right">OT Pay</th>
                    <th class="pb-3 text-right">Gross Pay</th>
                    <th class="pb-3 text-right text-red-400">Deductions (PF+ESIC)</th>
                    <th class="pb-3 text-right font-bold text-emerald-400 pr-2">Net Pay</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-mono text-[11px]">
                <?php foreach ($records as $rec): ?>
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-3.5 pl-2 font-sans">
                            <div class="font-bold text-white"><?= wf_e($rec['first_name'] . ' ' . $rec['last_name']) ?></div>
                            <div class="text-[10px] text-slate-500 font-mono"><?= wf_e($rec['employee_code']) ?> &bull; Bank: <?= wf_e($rec['bank_account_no']) ?></div>
                        </td>
                        <td class="py-3.5 text-center font-sans">
                            <span class="text-emerald-400 font-bold"><?= $rec['present_days'] ?>P</span> / 
                            <span class="text-blue-400"><?= $rec['paid_leaves'] ?>L</span> / 
                            <span class="text-red-400"><?= $rec['absent_days'] ?>A</span>
                        </td>
                        <td class="py-3.5 text-right text-slate-300"><?= wf_format_currency($rec['basic_earned']) ?></td>
                        <td class="py-3.5 text-right text-blue-400"><?= wf_format_currency($rec['overtime_pay']) ?></td>
                        <td class="py-3.5 text-right font-bold text-white"><?= wf_format_currency($rec['gross_pay']) ?></td>
                        <td class="py-3.5 text-right text-red-400"><?= wf_format_currency($rec['total_deductions']) ?></td>
                        <td class="py-3.5 text-right font-bold text-emerald-400 pr-2 text-xs"><?= wf_format_currency($rec['net_pay']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
