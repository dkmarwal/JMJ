<?php
/**
 * JMJ Enterprises Solutions - Footer Component
 */
$secFooterLinks = \Models\Service::allActiveByCategory('security-services');
$cleanFooterLinks = \Models\Service::allActiveByCategory('cleaning-services');
?>
<!-- Comprehensive Corporate B2B Footer -->
<footer class="bg-[#090F1C] text-slate-400 text-xs pt-16 pb-12 border-t-2 border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Top Footer Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-slate-800/80">
            <!-- Brand Column -->
            <div class="lg:col-span-4 space-y-5">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 flex items-center justify-center overflow-hidden rounded-xl border border-slate-700 bg-white p-1">
                        <img src="<?= asset('img/logo.jpg') ?>" alt="JMJ Logo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight text-white block">JMJ ENTERPRISES</span>
                        <span class="text-[9px] font-bold tracking-widest text-[#F39C12] uppercase">Security & Facility Solutions</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    JMJ Enterprises Solutions Ltd. (Est. 2013) anchors enterprise infrastructures with PSARA-compliant tactical manned guarding while deploying hospital-grade sanitization matrices across 10 strategic state hubs in India.
                </p>
                <div class="flex items-center space-x-3 pt-2">
                    <?php if (setting('social_facebook')): ?>
                        <a href="<?= e(setting('social_facebook')) ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-800 hover:border-[#F39C12] hover:text-[#F39C12] flex items-center justify-center text-sm transition" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (setting('social_linkedin')): ?>
                        <a href="<?= e(setting('social_linkedin')) ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-800 hover:border-[#F39C12] hover:text-[#F39C12] flex items-center justify-center text-sm transition" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (setting('social_instagram')): ?>
                        <a href="<?= e(setting('social_instagram')) ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-800 hover:border-[#F39C12] hover:text-[#F39C12] flex items-center justify-center text-sm transition" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (setting('whatsapp_number')): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (string)setting('whatsapp_number')) ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-emerald-950 border border-emerald-800 hover:border-emerald-500 text-emerald-400 flex items-center justify-center text-sm transition" aria-label="WhatsApp Dispatch">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Security Services Column -->
            <div class="lg:col-span-3 space-y-4">
                <h4 class="text-sm font-extrabold uppercase tracking-wider text-white flex items-center border-b border-slate-800 pb-2">
                    <i class="fas fa-shield-halved text-[#F39C12] mr-2"></i> Security Services
                </h4>
                <ul class="space-y-2 text-xs">
                    <?php foreach (array_slice($secFooterLinks, 0, 7) as $s): ?>
                        <li>
                            <a href="<?= url('security-services/' . $s['slug']) ?>" class="hover:text-white transition flex items-center">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-2"></span>
                                <span class="truncate"><?= e($s['name']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li>
                        <a href="<?= url('security-services') ?>" class="text-[#F39C12] hover:underline font-bold mt-1 inline-block">
                            View All 12 Security Divisions &rarr;
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Cleaning Services Column -->
            <div class="lg:col-span-2 space-y-4">
                <h4 class="text-sm font-extrabold uppercase tracking-wider text-white flex items-center border-b border-slate-800 pb-2">
                    <i class="fas fa-sparkles text-[#F39C12] mr-2"></i> Cleaning Services
                </h4>
                <ul class="space-y-2 text-xs">
                    <?php foreach (array_slice($cleanFooterLinks, 0, 7) as $s): ?>
                        <li>
                            <a href="<?= url('cleaning-services/' . $s['slug']) ?>" class="hover:text-white transition flex items-center">
                                <span class="w-1.5 h-1.5 bg-[#254E70] rounded-full mr-2"></span>
                                <span class="truncate"><?= e($s['name']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <li>
                        <a href="<?= url('cleaning-services') ?>" class="text-[#F39C12] hover:underline font-bold mt-1 inline-block">
                            View All 14 Cleaning Divisions &rarr;
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact & HQ Information -->
            <div class="lg:col-span-3 space-y-4">
                <h4 class="text-sm font-extrabold uppercase tracking-wider text-white flex items-center border-b border-slate-800 pb-2">
                    <i class="fas fa-headset text-[#F39C12] mr-2"></i> Corporate Dispatch
                </h4>
                <div class="space-y-3 text-xs leading-relaxed">
                    <p class="flex items-start">
                        <i class="fas fa-location-dot text-[#F39C12] mr-2.5 mt-1 shrink-0"></i>
                        <span><?= e(setting('company_address', '250, Sant Nagar, East of Kailash, New Delhi – 110065')) ?></span>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-phone-volume text-[#F39C12] mr-2.5 shrink-0"></i>
                        <a href="tel:<?= e(setting('phone_toll_free', '18008890832')) ?>" class="hover:text-white font-bold text-white"><?= e(setting('phone_toll_free', '18008890832')) ?> (Toll-Free)</a>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-mobile-screen text-[#F39C12] mr-2.5 shrink-0"></i>
                        <a href="tel:<?= e(setting('phone_primary', '+91-9999381777')) ?>" class="hover:text-white"><?= e(setting('phone_primary', '+91-9999381777')) ?></a>
                    </p>
                    <p class="flex items-center">
                        <i class="fas fa-envelope text-[#F39C12] mr-2.5 shrink-0"></i>
                        <a href="mailto:<?= e(setting('email_support', 'jmjsanu@gmail.com')) ?>" class="hover:text-white"><?= e(setting('email_support', 'jmjsanu@gmail.com')) ?></a>
                    </p>
                </div>

                <!-- Newsletter Quick Subscribe -->
                <div class="pt-2">
                    <span class="text-[11px] font-bold text-slate-300 block mb-1.5">Subscribe to Security & Facility Bulletins</span>
                    <form action="<?= url('api/newsletter') ?>" method="POST" class="ajax-form flex items-center">
                        <?= csrf_field() ?>
                        <input type="email" name="email" required placeholder="Enter work email" class="w-full bg-slate-900 border border-slate-700 rounded-l-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-[#F39C12]">
                        <button type="submit" class="bg-[#F39C12] text-[#090F1C] px-3.5 py-2 rounded-r-lg font-extrabold hover:bg-amber-500 transition text-xs" aria-label="Subscribe">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 10 States Footprint Bar -->
        <div class="py-6 border-b border-slate-800/60 text-center">
            <span class="text-[10px] font-extrabold tracking-widest uppercase text-slate-500 block mb-2">10 Strategic Pan-India Regional Operations Hubs</span>
            <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 text-[11px] text-slate-400 font-medium">
                <span class="flex items-center"><i class="fas fa-city text-[#F39C12] mr-1.5 text-[10px]"></i> Delhi NCR (HQ)</span>
                <span class="text-slate-700">•</span>
                <span>Haryana (Gurgaon)</span>
                <span class="text-slate-700">•</span>
                <span>Uttar Pradesh (Noida)</span>
                <span class="text-slate-700">•</span>
                <span>Karnataka (Bangalore)</span>
                <span class="text-slate-700">•</span>
                <span>Maharashtra (Mumbai)</span>
                <span class="text-slate-700">•</span>
                <span>Tamil Nadu</span>
                <span class="text-slate-700">•</span>
                <span>Telangana</span>
                <span class="text-slate-700">•</span>
                <span>West Bengal</span>
                <span class="text-slate-700">•</span>
                <span>Madhya Pradesh</span>
                <span class="text-slate-700">•</span>
                <span>Punjab</span>
            </div>
        </div>

        <!-- Bottom Copyright & Legal Links -->
        <div class="pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-[11px] text-slate-500">
            <div>
                &copy; <?= date('Y') ?> <?= e(setting('company_name', 'JMJ Enterprises Solutions Ltd.')) ?> All rights reserved. PSARA Compliant.
            </div>
            <div class="flex items-center space-x-5">
                <a href="<?= url('about') ?>" class="hover:text-white transition">About Us</a>
                <a href="<?= url('privacy-policy') ?>" class="hover:text-white transition">Privacy Policy</a>
                <a href="<?= url('terms-conditions') ?>" class="hover:text-white transition">Terms & Conditions</a>
                <a href="<?= url('sitemap.xml') ?>" target="_blank" class="hover:text-white transition">XML Sitemap</a>
                <a href="<?= url('contact') ?>" class="hover:text-white transition">Contact Us</a>
            </div>
        </div>
    </div>
</footer>

<!-- Organization Schema Markup -->
<?= \Services\SeoService::renderOrganizationSchema() ?>
