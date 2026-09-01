<?php
/**
 * JMJ Enterprises Solutions - Homepage View
 */
?>

<!-- 1. CINEMATIC HERO SECTION -->
<section class="relative bg-[#090F1C] text-white overflow-hidden py-20 lg:py-28 border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-12 gap-12 items-center">
        <!-- Copywriting Block -->
        <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
            <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
                <i class="fas fa-shield-halved mr-2 text-sm animate-pulse"></i> PSARA Certified • Intelligent Protection & Hygiene
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-[1.12]">
                Securing Assets. <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F39C12] via-amber-400 to-amber-200">
                    Perfecting Spaces.
                </span>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-300 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                JMJ Enterprises Solutions anchors high-threat corporate infrastructure with vetted manned security personnel while deploying hospital-grade sanitization matrices across 10 strategic state hubs in India.
            </p>
            
            <!-- Call To Action Buttons -->
            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                <button type="button" class="open-quote-modal inline-flex items-center justify-center bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black px-8 py-4 rounded-xl shadow-xl transition-all duration-300 hover:scale-[1.02] text-xs uppercase tracking-widest min-w-[220px]">
                    <i class="fas fa-file-contract mr-2"></i> Get a Free Quote
                </button>
                <a href="#services-overview" class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/20 text-white font-extrabold px-8 py-4 rounded-xl transition-all duration-300 text-xs uppercase tracking-widest min-w-[180px]">
                    Explore Services <i class="fas fa-arrow-down ml-2 text-[#F39C12]"></i>
                </a>
            </div>

            <!-- Operational Badges -->
            <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-slate-400 font-semibold border-t border-slate-800/80">
                <span class="flex items-center"><i class="fas fa-check-circle text-[#F39C12] mr-2"></i> 100% Police Vetted</span>
                <span class="flex items-center"><i class="fas fa-check-circle text-[#F39C12] mr-2"></i> 24/7 Dispatch Control</span>
                <span class="flex items-center"><i class="fas fa-check-circle text-[#F39C12] mr-2"></i> ISO 9001:2015 Certified</span>
            </div>
        </div>

        <!-- Visual Mosaic Grid -->
        <div class="lg:col-span-5 relative hidden lg:block">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6 space-y-4">
                    <div class="relative group overflow-hidden rounded-2xl border border-slate-800 shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&q=80&w=600" alt="Tactical Guarding Deployment" class="h-44 w-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#090F1C] via-transparent to-transparent opacity-60"></div>
                        <span class="absolute bottom-3 left-3 text-[10px] font-bold text-white uppercase bg-black/60 px-2.5 py-1 rounded-md border border-slate-700">Manned Guarding</span>
                    </div>
                    <div class="relative group overflow-hidden rounded-2xl border border-slate-800 shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&q=80&w=600" alt="Industrial Sanitization Protocol" class="h-60 w-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#090F1C] via-transparent to-transparent opacity-60"></div>
                        <span class="absolute bottom-3 left-3 text-[10px] font-bold text-white uppercase bg-black/60 px-2.5 py-1 rounded-md border border-slate-700">Industrial Hygiene</span>
                    </div>
                </div>
                <div class="col-span-6 space-y-4 pt-8">
                    <div class="relative group overflow-hidden rounded-2xl border border-slate-800 shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=600" alt="Corporate Command Center" class="h-60 w-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#090F1C] via-transparent to-transparent opacity-60"></div>
                        <span class="absolute bottom-3 left-3 text-[10px] font-bold text-white uppercase bg-black/60 px-2.5 py-1 rounded-md border border-slate-700">Corporate Facilities</span>
                    </div>
                    <div class="relative group overflow-hidden rounded-2xl border border-slate-800 shadow-2xl">
                        <img src="<?= asset('img/security.JPG') ?>" alt="JMJ Guard Squad" class="h-44 w-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#090F1C] via-transparent to-transparent opacity-60"></div>
                        <span class="absolute bottom-3 left-3 text-[10px] font-bold text-white uppercase bg-black/60 px-2.5 py-1 rounded-md border border-slate-700">PSARA Roster</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. TRUST & STATISTICS SECTION -->
<section class="bg-white py-12 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="block text-3xl sm:text-4xl font-black text-[#090F1C] tracking-tight"><?= e(setting('stat_experience_years', '13+')) ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Years of Industry Excellence</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="block text-3xl sm:text-4xl font-black text-[#090F1C] tracking-tight"><?= e(setting('stat_clients_served', '450+')) ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Corporate & Institutional Clients</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="block text-3xl sm:text-4xl font-black text-[#090F1C] tracking-tight"><?= e(setting('stat_guards_deployed', '2,500+')) ?></span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Trained & Vetted Personnel</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="block text-3xl sm:text-4xl font-black text-[#090F1C] tracking-tight"><?= e(setting('stat_states_footprint', '10')) ?> States</span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Pan-India Operations Network</span>
            </div>
        </div>
    </div>
</section>

<!-- 3. ABOUT COMPANY INTRO SECTION -->
<section class="py-24 bg-[#F8FAFC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">
                    Corporate Profile & Heritage
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight leading-tight">
                    Reliable Corporate Protection & Clinical Hygiene Since 2013
                </h2>
                <div class="h-1 w-20 bg-[#F39C12] rounded-full"></div>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Headquartered in New Delhi, <strong>JMJ Enterprises Solutions Ltd.</strong> has grown into an integrated facilities powerhouse. We bridge the gap between rigorous manned security guarding and specialized commercial hygiene.
                </p>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Operating under full <strong>PSARA compliance</strong> and international ISO standards, we supply background-verified security guards, Lady Security Officers, emergency hospital protection, and advanced floor restoration crews to multinational campuses, embassies, and industrial complexes.
                </p>
                
                <div class="grid sm:grid-cols-2 gap-4 pt-2">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-certificate text-[#F39C12] text-lg mt-1"></i>
                        <div>
                            <h4 class="font-bold text-sm text-[#090F1C]">100% PSARA Licensed</h4>
                            <p class="text-xs text-slate-500">Zero legal liability for corporate clients.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-headset text-[#F39C12] text-lg mt-1"></i>
                        <div>
                            <h4 class="font-bold text-sm text-[#090F1C]">24/7 Operations Control</h4>
                            <p class="text-xs text-slate-500">Real-time shift tracking & rapid reserve guard pool.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="<?= url('about') ?>" class="inline-flex items-center text-xs font-extrabold text-[#090F1C] bg-white hover:bg-slate-50 px-6 py-3.5 rounded-xl border border-slate-300 shadow-sm transition uppercase tracking-wider">
                        Know More About Us <i class="fas fa-arrow-right ml-2 text-[#F39C12]"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-6 relative">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                    <img src="<?= asset('img/hospital.JPG') ?>" alt="JMJ Hospital Facility Staff" class="w-full h-[450px] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#090F1C] via-transparent to-transparent opacity-70"></div>
                    <div class="absolute bottom-6 left-6 right-6 text-white space-y-1">
                        <span class="text-[10px] font-bold tracking-widest text-[#F39C12] uppercase block">Operations Showcase</span>
                        <h4 class="text-lg font-black">Healthcare & Corporate Facility Management</h4>
                        <p class="text-xs text-slate-300">Dedicated professional personnel trained for high-density public environments.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. SECURITY SERVICES SHOWCASE -->
<section id="services-overview" class="py-24 bg-[#090F1C] text-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-16">
            <div>
                <span class="text-xs font-bold tracking-widest text-[#F39C12] uppercase block">Manned Guarding & Security Fleet</span>
                <h2 class="text-3xl sm:text-5xl font-black tracking-tight mt-2 text-white">Security Services</h2>
                <div class="h-1 w-20 bg-[#F39C12] mt-4 rounded-full"></div>
            </div>
            <a href="<?= url('security-services') ?>" class="text-xs font-extrabold text-[#F39C12] hover:text-amber-400 uppercase tracking-widest flex items-center">
                Explore All 12 Security Divisions <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($securityServices as $srv): ?>
                <div class="bg-[#0F1E36]/80 rounded-2xl p-6 border border-slate-800 hover:border-[#F39C12] transition-all duration-300 shadow-xl flex flex-col justify-between group hover:-translate-y-1.5">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-slate-900 text-[#F39C12] flex items-center justify-center text-xl mb-5 group-hover:bg-[#F39C12] group-hover:text-[#090F1C] transition-colors duration-300">
                            <i class="<?= e($srv['icon'] ?: 'fas fa-shield-alt') ?>"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2 leading-snug group-hover:text-[#F39C12] transition">
                            <?= e($srv['name']) ?>
                        </h3>
                        <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed mb-6">
                            <?= e($srv['short_summary']) ?>
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <a href="<?= url('security-services/' . $srv['slug']) ?>" class="text-xs font-extrabold text-[#F39C12] group-hover:underline flex items-center">
                            Service Details <i class="fas fa-chevron-right ml-1.5 text-[10px]"></i>
                        </a>
                        <button type="button" class="open-quote-modal text-[11px] text-slate-400 hover:text-white" data-service="<?= e($srv['name']) ?>">
                            Quote <i class="fas fa-arrow-up-right-from-square ml-1"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. CLEANING & SANITIZATION SERVICES SHOWCASE -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-16">
            <div>
                <span class="text-xs font-bold tracking-widest text-[#254E70] uppercase block">Engineering Cleanliness</span>
                <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight mt-2">Cleaning Services</h2>
                <div class="h-1 w-20 bg-[#F39C12] mt-4 rounded-full"></div>
            </div>
            <a href="<?= url('cleaning-services') ?>" class="text-xs font-extrabold text-[#254E70] hover:text-[#090F1C] uppercase tracking-widest flex items-center">
                Explore All 14 Cleaning Divisions <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($cleaningServices as $srv): ?>
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80 hover:border-[#254E70] transition-all duration-300 shadow-sm flex flex-col justify-between group hover:-translate-y-1.5">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white text-[#254E70] border border-slate-200 flex items-center justify-center text-xl mb-5 group-hover:bg-[#254E70] group-hover:text-white transition-colors duration-300 shadow-sm">
                            <i class="<?= e($srv['icon'] ?: 'fas fa-sparkles') ?>"></i>
                        </div>
                        <h3 class="text-lg font-bold text-[#090F1C] mb-2 leading-snug group-hover:text-[#254E70] transition">
                            <?= e($srv['name']) ?>
                        </h3>
                        <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed mb-6">
                            <?= e($srv['short_summary']) ?>
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                        <a href="<?= url('cleaning-services/' . $srv['slug']) ?>" class="text-xs font-extrabold text-[#254E70] group-hover:underline flex items-center">
                            Service Details <i class="fas fa-chevron-right ml-1.5 text-[10px]"></i>
                        </a>
                        <button type="button" class="open-quote-modal text-[11px] text-slate-400 hover:text-slate-800" data-service="<?= e($srv['name']) ?>">
                            Quote <i class="fas fa-arrow-up-right-from-square ml-1"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 6. WHY CHOOSE JMJ ENTERPRISES -->
<section class="py-24 bg-[#F8FAFC] border-t border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">Corporate Advantage</span>
            <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight mt-3">Why Leading Entities Choose JMJ</h2>
            <div class="h-1 w-20 bg-[#F39C12] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-premium space-y-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 bg-amber-50 text-[#F39C12] rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 class="text-xl font-extrabold text-[#090F1C]">Trained & Vetted Professionals</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    100% of our security guards and sanitation staff undergo mandatory criminal background checks, address verification, and intensive module training.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-premium space-y-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 bg-slate-100 text-[#254E70] rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-extrabold text-[#090F1C]">Strict PSARA Compliance</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Operate with absolute peace of mind. We hold full Private Security Agencies Regulation Act licensing with full statutory EPF/ESI compliance.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-premium space-y-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 bg-amber-50 text-[#F39C12] rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-xl font-extrabold text-[#090F1C]">24/7 Dispatch Control Desk</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Central command operations monitoring shift turnouts, field marshal mobile inspections, and emergency escalation channels round the clock.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-premium space-y-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 bg-slate-100 text-[#254E70] rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fas fa-sliders"></i>
                </div>
                <h3 class="text-xl font-extrabold text-[#090F1C]">Customized Service Matrices</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    We tailor security rosters and cleaning frequencies directly around your facility square footage, shift patterns, and threat parameters.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-premium space-y-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 bg-amber-50 text-[#F39C12] rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fas fa-microchip"></i>
                </div>
                <h3 class="text-xl font-extrabold text-[#090F1C]">Advanced Tech & Machinery</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    High-speed Taski scrubbers, diamond marble polishing pads, IRATA rope access, and AI CCTV integrations elevate standards.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-premium space-y-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 bg-slate-100 text-[#254E70] rounded-xl flex items-center justify-center text-xl font-bold">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h3 class="text-xl font-extrabold text-[#090F1C]">10 States National Footprint</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Seamless multi-location contracts across Delhi NCR, Haryana, UP, Karnataka, Maharashtra, West Bengal, and southern hubs under a single vendor SLA.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 7. INDUSTRIES WE SERVE -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full">Sector Expertise</span>
            <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight mt-3">Industries We Protect & Maintain</h2>
            <div class="h-1 w-20 bg-[#F39C12] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 text-center">
            <?php 
            $industries = [
                ['icon' => 'fas fa-building', 'name' => 'Corporate Offices'],
                ['icon' => 'fas fa-hospital', 'name' => 'Hospitals & Clinics'],
                ['icon' => 'fas fa-hotel', 'name' => 'Hotels & Resorts'],
                ['icon' => 'fas fa-graduation-cap', 'name' => 'Schools & Colleges'],
                ['icon' => 'fas fa-industry', 'name' => 'Industrial Plants'],
                ['icon' => 'fas fa-warehouse', 'name' => 'Logistics Hubs'],
                ['icon' => 'fas fa-house-chimney-window', 'name' => 'Gated Societies'],
                ['icon' => 'fas fa-cart-shopping', 'name' => 'Retail & Malls'],
                ['icon' => 'fas fa-utensils', 'name' => 'Commercial Kitchens'],
                ['icon' => 'fas fa-landmark-flag', 'name' => 'Embassies & Consulates'],
                ['icon' => 'fas fa-building-columns', 'name' => 'Banks & ATMs'],
                ['icon' => 'fas fa-globe', 'name' => 'Multinational MNCs']
            ];
            foreach ($industries as $ind): ?>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-[#F39C12] hover:bg-white transition-all duration-300 shadow-sm flex flex-col items-center justify-center space-y-3 group">
                    <div class="w-12 h-12 rounded-xl bg-white text-[#254E70] group-hover:bg-[#090F1C] group-hover:text-[#F39C12] flex items-center justify-center text-xl shadow-sm transition">
                        <i class="<?= $ind['icon'] ?>"></i>
                    </div>
                    <span class="text-xs font-bold text-[#090F1C] leading-snug"><?= $ind['name'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 8. OPERATIONAL PROCESS BLUEPRINT -->
<section class="py-24 bg-[#090F1C] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <span class="text-xs font-bold tracking-widest text-[#F39C12] uppercase">Standard Operating Blueprint</span>
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight mt-3">How Our Deployment Process Works</h2>
            <div class="h-1 w-20 bg-[#F39C12] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-6 gap-4 relative">
            <?php
            $steps = [
                ['num' => '01', 'title' => 'Requirement Gathering', 'desc' => 'Understanding your facility risk profile, shift patterns, and square footage.'],
                ['num' => '02', 'title' => 'On-Site Assessment', 'desc' => 'Physical audit of perimeters, high-traffic points, and surface materials.'],
                ['num' => '03', 'title' => 'Customized Proposal', 'desc' => 'Detailed SLA, guard profile specifications, and chemical matrix.'],
                ['num' => '04', 'title' => 'Roster Deployment', 'desc' => 'Deploying background-verified guards and briefed cleaning squads.'],
                ['num' => '05', 'title' => 'Quality Monitoring', 'desc' => 'Unannounced nighttime field audits and digital attendance tracking.'],
                ['num' => '06', 'title' => 'Ongoing Support', 'desc' => '24/7 dispatch helpline, client reviews, and continuous optimization.']
            ];
            foreach ($steps as $st): ?>
                <div class="bg-[#0F1E36]/70 p-6 rounded-2xl border border-slate-800 relative hover:border-[#F39C12] transition duration-300 flex flex-col justify-between">
                    <div>
                        <span class="text-2xl font-black text-[#F39C12] block mb-3"><?= $st['num'] ?></span>
                        <h4 class="font-extrabold text-sm text-white mb-2 leading-snug"><?= $st['title'] ?></h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed"><?= $st['desc'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 9. PAN-INDIA 10-STATES FOOTPRINT SECTION -->
<section class="py-24 bg-white border-t border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-5 space-y-6">
                <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-4 py-1.5 rounded-full shadow-sm">
                    Pan-India Infrastructure
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight leading-tight">
                    National Scale. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#254E70] to-[#090F1C]">Localized Command.</span>
                </h2>
                <div class="h-1 w-16 bg-[#F39C12] rounded-full my-4"></div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    With **10 strategic state regional offices** operating across India, JMJ Enterprises Solutions anchors rapid-deployment protocols for multi-location corporate entities, banking networks, and manufacturing complexes.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <span class="block text-3xl font-black text-[#090F1C]">10</span>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">State Command Hubs</span>
                    </div>
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <span class="block text-3xl font-black text-[#090F1C]">24/7</span>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1 block">Regional Dispatch</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-slate-50 rounded-3xl p-8 md:p-10 shadow-premium border border-slate-200 grid sm:grid-cols-2 gap-8 relative">
                    <div class="space-y-4">
                        <h4 class="font-extrabold text-sm uppercase tracking-widest text-[#254E70] flex items-center border-b border-slate-200 pb-2">
                            <i class="fas fa-compass text-[#F39C12] mr-2"></i> Northern Operations
                        </h4>
                        <ul class="space-y-2.5 text-sm">
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Delhi NCR <span class="text-[11px] text-slate-400 ml-2 font-normal">(Central HQ)</span></li>
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Haryana <span class="text-[11px] text-slate-400 ml-2 font-normal">(Gurgaon Hub)</span></li>
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Uttar Pradesh <span class="text-[11px] text-slate-400 ml-2 font-normal">(Noida Hub)</span></li>
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Punjab Office</li>
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-extrabold text-sm uppercase tracking-widest text-[#254E70] flex items-center border-b border-slate-200 pb-2">
                            <i class="fas fa-location-crosshairs text-[#F39C12] mr-2"></i> Southern & Western Loops
                        </h4>
                        <ul class="space-y-2.5 text-sm">
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Karnataka <span class="text-[11px] text-slate-400 ml-2 font-normal">(Bangalore Hub)</span></li>
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Maharashtra <span class="text-[11px] text-slate-400 ml-2 font-normal">(Mumbai Loop)</span></li>
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Tamil Nadu Office</li>
                            <li class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Telangana Hub</li>
                        </ul>
                    </div>

                    <div class="sm:col-span-2 space-y-4 pt-4 border-t border-slate-200">
                        <h4 class="font-extrabold text-sm uppercase tracking-widest text-[#254E70] flex items-center">
                            <i class="fas fa-network-wired text-[#F39C12] mr-2"></i> East & Central Deployments
                        </h4>
                        <div class="grid sm:grid-cols-2 gap-2.5 text-sm">
                            <div class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> West Bengal Hub</div>
                            <div class="flex items-center font-bold text-[#090F1C]"><span class="w-2 h-2 bg-[#F39C12] rounded-full mr-2.5"></span> Madhya Pradesh Center</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 10. CLIENT TESTIMONIALS -->
<section class="py-24 bg-[#F8FAFC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">Client Verification</span>
            <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight mt-3">What Our Enterprise Partners Say</h2>
            <div class="h-1 w-20 bg-[#F39C12] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($testimonials as $test): ?>
                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-premium flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex text-[#F39C12] text-sm">
                            <?php for ($i = 0; $i < (int)$test['rating']; $i++): ?>
                                <i class="fas fa-star mr-1"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 italic leading-relaxed">
                            "<?= e($test['testimonial']) ?>"
                        </p>
                    </div>
                    <div class="flex items-center space-x-3.5 pt-4 border-t border-slate-100">
                        <img src="<?= upload_url($test['photo']) ?>" alt="<?= e($test['client_name']) ?>" class="w-12 h-12 rounded-full object-cover border-2 border-slate-200">
                        <div>
                            <h4 class="font-extrabold text-sm text-[#090F1C]"><?= e($test['client_name']) ?></h4>
                            <span class="text-xs text-slate-400 block"><?= e($test['designation']) ?>, <?= e($test['company']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 11. LATEST BLOGS & INSIGHTS -->
<section class="py-24 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-16">
            <div>
                <span class="text-xs font-bold tracking-widest text-[#254E70] uppercase block">Thought Leadership</span>
                <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight mt-2">Latest Industry Insights</h2>
                <div class="h-1 w-20 bg-[#F39C12] mt-4 rounded-full"></div>
            </div>
            <a href="<?= url('blog') ?>" class="text-xs font-extrabold text-[#254E70] hover:text-[#090F1C] uppercase tracking-widest flex items-center">
                View All Articles <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($latestBlogs as $blog): ?>
                <article class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm flex flex-col justify-between group hover:-translate-y-1.5 transition-all duration-300">
                    <div>
                        <div class="h-48 overflow-hidden relative">
                            <img src="<?= upload_url($blog['featured_image']) ?>" alt="<?= e($blog['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <span class="absolute top-4 left-4 bg-[#090F1C] text-[#F39C12] px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-slate-800">
                                <?= e($blog['category_name']) ?>
                            </span>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center space-x-3 text-[11px] text-slate-400">
                                <span><i class="fas fa-calendar-day text-[#F39C12] mr-1"></i> <?= format_date($blog['publish_at']) ?></span>
                                <span>•</span>
                                <span><i class="fas fa-clock text-[#F39C12] mr-1"></i> <?= (int)$blog['reading_time'] ?> min read</span>
                            </div>
                            <h3 class="text-base font-bold text-[#090F1C] leading-snug group-hover:text-[#254E70] transition">
                                <a href="<?= url('blog/' . $blog['slug']) ?>"><?= e($blog['title']) ?></a>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                <?= e($blog['short_description']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <a href="<?= url('blog/' . $blog['slug']) ?>" class="text-xs font-extrabold text-[#254E70] hover:text-[#090F1C] flex items-center">
                            Read Full Article <i class="fas fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 12. ON-SITE AUDIT & MANDATE INITIATION -->
<section id="contact-mandate" class="py-24 bg-[#090F1C] text-white relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-12 bg-[#0F1E36] shadow-2xl rounded-3xl overflow-hidden border border-slate-800">
            <!-- Left Details Box -->
            <div class="md:col-span-5 bg-gradient-to-br from-[#090F1C] to-[#0F1E36] p-8 md:p-12 flex flex-col justify-between relative border-b md:border-b-0 md:border-r border-slate-800">
                <div class="space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#F39C12] block">Direct Consultation</span>
                    <h3 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Initiate Security & Cleaning Audit</h3>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                        Book an on-site structural risk assessment or establish a custom commercial cleaning contract matrix with our operations headquarters.
                    </p>
                    <div class="space-y-4 text-xs sm:text-sm pt-4">
                        <p class="flex items-start"><i class="fas fa-map-location-dot text-[#F39C12] mr-3 text-base mt-0.5"></i> <?= e(setting('company_address', '250, Sant Nagar, East of Kailash, New Delhi – 110065')) ?></p>
                        <p class="flex items-center"><i class="fas fa-phone-volume text-[#F39C12] mr-3 text-base"></i> <?= e(setting('phone_toll_free', '18008890832')) ?></p>
                        <p class="flex items-center"><i class="fas fa-envelope-open text-[#F39C12] mr-3 text-base"></i> <?= e(setting('email_support', 'jmjsanu@gmail.com')) ?></p>
                    </div>
                </div>
                <div class="mt-8 pt-4 border-t border-slate-800 text-[11px] text-slate-500">
                    * All deployment surveys run strictly under company NDA protocols.
                </div>
            </div>

            <!-- Right Form Input Panel -->
            <div class="md:col-span-7 p-8 md:p-12 bg-white text-slate-800">
                <form action="<?= url('api/lead') ?>" method="POST" class="ajax-form space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="survey">

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contact Name *</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]" placeholder="e.g. Rahul Sharma">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Entity / Company Name</label>
                            <input type="text" name="company" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]" placeholder="e.g. Apex Manufacturing">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Operations Email *</label>
                            <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]" placeholder="ops@entity.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mobile Phone *</label>
                            <input type="tel" name="phone" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]" placeholder="+91 99999 00000">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Requested Roster Mix</label>
                            <select name="service_required" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12] text-slate-700">
                                <option value="Bank Security Guards (High-Risk Profile)">Bank Security Guards (High-Risk Profile)</option>
                                <option value="Integrated Hospital Guards & Cleaning Crews">Integrated Hospital Guards & Cleaning Crews</option>
                                <option value="Corporate MNC Roster & Facilities Package">Corporate MNC Roster & Facilities Package</option>
                                <option value="Industrial Plant Cleaning & Guard Contract">Industrial Plant Cleaning & Guard Contract</option>
                                <option value="Commercial Floor Stripping & Waxing Contract">Commercial Floor Stripping & Waxing Contract</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deployment City *</label>
                            <input type="text" name="location" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]" placeholder="e.g. Delhi NCR, Bangalore">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Detailed Scope Requirements</label>
                        <textarea name="message" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#F39C12]" placeholder="Detail active shift patterns, total facility square footage, or key access pain points..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black py-4 px-6 rounded-xl shadow-lg transition-all duration-300 uppercase tracking-widest text-sm">
                        Submit Operational Mandate <i class="fas fa-file-contract ml-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
