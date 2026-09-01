/**
 * JMJ Enterprises Solutions - Admin Dashboard Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Admin Sidebar Toggle for Mobile & Compact
    const sidebarToggle = document.getElementById('admin-sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');

    function toggleAdminSidebar() {
        if (!sidebar) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay?.classList.toggle('hidden');
    }

    sidebarToggle?.addEventListener('click', toggleAdminSidebar);
    overlay?.addEventListener('click', toggleAdminSidebar);

    // 2. Copy Media URL to Clipboard
    document.querySelectorAll('.copy-media-url').forEach(btn => {
        btn.addEventListener('click', () => {
            const url = btn.getAttribute('data-url');
            if (url) {
                navigator.clipboard.writeText(url).then(() => {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check text-green-500 mr-1"></i> Copied!';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                    }, 2000);
                });
            }
        });
    });

    // 3. Confirm Delete Prompts
    document.querySelectorAll('.confirm-action').forEach(el => {
        el.addEventListener('click', (e) => {
            const msg = el.getAttribute('data-confirm') || 'Are you sure you want to perform this action?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // 4. Live Slug Generator
    const titleInput = document.getElementById('slug-source-title');
    const slugInput = document.getElementById('slug-target-input');

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', () => {
            if (slugInput.dataset.manual !== 'true') {
                slugInput.value = titleInput.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .trim()
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
        slugInput.addEventListener('input', () => {
            slugInput.dataset.manual = 'true';
        });
    }

    // 5. Dynamic Feature & FAQ Row Adders in Service Editor
    const addFeatureBtn = document.getElementById('add-feature-row');
    const featuresContainer = document.getElementById('features-rows-container');

    if (addFeatureBtn && featuresContainer) {
        addFeatureBtn.addEventListener('click', () => {
            const idx = featuresContainer.children.length;
            const row = document.createElement('div');
            row.className = 'p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 relative';
            row.innerHTML = `
                <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs remove-row-btn"><i class="fas fa-trash"></i></button>
                <div class="grid sm:grid-cols-3 gap-2">
                    <input type="text" name="features[${idx}][title]" placeholder="Feature Title *" class="sm:col-span-2 w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-500">
                    <input type="text" name="features[${idx}][icon]" placeholder="Icon class (e.g. fas fa-check)" value="fas fa-check-circle" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-500">
                </div>
                <textarea name="features[${idx}][description]" rows="2" placeholder="Brief feature description..." class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-500"></textarea>
            `;
            featuresContainer.appendChild(row);
            row.querySelector('.remove-row-btn').addEventListener('click', () => row.remove());
        });

        featuresContainer.querySelectorAll('.remove-row-btn').forEach(btn => {
            btn.addEventListener('click', (e) => e.target.closest('.p-4').remove());
        });
    }

    const addFaqBtn = document.getElementById('add-faq-row');
    const faqsContainer = document.getElementById('faqs-rows-container');

    if (addFaqBtn && faqsContainer) {
        addFaqBtn.addEventListener('click', () => {
            const idx = faqsContainer.children.length;
            const row = document.createElement('div');
            row.className = 'p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 relative';
            row.innerHTML = `
                <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs remove-row-btn"><i class="fas fa-trash"></i></button>
                <input type="text" name="faqs[${idx}][question]" placeholder="Question *" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-500">
                <textarea name="faqs[${idx}][answer]" rows="2" placeholder="Answer *" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-500"></textarea>
            `;
            faqsContainer.appendChild(row);
            row.querySelector('.remove-row-btn').addEventListener('click', () => row.remove());
        });

        faqsContainer.querySelectorAll('.remove-row-btn').forEach(btn => {
            btn.addEventListener('click', (e) => e.target.closest('.p-4').remove());
        });
    }
});
