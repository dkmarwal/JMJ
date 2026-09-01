<?php
/**
 * JMJ Enterprises Solutions - Main Master Layout
 */
$metaTitle = $pageTitle ?? ($seo['meta_title'] ?? APP_NAME . ' | Security & Cleaning Solutions');
$metaDescription = $pageDescription ?? ($seo['meta_description'] ?? 'JMJ Enterprises Solutions Ltd. provides premier corporate security guarding and commercial cleaning solutions across India.');
$metaKeywords = $seo['meta_keywords'] ?? 'security services, cleaning services, manned guarding, delhi security';
$canonicalUrl = $seo['canonical_url'] ?? url(trim($_SERVER['REQUEST_URI'] ?? '', '/'));
$ogImage = !empty($seo['og_image']) ? upload_url($seo['og_image']) : asset('img/logo.jpg');
$robots = $seo['robots'] ?? 'index, follow';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Primary SEO Meta Tags -->
    <title><?= e($metaTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="keywords" content="<?= e($metaKeywords) ?>">
    <meta name="robots" content="<?= e($robots) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">

    <!-- OpenGraph Social Meta Tags -->
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="<?= isset($isArticle) && $isArticle ? 'article' : 'website' ?>">
    <meta property="og:title" content="<?= e($metaTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= e(setting('company_name', 'JMJ Enterprises Solutions')) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($metaTitle) ?>">
    <meta name="twitter:description" content="<?= e($metaDescription) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <!-- CSRF Token Meta for AJAX -->
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Pro Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#090F1C',     /* Midnight Obsidian */
                            navy: '#0F1E36',     /* Deep Corporate Navy */
                            steel: '#254E70',    /* Slate Steel */
                            gold: '#F39C12',     /* Security Amber / Gold */
                            canvas: '#F8FAFC'    /* Clean Slate Canvas */
                        }
                    },
                    boxShadow: {
                        'premium': '0 20px 50px -12px rgba(9, 15, 28, 0.08)',
                        'glow': '0 0 25px rgba(243, 156, 18, 0.2)'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS Stylesheet -->
    <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Header & Navigation -->
    <?php include VIEWS_PATH . '/layouts/header.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Footer & Schema -->
    <?php include VIEWS_PATH . '/layouts/footer.php'; ?>

    <!-- Global Quick Quote Modal -->
    <?php include VIEWS_PATH . '/partials/quote_modal.php'; ?>

    <!-- Flash Toast Alerts -->
    <?php include VIEWS_PATH . '/partials/toast.php'; ?>

    <!-- Main JS Application Script -->
    <script src="<?= asset('assets/js/main.js') ?>"></script>
</body>
</html>
