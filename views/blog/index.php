<?php
/**
 * JMJ Enterprises Solutions - Blog Magazine Listing View
 */
include VIEWS_PATH . '/partials/breadcrumb.php';
?>

<!-- Hero Header Section -->
<section class="bg-[#090F1C] text-white py-16 lg:py-20 relative overflow-hidden border-b-4 border-[#F39C12]">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#254E70_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-white/10 text-[#F39C12] border border-white/20 tracking-wide uppercase">
            <i class="fas fa-newspaper mr-2"></i> Security & Commercial Facility Insights
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white">
            Knowledge Base & Industry Intelligence
        </h1>
        <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Expert analysis on physical security risk assessments, PSARA compliance, hospital pathogen prevention, and industrial cleaning techniques.
        </p>

        <!-- Search Bar -->
        <div class="max-w-xl mx-auto pt-4">
            <form action="<?= url('blog') ?>" method="GET" class="relative">
                <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="Search articles by topic, keyword, or standard..." class="w-full bg-slate-900/90 border border-slate-700 rounded-2xl px-5 py-4 pl-12 text-sm text-white focus:outline-none focus:border-[#F39C12] shadow-xl">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 bg-[#F39C12] text-[#090F1C] font-extrabold px-4 py-2 rounded-xl text-xs uppercase tracking-wider hover:bg-amber-500 transition">
                    Search
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Main Blog Section with Sidebar -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Category Filter Tabs -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-4 mb-12 border-b border-slate-200 scrollbar-none">
            <a href="<?= url('blog') ?>" class="px-5 py-2.5 rounded-xl text-xs font-extrabold whitespace-nowrap transition <?= empty($currentCat) ? 'bg-[#090F1C] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                All Articles (<?= (int)$total ?>)
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= url('blog?category=' . $cat['slug']) ?>" class="px-5 py-2.5 rounded-xl text-xs font-extrabold whitespace-nowrap transition <?= ($currentCat === $cat['slug']) ? 'bg-[#090F1C] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    <?= e($cat['name']) ?> (<?= (int)$cat['post_count'] ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <div class="grid lg:grid-cols-12 gap-12">
            
            <!-- Left 8 Columns: Blog Articles Grid -->
            <div class="lg:col-span-8 space-y-12">
                <?php if (empty($posts)): ?>
                    <div class="bg-slate-50 border border-slate-200 rounded-3xl p-12 text-center space-y-4">
                        <i class="fas fa-folder-open text-4xl text-slate-300"></i>
                        <h3 class="text-xl font-bold text-[#090F1C]">No articles matched your criteria</h3>
                        <p class="text-xs text-slate-500">Try adjusting your search query or selected category filter.</p>
                        <a href="<?= url('blog') ?>" class="inline-flex items-center text-xs font-bold text-[#F39C12] hover:underline">
                            View all articles &rarr;
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid sm:grid-cols-2 gap-8">
                        <?php foreach ($posts as $post): ?>
                            <article class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm flex flex-col justify-between group hover:-translate-y-1.5 transition-all duration-300">
                                <div>
                                    <div class="h-48 overflow-hidden relative">
                                        <img src="<?= upload_url($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        <span class="absolute top-4 left-4 bg-[#090F1C] text-[#F39C12] px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-slate-800">
                                            <?= e($post['category_name']) ?>
                                        </span>
                                    </div>
                                    <div class="p-6 space-y-3">
                                        <div class="flex items-center space-x-3 text-[11px] text-slate-400">
                                            <span><i class="fas fa-calendar-day text-[#F39C12] mr-1"></i> <?= format_date($post['publish_at']) ?></span>
                                            <span>•</span>
                                            <span><i class="fas fa-clock text-[#F39C12] mr-1"></i> <?= (int)$post['reading_time'] ?> min read</span>
                                        </div>
                                        <h3 class="text-base font-bold text-[#090F1C] leading-snug group-hover:text-[#254E70] transition">
                                            <a href="<?= url('blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a>
                                        </h3>
                                        <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed">
                                            <?= e($post['short_description']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="p-6 pt-0 border-t border-slate-200 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-400 font-semibold">By <?= e($post['author_name']) ?></span>
                                    <a href="<?= url('blog/' . $post['slug']) ?>" class="text-xs font-extrabold text-[#254E70] hover:text-[#090F1C] flex items-center">
                                        Read <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="flex justify-center items-center space-x-2 pt-8">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <a href="<?= url('blog?page=' . $p . ($currentCat ? '&category=' . $currentCat : '') . ($searchQuery ? '&q=' . urlencode($searchQuery) : '')) ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-bold transition <?= $p === $page ? 'bg-[#090F1C] text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                                    <?= $p ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Right 4 Columns: Sidebar Widgets -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- Category Archive Widget -->
                <div class="bg-slate-50 p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-[#090F1C] border-b border-slate-200 pb-2">
                        Browse by Category
                    </h4>
                    <div class="space-y-2 text-xs">
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?= url('blog?category=' . $cat['slug']) ?>" class="flex justify-between items-center py-2 px-3 rounded-xl hover:bg-white transition text-slate-700 font-semibold">
                                <span><?= e($cat['name']) ?></span>
                                <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full text-[10px]"><?= (int)$cat['post_count'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Popular Tags Cloud -->
                <?php if (!empty($tags)): ?>
                    <div class="bg-slate-50 p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-[#090F1C] border-b border-slate-200 pb-2">
                            Tags & Topics
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($tags as $t): ?>
                                <a href="<?= url('blog?tag=' . $t['slug']) ?>" class="text-[11px] font-bold bg-white hover:bg-[#090F1C] hover:text-white px-3 py-1.5 rounded-lg border border-slate-200 transition text-slate-600">
                                    #<?= e($t['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Consultation Callout -->
                <div class="bg-[#090F1C] p-6 sm:p-8 rounded-3xl text-white space-y-4 border-l-4 border-[#F39C12]">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#F39C12]">Enterprise Support</span>
                    <h4 class="text-lg font-black leading-snug">Need tailored security or cleaning deployment?</h4>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Book a free on-site survey with our operations directors across Delhi NCR or our 10 national hubs.
                    </p>
                    <button type="button" class="open-quote-modal w-full bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-extrabold py-3 rounded-xl text-xs uppercase tracking-wider transition">
                        Request Site Survey
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>
