<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white tracking-tight">Client Invoicing & Billing Center</h2>
            <p class="text-xs text-slate-400">Automated GST billing, deployed shift calculations, and muster roll attachments</p>
        </div>
    </div>

    <!-- Generate Invoice Box -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white mb-4">Generate Monthly Client Invoice</h3>
        <form action="<?= wf_url('billing/generate') ?>" method="POST" class="flex flex-wrap items-end gap-4">
            <?= wf_csrf_field() ?>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Select Client</label>
                <select name="client_id" required class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    <?php foreach ($clients as $cl): ?>
                        <option value="<?= $cl['id'] ?>"><?= wf_e($cl['company_name']) ?> (<?= wf_e($cl['client_code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Billing Month</label>
                <select name="month" class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= (date('n') == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Year</label>
                <input type="number" name="year" value="<?= date('Y') ?>" class="w-24 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
            </div>
            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Generate Tax Invoice</span>
            </button>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold text-white tracking-tight mb-4">Issued Tax Invoices</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="pb-3 pl-2">Invoice #</th>
                        <th class="pb-3">Client Account</th>
                        <th class="pb-3">Billing Period</th>
                        <th class="pb-3 text-right">Taxable Subtotal</th>
                        <th class="pb-3 text-right">GST (18%)</th>
                        <th class="pb-3 text-right font-bold text-white">Grand Total</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 pr-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="8" class="py-6 text-center text-slate-500">No invoices issued yet. Generate an invoice above.</td></tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 pl-2 font-mono font-bold text-white"><?= wf_e($inv['invoice_number']) ?></td>
                                <td class="py-3.5">
                                    <div class="font-bold text-white"><?= wf_e($inv['client_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono">GSTIN: <?= wf_e($inv['gst_number'] ?: 'N/A') ?></div>
                                </td>
                                <td class="py-3.5 text-slate-300"><?= date('F Y', mktime(0, 0, 0, (int)$inv['billing_month'], 1, (int)$inv['billing_year'])) ?></td>
                                <td class="py-3.5 text-right font-mono text-slate-300"><?= wf_format_currency($inv['subtotal']) ?></td>
                                <td class="py-3.5 text-right font-mono text-slate-400"><?= wf_format_currency($inv['gst_amount']) ?></td>
                                <td class="py-3.5 text-right font-mono font-bold text-emerald-400"><?= wf_format_currency($inv['grand_total']) ?></td>
                                <td class="py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <?= wf_e($inv['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3.5 pr-2 text-right">
                                    <a href="<?= wf_url('billing/invoice?id=' . $inv['id']) ?>" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-semibold text-[11px]">View Invoice &rarr;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
