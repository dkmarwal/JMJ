<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Workforce Staff Directory</h2>
            <p class="text-xs text-slate-400">Deployed security officers, cleaning specialists, and facility personnel</p>
        </div>
        <a href="<?= wf_url('staff/create') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
            <i class="fa-solid fa-user-plus"></i>
            <span>Onboard New Employee</span>
        </a>
    </div>

    <!-- Filters Strip -->
    <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl flex flex-wrap items-center justify-between gap-4">
        <form action="<?= wf_url('staff') ?>" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="<?= wf_e($filters['search'] ?? '') ?>" placeholder="Search name, code, phone..." class="px-3.5 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none w-64">
            <select name="category" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= wf_e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">Filter</button>
        </form>
        <div class="text-xs text-slate-400">Total: <strong class="text-white"><?= count($employees) ?></strong> Personnel</div>
    </div>

    <!-- Employee Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Employee</th>
                        <th class="pb-3">Category</th>
                        <th class="pb-3">Designation</th>
                        <th class="pb-3">Assigned Site Post</th>
                        <th class="pb-3">Mobile Phone</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 pr-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($employees)): ?>
                        <tr><td colspan="7" class="py-6 text-center text-slate-500">No staff found matching criteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($employees as $emp): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-xs">
                                            <?= strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white"><?= wf_e($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                                            <div class="text-[10px] text-slate-500 font-mono"><?= wf_e($emp['employee_code']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-950 text-slate-300 border border-slate-800">
                                        <?= wf_e($emp['category_name']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 text-slate-300"><?= wf_e($emp['designation']) ?></td>
                                <td class="py-3.5 text-emerald-400 font-medium"><?= wf_e($emp['current_site_name'] ?? 'Standby Pool') ?></td>
                                <td class="py-3.5 font-mono text-slate-300"><?= wf_e($emp['phone']) ?></td>
                                <td class="py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <?= wf_e($emp['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 pr-2 text-right space-x-2">
                                    <a href="<?= wf_url('staff/id-card?id=' . $emp['id']) ?>" target="_blank" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-emerald-400 font-semibold text-[11px]" title="Digital ID Card">
                                        <i class="fa-solid fa-id-card"></i>
                                    </a>
                                    <a href="<?= wf_url('staff/view?id=' . $emp['id']) ?>" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-semibold text-[11px]">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
