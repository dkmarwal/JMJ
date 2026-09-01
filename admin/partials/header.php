<?php
/**
 * JMJ Enterprises Solutions - Admin Dashboard Header
 */
$currentUser = \Core\Auth::user();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Console') ?> | JMJ Enterprises Portal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#090F1C',
                            navy: '#0F1E36',
                            steel: '#254E70',
                            gold: '#F39C12'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased flex overflow-hidden">

    <!-- Sidebar Component Included in Next Step -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content Stage -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Top Operational Admin Bar -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-10 shadow-sm">
            <div class="flex items-center space-x-3">
                <button id="admin-sidebar-toggle" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight flex items-center">
                    <?= e($pageTitle ?? 'Dashboard') ?>
                </h1>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center space-x-4">
                <a href="<?= url() ?>" target="_blank" class="hidden sm:flex items-center space-x-1.5 text-xs font-bold text-slate-500 hover:text-amber-600 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                    <i class="fas fa-external-link-alt text-[10px]"></i>
                    <span>Live Portal</span>
                </a>

                <div class="flex items-center space-x-3 border-l border-slate-200 pl-4">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-black text-xs">
                        <?= strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="hidden sm:block text-left">
                        <span class="text-xs font-bold text-slate-900 block leading-tight"><?= e($currentUser['name'] ?? 'Admin') ?></span>
                        <span class="text-[10px] text-slate-400 font-semibold block uppercase"><?= e($currentUser['role_label'] ?? 'Administrator') ?></span>
                    </div>
                    <a href="<?= url('admin/logout.php') ?>" class="text-slate-400 hover:text-red-600 text-sm p-1.5 transition" title="Logout">
                        <i class="fas fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body with Internal Scroll -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-100">
            <!-- Flash Message Alerts -->
            <?php 
            $flashes = \Core\Session::getFlashes();
            if (!empty($flashes)): ?>
                <div class="mb-6 space-y-2">
                    <?php foreach ($flashes as $f): ?>
                        <div class="p-4 rounded-xl text-xs font-bold flex items-center justify-between <?= $f['type'] === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
                            <div class="flex items-center space-x-2">
                                <i class="fas <?= $f['type'] === 'success' ? 'fa-check-circle' : 'fa-triangle-exclamation' ?>"></i>
                                <span><?= e($f['message']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
