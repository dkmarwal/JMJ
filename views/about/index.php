<?php
/**
 * JMJ Enterprises Solutions - About Us View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Hero Header Banner -->
<section class="bg-[#090F1C] text-white py-16 lg:py-24 relative overflow-hidden border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
            <i class="fas fa-shield-halved mr-2"></i> About JMJ Enterprises Solutions
        </span>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
            Engineering Security & Facility Excellence
        </h1>
        <p class="text-base sm:text-lg text-slate-300 max-w-3xl mx-auto leading-relaxed">
            Founded in 2013, JMJ Enterprises Solutions has established itself as India's premier B2B security manned guarding and commercial sanitization contractor operating across 10 strategic state regional hubs.
        </p>
    </div>
</section>

<!-- Company History & Heritage -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full shadow-sm">
                    Our Story & Legacy
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-[#090F1C] tracking-tight">
                    Over a Decade of Trust, Protection & Innovation
                </h2>
                <div class="h-1 w-20 bg-[#F39C12] rounded-full"></div>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    JMJ Enterprises Solutions Ltd. began operations in New Delhi in 2013 with a core mission: to bring institutional professionalism, rigorous PSARA compliance, and modern operational discipline to the Indian private security and housekeeping industry.
                </p>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Over the past 13+ years, we have scaled our operational capabilities to support Fortune 500 corporate offices, high-traffic multi-specialty hospitals, international diplomatic embassies, heavy manufacturing plants, and luxury residential estates across 10 Indian states.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="block text-2xl font-black text-[#090F1C]">100%</span>
                        <span class="text-xs text-slate-500 font-bold uppercase mt-1 block">PSARA & Labor Compliant</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="block text-2xl font-black text-[#090F1C]">2,500+</span>
                        <span class="text-xs text-slate-500 font-bold uppercase mt-1 block">Vetted Active Roster</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 relative">
                <div class="grid grid-cols-2 gap-4">
                    <img src="<?= asset('img/security.JPG') ?>" alt="JMJ Security Patrol" class="rounded-2xl shadow-xl w-full h-64 object-cover border-2 border-slate-200">
                    <img src="<?= asset('img/hospital.JPG') ?>" alt="JMJ Hospital Facility" class="rounded-2xl shadow-xl w-full h-64 object-cover border-2 border-slate-200 mt-6">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision, Mission & Values -->
<section class="py-24 bg-[#F8FAFC] border-t border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Mission -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-premium space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-[#F39C12] flex items-center justify-center text-2xl font-bold">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="text-2xl font-black text-[#090F1C]">Our Mission</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    To deliver uncompromising physical security protection and hospital-grade commercial cleanliness through disciplined workforce management, continuous training, and transparent service level agreements.
                </p>
            </div>

            <!-- Vision -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-premium space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-[#254E70] flex items-center justify-center text-2xl font-bold">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="text-2xl font-black text-[#090F1C]">Our Vision</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    To remain India's most dependable integrated security and facility solutions brand, renowned for zero-breach security records and environmental sustainability in commercial sanitization.
                </p>
            </div>

            <!-- Values -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-premium space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-[#F39C12] flex items-center justify-center text-2xl font-bold">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3 class="text-2xl font-black text-[#090F1C]">Core Values</h3>
                <ul class="text-xs sm:text-sm text-slate-600 space-y-2">
                    <li class="flex items-center"><i class="fas fa-check text-[#F39C12] mr-2"></i> <strong>Integrity:</strong> Unwavering honesty & vetted honesty.</li>
                    <li class="flex items-center"><i class="fas fa-check text-[#F39C12] mr-2"></i> <strong>Vigilance:</strong> 24/7 proactive threat prevention.</li>
                    <li class="flex items-center"><i class="fas fa-check text-[#F39C12] mr-2"></i> <strong>Excellence:</strong> ISO certified operational discipline.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Training & Quality Assurance -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full">Human Capital & Training</span>
            <h2 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight mt-3">The JMJ Training Academy Standard</h2>
            <div class="h-1 w-20 bg-[#F39C12] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <span class="text-2xl font-black text-[#F39C12]">01</span>
                <h4 class="font-extrabold text-base text-[#090F1C]">Police & Background Verification</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Formal police clearance certificates, Aadhaar identity validation, and ancestral residence vetting.</p>
            </div>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <span class="text-2xl font-black text-[#F39C12]">02</span>
                <h4 class="font-extrabold text-base text-[#090F1C]">100+ Hours PSARA Syllabus</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Comprehensive classroom curriculum covering access control, firefighting, physical defense, and first aid.</p>
            </div>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <span class="text-2xl font-black text-[#F39C12]">03</span>
                <h4 class="font-extrabold text-base text-[#090F1C]">Sector-Specific Simulation</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Tailored drills for hospital crowd de-escalation, bank vault defense, and embassy security protocols.</p>
            </div>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <span class="text-2xl font-black text-[#F39C12]">04</span>
                <h4 class="font-extrabold text-base text-[#090F1C]">Surprise Night Audits</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Mobile patrol marshals conduct unannounced midnight field inspections testing guard alertness.</p>
            </div>
        </div>
    </div>
</section>

<!-- Reusable CTA Banner -->
<?php 
$ctaTitle = 'Upgrade Your Security & Facility Standards Today';
$ctaSubtitle = 'Consult with our security and cleaning directors for a personalized site threat assessment.';
include VIEWS_PATH . '/partials/cta_banner.php'; 
?>
