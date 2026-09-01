/**
 * JMJ Enterprises Solutions - Rich Blog WYSIWYG Editor Script
 */

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('blog-editor-canvas');
    const hiddenInput = document.getElementById('blog-content-input');
    const form = document.getElementById('blog-editor-form');

    if (!canvas || !hiddenInput) return;

    // Sync from canvas to hidden input
    function syncContent() {
        hiddenInput.value = canvas.innerHTML;
    }

    canvas.addEventListener('input', syncContent);
    canvas.addEventListener('blur', syncContent);
    form?.addEventListener('submit', syncContent);

    // Toolbar Command Handlers
    document.querySelectorAll('.editor-toolbar button[data-cmd]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const cmd = btn.getAttribute('data-cmd');
            const val = btn.getAttribute('data-val') || null;

            if (cmd === 'createLink') {
                const url = prompt('Enter link URL (e.g. https://...):');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
            } else if (cmd === 'insertImage') {
                const imgUrl = prompt('Enter Image URL:');
                if (imgUrl) {
                    document.execCommand('insertImage', false, imgUrl);
                }
            } else if (cmd === 'formatBlock') {
                document.execCommand('formatBlock', false, val);
            } else if (cmd === 'insertTable') {
                const tableHtml = '<table class="w-full border-collapse border border-slate-300 my-4"><thead><tr><th class="border border-slate-300 p-2 bg-slate-100">Header 1</th><th class="border border-slate-300 p-2 bg-slate-100">Header 2</th></tr></thead><tbody><tr><td class="border border-slate-300 p-2">Item 1</td><td class="border border-slate-300 p-2">Item 2</td></tr></tbody></table><p></p>';
                document.execCommand('insertHTML', false, tableHtml);
            } else {
                document.execCommand(cmd, false, val);
            }
            syncContent();
        });
    });

    // Live Google Search Preview
    const seoTitleInput = document.getElementById('seo-title-input');
    const seoDescInput = document.getElementById('seo-desc-input');
    const previewTitle = document.getElementById('google-preview-title');
    const previewDesc = document.getElementById('google-preview-desc');

    if (seoTitleInput && previewTitle) {
        seoTitleInput.addEventListener('input', () => {
            previewTitle.textContent = seoTitleInput.value || 'Article Title - JMJ Enterprises Solutions';
        });
    }
    if (seoDescInput && previewDesc) {
        seoDescInput.addEventListener('input', () => {
            previewDesc.textContent = seoDescInput.value || 'Meta description preview will appear here summarizing the article...';
        });
    }
});
