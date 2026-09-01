<div class="space-y-6">
    <div>
        <h2 class="text-lg font-bold text-white tracking-tight">Cleaning Checklists, Pantry & Inventory</h2>
        <p class="text-xs text-slate-400">Digital hygiene standard checklists, zone sanitization tasks, and chemical stock tracking</p>
    </div>

    <!-- Consumable Stock Matrix -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-emerald-400"></i>
            <span>Consumable Stock Levels & Chemical Inventory</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($inventory as $item): ?>
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white truncate"><?= wf_e($item['item_name']) ?></span>
                        <span class="text-[10px] uppercase font-bold text-slate-500"><?= wf_e($item['category']) ?></span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-xl font-bold text-emerald-400"><?= $item['current_stock'] ?></span>
                            <span class="text-xs text-slate-400"><?= wf_e($item['unit']) ?></span>
                        </div>
                        <span class="text-[10px] text-slate-500">Min Alert: <?= $item['min_alert_level'] ?> <?= wf_e($item['unit']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Standard Task Templates -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4">Standard Operating Checklist Templates</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($templates as $tmpl): ?>
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white"><?= wf_e($tmpl['title']) ?></span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($tmpl['frequency']) ?></span>
                    </div>
                    <div class="text-[11px] text-slate-400 space-y-1">
                        <?php
                        $items = json_decode($tmpl['items_checklist'], true) ?? [];
                        foreach (array_slice($items, 0, 3) as $it): ?>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                                <span><?= wf_e($it) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
