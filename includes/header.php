<?php
$title = $title ?? SITE_NAME;
$description = $description ?? t('footer_intro');
$flashMessage = take_flash();
$navigation = [
    ['index.php', t('home')], ['about.php', t('about')], ['program.php', t('program')],
    ['criteria.php', t('criteria')], ['testimonials.php', t('testimonials')],
    ['faq.php', t('faq')], ['contact.php', t('contact')],
];
$currentFile = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!doctype html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($description) ?>">
    <meta name="theme-color" content="#101c4f">
    <title><?= e($title) ?> · <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/site.css">
</head>
<body>
<a class="skip-link" href="#main"><?= $lang === 'en' ? 'Skip to content' : 'Aller au contenu' ?></a>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="index.php" aria-label="<?= e(SITE_NAME) ?> — <?= e(t('home')) ?>">
            <img src="logo_fondation.png" alt="<?= e(SITE_NAME) ?>">
        </a>
        <button class="menu-toggle" type="button" aria-label="<?= e(t('menu')) ?>" aria-expanded="false" aria-controls="primary-nav">
            <span></span><span></span><span></span>
        </button>
        <nav class="primary-nav" id="primary-nav" aria-label="<?= $lang === 'en' ? 'Main navigation' : 'Navigation principale' ?>">
            <?php foreach ($navigation as [$href, $label]): ?>
                <a href="<?= e($href) ?>" class="<?= $currentFile === $href ? 'is-active' : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <a class="nav-track" href="track.php"><i class="bi bi-search" aria-hidden="true"></i> <?= e(t('track')) ?></a>
            <a class="button button-red nav-apply" href="apply.php"><?= e(t('apply')) ?> <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            <a class="language-switch" href="<?= e(current_page_url()) ?>" aria-label="<?= $lang === 'fr' ? 'Switch to English' : 'Passer en français' ?>">
                <?= $lang === 'fr' ? 'EN' : 'FR' ?>
            </a>
        </nav>
    </div>
</header>
<?php if (!db_configured() && !str_starts_with($currentFile, 'admin')): ?>
    <div class="system-notice"><i class="bi bi-info-circle" aria-hidden="true"></i><span><?= e(t('need_db')) ?></span></div>
<?php endif; ?>
<?php if ($flashMessage): ?>
    <div class="flash-message <?= e($flashMessage['type']) ?>" role="status"><?= e($flashMessage['message']) ?></div>
<?php endif; ?>
<main id="main">
