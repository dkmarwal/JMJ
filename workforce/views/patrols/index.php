<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Guard Tour Patrols & Checkpoints</h2>
            <p class="text-xs text-slate-400">Real-time patrol execution, sequence tracking, and deviation alerts</p>
        </div>
    </div>

    <!-- Active Routes Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <?php foreach ($routes as $rt): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-white"><?= wf_e($rt['name']) ?></div>
                    <div class="text-[11px] text-emerald-400"><?= wf_e($rt['site_name']) ?> &bull; <?= $rt['estimated_minutes'] ?> mins tour</div>
                </div>
                <div class="text-right">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <?= $rt['checkpoints_count'] ?> Checkpoints
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Recent Patrol Tours Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4">Recent Tour Scans & Progress</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Patrol Route</th>
                        <th class="pb-3">Guard On Patrol</th>
                        <th class="pb-3">Site Complex</th>
                        <th class="pb-3">Tour Start Time</th>
                        <th class="pb-3">Checkpoints Scanned</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($tours)): ?>
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No guard patrol tours recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tours as $pt): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2 font-bold text-white"><?= wf_e($pt['route_name']) ?></td>
                                <td class="py-3.5 text-slate-300"><?= wf_e($pt['first_name'] . ' ' . $pt['last_name']) ?> (<?= wf_e($pt['employee_code']) ?>)</td>
                                <td class="py-3.5 text-slate-400"><?= wf_e($pt['site_name']) ?></td>
                                <td class="py-3.5 font-mono text-emerald-400"><?= wf_format_time($pt['start_time']) ?></td>
                                <td class="py-3.5 font-bold text-white"><?= $pt['scanned_checkpoints'] ?> / <?= $pt['total_checkpoints'] ?></td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $pt['status'] === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' ?>">
                                        <?= wf_e($pt['status']) ?>
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
