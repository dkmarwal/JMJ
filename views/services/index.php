<?php
/**
 * JMJ Enterprises Solutions - Combined Services Hub View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Hero Section -->
<section class="bg-[#090F1C] text-white py-16 lg:py-24 relative overflow-hidden border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
            <i class="fas fa-shield-halved mr-2"></i> Comprehensive B2B Capabilities
        </span>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
            Integrated Security & Cleaning Solutions
        </h1>
        <p class="text-base sm:text-lg text-slate-300 max-w-3xl mx-auto leading-relaxed">
            Discover our complete roster of PSARA-certified manned security protection and hospital-grade commercial cleaning services engineered for Indian enterprise infrastructures.
        </p>
    </div>
</section>

<!-- Security Services Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12 border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#F39C12]">Manned Protection</span>
                <h2 class="text-2xl sm:text-4xl font-black text-[#090F1C]">Security Guard Services</h2>
            </div>
            <a href="<?= url('security-services') ?>" class="text-xs font-extrabold text-[#F39C12] hover:underline uppercase tracking-wider">
                Security Landing Page &rarr;
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($securityServices as $srv): ?>
                <a href="<?= url('security-services/' . $srv['slug']) ?>" class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-[#F39C12] hover:shadow-lg transition duration-300 group block">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#F39C12] flex items-center justify-center text-lg mb-3 group-hover:bg-[#090F1C] transition">
                        <i class="<?= e($srv['icon'] ?: 'fas fa-shield-alt') ?>"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#090F1C] group-hover:text-[#F39C12] transition mb-1"><?= e($srv['name']) ?></h3>
                    <p class="text-xs text-slate-500 line-clamp-2"><?= e($srv['short_summary']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Cleaning Services Section -->
<section class="py-20 bg-[#F8FAFC] border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12 border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#254E70]">Commercial Sanitization</span>
                <h2 class="text-2xl sm:text-4xl font-black text-[#090F1C]">Cleaning & Housekeeping Services</h2>
            </div>
            <a href="<?= url('cleaning-services') ?>" class="text-xs font-extrabold text-[#254E70] hover:underline uppercase tracking-wider">
                Cleaning Landing Page &rarr;
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($cleaningServices as $srv): ?>
                <a href="<?= url('cleaning-services/' . $srv['slug']) ?>" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-[#254E70] hover:shadow-lg transition duration-300 group block">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-[#254E70] flex items-center justify-center text-lg mb-3 group-hover:bg-[#254E70] group-hover:text-white transition">
                        <i class="<?= e($srv['icon'] ?: 'fas fa-sparkles') ?>"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#090F1C] group-hover:text-[#254E70] transition mb-1"><?= e($srv['name']) ?></h3>
                    <p class="text-xs text-slate-500 line-clamp-2"><?= e($srv['short_summary']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Reusable CTA Banner -->
<?php 
$ctaTitle = 'Need Customized Security & Facility Packages?';
$ctaSubtitle = 'Combine security guarding and commercial housekeeping into a single unified corporate SLA.';
include VIEWS_PATH . '/partials/cta_banner.php'; 
?>
