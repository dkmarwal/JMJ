/**
 * JMJ Enterprises Solutions - Main Frontend Script
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Drawer & Overlays
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const menuIcon = document.getElementById('menu-icon');

    function toggleMobileMenu() {
        if (!mobileDrawer) return;
        const isOpen = !mobileDrawer.classList.contains('translate-x-full');
        if (isOpen) {
            mobileDrawer.classList.add('translate-x-full');
            drawerOverlay?.classList.add('opacity-0', 'pointer-events-none');
            if (menuIcon) {
                menuIcon.classList.remove('fa-xmark');
                menuIcon.classList.add('fa-bars');
            }
            document.body.classList.remove('overflow-hidden');
        } else {
            mobileDrawer.classList.remove('translate-x-full');
            drawerOverlay?.classList.remove('opacity-0', 'pointer-events-none');
            if (menuIcon) {
                menuIcon.classList.remove('fa-bars');
                menuIcon.classList.add('fa-xmark');
            }
            document.body.classList.add('overflow-hidden');
        }
    }

    mobileBtn?.addEventListener('click', toggleMobileMenu);
    drawerOverlay?.addEventListener('click', toggleMobileMenu);

    // Mobile Accordions
    document.querySelectorAll('.mobile-accordion-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-target');
            const targetEl = document.getElementById(targetId);
            const icon = btn.querySelector('.accordion-icon');
            if (targetEl) {
                targetEl.classList.toggle('hidden');
                icon?.classList.toggle('rotate-180');
            }
        });
    });

    // 2. Quick Quote Modal Handler
    const quoteModal = document.getElementById('quote-modal');
    const quoteModalBackdrop = document.getElementById('quote-modal-backdrop');
    const quoteCloseBtns = document.querySelectorAll('.close-quote-modal');
    const openQuoteBtns = document.querySelectorAll('.open-quote-modal');

    function openQuoteModal(serviceName = '') {
        if (!quoteModal) return;
        quoteModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        const serviceSelect = document.getElementById('modal-service-select');
        if (serviceSelect && serviceName) {
            for (let i = 0; i < serviceSelect.options.length; i++) {
                if (serviceSelect.options[i].text.toLowerCase().includes(serviceName.toLowerCase())) {
                    serviceSelect.selectedIndex = i;
                    break;
                }
            }
        }
    }

    function closeQuoteModal() {
        if (!quoteModal) return;
        quoteModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    openQuoteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const service = btn.getAttribute('data-service') || '';
            openQuoteModal(service);
        });
    });

    quoteCloseBtns.forEach(btn => btn.addEventListener('click', closeQuoteModal));
    quoteModalBackdrop?.addEventListener('click', closeQuoteModal);

    // 3. Search Modal Handler
    const searchModal = document.getElementById('search-modal');
    const searchCloseBtns = document.querySelectorAll('.close-search-modal');
    const openSearchBtns = document.querySelectorAll('.open-search-modal');

    function openSearchModal() {
        if (!searchModal) return;
        searchModal.classList.remove('hidden');
        document.getElementById('global-search-input')?.focus();
        document.body.classList.add('overflow-hidden');
    }

    function closeSearchModal() {
        if (!searchModal) return;
        searchModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    openSearchBtns.forEach(btn => btn.addEventListener('click', (e) => {
        e.preventDefault();
        openSearchModal();
    }));
    searchCloseBtns.forEach(btn => btn.addEventListener('click', closeSearchModal));

    // 4. FAQ Accordions
    document.querySelectorAll('.faq-toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('.faq-icon');
            if (content) {
                content.classList.toggle('hidden');
                icon?.classList.toggle('rotate-180');
            }
        });
    });

    // 5. Gallery Filter
    const galleryFilterBtns = document.querySelectorAll('.gallery-filter-btn');
    const galleryCards = document.querySelectorAll('.gallery-card');

    galleryFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.getAttribute('data-filter');
            galleryFilterBtns.forEach(b => {
                b.classList.remove('bg-brand-dark', 'text-white', 'border-brand-gold');
                b.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
            });
            btn.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
            btn.classList.add('bg-brand-dark', 'text-white', 'border-brand-gold');

            galleryCards.forEach(card => {
                const category = card.getAttribute('data-category');
                if (filter === 'all' || category === filter) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });

    // 6. Generic Toast Notification
    window.showToast = function(type, message) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 z-50 flex items-center p-4 rounded-xl shadow-2xl text-sm font-semibold transition-all duration-300 transform translate-y-4 opacity-0 ${
            type === 'success' ? 'bg-slate-900 text-white border-l-4 border-amber-500' : 'bg-red-900 text-white border-l-4 border-red-500'
        }`;
        toast.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-circle-check text-amber-400' : 'fa-triangle-exclamation text-red-400'} text-lg mr-3"></i>
            <div>${message}</div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        }, 50);

        setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4500);
    };

    // 7. AJAX Form Submissions
    document.querySelectorAll('form.ajax-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const origText = submitBtn ? submitBtn.innerHTML : 'Submit';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...`;
            }

            const formData = new FormData(form);

            try {
                const response = await fetch(form.getAttribute('action') || window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast('success', data.message || 'Thank you! Your request has been submitted successfully.');
                    form.reset();
                    closeQuoteModal();
                } else {
                    showToast('error', data.error || 'Please review your entries and try again.');
                }
            } catch (err) {
                showToast('error', 'Network error. Please verify your connection or call our dispatch desk directly.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origText;
                }
            }
        });
    });
});
