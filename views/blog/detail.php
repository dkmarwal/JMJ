<?php
/**
 * JMJ Enterprises Solutions - Single Blog Article View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Article Header Section -->
<section class="bg-[#090F1C] text-white py-14 lg:py-20 relative overflow-hidden border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-5">
        <a href="<?= url('blog?category=' . $post['category_slug']) ?>" class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase hover:bg-white/20 transition">
            <i class="fas fa-tag mr-1.5"></i> <?= e($post['category_name']) ?>
        </a>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
            <?= e($post['title']) ?>
        </h1>

        <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-slate-300 pt-2 font-medium">
            <span class="flex items-center"><i class="fas fa-user-tie text-[#F39C12] mr-1.5"></i> By <?= e($post['author_name']) ?></span>
            <span>•</span>
            <span class="flex items-center"><i class="fas fa-calendar text-[#F39C12] mr-1.5"></i> <?= format_date($post['publish_at']) ?></span>
            <span>•</span>
            <span class="flex items-center"><i class="fas fa-clock text-[#F39C12] mr-1.5"></i> <?= (int)$post['reading_time'] ?> min read</span>
            <span>•</span>
            <span class="flex items-center"><i class="fas fa-eye text-[#F39C12] mr-1.5"></i> <?= (int)$post['views'] ?> views</span>
        </div>
    </div>
</section>

<!-- Article Main Body -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Featured Image -->
        <?php if (!empty($post['featured_image'])): ?>
            <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-200 mb-12 h-[420px]">
                <img src="<?= upload_url($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="w-full h-full object-cover">
            </div>
        <?php endif; ?>

        <!-- Short Abstract Box -->
        <?php if (!empty($post['short_description'])): ?>
            <div class="bg-slate-50 p-6 rounded-2xl border-l-4 border-[#F39C12] text-slate-700 text-sm sm:text-base font-semibold italic leading-relaxed mb-10">
                <?= e($post['short_description']) ?>
            </div>
        <?php endif; ?>

        <!-- Article Rich Content -->
        <div class="article-content leading-relaxed text-slate-800">
            <?= $post['content'] ?>
        </div>

        <!-- Tags List -->
        <?php if (!empty($post['tags'])): ?>
            <div class="pt-10 border-t border-slate-200 mt-12 flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-400 mr-2 uppercase tracking-wider">Tags:</span>
                <?php foreach ($post['tags'] as $t): ?>
                    <a href="<?= url('blog?tag=' . $t['slug']) ?>" class="text-xs font-semibold bg-slate-100 hover:bg-[#090F1C] hover:text-white px-3 py-1.5 rounded-lg text-slate-700 transition">
                        #<?= e($t['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Social Share Bar -->
        <div class="py-8 my-8 border-t border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <span class="text-xs font-extrabold uppercase tracking-wider text-[#090F1C]">Share this intelligence report:</span>
            <div class="flex items-center space-x-3">
                <?php
                $articleUrl = urlencode(url('blog/' . $post['slug']));
                $articleTitle = urlencode($post['title']);
                ?>
                <a href="https://api.whatsapp.com/send?text=<?= $articleTitle ?>%20<?= $articleUrl ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-sm hover:opacity-90 transition" aria-label="Share on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $articleUrl ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-blue-700 text-white flex items-center justify-center text-sm hover:opacity-90 transition" aria-label="Share on LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?= $articleTitle ?>&url=<?= $articleUrl ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm hover:opacity-90 transition" aria-label="Share on X">
                    <i class="fab fa-x-twitter"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $articleUrl ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm hover:opacity-90 transition" aria-label="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </div>
        </div>

        <!-- Author Bio Card -->
        <div class="bg-slate-50 p-6 sm:p-8 rounded-3xl border border-slate-200 flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-2xl text-[#254E70] shrink-0 font-black shadow-sm overflow-hidden">
                <?php if (!empty($post['author_avatar'])): ?>
                    <img src="<?= upload_url($post['author_avatar']) ?>" alt="<?= e($post['author_name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="fas fa-user-tie"></i>
                <?php endif; ?>
            </div>
            <div class="space-y-1 text-center sm:text-left">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#F39C12]">Author Profile</span>
                <h4 class="text-base font-extrabold text-[#090F1C]"><?= e($post['author_name']) ?></h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    <?= e($post['author_bio'] ?: 'Editorial contributor at JMJ Enterprises Solutions Ltd. specializing in enterprise security regulations, PSARA standards, and commercial facility engineering.') ?>
                </p>
            </div>
        </div>

        <!-- Previous & Next Navigation -->
        <div class="grid sm:grid-cols-2 gap-4 mt-8 pt-8 border-t border-slate-200">
            <?php if (!empty($post['prev'])): ?>
                <a href="<?= url('blog/' . $post['prev']['slug']) ?>" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-[#F39C12] transition block group">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">&larr; Previous Article</span>
                    <span class="text-xs font-bold text-[#090F1C] group-hover:text-[#F39C12] transition line-clamp-1 mt-1"><?= e($post['prev']['title']) ?></span>
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if (!empty($post['next'])): ?>
                <a href="<?= url('blog/' . $post['next']['slug']) ?>" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 hover:border-[#F39C12] transition block text-right group">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Next Article &rarr;</span>
                    <span class="text-xs font-bold text-[#090F1C] group-hover:text-[#F39C12] transition line-clamp-1 mt-1"><?= e($post['next']['title']) ?></span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Related Articles -->
        <?php if (!empty($post['related'])): ?>
            <div class="mt-16 pt-12 border-t border-slate-200 space-y-6">
                <h3 class="text-2xl font-black text-[#090F1C]">Related Insights</h3>
                <div class="grid sm:grid-cols-3 gap-6">
                    <?php foreach ($post['related'] as $rel): ?>
                        <article class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 group hover:-translate-y-1 transition duration-200">
                            <div class="h-32 overflow-hidden">
                                <img src="<?= upload_url($rel['featured_image']) ?>" alt="<?= e($rel['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                            <div class="p-4 space-y-1.5">
                                <span class="text-[10px] font-bold text-[#F39C12] uppercase"><?= e($rel['category_name']) ?></span>
                                <h4 class="text-xs font-bold text-[#090F1C] leading-snug line-clamp-2">
                                    <a href="<?= url('blog/' . $rel['slug']) ?>" class="hover:text-[#254E70]"><?= e($rel['title']) ?></a>
                                </h4>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- JSON-LD Article Schema Markup -->
<?= \Services\SeoService::renderArticleSchema($post) ?>

<!-- Reusable CTA Banner -->
<?php 
$ctaTitle = 'Need Expert Security or Cleaning Consultation?';
$ctaSubtitle = 'Speak with our regional directors to deploy vetted guards or sanitization teams.';
include VIEWS_PATH . '/partials/cta_banner.php'; 
?>
