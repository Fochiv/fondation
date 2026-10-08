<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'Eligibility criteria' : 'Critères d’éligibilité';
require __DIR__ . '/includes/header.php';
$eligible = $en
    ? ['Entrepreneurs and project founders', 'Small and medium-sized enterprises', 'Start-ups and early-stage ventures', 'Craftspeople, farmers and local producers', 'Associations or other organisations where the programme permits']
    : ['Entrepreneurs et porteurs de projets', 'Petites et moyennes entreprises', 'Startups et jeunes entreprises', 'Artisans, agriculteurs et producteurs locaux', 'Associations ou autres structures si le programme les autorise'];
$prepare = $en
    ? ['A clear description of the problem and proposed solution', 'The project location, sector and stage of development', 'Realistic estimates of total cost and funding requested', 'An explanation of how the requested support would be used', 'Accurate contact details so the team can follow up']
    : ['Une description claire du problème et de la solution proposée', 'La localisation, le secteur et le stade d’avancement du projet', 'Une estimation réaliste du coût total et du montant demandé', 'Une explication précise de l’utilisation du financement demandé', 'Des coordonnées exactes pour permettre le suivi du dossier'];
?>
<section class="page-hero page-hero-navy"><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'BEFORE YOU APPLY' : 'AVANT DE CANDIDATER' ?></span><h1><?= $en ? 'Know the criteria. Prepare your application.' : 'Connaître les critères. Préparer sa candidature.' ?></h1><p><?= $en ? 'Use this guide to understand who may apply and what information to prepare.' : 'Ce guide vous aide à comprendre qui peut candidater et quelles informations préparer.' ?></p></div></section>
<section class="section"><div class="container eligibility-grid">
    <div><span class="eyebrow"><?= $en ? 'WHO MAY APPLY?' : 'QUI PEUT CANDIDATER ?' ?></span><h2><?= $en ? 'Applicants with a clear, considered project' : 'Les porteurs d’un projet construit et réfléchi' ?></h2><p><?= $en ? 'Eligibility depends on the current programme cycle. The applicant and project must meet the requirements published by the programme at the time of submission.' : 'L’éligibilité dépend du cycle de programme en cours. Le candidat et son projet doivent répondre aux exigences publiées au moment de la soumission.' ?></p>
        <ul class="check-list"><?php foreach ($eligible as $item): ?><li><i class="bi bi-check-circle-fill"></i><?= e($item) ?></li><?php endforeach; ?></ul></div>
    <div class="criteria-side"><div class="value-icon"><i class="bi bi-journal-check"></i></div><h3><?= $en ? 'Get ready before you start' : 'Préparez votre dossier en amont' ?></h3><p><?= $en ? 'Having these details ready can make the application clearer.' : 'Préparer ces éléments peut rendre votre candidature plus claire.' ?></p><ul class="check-list"><?php foreach ($prepare as $item): ?><li><i class="bi bi-check2"></i><?= e($item) ?></li><?php endforeach; ?></ul><a class="button button-red" href="apply.php"><?= e(t('send_project')) ?> <i class="bi bi-arrow-up-right"></i></a></div>
</div></section>
<section class="section section-soft"><div class="container">
    <div class="warning-box"><div class="warning-icon"><i class="bi bi-exclamation-circle"></i></div><div><h2><?= $en ? 'An important note' : 'Une précision importante' ?></h2><p>« <?= $en ? 'Meeting the criteria does not automatically guarantee funding. Every application is reviewed.' : 'Le respect des critères ne garantit pas automatiquement l’obtention d’un financement. Chaque candidature fait l’objet d’une étude.' ?> »</p></div></div>
    <div class="simple-cta"><div><span class="eyebrow"><?= $en ? 'UNDERSTOOD THE PROCESS?' : 'PRÊT À DÉCOUVRIR LE PARCOURS ?' ?></span><h2><?= $en ? 'Prepare your project application.' : 'Préparez la soumission de votre projet.' ?></h2></div><a class="button button-navy" href="apply.php"><?= e(t('send_project')) ?> <i class="bi bi-arrow-right"></i></a></div>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
