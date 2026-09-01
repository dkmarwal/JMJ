<?php
/**
 * JMJ Enterprises Solutions - Admin Authentication Login
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

use Core\Auth;
use Core\Csrf;
use Core\Session;
use Core\RateLimiter;

Session::start();

// If already logged in, redirect to dashboard
if (Auth::check()) {
    redirect('admin/dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validate()) {
        $error = 'Session security token expired. Please try again.';
    } elseif (!RateLimiter::check('admin_login', 5, 300)) {
        $error = 'Too many failed login attempts. Please wait 5 minutes.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please provide both email address and password.';
        } elseif (Auth::attempt($email, $password)) {
            RateLimiter::clear('admin_login');
            Session::setFlash('success', 'Welcome back to the JMJ Administration Portal!');
            redirect('admin/dashboard.php');
        } else {
            $error = 'Invalid credentials. Please check your email and password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#090F1C]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal Login | JMJ Enterprises Solutions</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-[#090F1C] text-slate-200">
    <div class="w-full max-w-md space-y-8">
        
        <!-- Header Brand -->
        <div class="text-center space-y-2">
            <div class="w-16 h-16 rounded-2xl bg-white p-1 mx-auto border border-slate-700 shadow-xl overflow-hidden">
                <img src="<?= asset('img/logo.jpg') ?>" alt="JMJ Logo" class="w-full h-full object-cover">
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">JMJ ENTERPRISES</h2>
            <span class="text-xs font-bold uppercase tracking-widest text-[#F39C12] block">Administration Command Center</span>
        </div>

        <!-- Login Card -->
        <div class="bg-[#0F1E36] p-8 rounded-3xl border border-slate-800 shadow-2xl space-y-6">
            <?php if (!empty($error)): ?>
                <div class="p-4 rounded-xl bg-red-950/60 border border-red-800 text-xs font-bold text-red-300 flex items-center space-x-2">
                    <i class="fas fa-triangle-exclamation text-red-400"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= url('admin/login.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Authorized Email</label>
                    <div class="relative">
                        <input type="email" name="email" required value="<?= e($_POST['email'] ?? 'admin@jmjenterprises.com') ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 pl-10 text-sm text-white focus:outline-none focus:border-[#F39C12] transition" placeholder="admin@jmjenterprises.com">
                        <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Security Password</label>
                    <div class="relative">
                        <input type="password" name="password" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 pl-10 text-sm text-white focus:outline-none focus:border-[#F39C12] transition" placeholder="••••••••••••">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black py-3.5 px-4 rounded-xl shadow-lg transition duration-200 uppercase tracking-widest text-xs flex items-center justify-center space-x-2">
                    <i class="fas fa-shield-halved"></i>
                    <span>Authenticate & Access Console</span>
                </button>
            </form>

            <!-- Test Credentials Helper Callout -->
            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 text-[11px] text-slate-400 space-y-1">
                <span class="font-bold text-[#F39C12] block">Demo Credentials:</span>
                <p>Email: <code class="text-white">admin@jmjenterprises.com</code></p>
                <p>Password: <code class="text-white">Admin@123456</code></p>
            </div>
        </div>

        <div class="text-center text-xs text-slate-500">
            <a href="<?= url() ?>" class="hover:text-slate-300 transition">&larr; Return to Public Portal</a>
        </div>
    </div>
</body>
</html>
