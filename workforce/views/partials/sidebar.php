<?php
$user = \Core\Auth::user();
$role = $user['role_name'] ?? 'GUEST';
?>
<aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 h-screen sticky top-0 overflow-y-auto select-none">
    <!-- Brand Logo Header -->
    <div class="h-16 flex items-center px-4 border-b border-slate-800 gap-3 brand-container">
        <img src="<?= wf_url('assets/images/logo.png') ?>" alt="JMJ Enterprise Solutions" class="w-9 h-9 shrink-0 rounded-full border-2 border-amber-400 shadow-md object-cover bg-slate-900">
        <div class="brand-text overflow-hidden">
            <div class="font-bold text-white text-xs tracking-tight leading-tight uppercase truncate">JMJ ENTERPRISES</div>
            <div class="text-[9px] text-amber-400 font-semibold tracking-wider uppercase truncate">Workforce Hub</div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 space-y-1 text-xs font-medium text-slate-300">
        <!-- Main Dashboard -->
        <a href="<?= wf_url('dashboard') ?>" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?= wf_is_active_route('dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
            <i class="fa-solid fa-gauge-high w-4 text-center shrink-0"></i>
            <span class="nav-text truncate">Dashboard</span>
            <span class="sidebar-tooltip">Dashboard</span>
        </a>

        <?php if (\Core\Auth::can('sites.view')): ?>
            <!-- Live Radar Map -->
            <a href="<?= wf_url('sites/radar') ?>" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?= wf_is_active_route('sites/radar') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-satellite-dish w-4 text-center text-emerald-400 shrink-0"></i>
                <span class="nav-text flex-1 truncate">Live Operations Radar</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping badge-indicator"></span>
                <span class="sidebar-tooltip">Live Operations Radar</span>
            </a>
        <?php endif; ?>

        <?php if (\Core\Auth::can('clients.view') || \Core\Auth::can('sites.view')): ?>
            <div class="nav-category-header pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Enterprise CRM</div>
            <?php if (\Core\Auth::can('clients.view')): ?>
                <a href="<?= wf_url('clients') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('clients') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-building-user w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Clients & Accounts</span>
                    <span class="sidebar-tooltip">Clients & Accounts</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('sites.view')): ?>
                <a href="<?= wf_url('sites') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= (wf_is_active_route('sites') && !wf_is_active_route('sites/radar')) ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-map-location-dot w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Sites & Geofences</span>
                    <span class="sidebar-tooltip">Sites & Geofences</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Core\Auth::can('staff.view') || \Core\Auth::can('staff.onboard')): ?>
            <div class="nav-category-header pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Workforce & HR</div>
            <?php if (\Core\Auth::can('staff.view')): ?>
                <a href="<?= wf_url('staff') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('staff') && !wf_is_active_route('staff/create') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-id-card-clip w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Employee Directory</span>
                    <span class="sidebar-tooltip">Employee Directory</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('staff.onboard')): ?>
                <a href="<?= wf_url('staff/create') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('staff/create') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-user-plus w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Staff Onboarding</span>
                    <span class="sidebar-tooltip">Staff Onboarding</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Core\Auth::can('roster.manage') || \Core\Auth::can('shifts.manage') || \Core\Auth::can('sites.view')): ?>
            <div class="nav-category-header pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Rosters & Shifts</div>
            <?php if (\Core\Auth::can('roster.manage')): ?>
                <a href="<?= wf_url('shifts/roster') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('shifts/roster') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-calendar-days w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Operational Roster</span>
                    <span class="sidebar-tooltip">Operational Roster</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('shifts.manage') || \Core\Auth::can('roster.manage') || \Core\Auth::can('sites.view')): ?>
                <a href="<?= wf_url('shifts') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('shifts') && !wf_is_active_route('shifts/roster') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-clock w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Shift Schedules</span>
                    <span class="sidebar-tooltip">Shift Schedules</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Core\Auth::can('attendance.view') || \Core\Auth::can('attendance.disputes')): ?>
            <div class="nav-category-header pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Attendance Engine</div>
            <?php if (\Core\Auth::can('attendance.view')): ?>
                <a href="<?= wf_url('attendance') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('attendance') && !wf_is_active_route('attendance/muster') && !wf_is_active_route('attendance/disputes') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-fingerprint w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Live Attendance Feed</span>
                    <span class="sidebar-tooltip">Live Attendance Feed</span>
                </a>
                <a href="<?= wf_url('attendance/muster') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('attendance/muster') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-table-list w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Monthly Muster Roll</span>
                    <span class="sidebar-tooltip">Monthly Muster Roll</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('attendance.disputes')): ?>
                <a href="<?= wf_url('attendance/disputes') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('attendance/disputes') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-scale-unbalanced-flip w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Staff Disputes</span>
                    <span class="sidebar-tooltip">Staff Disputes</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Core\Auth::can('patrols.view') || \Core\Auth::can('tasks.manage') || \Core\Auth::can('audits.view') || \Core\Auth::can('incidents.manage') || \Core\Auth::can('incidents.report')): ?>
            <div class="nav-category-header pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Field Operations</div>
            <?php if (\Core\Auth::can('patrols.view')): ?>
                <a href="<?= wf_url('patrols') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('patrols') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-route w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Guard Tour Patrols</span>
                    <span class="sidebar-tooltip">Guard Tour Patrols</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('tasks.manage')): ?>
                <a href="<?= wf_url('tasks') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('tasks') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-broom-ball w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Cleaning & Inventory</span>
                    <span class="sidebar-tooltip">Cleaning & Inventory</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('audits.view')): ?>
                <a href="<?= wf_url('audits') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('audits') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-clipboard-check w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Field Audits</span>
                    <span class="sidebar-tooltip">Field Audits</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('incidents.manage') || \Core\Auth::can('incidents.report')): ?>
                <a href="<?= wf_url('incidents') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('incidents') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-triangle-exclamation w-4 text-center text-red-400 shrink-0"></i>
                    <span class="nav-text flex-1 truncate">Incident Command</span>
                    <span class="badge-indicator px-1.5 py-0.2 rounded text-[9px] font-bold bg-red-500/20 text-red-400 border border-red-500/30">SOS</span>
                    <span class="sidebar-tooltip">Incident Command (SOS)</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Core\Auth::can('payroll.calculate') || \Core\Auth::can('billing.invoices')): ?>
            <div class="nav-category-header pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Finance & Billing</div>
            <?php if (\Core\Auth::can('payroll.calculate')): ?>
                <a href="<?= wf_url('payroll') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('payroll') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-money-check-dollar w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Payroll Engine</span>
                    <span class="sidebar-tooltip">Payroll Engine</span>
                </a>
            <?php endif; ?>
            <?php if (\Core\Auth::can('billing.invoices')): ?>
                <a href="<?= wf_url('billing') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('billing') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-file-invoice-dollar w-4 text-center shrink-0"></i>
                    <span class="nav-text truncate">Client Invoices</span>
                    <span class="sidebar-tooltip">Client Invoices</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Core\Auth::can('reports.view')): ?>
            <div class="nav-category-header pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Reports</div>
            <a href="<?= wf_url('reports') ?>" class="group relative flex items-center gap-3 px-3 py-2 rounded-xl transition <?= wf_is_active_route('reports') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/25 font-semibold' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-chart-line w-4 text-center shrink-0"></i>
                <span class="nav-text truncate">Analytics & Exports</span>
                <span class="sidebar-tooltip">Analytics & Exports</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Bottom Mobile App Link & Sign Out -->
    <div class="sidebar-footer p-3 border-t border-slate-800 space-y-2">
        <a href="<?= wf_url('mobile') ?>" target="_blank" class="group relative flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-emerald-500 text-slate-400 hover:text-white text-xs transition">
            <i class="fa-solid fa-mobile-screen text-emerald-400 shrink-0"></i>
            <span class="pwa-text truncate">Launch Mobile PWA</span>
            <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-[10px] pwa-text shrink-0"></i>
            <span class="sidebar-tooltip">Launch Mobile PWA</span>
        </a>
        <a href="<?= wf_url('logout') ?>" class="group relative flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 text-xs font-bold transition shadow-sm">
            <i class="fa-solid fa-arrow-right-from-bracket shrink-0"></i>
            <span class="signout-text">Sign Out</span>
            <span class="sidebar-tooltip">Sign Out</span>
        </a>
    </div>
</aside>
