<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Operational Shift Roster</h2>
            <p class="text-xs text-slate-400">Daily assignments, reliever deployments, and attendance state</p>
        </div>
        <a href="<?= wf_url('shifts/relievers') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition shadow-lg shadow-blue-600/20">
            <i class="fa-solid fa-person-walking-arrow-right"></i>
            <span>Dispatch Emergency Reliever</span>
        </a>
    </div>

    <!-- Filters Strip -->
    <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl flex flex-wrap items-center justify-between gap-4">
        <form action="<?= wf_url('shifts/roster') ?>" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Roster Date</label>
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
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">Apply Filter</button>
            </div>
        </form>
        <div class="text-xs text-slate-400">Scheduled: <strong class="text-white"><?= count($rosterEntries) ?></strong> Staff Members</div>
    </div>

    <!-- Roster Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Scheduled Personnel</th>
                        <th class="pb-3">Site Complex</th>
                        <th class="pb-3">Shift Timing</th>
                        <th class="pb-3">Reliever Tag</th>
                        <th class="pb-3">Roster Status</th>
                        <th class="pb-3">Attendance Check-In</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($rosterEntries)): ?>
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No shift roster entries found for <?= wf_format_date($selectedDate) ?>.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rosterEntries as $r): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2">
                                    <div class="font-bold text-white"><?= wf_e($r['first_name'] . ' ' . $r['last_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= wf_e($r['employee_code']) ?> &bull; <?= wf_e($r['category_name']) ?></div>
                                </td>
                                <td class="py-3.5 text-slate-200"><?= wf_e($r['site_name']) ?></td>
                                <td class="py-3.5 text-slate-300">
                                    <div><?= wf_e($r['shift_name']) ?></div>
                                    <div class="text-[10px] font-mono text-emerald-400"><?= wf_format_time($r['start_time']) ?> - <?= wf_format_time($r['end_time']) ?></div>
                                </td>
                                <td class="py-3.5">
                                    <?php if ($r['is_reliever']): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">Emergency Reliever</span>
                                    <?php else: ?>
                                        <span class="text-slate-500 text-[10px]">Primary Assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $r['status'] === 'present' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($r['status'] === 'no_show' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-slate-800 text-slate-300') ?>">
                                        <?= wf_e($r['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 font-mono text-slate-300">
                                    <?= $r['check_in_time'] ? wf_format_time($r['check_in_time']) : '<span class="text-slate-500">Not Checked In</span>' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
