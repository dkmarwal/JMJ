<div class="max-w-4xl mx-auto space-y-6 print-full">
    <div class="flex items-center justify-between no-print">
        <a href="<?= wf_url('billing') ?>" class="text-xs text-slate-400 hover:text-white">&larr; Back to Billing Center</a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs flex items-center gap-2 shadow-lg shadow-emerald-600/30 transition">
            <i class="fa-solid fa-print"></i>
            <span>Print Official Tax Invoice</span>
        </button>
    </div>

    <!-- Official GST Invoice Box -->
    <div class="bg-white text-slate-900 rounded-3xl p-8 shadow-2xl border border-slate-200 print-full">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
            <div>
                <div class="flex items-center gap-3.5 mb-2">
                    <img src="<?= wf_url('assets/images/logo.png') ?>" alt="JMJ Logo" class="w-14 h-14 rounded-full border-2 border-amber-400 object-cover shadow-md">
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-slate-900 leading-none"><?= wf_e($invoice['company_name'] ?? 'JMJ Enterprise Solutions') ?></h1>
                        <p class="text-[10px] text-amber-700 font-bold uppercase tracking-wider mt-1">PSARA License: <?= wf_e($invoice['psara_license_no'] ?? 'PSARA/DL/2016/9821') ?></p>
                    </div>
                </div>
                <div class="text-xs text-slate-600 space-y-0.5">
                    <p><?= wf_e($invoice['comp_address'] ?? '250, Sant Nagar, East of Kailash, New Delhi - 110065') ?></p>
                    <p><strong>GSTIN:</strong> <?= wf_e($invoice['comp_gst'] ?? '07AACFJ1234F1Z5') ?> &bull; <strong>PAN:</strong> <?= wf_e($invoice['comp_pan'] ?? 'AACFJ1234F') ?></p>
                </div>
            </div>

            <div class="text-right">
                <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase bg-emerald-100 text-emerald-800">Original Tax Invoice</span>
                <div class="text-base font-mono font-black text-slate-900 mt-2"><?= wf_e($invoice['invoice_number']) ?></div>
                <div class="text-xs text-slate-500 mt-1">Date: <?= wf_format_date($invoice['issue_date']) ?></div>
                <div class="text-xs text-slate-500">Due: <?= wf_format_date($invoice['due_date']) ?></div>
            </div>
        </div>

        <!-- Billed To -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Billed To (Client):</span>
                <strong class="text-sm font-bold text-slate-900 block"><?= wf_e($invoice['client_name']) ?></strong>
                <p class="text-slate-600 mt-1"><?= wf_e($invoice['billing_address']) ?>, <?= wf_e($invoice['client_city']) ?>, <?= wf_e($invoice['client_state']) ?> - <?= wf_e($invoice['client_pincode']) ?></p>
                <p class="text-slate-600 mt-1 font-mono"><strong>Client GSTIN:</strong> <?= wf_e($invoice['gst_number'] ?: 'Unregistered') ?></p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Billing Period:</span>
                <strong class="text-sm font-bold text-slate-900 block"><?= date('F Y', mktime(0, 0, 0, (int)$invoice['billing_month'], 1, (int)$invoice['billing_year'])) ?></strong>
                <p class="text-slate-600 mt-1">Payment Terms: <strong>Net 15 Days</strong></p>
                <p class="text-slate-600 mt-1">Supply SAC Code: <strong>998525</strong> (Security & Facility Support)</p>
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="w-full text-left text-xs mb-6 border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-300 text-slate-700 uppercase tracking-wider text-[10px] font-bold">
                    <th class="py-2.5">Description of Service Deployed</th>
                    <th class="py-2.5 text-center">Verified Shifts</th>
                    <th class="py-2.5 text-right">Rate / Shift</th>
                    <th class="py-2.5 text-right font-bold">Amount (INR)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="py-3 text-slate-900 font-medium"><?= wf_e($item['description']) ?></td>
                        <td class="py-3 text-center font-mono"><?= $item['deployed_shifts_count'] ?></td>
                        <td class="py-3 text-right font-mono"><?= wf_format_currency($item['rate_per_shift']) ?></td>
                        <td class="py-3 text-right font-mono font-bold text-slate-900"><?= wf_format_currency($item['amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals Calculation -->
        <div class="flex justify-end border-t border-slate-200 pt-4 mb-6">
            <div class="w-72 space-y-2 text-xs text-slate-700 font-mono">
                <div class="flex justify-between"><span>Taxable Subtotal:</span> <strong><?= wf_format_currency($invoice['subtotal']) ?></strong></div>
                <div class="flex justify-between"><span>CGST (9.0%):</span> <span><?= wf_format_currency($invoice['gst_amount'] / 2) ?></span></div>
                <div class="flex justify-between"><span>SGST (9.0%):</span> <span><?= wf_format_currency($invoice['gst_amount'] / 2) ?></span></div>
                <div class="flex justify-between border-t-2 border-slate-900 pt-2 text-sm font-bold text-slate-900">
                    <span>Grand Total:</span> 
                    <span class="text-emerald-700 font-black"><?= wf_format_currency($invoice['grand_total']) ?></span>
                </div>
            </div>
        </div>

        <!-- Bank Details & Authorized Signatory -->
        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-200 text-xs">
            <div>
                <strong class="text-slate-900 block mb-1">Direct Bank Remittance:</strong>
                <p class="text-slate-600 font-mono">Bank: State Bank of India<br>Account: 30291823901<br>IFSC: SBIN0001234 (Sant Nagar Branch)</p>
            </div>
            <div class="text-right flex flex-col justify-between items-end">
                <div class="text-[10px] text-slate-500 uppercase font-bold">For JMJ Enterprise Solutions Pvt. Ltd.</div>
                <div class="pt-8 font-bold text-slate-900">Authorized Signatory</div>
            </div>
        </div>
    </div>
</div>
