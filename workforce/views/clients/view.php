<div class="space-y-6">
    <!-- Header -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-950 text-slate-400 border border-slate-800"><?= wf_e($client['client_code']) ?></span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($client['status']) ?></span>
            </div>
            <h2 class="text-xl font-bold text-white"><?= wf_e($client['company_name']) ?></h2>
            <p class="text-xs text-slate-400 mt-1"><?= wf_e($client['billing_address']) ?>, <?= wf_e($client['city']) ?>, <?= wf_e($client['state']) ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= wf_url('sites/create') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-plus"></i>
                <span>Add Client Site</span>
            </a>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Account Contact</h3>
            <div class="text-xs space-y-2 text-slate-300">
                <div><span class="text-slate-500 block">Contact Person:</span> <strong class="text-white"><?= wf_e($client['contact_person']) ?></strong></div>
                <div><span class="text-slate-500 block">Email:</span> <?= wf_e($client['email']) ?></div>
                <div><span class="text-slate-500 block">Phone:</span> <?= wf_e($client['phone']) ?></div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Tax & Billing Credentials</h3>
            <div class="text-xs space-y-2 text-slate-300">
                <div><span class="text-slate-500 block">GSTIN:</span> <span class="font-mono text-emerald-400"><?= wf_e($client['gst_number'] ?: 'Not Provided') ?></span></div>
                <div><span class="text-slate-500 block">PAN:</span> <span class="font-mono text-white"><?= wf_e($client['pan_number'] ?: 'Not Provided') ?></span></div>
                <div><span class="text-slate-500 block">Billing Cycle:</span> <?= ucfirst($client['billing_cycle']) ?></div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl shadow-xl space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Operational Footprint</h3>
            <div class="text-xs space-y-2 text-slate-300">
                <div><span class="text-slate-500 block">Active Sites:</span> <strong class="text-white text-base"><?= count($sites) ?></strong> Complexes</div>
                <div><span class="text-slate-500 block">SLA Compliance:</span> <span class="text-emerald-400 font-bold">100% Compliant</span></div>
            </div>
        </div>
    </div>

    <!-- Active Deployed Sites Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4">Client Sites & Guard Posts</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Site Name</th>
                        <th class="pb-3">Type</th>
                        <th class="pb-3">Address</th>
                        <th class="pb-3">Geofence</th>
                        <th class="pb-3">Deployed Staff</th>
                        <th class="pb-3 pr-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($sites)): ?>
                        <tr><td colspan="6" class="py-6 text-center text-slate-500">No sites configured yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sites as $st): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2 font-bold text-white"><?= wf_e($st['site_name']) ?></td>
                                <td class="py-3.5 uppercase text-slate-400 text-[10px]"><?= wf_e($st['site_type']) ?></td>
                                <td class="py-3.5 text-slate-300"><?= wf_e($st['address']) ?>, <?= wf_e($st['city']) ?></td>
                                <td class="py-3.5 text-emerald-400 font-semibold"><?= $st['geofence_radius'] ?>m Radius</td>
                                <td class="py-3.5 font-bold text-white"><?= $st['deployed_staff_count'] ?> Staff</td>
                                <td class="py-3.5 pr-2 text-right">
                                    <a href="<?= wf_url('sites/view?id=' . $st['id']) ?>" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-semibold text-[11px]">View Site</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
