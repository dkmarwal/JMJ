<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Monthly Workforce Muster Roll</h2>
            <p class="text-xs text-slate-400">Day-by-day attendance fulfillment matrix for statutory audits and payroll verification</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
                <i class="fa-solid fa-print mr-1.5"></i> Print Muster Sheet
            </button>
        </div>
    </div>

    <!-- Month & Year Filter -->
    <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl">
        <form action="<?= wf_url('attendance/muster') ?>" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Billing Month</label>
                <select name="month" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Year</label>
                <input type="number" name="year" value="<?= $year ?>" class="w-24 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
            </div>
            <div class="pt-4">
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition">Generate Muster</button>
            </div>
        </form>
    </div>

    <!-- High Density Muster Roll Matrix -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl overflow-x-auto">
        <table class="w-full text-center text-[10px] border-collapse">
            <thead>
                <tr class="bg-slate-950 text-slate-400 uppercase tracking-wider font-bold">
                    <th class="p-2 text-left border border-slate-800 sticky left-0 bg-slate-950 z-10">Employee Details</th>
                    <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
                        <th class="p-1 border border-slate-800 w-7"><?= $d ?></th>
                    <?php endfor; ?>
                    <th class="p-2 border border-slate-800 text-emerald-400 font-bold">P</th>
                    <th class="p-2 border border-slate-800 text-red-400 font-bold">A</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 font-mono">
                <?php foreach ($employees as $emp): ?>
                    <?php
                    $presentCount = 0;
                    $absentCount = 0;
                    ?>
                    <tr class="hover:bg-slate-800/30">
                        <td class="p-2 text-left font-sans font-semibold text-white border border-slate-800 sticky left-0 bg-slate-900 z-10 whitespace-nowrap">
                            <?= wf_e($emp['first_name'] . ' ' . $emp['last_name']) ?>
                            <span class="text-[9px] text-slate-500 block"><?= wf_e($emp['employee_code']) ?></span>
                        </td>

                        <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
                            <?php
                            $att = $attendanceMatrix[$emp['id']][$d] ?? null;
                            $char = '-';
                            $bg = 'text-slate-600';

                            if ($att) {
                                if (in_array($att['status'], ['CHECKED_IN', 'CHECKED_OUT', 'VERIFIED'])) {
                                    $char = 'P';
                                    $bg = 'bg-emerald-500/20 text-emerald-400 font-bold';
                                    $presentCount++;
                                } elseif ($att['status'] === 'REJECTED') {
                                    $char = 'A';
                                    $bg = 'bg-red-500/20 text-red-400 font-bold';
                                    $absentCount++;
                                }
                            }
                            ?>
                            <td class="p-1 border border-slate-800 <?= $bg ?>"><?= $char ?></td>
                        <?php endfor; ?>

                        <td class="p-2 border border-slate-800 text-emerald-400 font-bold"><?= $presentCount ?></td>
                        <td class="p-2 border border-slate-800 text-red-400 font-bold"><?= max(0, 26 - $presentCount) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
