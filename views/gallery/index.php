<?php
/**
 * JMJ Enterprises Solutions - Gallery View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Hero Header Section -->
<section class="bg-[#090F1C] text-white py-16 lg:py-24 relative overflow-hidden border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
            <i class="fas fa-camera mr-2"></i> Field Deployments & Operations
        </span>
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
            Operations & Fleet Gallery
        </h1>
        <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Real photographic records of JMJ guard squads, healthcare sterile cleans, industrial floor waxing, and high-altitude facade teams.
        </p>
    </div>
</section>

<!-- Gallery Filter & Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Category Filter Buttons -->
        <div class="flex flex-wrap justify-center items-center gap-3 mb-12">
            <button type="button" class="gallery-filter-btn px-6 py-2.5 rounded-xl text-xs font-extrabold transition bg-[#090F1C] text-white border border-[#F39C12]" data-filter="all">
                All Projects & Rosters
            </button>
            <?php foreach ($categories as $cat): ?>
                <button type="button" class="gallery-filter-btn px-6 py-2.5 rounded-xl text-xs font-extrabold transition bg-white text-slate-700 border border-slate-200 hover:bg-slate-50" data-filter="<?= e($cat['slug']) ?>">
                    <?= e($cat['name']) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Masonry Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($items as $item): ?>
                <div class="gallery-card rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group" data-category="<?= e($item['category_slug']) ?>">
                    <div class="h-64 overflow-hidden relative">
                        <img src="<?= upload_url($item['image_path']) ?>" alt="<?= e($item['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#090F1C] via-transparent to-transparent opacity-75"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#F39C12] bg-black/50 px-2 py-0.5 rounded border border-slate-700 inline-block">
                                <?= e($item['category_name']) ?>
                            </span>
                            <h4 class="text-sm font-bold text-white leading-snug"><?= e($item['title']) ?></h4>
                            <?php if (!empty($item['caption'])): ?>
                                <p class="text-[11px] text-slate-300 line-clamp-1"><?= e($item['caption']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- Reusable CTA Banner -->
<?php 
$ctaTitle = 'Require Professional Facility Management?';
$ctaSubtitle = 'Request a formal operational proposal and site audit from JMJ Enterprises.';
include VIEWS_PATH . '/partials/cta_banner.php'; 
?>
