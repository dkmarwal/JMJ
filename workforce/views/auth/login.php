<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 p-8 rounded-2xl shadow-2xl">
    <form action="<?= wf_url('login') ?>" method="POST" class="space-y-5">
        <?= wf_csrf_field() ?>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Corporate Email / Username</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <input type="email" id="email" name="email" required value="superadmin@jmjenterprisessolutions.com"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition text-sm">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Password</label>
                <a href="#" onclick="alert('Please contact your JMJ Operations Dispatcher at +91-9999381777 to reset credentials.'); return false;" class="text-xs text-emerald-400 hover:text-emerald-300">Forgot?</a>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input type="password" id="password" name="password" required value="Admin@123456"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition text-sm">
            </div>
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm tracking-wide shadow-lg shadow-emerald-600/25 transition duration-200 flex items-center justify-center gap-2">
            <span>Authenticate Securely</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </form>

    <!-- Quick Role Switcher for Testing & Verification -->
    <div class="mt-8 pt-6 border-t border-slate-800/80">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 text-center">Quick Role Switcher (Test Accounts)</p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button type="button" onclick="fillCreds('superadmin@jmjenterprisessolutions.com', 'Admin@123456')" 
                    class="p-2 rounded-lg bg-slate-950/60 border border-slate-800 text-slate-300 hover:border-emerald-500 hover:text-white text-left transition">
                <div class="font-semibold text-emerald-400">Super Admin</div>
                <div class="text-[10px] text-slate-500 truncate">superadmin@...</div>
            </button>
            <button type="button" onclick="fillCreds('ops@jmjenterprisessolutions.com', 'Ops@123456')" 
                    class="p-2 rounded-lg bg-slate-950/60 border border-slate-800 text-slate-300 hover:border-emerald-500 hover:text-white text-left transition">
                <div class="font-semibold text-blue-400">Operations Lead</div>
                <div class="text-[10px] text-slate-500 truncate">ops@...</div>
            </button>
            <button type="button" onclick="fillCreds('fieldofficer@jmjenterprisessolutions.com', 'Field@123456')" 
                    class="p-2 rounded-lg bg-slate-950/60 border border-slate-800 text-slate-300 hover:border-emerald-500 hover:text-white text-left transition">
                <div class="font-semibold text-amber-400">Field Officer</div>
                <div class="text-[10px] text-slate-500 truncate">fieldofficer@...</div>
            </button>
            <button type="button" onclick="fillCreds('supervisor@jmjenterprisessolutions.com', 'Super@123456')" 
                    class="p-2 rounded-lg bg-slate-950/60 border border-slate-800 text-slate-300 hover:border-emerald-500 hover:text-white text-left transition">
                <div class="font-semibold text-purple-400">Site Supervisor</div>
                <div class="text-[10px] text-slate-500 truncate">supervisor@...</div>
            </button>
            <button type="button" onclick="fillCreds('guard@jmjenterprises.com', 'Guard@123456')" 
                    class="p-2 rounded-lg bg-slate-950/60 border border-slate-800 text-slate-300 hover:border-emerald-500 hover:text-white text-left transition">
                <div class="font-semibold text-emerald-400">Security Guard (PWA)</div>
                <div class="text-[10px] text-slate-500 truncate">guard@...</div>
            </button>
            <button type="button" onclick="fillCreds('client@abccorp.com', 'Client@123456')" 
                    class="p-2 rounded-lg bg-slate-950/60 border border-slate-800 text-slate-300 hover:border-emerald-500 hover:text-white text-left transition">
                <div class="font-semibold text-indigo-400">Client Portal</div>
                <div class="text-[10px] text-slate-500 truncate">client@...</div>
            </button>
        </div>
    </div>
</div>

<script>
function fillCreds(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
}
</script>
