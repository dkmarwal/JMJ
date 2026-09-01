<div class="max-w-4xl mx-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-base font-bold text-white mb-1">Onboard New Workforce Employee</h2>
        <p class="text-xs text-slate-400 mb-6">Personal details, statutory verification, and bank credentials for automated payroll</p>

        <form action="<?= wf_url('staff/store') ?>" method="POST" class="space-y-6">
            <?= wf_csrf_field() ?>

            <!-- Section 1: Basic Identity -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user"></i>
                    <span>1. Personal & Contact Information</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">First Name *</label>
                        <input type="text" name="first_name" required placeholder="e.g. Ramesh" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Last Name *</label>
                        <input type="text" name="last_name" required placeholder="e.g. Kumar" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Gender</label>
                        <select name="gender" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Date of Birth</label>
                        <input type="date" name="dob" value="1995-01-01" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Primary Mobile Number *</label>
                        <input type="text" name="phone" required placeholder="+91-9876543210" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Emergency Kin Contact</label>
                        <input type="text" name="emergency_phone" placeholder="+91-9876543211" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Role & Deployment -->
            <div class="pt-4 border-t border-slate-800">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>2. Employment & Classification</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Workforce Category *</label>
                        <select name="category_id" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= wf_e($cat['name']) ?> (<?= ucfirst($cat['department']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Assigned Branch</label>
                        <select name="branch_id" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                            <?php foreach ($branches as $br): ?>
                                <option value="<?= $br['id'] ?>"><?= wf_e($br['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Designation Title</label>
                        <input type="text" name="designation" value="Security Officer" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Payroll & Banking -->
            <div class="pt-4 border-t border-slate-800">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    <span>3. Statutory & Banking Credentials</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Base Monthly Salary (₹) *</label>
                        <input type="number" step="100" name="basic_salary" required value="18500" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Bank Name</label>
                        <input type="text" name="bank_name" value="State Bank of India" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Account Number</label>
                        <input type="text" name="bank_account_no" placeholder="30291823901" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">IFSC Code</label>
                        <input type="text" name="ifsc_code" placeholder="SBIN0001234" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">PF UAN Number</label>
                        <input type="text" name="pf_uan" placeholder="101293849102" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">ESIC Number</label>
                        <input type="text" name="esic_no" placeholder="112938491029" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="<?= wf_url('staff') ?>" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">Cancel</a>
                <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition shadow-lg shadow-emerald-600/20">Complete Onboarding & Issue ID</button>
            </div>
        </form>
    </div>
</div>
