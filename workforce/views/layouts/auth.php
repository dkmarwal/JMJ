<!DOCTYPE html>
<html lang="en" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= wf_e($pageTitle ?? 'JMJ Workforce Hub') ?></title>
    
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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            500: '#16a34a',
                            600: '#15803d',
                            700: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        html.light body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
            color: #1e293b !important;
        }
        html.light .bg-slate-900\/80 {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
        }
        html.light .text-white {
            color: #0f172a !important;
        }
        html.light .text-slate-400 {
            color: #64748b !important;
        }
        html.light input {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.light .bg-slate-950,
        html.light .bg-slate-950\/60 {
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            color: #334155 !important;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 font-sans antialiased">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <div class="inline-block p-1 bg-gradient-to-tr from-amber-500 to-yellow-300 rounded-full shadow-2xl mb-3">
                <img src="<?= wf_url('assets/images/logo.png') ?>" alt="JMJ Enterprises Solutions Ltd" class="w-20 h-20 rounded-full object-cover bg-slate-950 shadow-inner">
            </div>
            <h1 class="text-xl font-black tracking-tight text-white uppercase">JMJ Enterprise Solutions</h1>
            <p class="text-[11px] uppercase tracking-widest text-amber-400 font-bold mt-0.5">Workforce & Operations Management Hub</p>
        </div>

        <?php if (\Core\Session::hasFlash('error')): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <div>
                    <?php foreach (\Core\Session::getFlash('error') as $err): ?>
                        <p><?= wf_e($err) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (\Core\Session::hasFlash('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <div>
                    <?php foreach (\Core\Session::getFlash('success') as $succ): ?>
                        <p><?= wf_e($succ) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?= $content ?>

        <!-- Footer Notice -->
        <div class="mt-6 text-center text-xs text-slate-500">
            <p>&copy; <?= date('Y') ?> JMJ Enterprise Solutions Pvt. Ltd.</p>
            <p class="mt-1">PSARA License: PSARA/DL/2016/9821 &bull; Authorized Access Only</p>
        </div>
    </div>
</body>
</html>
