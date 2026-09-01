<?php
$user = \Core\Auth::user();
?>
<header class="h-16 bg-slate-900/90 backdrop-blur-md border-b border-slate-800 flex items-center justify-between px-6 sticky top-0 z-30 transition-colors">
    <!-- Left Area: Sidebar Toggle & Page Title -->
    <div class="flex items-center gap-3.5">
        <!-- Sidebar Toggle Collapse / Expand Button -->
        <button type="button" onclick="toggleSidebarCollapse()" class="w-9 h-9 rounded-xl bg-slate-950/60 hover:bg-slate-800 border border-slate-800 flex items-center justify-center text-slate-300 hover:text-white transition shadow-sm" title="Toggle Sidebar Navigation (Collapse/Expand)">
            <i class="fa-solid fa-bars-staggered text-sm"></i>
        </button>

        <div>
            <h1 class="text-sm font-bold text-white tracking-tight leading-tight"><?= wf_e($pageTitle ?? 'Operations Console') ?></h1>
            <div class="text-[10px] text-slate-400 leading-tight">JMJ Enterprise Solutions &bull; Operations Hub</div>
        </div>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center gap-3">
        <!-- Live System Clock -->
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-300">
            <i class="fa-regular fa-clock text-emerald-400"></i>
            <span id="liveClock"><?= date('d M Y, H:i:s') ?></span>
            <span class="text-[10px] text-slate-500 font-bold uppercase">IST</span>
        </div>

        <!-- Theme Switcher Toggle Button (Dark / Light) -->
        <button type="button" onclick="toggleJMJTheme()" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-950/60 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-200 transition shadow-sm" title="Toggle Dark / Light Theme">
            <i id="themeToggleIcon" class="fa-solid fa-sun text-amber-400"></i>
            <span id="themeToggleLabel" class="hidden md:inline text-[11px]">Light Mode</span>
        </button>

        <!-- Emergency SOS Button in Topbar -->
        <a href="<?= wf_url('incidents') ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white text-xs font-bold transition shadow-sm">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
            <span>SOS Command</span>
        </a>

        <!-- User Profile & Sign Out -->
        <div class="flex items-center gap-3 pl-2 border-l border-slate-800">
            <div class="w-8 h-8 rounded-full bg-emerald-600/30 border border-emerald-500/40 flex items-center justify-center text-emerald-300 font-bold text-xs">
                <?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?>
            </div>
            <div class="hidden md:block text-left">
                <div class="text-xs font-semibold text-white leading-tight"><?= wf_e($user['name'] ?? 'User') ?></div>
                <div class="text-[10px] text-slate-400 leading-tight"><?= wf_e($user['role_label'] ?? $user['role_name'] ?? '') ?></div>
            </div>
            <a href="<?= wf_url('logout') ?>" class="ml-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 text-xs font-bold transition shadow-sm" title="Sign Out of Portal">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span class="hidden sm:inline">Sign Out</span>
            </a>
        </div>
    </div>
</header>

<script>
setInterval(() => {
    const el = document.getElementById('liveClock');
    if (el) {
        const now = new Date();
        el.innerText = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('en-GB', { hour12: false });
    }
}, 1000);
</script>
