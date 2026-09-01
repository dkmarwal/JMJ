<!DOCTYPE html>
<html lang="en" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= wf_e($pageTitle ?? 'JMJ Workforce Hub') ?> - JMJ Operations</title>
    
    <!-- Theme & Sidebar Flash Prevention Script -->
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

            const sidebarCollapsed = localStorage.getItem('jmj_sidebar_collapsed');
            if (sidebarCollapsed === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#16a34a',
                            600: '#15803d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Smooth Theme & Layout Transitions */
        html, body, aside, header, main, .bg-slate-900, .bg-slate-950, table, tr, td, th {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        /* ==========================================================================
           MODERN SLEEK FLOATING SCROLLBARS (ZERO BULK / CLUTTER)
           ========================================================================== */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.25) transparent;
        }
        html.light * {
            scrollbar-color: rgba(100, 116, 139, 0.3) transparent;
        }

        /* WebKit Browsers (Chrome, Edge, Safari) */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.2);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(148, 163, 184, 0.45);
        }

        /* Light Theme Scrollbars */
        html.light ::-webkit-scrollbar-thumb {
            background-color: rgba(100, 116, 139, 0.25);
        }
        html.light ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(100, 116, 139, 0.5);
        }

        /* Sidebar Sleek Micro-Scrollbar */
        aside {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        aside::-webkit-scrollbar {
            width: 4px;
        }
        aside:hover {
            scrollbar-width: thin;
        }

        /* ==========================================================================
           SIDEBAR COLLAPSIBLE TOGGLE SYSTEM (MODERN SLIDE PATTERN)
           ========================================================================== */
        aside {
            transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: width;
        }
        .sidebar-collapsed aside {
            width: 4.5rem !important; /* 72px */
        }
        .sidebar-collapsed aside .nav-text,
        .sidebar-collapsed aside .brand-text,
        .sidebar-collapsed aside .nav-category-header,
        .sidebar-collapsed aside .badge-indicator,
        .sidebar-collapsed aside .pwa-text,
        .sidebar-collapsed aside .signout-text {
            display: none !important;
        }
        .sidebar-collapsed aside .brand-container {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .sidebar-collapsed aside nav a {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .sidebar-collapsed aside nav a i {
            margin: 0 !important;
            font-size: 1.15rem !important;
            transition: transform 0.2s ease;
        }
        .sidebar-collapsed aside nav a:hover i {
            transform: scale(1.1);
        }
        .sidebar-collapsed aside .sidebar-footer {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .sidebar-collapsed aside .sidebar-footer a {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Floating Modern Glass Tooltip when Sidebar is Collapsed */
        .sidebar-tooltip {
            display: none;
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%) translateX(6px);
            margin-left: 0.5rem;
            padding: 0.45rem 0.85rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
            z-index: 100;
            pointer-events: none;
            background-color: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            color: #ffffff;
            border: 1px solid rgba(51, 65, 85, 0.8);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
            animation: tooltipFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes tooltipFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50%) translateX(0);
            }
            to {
                opacity: 1;
                transform: translateY(-50%) translateX(6px);
            }
        }
        html.light .sidebar-tooltip {
            background-color: rgba(255, 255, 255, 0.98);
            color: #0f172a;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
        }
        .sidebar-collapsed aside nav a:hover .sidebar-tooltip {
            display: block !important;
        }

        /* ==========================================================================
           LIGHT MODE COMPREHENSIVE HIGH-CONTRAST SAAS STYLING
           ========================================================================== */
        html.light body {
            background-color: #f1f5f9 !important; /* Clean Slate Canvas */
            color: #1e293b !important;
        }

        /* Containers, Cards, Sections */
        html.light .bg-slate-900,
        html.light .bg-slate-900\/90,
        html.light .bg-slate-900\/80,
        html.light .bg-slate-900\/70,
        html.light .bg-slate-900\/60,
        html.light .bg-slate-900\/50 {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #1e293b !important;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04) !important;
        }

        /* Wells, Inset Panels, Inner Boxes */
        html.light .bg-slate-950,
        html.light .bg-slate-950\/90,
        html.light .bg-slate-950\/80,
        html.light .bg-slate-950\/70,
        html.light .bg-slate-950\/60,
        html.light .bg-slate-950\/40,
        html.light .bg-slate-950\/30 {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #1e293b !important;
        }

        /* Secondary Buttons / Pills */
        html.light .bg-slate-800,
        html.light .bg-slate-800\/80,
        html.light .bg-slate-800\/60,
        html.light .bg-slate-800\/30 {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
        }
        html.light .hover\:bg-slate-800:hover,
        html.light .hover\:bg-slate-800\/30:hover,
        html.light .hover\:bg-slate-800\/60:hover,
        html.light .hover\:bg-slate-700:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* Borders & Dividers */
        html.light .border-slate-800,
        html.light .border-slate-800\/80,
        html.light .border-slate-800\/60,
        html.light .border-slate-800\/30 {
            border-color: #e2e8f0 !important;
        }
        html.light .border-slate-700 {
            border-color: #cbd5e1 !important;
        }
        html.light .divide-slate-800 > :not([hidden]) ~ :not([hidden]),
        html.light .divide-slate-800\/60 > :not([hidden]) ~ :not([hidden]),
        html.light .divide-slate-800\/30 > :not([hidden]) ~ :not([hidden]) {
            border-color: #f1f5f9 !important;
        }

        /* Typography & Headings */
        html.light .text-white {
            color: #0f172a !important; /* Dark bold heading text */
        }
        html.light .text-slate-100,
        html.light .text-slate-200 {
            color: #1e293b !important;
        }
        html.light .text-slate-300 {
            color: #334155 !important;
        }
        html.light .text-slate-400 {
            color: #475569 !important; /* Sharp readable slate-600 */
        }
        html.light .text-slate-500 {
            color: #64748b !important; /* Sharp secondary text */
        }

        /* Emerald Accents & Status Badges */
        html.light .text-emerald-400,
        html.light .text-emerald-300 {
            color: #047857 !important; /* Forest Emerald for crystal clarity */
        }
        html.light .text-emerald-500 {
            color: #059669 !important;
        }
        html.light .bg-emerald-500\/10,
        html.light .bg-emerald-500\/20,
        html.light .bg-emerald-600\/10,
        html.light .bg-emerald-600\/20,
        html.light .bg-emerald-600\/30,
        html.light .bg-emerald-950\/60 {
            background-color: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
            color: #047857 !important;
        }
        html.light .border-emerald-500\/20,
        html.light .border-emerald-500\/30,
        html.light .border-emerald-500\/40,
        html.light .border-emerald-500\/60 {
            border-color: #a7f3d0 !important;
        }

        /* Blue Accents & Reliever Badges */
        html.light .text-blue-400,
        html.light .text-blue-300,
        html.light .text-indigo-400 {
            color: #1d4ed8 !important; /* Royal Blue */
        }
        html.light .bg-blue-500\/10,
        html.light .bg-blue-500\/20,
        html.light .bg-indigo-500\/10,
        html.light .bg-indigo-500\/20 {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
        }
        html.light .border-blue-500\/20,
        html.light .border-blue-500\/30 {
            border-color: #bfdbfe !important;
        }

        /* Amber / Warning Badges */
        html.light .text-amber-400,
        html.light .text-amber-300,
        html.light .text-yellow-400 {
            color: #b45309 !important; /* Warm Amber */
        }
        html.light .bg-amber-500\/10,
        html.light .bg-amber-500\/20 {
            background-color: #fffbeb !important;
            border-color: #fde68a !important;
            color: #b45309 !important;
        }
        html.light .border-amber-500\/20,
        html.light .border-amber-500\/30 {
            border-color: #fde68a !important;
        }

        /* Red / Incident / SOS Badges */
        html.light .text-red-400,
        html.light .text-red-300 {
            color: #b91c1c !important; /* Deep Ruby Red */
        }
        html.light .bg-red-500\/10,
        html.light .bg-red-500\/20 {
            background-color: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #b91c1c !important;
        }
        html.light .border-red-500\/20,
        html.light .border-red-500\/30 {
            border-color: #fecaca !important;
        }

        /* Purple Accents */
        html.light .text-purple-400,
        html.light .text-purple-300 {
            color: #7e22ce !important;
        }
        html.light .bg-purple-500\/10,
        html.light .bg-purple-500\/20 {
            background-color: #faf5ff !important;
            border-color: #e9d5ff !important;
            color: #7e22ce !important;
        }

        /* Form Inputs, Selects & Textareas */
        html.light input,
        html.light select,
        html.light textarea {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.light input::placeholder,
        html.light textarea::placeholder {
            color: #94a3b8 !important;
        }
        html.light input:focus,
        html.light select:focus,
        html.light textarea:focus {
            border-color: #10b981 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
        }

        /* Top Navbar Header */
        html.light header {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
        }

        /* Left Sidebar */
        html.light aside {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }
        html.light aside nav a:not(.bg-emerald-600) {
            color: #334155 !important;
        }
        html.light aside nav a:not(.bg-emerald-600):hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html.light aside nav a.bg-emerald-600 {
            background-color: #16a34a !important;
            color: #ffffff !important;
        }
        html.light aside .text-slate-500 {
            color: #64748b !important;
        }

        /* Tables in Light Mode */
        html.light table thead tr {
            border-color: #e2e8f0 !important;
            color: #475569 !important;
        }
        html.light table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Print Styling */
        @media print {
            aside, header, .no-print {
                display: none !important;
            }
            html, body {
                height: auto !important;
                overflow: visible !important;
                background: #ffffff !important;
                color: #000000 !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
                height: auto !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .flex-1 {
                overflow: visible !important;
                height: auto !important;
                width: 100% !important;
            }
            .print-full {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                border: 0 !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            @page {
                margin: 12mm;
                size: auto;
            }
        }
    </style>
</head>
<body class="h-full flex bg-slate-950 text-slate-100 font-sans antialiased overflow-hidden">
    <!-- Left Sidebar -->
    <?php \Core\View::partial('partials.sidebar'); ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- Top Navbar -->
        <?php \Core\View::partial('partials.header'); ?>

        <!-- Dynamic Content Body -->
        <main class="flex-1 overflow-y-auto p-8 space-y-6">
            <!-- Flash Messages -->
            <?php if (\Core\Session::hasFlash('error')): ?>
                <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm flex items-start gap-3 shadow-lg shadow-red-500/5">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-base"></i>
                    <div>
                        <?php foreach (\Core\Session::getFlash('error') as $err): ?>
                            <p class="font-medium"><?= wf_e($err) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (\Core\Session::hasFlash('success')): ?>
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-start gap-3 shadow-lg shadow-emerald-500/5">
                    <i class="fa-solid fa-circle-check mt-0.5 text-base"></i>
                    <div>
                        <?php foreach (\Core\Session::getFlash('success') as $succ): ?>
                            <p class="font-medium"><?= wf_e($succ) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <!-- Theme & Sidebar Switcher Global JS -->
    <script>
        function toggleJMJTheme() {
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
            updateThemeIcons();
        }

        function updateThemeIcons() {
            const isLight = document.documentElement.classList.contains('light');
            const icon = document.getElementById('themeToggleIcon');
            const label = document.getElementById('themeToggleLabel');
            if (icon) {
                icon.className = isLight ? 'fa-solid fa-moon text-amber-500' : 'fa-solid fa-sun text-amber-400';
            }
            if (label) {
                label.innerText = isLight ? 'Dark Mode' : 'Light Mode';
            }
        }

        function toggleSidebarCollapse() {
            const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
            localStorage.setItem('jmj_sidebar_collapsed', isCollapsed ? '1' : '0');
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcons);
    </script>
</body>
</html>
