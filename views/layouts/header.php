<?php
/**
 * JMJ Enterprises Solutions - Header & Sticky Navigation
 * Supports Desktop Full-Container Mega Menus & Responsive Mobile Drawer
 */
$securityServicesList = \Models\Service::allActiveByCategory('security-services');
$cleaningServicesList = \Models\Service::allActiveByCategory('cleaning-services');
?>
<!-- Operational Info Strip -->
<div class="bg-[#090F1C] text-slate-400 text-[11px] py-2.5 px-4 sm:px-6 lg:px-8 border-b border-slate-800/80">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center space-x-4 flex-wrap justify-center sm:justify-start">
            <span class="flex items-center text-slate-300 font-medium">
                <i class="fas fa-shield-halved text-[#F39C12] mr-1.5"></i> PSARA Compliant
            </span>
            <span class="hidden md:inline text-slate-700">•</span>
            <span class="hidden md:flex items-center">
                <i class="fas fa-map-marker-alt text-[#F39C12] mr-1.5"></i> Delhi NCR • Gurgaon • Noida • Bangalore • Pan-India
            </span>
            <span class="hidden lg:inline text-slate-700">•</span>
            <a href="mailto:<?= e(setting('email_support', 'jmjsanu@gmail.com')) ?>" class="hidden lg:flex items-center hover:text-white transition">
                <i class="fas fa-envelope text-[#F39C12] mr-1.5"></i> <?= e(setting('email_support', 'jmjsanu@gmail.com')) ?>
            </a>
        </div>
        <div class="flex items-center space-x-5 text-xs">
            <a href="tel:<?= e(setting('phone_toll_free', '18008890832')) ?>" class="text-[#F39C12] font-bold flex items-center hover:text-amber-400 transition">
                <i class="fas fa-phone mr-1.5"></i> Toll-Free: <?= e(setting('phone_toll_free', '18008890832')) ?>
            </a>
            <span class="text-slate-700 hidden sm:inline">|</span>
            <a href="<?= url('admin/login.php') ?>" class="text-slate-400 hover:text-white text-[11px] flex items-center transition">
                <i class="fas fa-lock text-slate-500 mr-1"></i> Staff Portal
            </a>
        </div>
    </div>
</div>

<!-- Main Sticky Header -->
<header class="bg-white/95 backdrop-blur-md shadow-lg sticky top-0 z-40 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 sm:h-24 flex justify-between items-center relative">
        <!-- Brand Logo -->
        <a href="<?= url() ?>" class="flex items-center space-x-3.5 group z-50 shrink-0">
            <div class="h-12 w-12 sm:h-14 sm:w-14 flex items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm group-hover:scale-105 transition-transform duration-300">
                <img src="<?= asset('img/logo.jpg') ?>" alt="JMJ Logo" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col">
                <span class="text-lg sm:text-xl font-black tracking-tight text-[#090F1C] leading-none">JMJ ENTERPRISES</span>
                <span class="text-[9px] sm:text-[10px] font-bold tracking-widest text-[#254E70] uppercase mt-1">Security & Facility Excellence</span>
            </div>
        </a>

        <!-- Desktop Navigation Bar -->
        <nav class="hidden xl:flex items-center space-x-7 font-bold text-slate-700 text-sm">
            <a href="<?= url() ?>" class="<?= is_active_route('/') && !is_active_route('about') && !is_active_route('security-services') && !is_active_route('cleaning-services') && !is_active_route('blog') && !is_active_route('gallery') && !is_active_route('contact') ? 'text-[#090F1C] border-b-2 border-[#F39C12] pb-1' : 'hover:text-[#090F1C] transition pb-1' ?>">
                Home
            </a>
            
            <a href="<?= url('about') ?>" class="<?= is_active_route('about') ? 'text-[#090F1C] border-b-2 border-[#F39C12] pb-1' : 'hover:text-[#090F1C] transition pb-1' ?>">
                About Us
            </a>

            <!-- Security Services Mega Menu Trigger (Full Container Width) -->
            <div class="group py-7">
                <a href="<?= url('security-services') ?>" class="flex items-center <?= is_active_route('security-services') ? 'text-[#090F1C] border-b-2 border-[#F39C12] pb-1' : 'hover:text-[#090F1C] transition pb-1' ?>">
                    <span>Security Services</span>
                    <i class="fas fa-chevron-down ml-1.5 text-xs text-slate-400 group-hover:text-[#F39C12] group-hover:rotate-180 transition-transform duration-200"></i>
                </a>

                <!-- Security Mega Menu Box (Spans Full Container Width) -->
                <div class="mega-menu-wrapper absolute left-0 right-0 top-full w-full bg-white rounded-3xl shadow-2xl border border-slate-200/90 p-8 z-50">
                    <div class="flex justify-between items-center pb-4 mb-6 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-[#254E70] flex items-center">
                                <i class="fas fa-shield-halved text-[#F39C12] mr-2"></i> Manned Guarding & Protection Matrix
                            </span>
                            <p class="text-xs text-slate-500 mt-0.5">12 Specialized PSARA-compliant manned security divisions deployed across India</p>
                        </div>
                        <a href="<?= url('security-services') ?>" class="text-xs font-extrabold text-[#F39C12] hover:text-amber-600 flex items-center bg-amber-50 px-4 py-2 rounded-xl transition">
                            Explore Security Division Hub <i class="fas fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>

                    <!-- 4-Column Grid: 12 Services -->
                    <div class="grid grid-cols-4 gap-4">
                        <?php foreach ($securityServicesList as $srv): ?>
                            <a href="<?= url('security-services/' . $srv['slug']) ?>" class="p-3 rounded-2xl hover:bg-slate-50 border border-slate-100/80 hover:border-amber-200 flex items-start space-x-3 transition group/item">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 text-[#F39C12] flex items-center justify-center text-sm shrink-0 group-hover/item:bg-[#090F1C] group-hover/item:text-[#F39C12] transition shadow-sm">
                                    <i class="<?= e($srv['icon'] ?: 'fas fa-shield-alt') ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-[#090F1C] group-hover/item:text-[#F39C12] transition leading-snug truncate"><?= e($srv['name']) ?></h4>
                                    <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5"><?= e($srv['short_summary']) ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Bottom Mega Menu Feature Strip -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500 font-medium">
                        <div class="flex items-center space-x-6">
                            <span class="flex items-center"><i class="fas fa-check-circle text-emerald-500 mr-1.5"></i> 100% Police Vetted</span>
                            <span class="flex items-center"><i class="fas fa-check-circle text-emerald-500 mr-1.5"></i> PSARA Certified Agency</span>
                            <span class="flex items-center"><i class="fas fa-check-circle text-emerald-500 mr-1.5"></i> 24/7 Regional Patrol Marshals</span>
                        </div>
                        <button type="button" class="open-quote-modal text-[#F39C12] font-black hover:underline uppercase tracking-wider text-[11px]" data-service="Security Services">
                            Request Guard Deployment Quote &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cleaning Services Mega Menu Trigger (Full Container Width) -->
            <div class="group py-7">
                <a href="<?= url('cleaning-services') ?>" class="flex items-center <?= is_active_route('cleaning-services') ? 'text-[#090F1C] border-b-2 border-[#F39C12] pb-1' : 'hover:text-[#090F1C] transition pb-1' ?>">
                    <span>Cleaning Services</span>
                    <i class="fas fa-chevron-down ml-1.5 text-xs text-slate-400 group-hover:text-[#F39C12] group-hover:rotate-180 transition-transform duration-200"></i>
                </a>

                <!-- Cleaning Mega Menu Box (Spans Full Container Width) -->
                <div class="mega-menu-wrapper absolute left-0 right-0 top-full w-full bg-white rounded-3xl shadow-2xl border border-slate-200/90 p-8 z-50">
                    <div class="flex justify-between items-center pb-4 mb-6 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-[#254E70] flex items-center">
                                <i class="fas fa-sparkles text-[#F39C12] mr-2"></i> Commercial & Industrial Sanitization Matrix
                            </span>
                            <p class="text-xs text-slate-500 mt-0.5">14 Specialized commercial, healthcare, and industrial hygiene capabilities</p>
                        </div>
                        <a href="<?= url('cleaning-services') ?>" class="text-xs font-extrabold text-[#254E70] hover:text-[#090F1C] flex items-center bg-slate-100 px-4 py-2 rounded-xl transition">
                            Explore Cleaning Division Hub <i class="fas fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>

                    <!-- 4-Column Grid: 14 Services -->
                    <div class="grid grid-cols-4 gap-4">
                        <?php foreach ($cleaningServicesList as $srv): ?>
                            <a href="<?= url('cleaning-services/' . $srv['slug']) ?>" class="p-3 rounded-2xl hover:bg-slate-50 border border-slate-100/80 hover:border-slate-300 flex items-start space-x-3 transition group/item">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#254E70] flex items-center justify-center text-sm shrink-0 group-hover/item:bg-[#090F1C] group-hover/item:text-[#F39C12] transition shadow-sm">
                                    <i class="<?= e($srv['icon'] ?: 'fas fa-sparkles') ?>"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-[#090F1C] group-hover/item:text-[#254E70] transition leading-snug truncate"><?= e($srv['name']) ?></h4>
                                    <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5"><?= e($srv['short_summary']) ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Bottom Mega Menu Feature Strip -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500 font-medium">
                        <div class="flex items-center space-x-6">
                            <span class="flex items-center"><i class="fas fa-check-circle text-emerald-500 mr-1.5"></i> Hospital-Grade Disinfectants</span>
                            <span class="flex items-center"><i class="fas fa-check-circle text-emerald-500 mr-1.5"></i> High-Speed Taski Machinery</span>
                            <span class="flex items-center"><i class="fas fa-check-circle text-emerald-500 mr-1.5"></i> ISO 9001:2015 Standards</span>
                        </div>
                        <button type="button" class="open-quote-modal text-[#254E70] font-black hover:underline uppercase tracking-wider text-[11px]" data-service="Cleaning Services">
                            Schedule Facility Site Audit &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <a href="<?= url('blog') ?>" class="<?= is_active_route('blog') ? 'text-[#090F1C] border-b-2 border-[#F39C12] pb-1' : 'hover:text-[#090F1C] transition pb-1' ?>">
                Blog
            </a>
            <a href="<?= url('gallery') ?>" class="<?= is_active_route('gallery') ? 'text-[#090F1C] border-b-2 border-[#F39C12] pb-1' : 'hover:text-[#090F1C] transition pb-1' ?>">
                Gallery
            </a>
            <a href="<?= url('contact') ?>" class="<?= is_active_route('contact') ? 'text-[#090F1C] border-b-2 border-[#F39C12] pb-1' : 'hover:text-[#090F1C] transition pb-1' ?>">
                Contact
            </a>
        </nav>

        <!-- Header Actions: Search + CTA -->
        <div class="flex items-center space-x-3 sm:space-x-4 z-50 shrink-0">
            <!-- Search Icon -->
            <button type="button" class="open-search-modal text-slate-600 hover:text-[#090F1C] p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition" aria-label="Open Search">
                <i class="fas fa-magnifying-glass text-sm sm:text-base"></i>
            </button>

            <!-- Free Quote Modal CTA -->
            <button type="button" class="open-quote-modal hidden sm:inline-flex bg-[#090F1C] hover:bg-[#254E70] text-white text-xs font-extrabold px-5 py-3.5 rounded-xl uppercase tracking-wider shadow-md transition-all duration-300 border border-slate-800">
                <i class="fas fa-file-signature mr-2 text-[#F39C12]"></i> Get a Free Quote
            </button>

            <!-- Mobile Hamburger Button -->
            <button id="mobile-menu-btn" class="xl:hidden text-[#090F1C] p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition focus:outline-none" aria-label="Toggle Navigation Menu">
                <i class="fas fa-bars text-xl text-slate-800" id="menu-icon"></i>
            </button>
        </div>
    </div>

    <!-- Sliding Mobile Navigation Drawer -->
    <div id="mobile-drawer" class="fixed top-0 bottom-0 right-0 w-full sm:w-96 h-screen bg-[#090F1C] text-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out xl:hidden z-40 flex flex-col justify-between p-6 pt-24 overflow-y-auto">
        <!-- Navigation Links with Accordions -->
        <nav class="flex flex-col space-y-3 text-base font-bold">
            <a href="<?= url() ?>" class="px-3 py-2 rounded-lg hover:bg-slate-800 transition text-[#F39C12]">Home</a>
            <a href="<?= url('about') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-800 transition text-slate-200 hover:text-white">About Us</a>
            
            <!-- Security Accordion -->
            <div>
                <button type="button" class="mobile-accordion-btn w-full flex justify-between items-center px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200 transition" data-target="mobile-security-list">
                    <span>Security Services (12)</span>
                    <i class="fas fa-chevron-down accordion-icon text-xs text-slate-400 transition-transform"></i>
                </button>
                <div id="mobile-security-list" class="hidden pl-4 pr-2 py-2 space-y-1.5 bg-slate-900/60 rounded-xl mt-1 border border-slate-800 text-xs">
                    <a href="<?= url('security-services') ?>" class="block py-1 font-extrabold text-[#F39C12] hover:underline">View All Security Services Hub &rarr;</a>
                    <?php foreach ($securityServicesList as $srv): ?>
                        <a href="<?= url('security-services/' . $srv['slug']) ?>" class="block py-1 text-slate-300 hover:text-white transition"><?= e($srv['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Cleaning Accordion -->
            <div>
                <button type="button" class="mobile-accordion-btn w-full flex justify-between items-center px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-200 transition" data-target="mobile-cleaning-list">
                    <span>Cleaning Services (14)</span>
                    <i class="fas fa-chevron-down accordion-icon text-xs text-slate-400 transition-transform"></i>
                </button>
                <div id="mobile-cleaning-list" class="hidden pl-4 pr-2 py-2 space-y-1.5 bg-slate-900/60 rounded-xl mt-1 border border-slate-800 text-xs max-h-60 overflow-y-auto">
                    <a href="<?= url('cleaning-services') ?>" class="block py-1 font-extrabold text-[#F39C12] hover:underline">View All Cleaning Services Hub &rarr;</a>
                    <?php foreach ($cleaningServicesList as $srv): ?>
                        <a href="<?= url('cleaning-services/' . $srv['slug']) ?>" class="block py-1 text-slate-300 hover:text-white transition"><?= e($srv['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="<?= url('blog') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-800 transition text-slate-200 hover:text-white">Blog & Insights</a>
            <a href="<?= url('gallery') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-800 transition text-slate-200 hover:text-white">Gallery</a>
            <a href="<?= url('contact') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-800 transition text-slate-200 hover:text-white">Contact Us</a>
        </nav>

        <!-- Bottom Drawer Actions -->
        <div class="space-y-4 border-t border-slate-800 pt-6 mt-6">
            <button type="button" class="open-quote-modal block w-full bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-extrabold text-center py-3.5 rounded-xl uppercase tracking-wider text-xs transition">
                Request Free Site Survey
            </button>
            <div class="text-xs text-slate-400 space-y-1.5">
                <p class="flex items-center"><i class="fas fa-phone text-[#F39C12] mr-2"></i> <?= e(setting('phone_toll_free', '18008890832')) ?></p>
                <p class="flex items-center"><i class="fas fa-envelope text-[#F39C12] mr-2"></i> <?= e(setting('email_support', 'jmjsanu@gmail.com')) ?></p>
            </div>
        </div>
    </div>
    
    <!-- Background Tint Overlay -->
    <div id="drawer-overlay" class="fixed inset-0 bg-black/60 opacity-0 pointer-events-none transition-opacity duration-300 z-30 xl:hidden"></div>
</header>

<!-- Global Search Modal Popup -->
<div id="search-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity close-search-modal"></div>
    <div class="flex min-h-full items-start justify-center p-4 sm:p-6 text-center pt-20">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-2xl transition-all border border-slate-200">
            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <h3 class="text-base font-black text-[#090F1C] flex items-center">
                    <i class="fas fa-magnifying-glass text-[#F39C12] mr-2"></i> Search JMJ Services & Insights
                </h3>
                <button type="button" class="close-search-modal text-slate-400 hover:text-slate-700">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="<?= url('blog/search') ?>" method="GET" class="mt-4">
                <div class="relative">
                    <input type="text" id="global-search-input" name="q" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-5 py-4 pl-12 text-sm focus:outline-none focus:border-[#F39C12] focus:bg-white text-slate-800 transition" placeholder="Search corporate security, hospital sanitization, floor waxing, ATM guard...">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                <div class="mt-4 flex justify-between items-center text-xs text-slate-500">
                    <span>Popular: <a href="<?= url('security-services/corporate-security') ?>" class="text-[#254E70] underline">Corporate Security</a>, <a href="<?= url('cleaning-services/floor-waxing') ?>" class="text-[#254E70] underline">Floor Waxing</a>, <a href="<?= url('cleaning-services/hospital-cleaning') ?>" class="text-[#254E70] underline">Hospital Cleaning</a></span>
                    <button type="submit" class="bg-[#090F1C] text-white px-4 py-2 rounded-lg font-bold hover:bg-[#254E70] transition">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
