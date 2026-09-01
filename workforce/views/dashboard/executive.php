<!-- Metrics KPI Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <!-- Active Sites -->
    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-2xl relative overflow-hidden shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Deployed Sites</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $totalSites ?> <span class="text-xs font-normal text-slate-500">across <?= $totalClients ?> clients</span></div>
        <div class="mt-2 text-xs text-emerald-400 flex items-center gap-1 font-medium">
            <i class="fa-solid fa-circle-check text-[10px]"></i>
            <span>100% Geofenced & Active</span>
        </div>
    </div>

    <!-- Live Present Today -->
    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-2xl relative overflow-hidden shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Staff On Duty (Today)</span>
            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $presentToday ?> <span class="text-xs font-normal text-slate-500">/ <?= $scheduledToday ?> scheduled</span></div>
        <div class="mt-2 text-xs text-blue-400 flex items-center gap-1 font-medium">
            <i class="fa-solid fa-fingerprint text-[10px]"></i>
            <span>Verified 4-Layer Biometrics</span>
        </div>
    </div>

    <!-- Active Workforce Strength -->
    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-2xl relative overflow-hidden shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Workforce</span>
            <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center border border-purple-500/20">
                <i class="fa-solid fa-shield-cat"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $totalStaff ?> <span class="text-xs font-normal text-slate-500">field officers & guards</span></div>
        <div class="mt-2 text-xs text-purple-400 flex items-center gap-1 font-medium">
            <i class="fa-solid fa-id-badge text-[10px]"></i>
            <span>PSARA & Medical Cleared</span>
        </div>
    </div>

    <!-- Incidents & SOS Alerts -->
    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-2xl relative overflow-hidden shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Emergency & Tickets</span>
            <div class="w-9 h-9 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center border border-red-500/20">
                <i class="fa-solid fa-tower-broadcast"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-white"><?= $activeIncidents ?> <span class="text-xs font-normal text-slate-500">open tickets</span></div>
        <div class="mt-2 text-xs <?= $openSOS > 0 ? 'text-red-400 animate-pulse' : 'text-emerald-400' ?> flex items-center gap-1 font-medium">
            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
            <span><?= $openSOS > 0 ? "{$openSOS} Active SOS Panic Broadcasts" : 'All Site Perimeters Secure' ?></span>
        </div>
    </div>
</div>

<!-- Operations Quick Actions & Live Site Radar Summary -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Live Site Staffing Radar Overview -->
    <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-sm font-bold text-white tracking-tight">Client Sites & Live Staffing Radar</h3>
                <p class="text-xs text-slate-400">Real-time attendance fulfillment across deployed complexes</p>
            </div>
            <a href="<?= wf_url('sites/radar') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 text-xs font-semibold hover:bg-emerald-600 hover:text-white transition">
                <i class="fa-solid fa-satellite-dish"></i>
                <span>Open Radar Map</span>
            </a>
        </div>

        <div class="space-y-3">
            <?php foreach ($sitesStatus as $st): ?>
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center justify-between hover:border-slate-700 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-building-shield"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white"><?= wf_e($st['site_name']) ?></div>
                            <div class="text-[11px] text-slate-400"><?= wf_e($st['client_name']) ?> &bull; Geofence: <?= $st['geofence_radius'] ?>m</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold text-white"><?= $st['live_present_count'] ?> / <?= $st['assigned_count'] ?> Active</div>
                        <div class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $st['live_present_count'] >= $st['assigned_count'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' ?>">
                            <?= $st['live_present_count'] >= $st['assigned_count'] ? 'Full Strength' : 'Under Strength' ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Quick Operations Command Panel -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
        <div>
            <h3 class="text-sm font-bold text-white tracking-tight mb-1">Operational Dispatch</h3>
            <p class="text-xs text-slate-400 mb-5">Rapid incident response and field actions</p>

            <div class="space-y-3">
                <a href="<?= wf_url('shifts/relievers') ?>" class="flex items-center gap-3 p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500/20 transition">
                    <i class="fa-solid fa-person-walking-arrow-right text-lg"></i>
                    <div>
                        <div class="text-xs font-bold text-white">Dispatch Emergency Reliever</div>
                        <div class="text-[11px] text-blue-300">Match standby guards to absent sites</div>
                    </div>
                </a>

                <a href="<?= wf_url('audits/create') ?>" class="flex items-center gap-3 p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500/20 transition">
                    <i class="fa-solid fa-clipboard-check text-lg"></i>
                    <div>
                        <div class="text-xs font-bold text-white">Log Surprise Field Audit</div>
                        <div class="text-[11px] text-amber-300">Record turnout and register scores</div>
                    </div>
                </a>

                <a href="<?= wf_url('payroll') ?>" class="flex items-center gap-3 p-3.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 hover:bg-purple-500/20 transition">
                    <i class="fa-solid fa-calculator text-lg"></i>
                    <div>
                        <div class="text-xs font-bold text-white">Calculate Verified Payroll</div>
                        <div class="text-[11px] text-purple-300">Compute attendance-driven salaries</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="mt-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>Central Dispatch Hotline:</span>
                <span class="font-bold text-white">18008890832</span>
            </div>
        </div>
    </div>
</div>

<!-- Live Attendance Telemetry Stream -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-sm font-bold text-white tracking-tight">Today's Real-Time Attendance Stream</h3>
            <p class="text-xs text-slate-400">Live 4-layer verification logs (GPS Geofence + Dynamic QR + Selfie + Verification Score)</p>
        </div>
        <a href="<?= wf_url('attendance') ?>" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1">
            <span>View Complete Stream</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                    <th class="pb-3 pl-2">Staff Member</th>
                    <th class="pb-3">Site & Shift</th>
                    <th class="pb-3">Check-In Time</th>
                    <th class="pb-3">Verification Score</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                <?php if (empty($recentAttendance)): ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">No attendance check-ins logged yet today.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentAttendance as $att): ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 pl-2">
                                <div class="font-bold text-white"><?= wf_e($att['first_name'] . ' ' . $att['last_name']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= wf_e($att['employee_code']) ?> &bull; <?= wf_e($att['designation']) ?></div>
                            </td>
                            <td class="py-3.5">
                                <div class="font-medium text-slate-200"><?= wf_e($att['site_name']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= wf_e($att['shift_name']) ?></div>
                            </td>
                            <td class="py-3.5 text-slate-300 font-mono">
                                <?= wf_format_time($att['check_in_time']) ?>
                            </td>
                            <td class="py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= (int)$att['verification_score'] ?>%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-400"><?= (int)$att['verification_score'] ?>%</span>
                                </div>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
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
