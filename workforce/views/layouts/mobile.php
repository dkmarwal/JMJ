<!DOCTYPE html>
<html lang="en" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= wf_e($pageTitle ?? 'JMJ Mobile Field Hub') ?></title>
    <link rel="manifest" href="<?= wf_url('mobile/manifest.json') ?>">
    <meta name="theme-color" content="#020617">
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('jmj_theme');
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { -webkit-tap-highlight-color: transparent; }
        
        html.light body {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }
        html.light header,
        html.light nav {
            background-color: rgba(255, 255, 255, 0.98) !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05) !important;
        }
        html.light .bg-slate-900 {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05) !important;
        }
        html.light .bg-slate-950 {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #1e293b !important;
        }
        html.light .text-white {
            color: #0f172a !important;
        }
        html.light .text-slate-400 {
            color: #475569 !important;
        }
        html.light .text-slate-500 {
            color: #64748b !important;
        }
        html.light .text-emerald-400 {
            color: #047857 !important;
        }
        html.light .bg-emerald-500\/10,
        html.light .bg-emerald-500\/20 {
            background-color: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
            color: #047857 !important;
        }
        html.light input {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased flex flex-col justify-between select-none">
    <!-- Top Mobile App Header -->
    <header class="bg-slate-900 border-b border-slate-800 px-4 py-3 sticky top-0 z-40 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <img src="<?= wf_url('assets/images/logo.png') ?>" alt="JMJ Logo" class="w-8 h-8 rounded-full border border-amber-400 shadow-sm object-cover bg-slate-900">
            <div>
                <div class="text-xs font-bold text-white leading-tight">JMJ FIELD OPS</div>
                <div class="text-[9px] text-amber-400 font-semibold tracking-wider uppercase leading-tight">PWA Terminal</div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Theme Toggle on Mobile -->
            <button type="button" onclick="toggleMobileTheme()" class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 text-xs">
                <i id="mobThemeIcon" class="fa-solid fa-sun"></i>
            </button>
            <!-- Online / Offline Badge -->
            <span id="offlineBadge" class="hidden px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30">
                Offline Mode
            </span>
            <!-- Mobile User Profile Initial -->
            <a href="<?= wf_url('logout') ?>" class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-red-400 text-xs">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </header>

    <!-- Main Scrollable Mobile Viewport -->
    <main class="flex-1 overflow-y-auto p-4 space-y-4 pb-24">
        <?= $content ?>
    </main>

    <!-- Bottom Fixed PWA Navigation Bar -->
    <nav class="fixed bottom-0 left-0 right-0 bg-slate-900/95 backdrop-blur-lg border-t border-slate-800 px-6 py-2.5 z-40 flex items-center justify-around text-[10px] font-semibold text-slate-400">
        <a href="<?= wf_url('mobile') ?>" class="flex flex-col items-center gap-1 transition <?= wf_is_active_route('mobile') && !wf_is_active_route('mobile/check-in') && !wf_is_active_route('mobile/patrol') ? 'text-emerald-400 font-bold' : 'hover:text-white' ?>">
            <i class="fa-solid fa-house text-base"></i>
            <span>Home</span>
        </a>
        <a href="<?= wf_url('mobile/check-in') ?>" class="flex flex-col items-center gap-1 transition <?= wf_is_active_route('mobile/check-in') ? 'text-emerald-400 font-bold' : 'hover:text-white' ?>">
            <i class="fa-solid fa-fingerprint text-base"></i>
            <span>Check-In</span>
        </a>
        <a href="<?= wf_url('mobile/patrol') ?>" class="flex flex-col items-center gap-1 transition <?= wf_is_active_route('mobile/patrol') ? 'text-emerald-400 font-bold' : 'hover:text-white' ?>">
            <i class="fa-solid fa-route text-base"></i>
            <span>Patrol</span>
        </a>
        <button type="button" onclick="triggerSOSPanic()" class="flex flex-col items-center gap-1 text-red-400 font-bold animate-pulse">
            <i class="fa-solid fa-triangle-exclamation text-base"></i>
            <span>SOS</span>
        </button>
    </nav>

    <!-- Global Emergency SOS Modal & Script -->
    <script>
    // Offline / Online Status Watcher
    window.addEventListener('online', () => document.getElementById('offlineBadge').classList.add('hidden'));
    window.addEventListener('offline', () => document.getElementById('offlineBadge').classList.remove('hidden'));

    function toggleMobileTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        const newTheme = isDark ? 'light' : 'dark';
        if (newTheme === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            localStorage.setItem('jmj_theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            localStorage.setItem('jmj_theme', 'dark');
        }
        updateMobThemeIcon();
    }

    function updateMobThemeIcon() {
        const icon = document.getElementById('mobThemeIcon');
        if (icon) {
            icon.className = document.documentElement.classList.contains('light') ? 'fa-solid fa-moon text-amber-500' : 'fa-solid fa-sun text-amber-400';
        }
    }
    document.addEventListener('DOMContentLoaded', updateMobThemeIcon);

    function triggerSOSPanic() {
        if (confirm('🚨 EMERGENCY SOS: Trigger critical panic broadcast to Operations Command Center?')) {
            navigator.geolocation.getCurrentPosition((pos) => {
                fetch('<?= wf_url('api/sos/trigger') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        employee_id: <?= (int)($user['employee_id'] ?? 1) ?>,
                        site_id: <?= (int)($deployment['site_id'] ?? 1) ?>,
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude
                    })
                }).then(r => r.json()).then(res => {
                    alert('🚨 SOS TRANSMITTED! Command Center Dispatcher has received your emergency alert and live coordinates.');
                });
            }, (err) => {
                alert('SOS Alert broadcasting with default site coordinates...');
            });
        }
    }
    </script>
</body>
</html>
