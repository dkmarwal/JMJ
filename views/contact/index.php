<?php
/**
 * JMJ Enterprises Solutions - Contact Us View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Hero Header Section -->
<section class="bg-[#090F1C] text-white py-16 lg:py-24 relative overflow-hidden border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
            <i class="fas fa-headset mr-2"></i> 24/7 Operations Command & Headquarters
        </span>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
            Connect with Our Dispatch Team
        </h1>
        <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Reach out to our central operations team in New Delhi or our regional state hubs for immediate security guard deployment and commercial cleaning consultations.
        </p>
    </div>
</section>

<!-- Contact Info Grid & Form -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12">
            
            <!-- Left 5 Columns: Corporate Contact Points -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full">
                        Headquarters Information
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#090F1C] tracking-tight mt-3">
                        JMJ Enterprises Solutions Ltd.
                    </h2>
                </div>

                <div class="space-y-4 text-xs sm:text-sm">
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white text-[#F39C12] border border-slate-200 flex items-center justify-center text-lg shrink-0 shadow-sm">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Head Office Address</span>
                            <p class="font-bold text-[#090F1C] mt-0.5 leading-relaxed"><?= e(setting('company_address', '250, Sant Nagar, East of Kailash, New Delhi – 110065')) ?></p>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white text-[#F39C12] border border-slate-200 flex items-center justify-center text-lg shrink-0 shadow-sm">
                            <i class="fas fa-phone-volume"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">24/7 Dispatch Helpline</span>
                            <a href="tel:<?= e(setting('phone_toll_free', '18008890832')) ?>" class="font-black text-base text-[#090F1C] hover:text-[#F39C12] transition block mt-0.5"><?= e(setting('phone_toll_free', '18008890832')) ?> (Toll-Free)</a>
                            <a href="tel:<?= e(setting('phone_primary', '+91-9999381777')) ?>" class="text-xs text-slate-600 hover:text-[#090F1C] transition block mt-0.5">Direct: <?= e(setting('phone_primary', '+91-9999381777')) ?></a>
                            <a href="tel:<?= e(setting('phone_landline', '011-41037091')) ?>" class="text-xs text-slate-600 hover:text-[#090F1C] transition block">Landline: <?= e(setting('phone_landline', '011-41037091')) ?></a>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white text-[#F39C12] border border-slate-200 flex items-center justify-center text-lg shrink-0 shadow-sm">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Official Emails</span>
                            <a href="mailto:<?= e(setting('email_support', 'jmjsanu@gmail.com')) ?>" class="font-bold text-[#090F1C] hover:text-[#F39C12] transition block mt-0.5"><?= e(setting('email_support', 'jmjsanu@gmail.com')) ?></a>
                            <a href="mailto:<?= e(setting('email_corporate', 'info@jmjenterprisessolutions.com')) ?>" class="text-xs text-slate-600 hover:text-[#090F1C] transition block"><?= e(setting('email_corporate', 'info@jmjenterprisessolutions.com')) ?></a>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white text-[#F39C12] border border-slate-200 flex items-center justify-center text-lg shrink-0 shadow-sm">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Business & Operations Hours</span>
                            <p class="font-bold text-[#090F1C] mt-0.5"><?= e(setting('business_hours', 'Monday – Saturday: 8:00 AM – 6:00 PM | 24/7 Operations Control')) ?></p>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Quick Chat Button -->
                <?php if (setting('whatsapp_number')): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (string)setting('whatsapp_number')) ?>" target="_blank" rel="noopener" class="flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 px-6 rounded-2xl shadow-lg transition duration-200 text-xs uppercase tracking-wider">
                        <i class="fab fa-whatsapp text-lg"></i>
                        <span>Chat Instantly on WhatsApp</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Right 7 Columns: Inbound Form -->
            <div class="lg:col-span-7 bg-[#F8FAFC] p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-premium">
                <div class="mb-8 border-b border-slate-200 pb-4">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#F39C12] block">Online Dispatch Inbound</span>
                    <h3 class="text-2xl font-black text-[#090F1C]">Send an Official Enquiry</h3>
                </div>

                <form action="<?= url('api/lead') ?>" method="POST" class="ajax-form space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="contact">

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Your Full Name *</label>
                            <input type="text" name="name" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Company / Entity Name</label>
                            <input type="text" name="company" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Official Email *</label>
                            <input type="email" name="email" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Phone / Mobile *</label>
                            <input type="tel" name="phone" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Service Required *</label>
                            <select name="service_required" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] text-slate-700">
                                <option value="Corporate Manned Guarding">Corporate Manned Guarding</option>
                                <option value="Industrial Security Squad">Industrial Security Squad</option>
                                <option value="Hospital Guards & Cleaners">Hospital Guards & Cleaners</option>
                                <option value="Commercial Housekeeping Contract">Commercial Housekeeping Contract</option>
                                <option value="Floor Waxing & Polishing">Floor Waxing & Polishing</option>
                                <option value="High-Altitude Facade Glass Clean">High-Altitude Facade Glass Clean</option>
                                <option value="Integrated Facility Package">Integrated Facility Package</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Facility City / Location *</label>
                            <input type="text" name="location" required placeholder="e.g. New Delhi, Gurgaon" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Message / Detailed Scope</label>
                        <textarea name="message" rows="4" required placeholder="Describe your facility specifications, required guard strength, or cleaning schedule..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#090F1C] hover:bg-[#254E70] text-white font-black py-4 px-6 rounded-xl shadow-lg transition-all duration-300 uppercase tracking-widest text-xs sm:text-sm">
                        Submit Official Message <i class="fas fa-paper-plane ml-2 text-[#F39C12]"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- Google Map Embed Section -->
<section class="h-96 w-full bg-slate-200 border-t border-b border-slate-300">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3504.6067713498877!2d77.24756287550385!3d28.55152867570886!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce3c4b5748805%3A0xe54d318e6973e4b0!2sSant%20Nagar%2C%20East%20of%20Kailash%2C%20New%20Delhi%2C%20Delhi%20110065!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="JMJ Enterprises Solutions Location Map"></iframe>
</section>
