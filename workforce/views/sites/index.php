<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Client Sites & Infrastructure</h2>
            <p class="text-xs text-slate-400">GPS geofenced security posts, zones, and checkpoints</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= wf_url('sites/radar') ?>" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
                <i class="fa-solid fa-satellite-dish text-emerald-400"></i>
                <span>Operations Radar</span>
            </a>
            <a href="<?= wf_url('sites/create') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-plus"></i>
                <span>Configure Site</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($sites as $st): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl hover:border-slate-700 transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-950 text-slate-400 border border-slate-800"><?= wf_e($st['site_code']) ?></span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($st['status']) ?></span>
                    </div>
                    <h3 class="text-sm font-bold text-white mb-1"><?= wf_e($st['site_name']) ?></h3>
                    <p class="text-xs text-emerald-400 font-medium mb-3"><?= wf_e($st['client_name']) ?></p>

                    <div class="space-y-2 text-xs text-slate-300">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-slate-500 w-4"></i>
                            <span><?= wf_e($st['address']) ?>, <?= wf_e($st['city']) ?></span>
                        </div>
                        <div class="flex items-center gap-2 font-mono text-[11px] text-slate-400">
                            <i class="fa-solid fa-crosshairs text-slate-500 w-4"></i>
                            <span>GPS: <?= round((float)$st['latitude'], 4) ?>, <?= round((float)$st['longitude'], 4) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-400">
                            <i class="fa-solid fa-circle-dot text-emerald-500 w-4"></i>
                            <span>Geofence: <?= $st['geofence_radius'] ?>m Radius</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div class="text-xs">
                        <span class="font-bold text-white"><?= $st['deployed_staff_count'] ?></span>
                        <span class="text-slate-500">Deployed Guards</span>
                    </div>
                    <a href="<?= wf_url('sites/view?id=' . $st['id']) ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-white transition">
                        <span>Manage Site &rarr;</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
