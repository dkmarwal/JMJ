<div class="max-w-3xl mx-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-base font-bold text-white mb-1">Onboard Enterprise Client Account</h2>
        <p class="text-xs text-slate-400 mb-6">Create master corporate account, billing credentials, and tax profiles</p>

        <form action="<?= wf_url('clients/store') ?>" method="POST" class="space-y-4">
            <?= wf_csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Company / Entity Name *</label>
                    <input type="text" name="company_name" required placeholder="e.g. Acme Towers Private Limited" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Primary Contact Person *</label>
                    <input type="text" name="contact_person" required placeholder="e.g. Mr. Rajesh Sharma" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Billing Email *</label>
                    <input type="email" name="email" required placeholder="billing@acmecorp.in" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Contact Phone *</label>
                    <input type="text" name="phone" required placeholder="+91-9811001122" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Billing Address</label>
                    <textarea name="billing_address" rows="2" placeholder="Registered office address..." class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">City</label>
                    <input type="text" name="city" value="New Delhi" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">State</label>
                    <input type="text" name="state" value="Delhi" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">GST Number</label>
                    <input type="text" name="gst_number" placeholder="07AACFJ1234F1Z5" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">PAN Number</label>
                    <input type="text" name="pan_number" placeholder="AACFJ1234F" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="<?= wf_url('clients') ?>" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">Cancel</a>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition shadow-lg shadow-emerald-600/20">Save Client Account</button>
            </div>
        </form>
    </div>
</div>
