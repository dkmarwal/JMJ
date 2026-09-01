<?php
/**
 * JMJ Enterprises Solutions - Custom 404 Error View
 */
?>
<section class="py-28 bg-[#F8FAFC] text-center">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="w-24 h-24 rounded-3xl bg-amber-50 text-[#F39C12] border border-amber-200 flex items-center justify-center text-4xl font-black mx-auto shadow-sm">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        
        <span class="text-xs font-extrabold uppercase tracking-widest text-[#254E70] bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">
            404 Error - Page Not Found
        </span>

        <h1 class="text-3xl sm:text-5xl font-black text-[#090F1C] tracking-tight">
            <?= e($errorHeading ?? 'Oops! This page could not be located.') ?>
        </h1>

        <p class="text-sm sm:text-base text-slate-500 max-w-lg mx-auto leading-relaxed">
            <?= e($errorMessage ?? 'The URL you requested may have been moved, renamed, or is temporarily unavailable. Please use the navigation below to find what you are looking for.') ?>
        </p>

        <div class="flex flex-wrap justify-center items-center gap-4 pt-4">
            <a href="<?= url() ?>" class="inline-flex items-center justify-center bg-[#090F1C] hover:bg-[#254E70] text-white font-black px-6 py-3.5 rounded-xl shadow-lg transition text-xs uppercase tracking-wider">
                <i class="fas fa-house mr-2 text-[#F39C12]"></i> Return Home
            </a>
            <a href="<?= url('security-services') ?>" class="inline-flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-extrabold px-6 py-3.5 rounded-xl transition text-xs uppercase tracking-wider">
                Security Services
            </a>
            <a href="<?= url('cleaning-services') ?>" class="inline-flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-extrabold px-6 py-3.5 rounded-xl transition text-xs uppercase tracking-wider">
                Cleaning Services
            </a>
            <a href="<?= url('contact') ?>" class="inline-flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-extrabold px-6 py-3.5 rounded-xl transition text-xs uppercase tracking-wider">
                Contact Us
            </a>
        </div>
    </div>
</section>
