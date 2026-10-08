<?php
$adminTitle = $adminTitle ?? 'Administration';
$adminPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
$sidebarLinks = [
    ['index.php', 'Vue d’ensemble', 'grid-1x2'],
    ['applications.php', 'Candidatures', 'folder2-open'],
    ['testimonials.php', 'Témoignages', 'chat-square-quote'],
    ['messages.php', 'Messages', 'envelope'],
    ['settings.php', 'Paramètres', 'sliders'],
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($adminTitle) ?> · Administration</title>
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/site.css">
</head>
<body class="admin-body">
<aside class="admin-sidebar" id="admin-sidebar">
    <a class="admin-brand" href="index.php"><img src="../logo_fondation.png" alt="<?= e(SITE_NAME) ?>"><span>ESPACE ADMIN</span></a>
    <nav aria-label="Navigation d’administration">
        <small>MENU PRINCIPAL</small>
        <?php foreach ($sidebarLinks as [$href, $label, $icon]): ?><a class="<?= $adminPage === $href ? 'is-active' : '' ?>" href="<?= e($href) ?>"><i class="bi bi-<?= e($icon) ?>"></i><?= e($label) ?></a><?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-bottom"><a href="../index.php"><i class="bi bi-box-arrow-up-right"></i> Voir le site</a><form method="post" action="logout.php"><?= csrf_field() ?><button type="submit"><i class="bi bi-box-arrow-left"></i> Se déconnecter</button></form></div>
</aside>
<div class="admin-content">
    <header class="admin-topbar"><button type="button" class="admin-menu-toggle" aria-label="Ouvrir la navigation" aria-expanded="false" aria-controls="admin-sidebar"><i class="bi bi-list"></i></button><div><span class="admin-topbar-overline">ALIKO DANGOTE FOUNDATION</span><strong><?= e($adminTitle) ?></strong></div><span class="admin-user"><i class="bi bi-person-circle"></i><?= e($_SESSION['admin_email'] ?? 'Administrateur') ?></span></header>
    <main class="admin-main">
        <?php if ($flashMessage = take_flash()): ?><div class="flash-message <?= e($flashMessage['type']) ?>" role="status"><?= e($flashMessage['message']) ?></div><?php endif; ?>
