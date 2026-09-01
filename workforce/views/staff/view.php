<div class="space-y-6">
    <!-- Header -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-xl">
                <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-950 text-slate-400 border border-slate-800"><?= wf_e($employee['employee_code']) ?></span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($employee['status']) ?></span>
                </div>
                <h2 class="text-xl font-bold text-white"><?= wf_e($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
                <p class="text-xs text-slate-400 mt-0.5"><?= wf_e($employee['designation']) ?> &bull; <?= wf_e($employee['category_name']) ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= wf_url('staff/id-card?id=' . $employee['id']) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-id-card"></i>
                <span>Print Digital ID Card</span>
            </a>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Contact & Address -->
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Contact & Address</h3>
            <div class="text-xs space-y-2 text-slate-300">
                <div><span class="text-slate-500 block">Mobile Phone:</span> <span class="font-mono font-bold text-white"><?= wf_e($employee['phone']) ?></span></div>
                <div><span class="text-slate-500 block">Emergency Kin Contact:</span> <?= wf_e($employee['emergency_phone'] ?? '-') ?></div>
                <div><span class="text-slate-500 block">Current Address:</span> <?= wf_e($employee['current_address']) ?>, <?= wf_e($employee['city']) ?></div>
            </div>
        </div>

        <!-- Statutory & Medical Fitness -->
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Compliance & Verification</h3>
            <div class="text-xs space-y-2 text-slate-300">
                <div>
                    <span class="text-slate-500 block">Police Verification:</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($employee['police_verification_status']) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Medical Fitness:</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($employee['medical_fitness_status']) ?></span>
                </div>
                <div><span class="text-slate-500 block">Standard Uniform Issue:</span> <?= wf_e($employee['standard_uniform'] ?? 'Standard Uniform') ?></div>
            </div>
        </div>

        <!-- Financial & Banking -->
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Payroll & Banking</h3>
            <div class="text-xs space-y-2 text-slate-300">
                <div><span class="text-slate-500 block">Base Salary:</span> <strong class="text-emerald-400 text-sm"><?= wf_format_currency($employee['basic_salary']) ?> / mo</strong></div>
                <div><span class="text-slate-500 block">Bank Account:</span> <?= wf_e($employee['bank_name']) ?> &bull; <span class="font-mono text-white"><?= wf_e($employee['bank_account_no']) ?></span></div>
                <div><span class="text-slate-500 block">PF UAN / ESIC:</span> <span class="font-mono text-slate-400"><?= wf_e($employee['pf_uan'] ?: '-') ?> / <?= wf_e($employee['esic_no'] ?: '-') ?></span></div>
            </div>
        </div>
    </div>
</div>
