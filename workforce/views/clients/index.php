<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Enterprise Client Portfolio</h2>
            <p class="text-xs text-slate-400">Master accounts, active SLAs, and deployed infrastructure</p>
        </div>
        <a href="<?= wf_url('clients/create') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
            <i class="fa-solid fa-plus"></i>
            <span>Onboard Client</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($clients as $cl): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-950 text-slate-400 border border-slate-800"><?= wf_e($cl['client_code']) ?></span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($cl['status']) ?></span>
                    </div>
                    <h3 class="text-sm font-bold text-white mb-1"><?= wf_e($cl['company_name']) ?></h3>
                    <p class="text-xs text-slate-400 mb-4"><?= wf_e($cl['industry'] ?? 'Commercial Enterprise') ?></p>

                    <div class="space-y-2 text-xs text-slate-300">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user-tie text-slate-500 w-4"></i>
                            <span><?= wf_e($cl['contact_person']) ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-slate-500 w-4"></i>
                            <span><?= wf_e($cl['phone']) ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-slate-500 w-4"></i>
                            <span><?= wf_e($cl['city']) ?>, <?= wf_e($cl['state']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div class="text-xs">
                        <span class="font-bold text-white"><?= $cl['active_sites_count'] ?></span>
                        <span class="text-slate-500">Active Sites</span>
                    </div>
                    <a href="<?= wf_url('clients/view?id=' . $cl['id']) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-white transition">
                        <span>View Profile &rarr;</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
