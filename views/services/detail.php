<?php
/**
 * JMJ Enterprises Solutions - Single Service Detail View
 * SEO-Optimized Template with FAQs, Process, and Schema Markup
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
$isSecurity = ($service['category_slug'] === 'security-services');
$accentColor = $isSecurity ? '#F39C12' : '#254E70';
?>

<!-- Hero Header Section -->
<section class="bg-[#090F1C] text-white py-16 lg:py-24 relative overflow-hidden border-b-4 <?= $isSecurity ? 'border-[#F39C12]' : 'border-[#254E70]' ?>">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 grid lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-8 space-y-6">
            <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 <?= $isSecurity ? 'text-[#F39C12]' : 'text-blue-300' ?> border border-white/20 tracking-wide uppercase">
                <i class="<?= e($service['icon'] ?: ($isSecurity ? 'fas fa-shield-halved' : 'fas fa-sparkles')) ?> mr-2"></i>
                <?= e($service['category_name']) ?>
            </div>
            
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                <?= e($service['name']) ?>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                <?= e($service['short_summary']) ?>
            </p>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <button type="button" class="open-quote-modal inline-flex items-center justify-center bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black px-8 py-4 rounded-xl shadow-xl transition-all duration-300 text-xs uppercase tracking-widest min-w-[200px]" data-service="<?= e($service['name']) ?>">
                    <i class="fas fa-file-contract mr-2"></i> Request Free Quote
                </button>
                <a href="tel:<?= e(setting('phone_toll_free', '18008890832')) ?>" class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/20 text-white font-extrabold px-8 py-4 rounded-xl transition-all duration-300 text-xs uppercase tracking-widest">
                    <i class="fas fa-phone-volume mr-2 text-[#F39C12]"></i> 24/7 Dispatch Desk
                </a>
            </div>
        </div>

        <div class="lg:col-span-4 hidden lg:block">
            <div class="bg-[#0F1E36] p-6 rounded-3xl border border-slate-800 space-y-4 shadow-2xl">
                <h4 class="text-xs font-bold uppercase tracking-wider text-[#F39C12]">Compliance & Standards</h4>
                <div class="space-y-3 text-xs text-slate-300">
                    <div class="flex items-center"><i class="fas fa-certificate text-[#F39C12] mr-2.5"></i> <?= e($service['standards_compliance'] ?: 'PSARA Certified & ISO 9001:2015 Compliant') ?></div>
                    <div class="flex items-center"><i class="fas fa-user-shield text-[#F39C12] mr-2.5"></i> Background-Verified Personnel</div>
                    <div class="flex items-center"><i class="fas fa-clock text-[#F39C12] mr-2.5"></i> 24/7 Dispatch Helpline & Field Marshals</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Service Content Body -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12">
            
            <!-- Left 8 Columns: Service Narrative -->
            <div class="lg:col-span-8 space-y-12">
                
                <!-- Hero Featured Photography -->
                <?php if (!empty($service['hero_image'])): ?>
                    <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-200 h-[380px] relative">
                        <img src="<?= upload_url($service['hero_image']) ?>" alt="<?= e($service['name']) ?>" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>

                <!-- Overview Section -->
                <div class="space-y-4">
                    <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full">
                        Comprehensive Overview
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-[#090F1C] tracking-tight">
                        About Our <?= e($service['name']) ?>
                    </h2>
                    <div class="h-1 w-16 bg-[#F39C12] rounded-full"></div>
                    <div class="article-content prose max-w-none text-slate-700 leading-relaxed text-sm sm:text-base">
                        <?= $service['overview'] ?>
                    </div>
                </div>

                <!-- Features & Key Capabilities -->
                <?php if (!empty($service['features'])): ?>
                    <div class="space-y-6 pt-4">
                        <h3 class="text-2xl font-black text-[#090F1C]">Key Operational Features & Benefits</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <?php foreach ($service['features'] as $feat): ?>
                                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-sm space-y-2 hover:border-[#F39C12] transition duration-200">
                                    <div class="w-10 h-10 rounded-xl bg-white text-[#F39C12] border border-slate-200 flex items-center justify-center text-base shadow-sm">
                                        <i class="<?= e($feat['icon'] ?: 'fas fa-check-circle') ?>"></i>
                                    </div>
                                    <h4 class="font-extrabold text-base text-[#090F1C]"><?= e($feat['title']) ?></h4>
                                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed"><?= e($feat['description']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Methodology / How It Works -->
                <?php if (!empty($service['methodology'])): ?>
                    <div class="space-y-4 pt-4">
                        <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full">
                            Operating Blueprint
                        </span>
                        <h3 class="text-2xl font-black text-[#090F1C]">How We Execute Our Process</h3>
                        <div class="article-content bg-slate-50 p-6 sm:p-8 rounded-2xl border border-slate-200 text-sm sm:text-base leading-relaxed text-slate-700">
                            <?= $service['methodology'] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Target Sectors & Industries -->
                <?php if (!empty($service['target_sectors'])): ?>
                    <div class="p-6 sm:p-8 bg-[#090F1C] rounded-2xl text-white space-y-3 border-l-4 border-[#F39C12]">
                        <h4 class="font-extrabold text-base uppercase tracking-wider text-[#F39C12] flex items-center">
                            <i class="fas fa-building mr-2"></i> Primary Industries & Environments Served
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                            <?= e($service['target_sectors']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Service FAQs Accordion -->
                <?php if (!empty($service['faqs'])): ?>
                    <div class="space-y-6 pt-4">
                        <div>
                            <span class="text-xs font-extrabold tracking-widest text-[#254E70] uppercase bg-slate-100 px-3.5 py-1.5 rounded-full">Frequently Asked Questions</span>
                            <h3 class="text-2xl font-black text-[#090F1C] mt-2">Questions Regarding <?= e($service['name']) ?></h3>
                        </div>

                        <div class="space-y-3">
                            <?php foreach ($service['faqs'] as $qIdx => $faq): ?>
                                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50">
                                    <button type="button" class="faq-toggle-btn w-full px-6 py-4 text-left font-bold text-sm sm:text-base text-[#090F1C] flex justify-between items-center hover:bg-slate-100 transition">
                                        <span><?= e($faq['question']) ?></span>
                                        <i class="fas fa-chevron-down faq-icon text-xs text-slate-400 transition-transform duration-200"></i>
                                    </button>
                                    <div class="hidden px-6 py-4 text-xs sm:text-sm text-slate-600 bg-white border-t border-slate-200 leading-relaxed">
                                        <?= e($faq['answer']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- FAQPage Schema Markup -->
                    <?= \Services\SeoService::renderFaqSchema($service['faqs']) ?>
                <?php endif; ?>
            </div>

            <!-- Right 4 Columns: Sidebar Widgets -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- Quick Consultation Request Box -->
                <div class="bg-slate-50 p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-premium space-y-5 sticky top-28">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#F39C12]">Instant Consultation</span>
                        <h4 class="text-xl font-black text-[#090F1C]">Request Site Survey</h4>
                    </div>

                    <form action="<?= url('api/lead') ?>" method="POST" class="ajax-form space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="quote">
                        <input type="hidden" name="service_required" value="<?= e($service['name']) ?>">

                        <div>
                            <input type="text" name="name" required placeholder="Your Name *" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <input type="tel" name="phone" required placeholder="Phone Number *" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <input type="email" name="email" required placeholder="Work Email *" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <input type="text" name="location" required placeholder="Facility Location (City) *" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <textarea name="message" rows="3" placeholder="Brief scope requirements..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-[#F39C12]"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-[#090F1C] hover:bg-[#254E70] text-white font-extrabold py-3.5 px-4 rounded-xl text-xs uppercase tracking-wider transition">
                            Submit Consultation Request
                        </button>
                    </form>

                    <!-- Direct Dispatch Helpline Widget -->
                    <div class="pt-4 border-t border-slate-200 text-center space-y-1 text-xs">
                        <span class="text-slate-400 block font-semibold">Prefer to speak directly?</span>
                        <a href="tel:<?= e(setting('phone_toll_free', '18008890832')) ?>" class="text-sm font-black text-[#090F1C] hover:text-[#F39C12] transition block">
                            <i class="fas fa-phone mr-1 text-[#F39C12]"></i> <?= e(setting('phone_toll_free', '18008890832')) ?>
                        </a>
                    </div>
                </div>

                <!-- Related Services Widget -->
                <?php if (!empty($service['related'])): ?>
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-[#090F1C] border-b border-slate-100 pb-2">
                            Related <?= e($service['category_name']) ?>
                        </h4>
                        <div class="space-y-3">
                            <?php foreach ($service['related'] as $rel): ?>
                                <a href="<?= url($service['category_slug'] . '/' . $rel['slug']) ?>" class="p-3 rounded-xl hover:bg-slate-50 border border-slate-100 flex items-center space-x-3 transition group">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-[#254E70] flex items-center justify-center text-xs shrink-0 group-hover:bg-[#090F1C] group-hover:text-[#F39C12] transition">
                                        <i class="<?= e($rel['icon'] ?: 'fas fa-shield-alt') ?>"></i>
                                    </div>
                                    <div class="truncate">
                                        <span class="text-xs font-bold text-[#090F1C] group-hover:text-[#F39C12] transition block truncate"><?= e($rel['name']) ?></span>
                                        <span class="text-[10px] text-slate-400 block truncate"><?= e($rel['short_summary']) ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<!-- Reusable CTA Banner -->
<?php 
$ctaTitle = 'Deploy ' . $service['name'] . ' Across Your Facilities';
$ctaSubtitle = 'Contact our central operations team for a rapid deployment schedule or contract evaluation.';
include VIEWS_PATH . '/partials/cta_banner.php'; 
?>
