<?php
/**
 * JMJ Enterprises Solutions - Admin Service Editor
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

$currentUser = \Core\Auth::requireLogin();
$db = \Core\Database::getInstance();

$serviceId = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$service = null;
$features = [];
$faqs = [];

if ($serviceId) {
    $service = $db->fetch("SELECT * FROM services WHERE id = :id AND is_archived = 0", ['id' => $serviceId]);
    if (!$service) {
        \Core\Session::setFlash('error', 'Service not found.');
        redirect('admin/services.php');
    }
    $pageTitle = 'Edit Service: ' . $service['name'];
    $features = $db->fetchAll("SELECT * FROM service_features WHERE service_id = :sid ORDER BY display_order ASC", ['sid' => $serviceId]);
    $faqs = $db->fetchAll("SELECT * FROM service_faqs WHERE service_id = :sid ORDER BY display_order ASC", ['sid' => $serviceId]);
} else {
    $pageTitle = 'Create New Service Offering';
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Core\Csrf::validate()) {
        \Core\Session::setFlash('error', 'Security token expired.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $slug = slugify($_POST['slug'] ?: $name);
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $icon = trim($_POST['icon'] ?? 'fas fa-shield-alt');
        $shortSummary = trim($_POST['short_summary'] ?? '');
        $overview = $_POST['overview'] ?? '';
        $methodology = $_POST['methodology'] ?? '';
        $targetSectors = trim($_POST['target_sectors'] ?? '');
        $standards = trim($_POST['standards_compliance'] ?? '');
        $status = $_POST['status'] ?? 'published';
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $metaKeywords = trim($_POST['meta_keywords'] ?? '');

        // Image Handling
        $heroImage = $service['hero_image'] ?? 'uploads/services/default.jpg';
        if (!empty($_FILES['hero_image']['name'])) {
            $uploadResult = \Services\MediaService::handleUpload($_FILES['hero_image'], 'services');
            if ($uploadResult['success']) {
                $heroImage = $uploadResult['path'];
            }
        }

        $serviceData = [
            'category_id'          => $categoryId,
            'name'                 => $name,
            'slug'                 => $slug,
            'icon'                 => $icon,
            'short_summary'        => $shortSummary,
            'overview'             => $overview,
            'methodology'          => $methodology,
            'target_sectors'       => $targetSectors,
            'standards_compliance' => $standards,
            'hero_image'           => $heroImage,
            'status'               => $status,
            'meta_title'           => $metaTitle,
            'meta_description'     => $metaDesc,
            'meta_keywords'        => $metaKeywords
        ];

        if ($serviceId) {
            $db->update('services', $serviceData, 'id = :id', ['id' => $serviceId]);
            $targetId = $serviceId;
            \Services\AuditService::log("Updated service: {$name}", 'service', $serviceId, 'UPDATE');
            \Core\Session::setFlash('success', 'Service updated successfully.');
        } else {
            $targetId = $db->insert('services', $serviceData);
            \Services\AuditService::log("Created new service: {$name}", 'service', (int)$targetId, 'CREATE');
            \Core\Session::setFlash('success', 'Service created successfully.');
        }

        // Sync Features
        $db->query("DELETE FROM service_features WHERE service_id = :sid", ['sid' => $targetId]);
        if (!empty($_POST['features']) && is_array($_POST['features'])) {
            $fIdx = 0;
            foreach ($_POST['features'] as $f) {
                if (!empty($f['title'])) {
                    $db->insert('service_features', [
                        'service_id'  => $targetId,
                        'title'       => trim($f['title']),
                        'description' => trim($f['description'] ?? ''),
                        'icon'        => trim($f['icon'] ?? 'fas fa-check-circle'),
                        'display_order' => $fIdx++
                    ]);
                }
            }
        }

        // Sync FAQs
        $db->query("DELETE FROM service_faqs WHERE service_id = :sid", ['sid' => $targetId]);
        if (!empty($_POST['faqs']) && is_array($_POST['faqs'])) {
            $qIdx = 0;
            foreach ($_POST['faqs'] as $q) {
                if (!empty($q['question']) && !empty($q['answer'])) {
                    $db->insert('service_faqs', [
                        'service_id' => $targetId,
                        'question'   => trim($q['question']),
                        'answer'     => trim($q['answer']),
                        'display_order' => $qIdx++
                    ]);
                }
            }
        }

        redirect('admin/service-editor.php?id=' . $targetId);
    }
}

$categories = $db->fetchAll("SELECT * FROM service_categories ORDER BY name ASC");

include __DIR__ . '/partials/header.php';
?>

<form action="<?= url('admin/service-editor.php' . ($serviceId ? '?id=' . $serviceId : '')) ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
    <?= csrf_field() ?>

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#254E70]">Service Definition</span>
            <h2 class="text-xl font-black text-slate-900"><?= $serviceId ? 'Edit Service Details' : 'Add New Service Capability' ?></h2>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= url('admin/services.php') ?>" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition">
                Cancel
            </a>
            <button type="submit" class="bg-[#254E70] hover:bg-blue-900 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-md flex items-center">
                <i class="fas fa-floppy-disk mr-1.5"></i> <?= $serviceId ? 'Save Service' : 'Create Service' ?>
            </button>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        
        <!-- Left 8 Columns: Main Information -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Basic Data Box -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Service Title *</label>
                        <input type="text" id="slug-source-title" name="name" required value="<?= e($service['name'] ?? '') ?>" placeholder="e.g. Bank Security Guards" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 focus:outline-none focus:border-[#254E70]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">URL Slug</label>
                        <input type="text" id="slug-target-input" name="slug" value="<?= e($service['slug'] ?? '') ?>" placeholder="bank-security-guards" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-mono text-slate-800 focus:outline-none focus:border-[#254E70]">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Category Division *</label>
                        <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:outline-none">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($service['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Icon Class</label>
                        <input type="text" name="icon" value="<?= e($service['icon'] ?? 'fas fa-shield-alt') ?>" placeholder="e.g. fas fa-building-columns" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#254E70]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Short Summary (Listing Cards & Abstract) *</label>
                    <textarea name="short_summary" rows="2" required placeholder="Brief 2-sentence summary..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#254E70]"><?= e($service['short_summary'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Detailed Copywriting Section -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Detailed Overview (HTML Supported) *</label>
                    <textarea name="overview" rows="8" required placeholder="Comprehensive narrative explaining the service parameters, guard specifications, or chemical formulations..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs leading-relaxed font-mono text-slate-800 focus:outline-none focus:border-[#254E70]"><?= e($service['overview'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Operating Blueprint & Methodology (HTML Supported)</label>
                    <textarea name="methodology" rows="5" placeholder="Operational sequence and standard procedures..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs leading-relaxed font-mono text-slate-800 focus:outline-none focus:border-[#254E70]"><?= e($service['methodology'] ?? '') ?></textarea>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Target Sectors & Environments</label>
                        <textarea name="target_sectors" rows="3" placeholder="e.g. Commercial Banks, Vaults, Currency Transit Hubs" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#254E70]"><?= e($service['target_sectors'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Standards & Statutory Compliance</label>
                        <textarea name="standards_compliance" rows="3" placeholder="e.g. PSARA Licensed, Arms Act 1959, ISO 9001:2015" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-[#254E70]"><?= e($service['standards_compliance'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Features & Benefits Repeater -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#F39C12]">Service Highlights</span>
                        <h3 class="text-base font-black text-slate-900">Key Features & Capabilities</h3>
                    </div>
                    <button type="button" id="add-feature-row" class="bg-slate-900 text-white font-bold px-3 py-1.5 rounded-lg text-xs hover:bg-slate-800 transition">
                        <i class="fas fa-plus mr-1"></i> Add Feature
                    </button>
                </div>

                <div id="features-rows-container" class="space-y-3">
                    <?php if (empty($features)): ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 relative">
                            <div class="grid sm:grid-cols-3 gap-2">
                                <input type="text" name="features[0][title]" placeholder="Feature Title *" class="sm:col-span-2 w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                                <input type="text" name="features[0][icon]" placeholder="Icon class" value="fas fa-check-circle" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                            </div>
                            <textarea name="features[0][description]" rows="2" placeholder="Feature description..." class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]"></textarea>
                        </div>
                    <?php else: ?>
                        <?php foreach ($features as $fIdx => $feat): ?>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 relative">
                                <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs remove-row-btn"><i class="fas fa-trash"></i></button>
                                <div class="grid sm:grid-cols-3 gap-2">
                                    <input type="text" name="features[<?= $fIdx ?>][title]" value="<?= e($feat['title']) ?>" placeholder="Feature Title *" class="sm:col-span-2 w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                                    <input type="text" name="features[<?= $fIdx ?>][icon]" value="<?= e($feat['icon']) ?>" placeholder="Icon class" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                                </div>
                                <textarea name="features[<?= $fIdx ?>][description]" rows="2" placeholder="Feature description..." class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]"><?= e($feat['description']) ?></textarea>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Service FAQs Repeater -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#254E70]">FAQ Schema</span>
                        <h3 class="text-base font-black text-slate-900">Service Specific FAQs</h3>
                    </div>
                    <button type="button" id="add-faq-row" class="bg-slate-900 text-white font-bold px-3 py-1.5 rounded-lg text-xs hover:bg-slate-800 transition">
                        <i class="fas fa-plus mr-1"></i> Add FAQ
                    </button>
                </div>

                <div id="faqs-rows-container" class="space-y-3">
                    <?php if (empty($faqs)): ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 relative">
                            <input type="text" name="faqs[0][question]" placeholder="Question *" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                            <textarea name="faqs[0][answer]" rows="2" placeholder="Answer *" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]"></textarea>
                        </div>
                    <?php else: ?>
                        <?php foreach ($faqs as $qIdx => $faq): ?>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 relative">
                                <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs remove-row-btn"><i class="fas fa-trash"></i></button>
                                <input type="text" name="faqs[<?= $qIdx ?>][question]" value="<?= e($faq['question']) ?>" placeholder="Question *" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                                <textarea name="faqs[<?= $qIdx ?>][answer]" rows="2" placeholder="Answer *" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]"><?= e($faq['answer']) ?></textarea>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Right 4 Columns: Publishing & SEO -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Hero Image Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    Hero Photography
                </h4>

                <div class="h-44 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img src="<?= upload_url(!empty($service['hero_image']) ? $service['hero_image'] : 'uploads/services/default.jpg') ?>" alt="Preview" class="w-full h-full object-cover">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Upload Hero Image</label>
                    <input type="file" name="hero_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white">
                </div>
            </div>

            <!-- SEO Box -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">
                    Service SEO Meta
                </h4>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="<?= e($service['meta_title'] ?? '') ?>" placeholder="Service SEO Title" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3" placeholder="Service description for Google..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]"><?= e($service['meta_description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="<?= e($service['meta_keywords'] ?? '') ?>" placeholder="security guards, hospital sanitization" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#254E70]">
                </div>
            </div>

        </div>
    </div>
</form>

<?php include __DIR__ . '/partials/footer.php'; ?>
