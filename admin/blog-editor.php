<?php
/**
 * JMJ Enterprises Solutions - Admin Blog Editor
 * Hawks Infotech Blog Desk Professional WYSIWYG & SEO Workspace
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$db = \Core\Database::getInstance();

$postId = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$post = null;
$revisions = [];
$assignedTagIds = [];

if ($postId) {
    $post = $db->fetch("SELECT * FROM blog_posts WHERE id = :id AND is_archived = 0", ['id' => $postId]);
    if (!$post) {
        \Core\Session::setFlash('error', 'Post not found or has been archived.');
        redirect('admin/blogs.php');
    }
    $pageTitle = 'Edit Article: ' . $post['title'];
    
    // Fetch Tags
    $tagRows = $db->fetchAll("SELECT tag_id FROM blog_post_tags WHERE post_id = :pid", ['pid' => $postId]);
    $assignedTagIds = array_column($tagRows, 'tag_id');

    // Fetch Revisions
    $revisions = $db->fetchAll(
        "SELECT r.*, u.name as user_name 
         FROM blog_revisions r 
         JOIN users u ON r.user_id = u.id 
         WHERE r.post_id = :pid 
         ORDER BY r.id DESC LIMIT 10",
        ['pid' => $postId]
    );
} else {
    $pageTitle = 'Write New Blog Article';
}

// Handle Form Submission (Save / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Security token expired. Please re-submit.');
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = slugify($_POST['slug'] ?: $title);
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $content = $_POST['content'] ?? '';
        $shortDesc = trim($_POST['short_description'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $publishAt = !empty($_POST['publish_at']) ? $_POST['publish_at'] : date('Y-m-d H:i:s');
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $metaKeywords = trim($_POST['meta_keywords'] ?? '');
        $canonicalUrl = trim($_POST['canonical_url'] ?? '');
        $readingTime = calculate_reading_time($content);
        $tagIds = $_POST['tag_ids'] ?? [];

        // Handle Image Upload
        $featuredImage = $post['featured_image'] ?? 'uploads/blog/default.jpg';
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadResult = \Services\MediaService::handleUpload($_FILES['featured_image'], 'blog');
            if ($uploadResult['success']) {
                $featuredImage = $uploadResult['path'];
            }
        }

        $postData = [
            'category_id'       => $categoryId,
            'title'             => $title,
            'slug'              => $slug,
            'short_description' => $shortDesc,
            'content'           => $content,
            'featured_image'    => $featuredImage,
            'status'            => $status,
            'meta_title'        => $metaTitle,
            'meta_description'  => $metaDesc,
            'meta_keywords'     => $metaKeywords,
            'canonical_url'     => $canonicalUrl,
            'reading_time'      => $readingTime,
            'publish_at'        => $publishAt
        ];

        if ($postId) {
            // Save current revision snapshot
            $db->insert('blog_revisions', [
                'post_id'         => $postId,
                'user_id'         => $currentUser['id'],
                'title'           => $post['title'],
                'content'         => $post['content'],
                'revision_reason' => 'Auto-snapshot before update'
            ]);

            // Update Post
            $db->update('blog_posts', $postData, 'id = :id', ['id' => $postId]);
            \Services\AuditService::log("Updated blog post: {$title}", 'blog', $postId, 'UPDATE');
            $targetId = $postId;
            \Core\Session::setFlash('success', 'Article updated and revision saved successfully!');
        } else {
            $postData['author_id'] = $currentUser['id'];
            $targetId = $db->insert('blog_posts', $postData);
            \Services\AuditService::log("Created new blog post: {$title}", 'blog', (int)$targetId, 'CREATE');
            \Core\Session::setFlash('success', 'Article created successfully!');
        }

        // Sync Tags
        $db->query("DELETE FROM blog_post_tags WHERE post_id = :pid", ['pid' => $targetId]);
        if (!empty($tagIds)) {
            foreach ($tagIds as $tid) {
                $db->insert('blog_post_tags', ['post_id' => $targetId, 'tag_id' => (int)$tid]);
            }
        }

        redirect('admin/blog-editor.php?id=' . $targetId);
    }
}

// Handle Revision Rollback
if (isset($_GET['rollback_rev']) && $postId) {
    $revId = (int)$_GET['rollback_rev'];
    $rev = $db->fetch("SELECT * FROM blog_revisions WHERE id = :id AND post_id = :pid", ['id' => $revId, 'pid' => $postId]);
    if ($rev) {
        $db->update('blog_posts', ['title' => $rev['title'], 'content' => $rev['content']], 'id = :id', ['id' => $postId]);
        \Services\AuditService::log("Rolled back blog post #{$postId} to revision #{$revId}", 'blog', $postId, 'ROLLBACK');
        \Core\Session::setFlash('success', 'Article content restored to revision snapshot.');
        redirect('admin/blog-editor.php?id=' . $postId);
    }
}

$categories = $db->fetchAll("SELECT * FROM blog_categories WHERE is_archived = 0 ORDER BY name ASC");
$allTags = $db->fetchAll("SELECT * FROM blog_tags ORDER BY name ASC");

include __DIR__ . '/partials/header.php';
?>

<form id="blog-editor-form" action="<?= url('admin/blog-editor.php' . ($postId ? '?id=' . $postId : '')) ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <textarea id="blog-content-input" name="content" class="hidden"><?= e($post['content'] ?? '') ?></textarea>

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm mb-8">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Content Studio</span>
            <h2 class="text-xl font-black text-slate-900"><?= $postId ? 'Edit Article' : 'Draft New Article' ?></h2>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= url('admin/blogs.php') ?>" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition">
                Discard
            </a>
            <?php if ($postId && $post['status'] === 'published'): ?>
                <a href="<?= url('blog/' . $post['slug']) ?>" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-bold transition flex items-center">
                    <i class="fas fa-eye mr-1.5"></i> View Live
                </a>
            <?php endif; ?>
            <button type="submit" class="bg-[#F39C12] hover:bg-amber-500 text-[#090F1C] font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
                <i class="fas fa-floppy-disk mr-1.5"></i> <?= $postId ? 'Save Changes' : 'Publish Article' ?>
            </button>
        </div>
    </div>

    <!-- Dual Column Workspace -->
    <div class="grid lg:grid-cols-12 gap-8">
        
        <!-- Left 8 Columns: Article Canvas & Body -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Article Title Input -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Article Headline *</label>
                    <input type="text" id="slug-source-title" name="title" required value="<?= e($post['title'] ?? '') ?>" placeholder="e.g. PSARA Compliance Essentials for Delhi NCR Corporate Facilities" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-base font-bold text-slate-900 focus:outline-none focus:border-[#F39C12]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Clean URL Slug</label>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-500">
                        <span class="font-mono"><?= url('blog/') ?>/</span>
                        <input type="text" id="slug-target-input" name="slug" value="<?= e($post['slug'] ?? '') ?>" placeholder="psara-compliance-delhi" class="w-full bg-transparent border-0 font-mono text-slate-800 focus:outline-none ml-1">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Short Abstract / Excerpt</label>
                    <textarea name="short_description" rows="2" placeholder="Brief 2-sentence summary for search engines and listing cards..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#F39C12]"><?= e($post['short_description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- WYSIWYG Content Canvas -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden space-y-0">
                <!-- Formatting Toolbar -->
                <div class="editor-toolbar bg-slate-50 p-3 border-b border-slate-200 flex flex-wrap items-center gap-1.5">
                    <button type="button" data-cmd="bold" title="Bold"><i class="fas fa-bold"></i></button>
                    <button type="button" data-cmd="italic" title="Italic"><i class="fas fa-italic"></i></button>
                    <button type="button" data-cmd="underline" title="Underline"><i class="fas fa-underline"></i></button>
                    <span class="w-px h-5 bg-slate-300 mx-1"></span>
                    <button type="button" data-cmd="formatBlock" data-val="H2" title="Heading 2">H2</button>
                    <button type="button" data-cmd="formatBlock" data-val="H3" title="Heading 3">H3</button>
                    <button type="button" data-cmd="formatBlock" data-val="P" title="Paragraph">P</button>
                    <span class="w-px h-5 bg-slate-300 mx-1"></span>
                    <button type="button" data-cmd="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                    <button type="button" data-cmd="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                    <button type="button" data-cmd="formatBlock" data-val="BLOCKQUOTE" title="Quote"><i class="fas fa-quote-left"></i></button>
                    <span class="w-px h-5 bg-slate-300 mx-1"></span>
                    <button type="button" data-cmd="createLink" title="Insert Link"><i class="fas fa-link"></i></button>
                    <button type="button" data-cmd="insertImage" title="Insert Image from URL"><i class="fas fa-image"></i></button>
                    <button type="button" data-cmd="insertTable" title="Insert Table"><i class="fas fa-table"></i></button>
                    <span class="w-px h-5 bg-slate-300 mx-1"></span>
                    <button type="button" data-cmd="removeFormat" title="Clear Formatting"><i class="fas fa-eraser"></i></button>
                </div>

                <!-- Editable Div -->
                <div id="blog-editor-canvas" contenteditable="true" class="editor-canvas p-6 sm:p-8 bg-white focus:outline-none text-slate-800">
                    <?= $post['content'] ?? '<p>Write your detailed intelligence briefing or case study here...</p>' ?>
                </div>
            </div>

            <!-- SEO & Google Search Snippet Preview -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#F39C12]">Search Engine Optimization</span>
                    <h3 class="text-base font-black text-slate-900">SEO & Metadata Controls</h3>
                </div>

                <!-- Google SERP Snippet Preview -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="text-[10px] text-slate-400 font-mono block">https://jmjenterprisessolutions.com/blog/<?= e($post['slug'] ?? 'url-slug') ?></span>
                    <h4 id="google-preview-title" class="text-sm font-bold text-blue-800 hover:underline cursor-pointer">
                        <?= e(!empty($post['meta_title']) ? $post['meta_title'] : ($post['title'] ?? 'Article Title - JMJ Enterprises Solutions')) ?>
                    </h4>
                    <p id="google-preview-desc" class="text-xs text-slate-600 line-clamp-2">
                        <?= e(!empty($post['meta_description']) ? $post['meta_description'] : ($post['short_description'] ?? 'Meta description preview will appear here summarizing the article...')) ?>
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Custom Meta Title</label>
                        <input type="text" id="seo-title-input" name="meta_title" value="<?= e($post['meta_title'] ?? '') ?>" placeholder="SEO Title (60 chars max)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Meta Description</label>
                        <textarea id="seo-desc-input" name="meta_description" rows="2" placeholder="Search snippet description (160 chars max)..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]"><?= e($post['meta_description'] ?? '') ?></textarea>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Meta Keywords</label>
                            <input type="text" name="meta_keywords" value="<?= e($post['meta_keywords'] ?? '') ?>" placeholder="security, psara, delhi ncr" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Canonical URL (Optional)</label>
                            <input type="url" name="canonical_url" value="<?= e($post['canonical_url'] ?? '') ?>" placeholder="https://..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#F39C12]">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right 4 Columns: Publishing Controls & Revisions -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Publish Settings Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    Publishing Parameters
                </h4>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Post Status</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none">
                        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published (Live)</option>
                        <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="scheduled" <?= ($post['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                        <option value="pending_review" <?= ($post['status'] ?? '') === 'pending_review' ? 'selected' : '' ?>>Pending Review</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Publish Timestamp</label>
                    <input type="datetime-local" name="publish_at" value="<?= date('Y-m-d\TH:i', strtotime($post['publish_at'] ?? 'now')) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Category *</label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Featured Image Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    Featured Photography
                </h4>

                <div class="h-44 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 relative">
                    <img src="<?= upload_url(!empty($post['featured_image']) ? $post['featured_image'] : 'uploads/blog/default.jpg') ?>" alt="Featured Image Preview" class="w-full h-full object-cover">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Upload New Picture</label>
                    <input type="file" name="featured_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                </div>
            </div>

            <!-- Tag Selection Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    Topic Tags
                </h4>
                <div class="max-h-40 overflow-y-auto space-y-1.5 pr-2">
                    <?php foreach ($allTags as $tag): ?>
                        <label class="flex items-center space-x-2 text-xs text-slate-700 hover:text-slate-900 cursor-pointer">
                            <input type="checkbox" name="tag_ids[]" value="<?= $tag['id'] ?>" <?= in_array($tag['id'], $assignedTagIds) ? 'checked' : '' ?> class="rounded text-[#F39C12] focus:ring-0">
                            <span><?= e($tag['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Revisions & History Vault -->
            <?php if (!empty($revisions)): ?>
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 flex items-center justify-between">
                        <span>Version History</span>
                        <i class="fas fa-clock-rotate-left text-slate-400"></i>
                    </h4>
                    <div class="space-y-2 text-[11px]">
                        <?php foreach ($revisions as $rev): ?>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-slate-800 block"><?= format_date($rev['created_at']) ?></span>
                                    <span class="text-slate-400 text-[10px]">By <?= e($rev['user_name']) ?></span>
                                </div>
                                <a href="<?= url('admin/blog-editor.php?id=' . $postId . '&rollback_rev=' . $rev['id']) ?>" class="confirm-action text-[10px] font-bold text-amber-600 hover:underline" data-confirm="Restore this version snapshot?">
                                    Restore
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</form>

<script src="<?= asset('assets/js/editor.js') ?>"></script>
<?php include __DIR__ . '/partials/footer.php'; ?>
