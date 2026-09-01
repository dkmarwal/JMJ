<?php
/**
 * JMJ Enterprises Solutions - Quick Quote Popup Modal
 */
?>
<div id="quote-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div id="quote-modal-backdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200">
            <!-- Modal Header -->
            <div class="bg-[#090F1C] px-6 py-5 sm:px-8 flex justify-between items-center text-white border-b-2 border-[#F39C12]">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#F39C12] block">Fast 2-Hour Response Guarantee</span>
                    <h3 class="text-xl sm:text-2xl font-black text-white" id="modal-title">Request Site Survey & Custom Quote</h3>
                </div>
                <button type="button" class="close-quote-modal text-slate-400 hover:text-white transition p-2 rounded-xl focus:outline-none" aria-label="Close modal">
                    <i class="fas fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6 sm:px-8 bg-white">
                <form action="<?= url('api/lead') ?>" method="POST" class="ajax-form space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="quote">

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Contact Name *</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white transition" placeholder="e.g. Vikram Sharma">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Corporate / Entity Name</label>
                            <input type="text" name="company" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white transition" placeholder="e.g. Metro Healthcare Group">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Official Email Address *</label>
                            <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white transition" placeholder="ops@company.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Mobile Number *</label>
                            <input type="tel" name="phone" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white transition" placeholder="+91 99999 00000">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Service Category Required *</label>
                            <select id="modal-service-select" name="service_required" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white text-slate-700 transition">
                                <optgroup label="Security Services">
                                    <option value="Corporate Office Security Guards">Corporate Office Security Guards</option>
                                    <option value="Industrial & Warehouse Security">Industrial & Warehouse Security</option>
                                    <option value="Hospital & Healthcare Security">Hospital & Healthcare Security</option>
                                    <option value="ATM Security Guarding">ATM Security Guarding</option>
                                    <option value="Lady Security Officers">Lady Security Officers</option>
                                    <option value="Hotel & Hospitality Security">Hotel & Hospitality Security</option>
                                    <option value="Educational & Campus Security">Educational & Campus Security</option>
                                    <option value="Embassy Diplomatic Security">Embassy Diplomatic Security</option>
                                    <option value="Residential Gated Community Guarding">Residential Gated Community Guarding</option>
                                    <option value="CCTV Integrated Digital Solutions">CCTV Integrated Digital Solutions</option>
                                </optgroup>
                                <optgroup label="Cleaning & Facility Services">
                                    <option value="Hospital Pathogen Sanitization">Hospital Pathogen Sanitization</option>
                                    <option value="Industrial Plant Cleaning & Degreasing">Industrial Plant Cleaning & Degreasing</option>
                                    <option value="Commercial Building Housekeeping">Commercial Building Housekeeping</option>
                                    <option value="Floor Waxing & Polymer Stripping">Floor Waxing & Polymer Stripping</option>
                                    <option value="Italian Marble Diamond Polishing">Italian Marble Diamond Polishing</option>
                                    <option value="Post-Construction Deep Clean">Post-Construction Deep Clean</option>
                                    <option value="Corporate Daily Office Housekeeping">Corporate Daily Office Housekeeping</option>
                                    <option value="Commercial Carpet Steam Extraction">Commercial Carpet Steam Extraction</option>
                                    <option value="High-Altitude Facade Window Cleaning">High-Altitude Facade Window Cleaning</option>
                                    <option value="Upholstery & Office Chair Sanitization">Upholstery & Office Chair Sanitization</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Deployment City / State *</label>
                            <input type="text" name="location" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white transition" placeholder="e.g. Delhi NCR, Gurgaon, Bangalore">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Facility Scope & Shift Requirements</label>
                        <textarea name="message" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white transition" placeholder="Mention total facility square footage, number of guards needed, or specific pain points..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-extrabold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 uppercase tracking-widest text-sm flex items-center justify-center">
                        <span>Submit Consultation Request</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <p class="text-center text-[11px] text-slate-400">* All submissions are covered by our standard corporate Non-Disclosure Agreement (NDA).</p>
                </form>
            </div>
        </div>
    </div>
</div>
