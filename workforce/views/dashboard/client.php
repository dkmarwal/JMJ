<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-emerald-400 font-bold">Client Operations Portal</span>
                <h2 class="text-xl font-bold text-white mt-1"><?= wf_e($client['company_name'] ?? 'Enterprise Client') ?></h2>
                <p class="text-xs text-slate-400 mt-1">Live muster roll and site deployment oversight</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-right">
                    <div class="text-[10px] text-slate-400 uppercase font-semibold">Allocated Strength</div>
                    <div class="text-lg font-bold text-white"><?= $deployedStaff ?> Personnel</div>
                </div>
                <div class="px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-right">
                    <div class="text-[10px] text-emerald-400 uppercase font-semibold">Active On Duty</div>
                    <div class="text-lg font-bold text-emerald-400"><?= $presentToday ?> Present</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Client Sites -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($sites as $st): ?>
            <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-bold text-white"><?= wf_e($st['site_name']) ?></div>
                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
                </div>
                <div class="text-xs text-slate-400 mb-3"><?= wf_e($st['address']) ?>, <?= wf_e($st['city']) ?></div>
                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Geofence Radius:</span>
                    <span class="font-bold text-white"><?= $st['geofence_radius'] ?> meters</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Live Verified Attendance Muster -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-bold text-white tracking-tight">Today's Live Muster Roll</h3>
                <p class="text-xs text-slate-400">Real-time verified check-in telemetry across your sites</p>
            </div>
            <span class="text-xs text-emerald-400 font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Live Feed Active</span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Deployed Staff</th>
                        <th class="pb-3">Site Complex</th>
                        <th class="pb-3">Shift</th>
                        <th class="pb-3">Check-In Time</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($recentAttendance)): ?>
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-500">No active check-ins logged for your account today.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentAttendance as $att): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 pl-2">
                                    <div class="font-bold text-white"><?= wf_e($att['first_name'] . ' ' . $att['last_name']) ?></div>
                                    <div class="text-[10px] text-slate-400"><?= wf_e($att['designation']) ?></div>
                                </td>
                                <td class="py-3 text-slate-200"><?= wf_e($att['site_name']) ?></td>
                                <td class="py-3 text-slate-300"><?= wf_e($att['shift_name']) ?></td>
                                <td class="py-3 font-mono text-emerald-400"><?= wf_format_time($att['check_in_time']) ?></td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <?= wf_e($att['status']) ?>
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
