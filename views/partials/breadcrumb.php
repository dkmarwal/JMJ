<?php
/**
 * JMJ Enterprises Solutions - Breadcrumb Partial
 * Renders HTML breadcrumbs + Schema markup
 */
if (!empty($breadcrumbs)): ?>
    <nav aria-label="Breadcrumb" class="py-3.5 px-4 sm:px-6 lg:px-8 bg-slate-100/70 border-b border-slate-200/80 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto flex items-center space-x-2 flex-wrap">
            <a href="<?= url() ?>" class="hover:text-[#090F1C] flex items-center transition">
                <i class="fas fa-house mr-1.5 text-slate-400"></i> Home
            </a>
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <span class="text-slate-300">/</span>
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <span class="text-[#090F1C] font-bold"><?= e($crumb['name']) ?></span>
                <?php else: ?>
                    <a href="<?= e($crumb['url']) ?>" class="hover:text-[#090F1C] transition"><?= e($crumb['name']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </nav>
    <?= \Services\SeoService::renderBreadcrumbsSchema($breadcrumbs) ?>
<?php endif; ?>
