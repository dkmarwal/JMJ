<?php
/**
 * JMJ Enterprises Solutions - Dedicated Get a Quote Page View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Hero Header Section -->
<section class="bg-[#090F1C] text-white py-16 lg:py-24 relative overflow-hidden border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
            <i class="fas fa-file-contract mr-2"></i> Free On-Site Survey & Custom Proposal
        </span>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
            Request an Operational Proposal
        </h1>
        <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Fill out the facility parameters below to receive a detailed cost analysis and deployment schedule within 2 business hours.
        </p>
    </div>
</section>

<!-- Quote Request Form Container -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-50 p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-premium">
            <form action="<?= url('api/lead') ?>" method="POST" class="ajax-form space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="type" value="quote">

                <div class="border-b border-slate-200 pb-4">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#F39C12] block">Step 1 of 3</span>
                    <h3 class="text-xl font-black text-[#090F1C]">Company & Contact Information</h3>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Authorized Name *</label>
                        <input type="text" name="name" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Corporate Entity Name *</label>
                        <input type="text" name="company" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Official Email *</label>
                        <input type="email" name="email" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Direct Phone / Mobile *</label>
                        <input type="tel" name="phone" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                    </div>
                </div>

                <div class="border-b border-slate-200 pb-4 pt-4">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#F39C12] block">Step 2 of 3</span>
                    <h3 class="text-xl font-black text-[#090F1C]">Service Scope & Location</h3>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Primary Service Required *</label>
                        <select name="service_required" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] text-slate-700">
                            <optgroup label="Security Services">
                                <?php foreach ($securityServices as $s): ?>
                                    <option value="<?= e($s['name']) ?>"><?= e($s['name']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Cleaning Services">
                                <?php foreach ($cleaningServices as $s): ?>
                                    <option value="<?= e($s['name']) ?>"><?= e($s['name']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Deployment City & State *</label>
                        <input type="text" name="location" required placeholder="e.g. New Delhi, Gurgaon, Bangalore" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                    </div>
                </div>

                <div class="border-b border-slate-200 pb-4 pt-4">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#F39C12] block">Step 3 of 3</span>
                    <h3 class="text-xl font-black text-[#090F1C]">Facility Details & Specific Pain Points</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Detailed Scope & Requirements</label>
                    <textarea name="message" rows="4" placeholder="Detail approximate total square footage, required number of guard posts, daily vs weekly cleaning shifts, and target start date..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 uppercase tracking-widest text-sm flex items-center justify-center">
                    <span>Submit Free Quotation Request</span>
                    <i class="fas fa-file-signature ml-2"></i>
                </button>
                <p class="text-center text-[11px] text-slate-400">* All submissions are strictly covered under corporate Non-Disclosure Agreements.</p>
            </form>
        </div>
    </div>
</section>
