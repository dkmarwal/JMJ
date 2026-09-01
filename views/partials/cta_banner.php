<?php
/**
 * JMJ Enterprises Solutions - Reusable Conversion CTA Banner Partial
 */
$ctaTitle = $ctaTitle ?? 'Ready to Secure and Upgrade Your Facilities?';
$ctaSubtitle = $ctaSubtitle ?? 'Partner with India’s trusted PSARA-compliant manned guarding and hospital-grade sanitization network across 10 strategic state hubs.';
?>
<section class="py-20 bg-gradient-to-br from-[#090F1C] via-[#0F1E36] to-[#090F1C] text-white relative overflow-hidden border-t-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-extrabold bg-white/10 text-[#F39C12] border border-white/20 tracking-wider uppercase">
            <i class="fas fa-shield-halved mr-2 animate-pulse"></i> 24/7 Rapid Deployment Available
        </span>
        <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white max-w-4xl mx-auto leading-tight">
            <?= e($ctaTitle) ?>
        </h2>
        <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
            <?= e($ctaSubtitle) ?>
        </p>
        <div class="flex flex-wrap justify-center items-center gap-4 pt-4">
            <button type="button" class="open-quote-modal inline-flex items-center justify-center bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-extrabold px-8 py-4 rounded-xl shadow-xl transition-all duration-300 hover:scale-[1.02] text-xs uppercase tracking-widest min-w-[220px]">
                <i class="fas fa-file-contract mr-2"></i> Request Free Site Survey
            </button>
            <a href="tel:<?= e(setting('phone_toll_free', '18008890832')) ?>" class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/20 text-white font-extrabold px-8 py-4 rounded-xl transition-all duration-300 text-xs uppercase tracking-widest min-w-[200px]">
                <i class="fas fa-phone-volume mr-2 text-[#F39C12]"></i> Call Toll-Free: <?= e(setting('phone_toll_free', '18008890832')) ?>
            </a>
        </div>
    </div>
</section>
