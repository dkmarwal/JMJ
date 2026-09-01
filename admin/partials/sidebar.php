<?php
/**
 * JMJ Enterprises Solutions - Admin Sidebar Navigation
 * Hawks Infotech Blog Desk Navigation Blueprint
 */
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!-- Mobile Backdrop Overlay -->
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 hidden lg:hidden"></div>

<!-- Sidebar Column -->
<aside id="admin-sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-[#090F1C] text-slate-400 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full lg:translate-x-0 border-r border-slate-800 shrink-0">
    
    <!-- Top Brand Area -->
    <div class="h-16 flex items-center px-6 border-b border-slate-800/80 bg-[#060a14]">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg overflow-hidden bg-white p-0.5 border border-slate-700">
                <img src="<?= asset('img/logo.jpg') ?>" alt="JMJ" class="w-full h-full object-cover">
            </div>
            <div>
                <span class="text-sm font-black tracking-tight text-white block leading-none">JMJ ENTERPRISES</span>
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-[#F39C12] mt-0.5 block">Admin Control</span>
            </div>
        </div>
    </div>

    <!-- Navigation List with Sections -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6 text-xs font-semibold">
        
        <!-- Section: Overview -->
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Overview</span>
            <a href="<?= url('admin/dashboard.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?= $currentScript === 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie w-4 text-center"></i>
                <span>Executive Dashboard</span>
            </a>
        </div>

        <!-- Section: Blog Desk (Hawks Infotech Inspired) -->
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Blog Desk</span>
            <div class="space-y-0.5">
                <a href="<?= url('admin/blogs.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'blogs.php' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper w-4 text-center"></i>
                    <span>All Blog Posts</span>
                </a>
                <a href="<?= url('admin/blog-editor.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'blog-editor.php' && empty($_GET['id']) ? 'active' : '' ?>">
                    <i class="fas fa-pen-nib w-4 text-center"></i>
                    <span>Write New Article</span>
                </a>
                <a href="<?= url('admin/categories.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'categories.php' ? 'active' : '' ?>">
                    <i class="fas fa-folder-tree w-4 text-center"></i>
                    <span>Categories</span>
                </a>
                <a href="<?= url('admin/tags.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'tags.php' ? 'active' : '' ?>">
                    <i class="fas fa-tags w-4 text-center"></i>
                    <span>Tags</span>
                </a>
            </div>
        </div>

        <!-- Section: Services CMS -->
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Services CMS</span>
            <div class="space-y-0.5">
                <a href="<?= url('admin/services.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'services.php' ? 'active' : '' ?>">
                    <i class="fas fa-shield-halved w-4 text-center"></i>
                    <span>Service Catalog (26)</span>
                </a>
                <a href="<?= url('admin/service-editor.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'service-editor.php' && empty($_GET['id']) ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle w-4 text-center"></i>
                    <span>Add New Service</span>
                </a>
            </div>
        </div>

        <!-- Section: CRM & Enquiries -->
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Leads & CRM</span>
            <a href="<?= url('admin/enquiries.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'enquiries.php' ? 'active' : '' ?>">
                <i class="fas fa-envelope-open-text w-4 text-center"></i>
                <span>Enquiries & Quotes</span>
            </a>
        </div>

        <!-- Section: Assets & Media -->
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Assets & Showcase</span>
            <div class="space-y-0.5">
                <a href="<?= url('admin/media.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'media.php' ? 'active' : '' ?>">
                    <i class="fas fa-photo-film w-4 text-center"></i>
                    <span>Media Library</span>
                </a>
                <a href="<?= url('admin/gallery.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'gallery.php' ? 'active' : '' ?>">
                    <i class="fas fa-images w-4 text-center"></i>
                    <span>Gallery Portfolio</span>
                </a>
            </div>
        </div>

        <!-- Section: Content Modules -->
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Engagement</span>
            <div class="space-y-0.5">
                <a href="<?= url('admin/faqs.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'faqs.php' ? 'active' : '' ?>">
                    <i class="fas fa-circle-question w-4 text-center"></i>
                    <span>FAQs Engine</span>
                </a>
                <a href="<?= url('admin/testimonials.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'testimonials.php' ? 'active' : '' ?>">
                    <i class="fas fa-star w-4 text-center"></i>
                    <span>Testimonials</span>
                </a>
            </div>
        </div>

        <!-- Section: System & Security -->
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Administration</span>
            <div class="space-y-0.5">
                <a href="<?= url('admin/users.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'users.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-shield w-4 text-center"></i>
                    <span>Staff & RBAC</span>
                </a>
                <a href="<?= url('admin/seo.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'seo.php' ? 'active' : '' ?>">
                    <i class="fas fa-magnifying-glass-chart w-4 text-center"></i>
                    <span>SEO & Metadata</span>
                </a>
                <a href="<?= url('admin/settings.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'settings.php' ? 'active' : '' ?>">
                    <i class="fas fa-sliders w-4 text-center"></i>
                    <span>Global Settings</span>
                </a>
                <a href="<?= url('admin/audit-logs.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'audit-logs.php' ? 'active' : '' ?>">
                    <i class="fas fa-clock-rotate-left w-4 text-center"></i>
                    <span>Security Audit Logs</span>
                </a>
                <a href="<?= url('admin/archive.php') ?>" class="admin-nav-link flex items-center space-x-3 px-3 py-2 rounded-xl transition <?= $currentScript === 'archive.php' ? 'active' : '' ?>">
                    <i class="fas fa-vault w-4 text-center text-amber-500"></i>
                    <span>Archive Recovery Vault</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Bottom Footer Session Strip -->
    <div class="p-3 border-t border-slate-800 bg-[#060a14]">
        <a href="<?= url('admin/logout.php') ?>" class="flex items-center justify-center space-x-2 w-full py-2 px-3 rounded-lg bg-red-950/40 text-red-400 hover:bg-red-900/60 hover:text-white transition font-bold text-xs">
            <i class="fas fa-right-from-bracket"></i>
            <span>Log Out</span>
        </a>
    </div>
</aside>
