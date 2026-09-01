<?php
/**
 * JMJ Enterprises Solutions - Cleaning Services Hub View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Hero Section -->
<section class="bg-[#090F1C] text-white py-16 lg:py-24 relative overflow-hidden border-b-4 border-[#254E70]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
            <i class="fas fa-sparkles mr-2"></i> Clinical & Commercial Hygiene Excellence
        </span>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
            Professional Cleaning Services
        </h1>
        <p class="text-base sm:text-lg text-slate-300 max-w-3xl mx-auto leading-relaxed">
            Hospital-grade pathogen sanitization, industrial floor stripping and polymer waxing, corporate office housekeeping, and high-altitude facade window washing.
        </p>
    </div>
</section>

<!-- Cleaning Catalog Grid -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full">Hygiene Capabilities</span>
            <h2 class="text-3xl sm:text-4xl font-black text-[#090F1C] tracking-tight mt-3">14 Specialized Commercial & Industrial Cleaning Divisions</h2>
            <div class="h-1 w-20 bg-[#F39C12] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($services as $srv): ?>
                <div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl hover:border-[#254E70] transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1.5">
                    <div>
                        <div class="h-52 overflow-hidden relative">
                            <img src="<?= upload_url($srv['hero_image']) ?>" alt="<?= e($srv['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#090F1C] via-transparent to-transparent opacity-60"></div>
                            <span class="absolute top-4 left-4 bg-[#090F1C] text-white px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-slate-800">
                                <i class="<?= e($srv['icon'] ?: 'fas fa-sparkles') ?> mr-1 text-[#F39C12]"></i> Sanitization Matrix
                            </span>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3 class="text-xl font-bold text-[#090F1C] group-hover:text-[#254E70] transition leading-snug">
                                <a href="<?= url('cleaning-services/' . $srv['slug']) ?>"><?= e($srv['name']) ?></a>
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed line-clamp-3">
                                <?= e($srv['short_summary']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-200/80 flex items-center justify-between">
                        <a href="<?= url('cleaning-services/' . $srv['slug']) ?>" class="text-xs font-extrabold text-[#090F1C] group-hover:text-[#254E70] transition flex items-center">
                            Full Service Page <i class="fas fa-arrow-right ml-2 text-[#254E70]"></i>
                        </a>
                        <button type="button" class="open-quote-modal text-xs font-bold text-slate-500 hover:text-[#090F1C]" data-service="<?= e($srv['name']) ?>">
                            Get Quote <i class="fas fa-file-contract ml-1"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Reusable CTA Banner -->
<?php 
$ctaTitle = 'Schedule a Professional Commercial Cleaning Consultation';
$ctaSubtitle = 'Let our technical housekeeping managers create a customized sanitization contract for your facility.';
include VIEWS_PATH . '/partials/cta_banner.php'; 
?>
