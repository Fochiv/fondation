<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'The programme' : 'Le programme';
require __DIR__ . '/includes/header.php';
$features = $en ? [
    ['bullseye', 'Purpose', 'Identify projects with a clear purpose and a credible path to creating lasting value.'],
    ['person-check', 'Who can apply', 'Entrepreneurs, small businesses, start-ups, craftspeople and project founders.'],
    ['layers', 'Project stages', 'Applications may concern a new idea, a project in preparation or an existing activity ready to grow.'],
    ['clipboard2-check', 'How it works', 'Submit a complete application, keep your reference number and follow the review process.'],
] : [
    ['bullseye', 'Objectif', 'Identifier des projets avec un objectif clair et une trajectoire crédible de création de valeur durable.'],
    ['person-check', 'Qui peut candidater', 'Entrepreneurs, petites entreprises, startups, artisans et porteurs de projets.'],
    ['layers', 'Stades du projet', 'Les candidatures peuvent concerner une idée nouvelle, un projet en préparation ou une activité existante à développer.'],
    ['clipboard2-check', 'Fonctionnement', 'Soumettez un dossier complet, conservez votre numéro et suivez le processus d’étude.'],
];
$domainsFr = ['Agriculture', 'Élevage', 'Commerce', 'Technologie', 'Industrie', 'Transformation', 'Artisanat', 'Éducation', 'Santé', 'Transport', 'Énergie', 'Services', 'Restauration', 'Mode', 'Autres'];
$domainsEn = ['Agriculture', 'Livestock', 'Commerce', 'Technology', 'Industry', 'Processing', 'Crafts', 'Education', 'Health', 'Transport', 'Energy', 'Services', 'Hospitality', 'Fashion', 'Other'];
?>
<section class="page-hero page-hero-photo"><div class="page-hero-overlay"></div><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'PROJECT SUPPORT' : 'ACCOMPAGNEMENT DE PROJETS' ?></span><h1><?= $en ? 'A structured path for ideas ready to grow.' : 'Un parcours structuré pour les idées prêtes à grandir.' ?></h1><p><?= $en ? 'Explore the programme, the types of projects it considers and how to submit your application.' : 'Découvrez le programme, les projets étudiés et les étapes pour soumettre votre candidature.' ?></p></div></section>
<section class="section"><div class="container">
    <div class="section-heading section-heading-center"><span class="eyebrow"><?= $en ? 'THE PROGRAMME AT A GLANCE' : 'LE PROGRAMME EN BREF' ?></span><h2><?= $en ? 'Purposeful support, considered fairly' : 'Un accompagnement réfléchi et équitable' ?></h2><p><?= $en ? 'The programme reviews applications based on their completeness, relevance and the current published criteria.' : 'Les candidatures sont étudiées selon leur complétude, leur pertinence et les critères publiés en vigueur.' ?></p></div>
    <div class="value-grid">
        <?php foreach ($features as [$icon, $heading, $copy]): ?><article class="value-card"><div class="value-icon"><i class="bi bi-<?= e($icon) ?>"></i></div><h3><?= e($heading) ?></h3><p><?= e($copy) ?></p></article><?php endforeach; ?>
    </div>
</div></section>
<section class="section section-soft" id="domains"><div class="container">
    <div class="section-heading"><div><span class="eyebrow"><?= $en ? 'AREAS OF INTEREST' : 'DOMAINES D’INTERVENTION' ?></span><h2><?= $en ? 'A broad range of local enterprise' : 'Un large éventail d’initiatives locales' ?></h2></div><p class="heading-description"><?= $en ? 'The areas below are indicative. Confirm current priorities in the published eligibility criteria before applying.' : 'Les domaines ci-dessous sont indicatifs. Vérifiez les priorités en vigueur dans les critères publiés avant de candidater.' ?></p></div>
    <div class="domain-pills"><?php foreach (($en ? $domainsEn : $domainsFr) as $domain): ?><span><i class="bi bi-arrow-up-right"></i><?= e($domain) ?></span><?php endforeach; ?></div>
    <div class="notice-box"><i class="bi bi-info-circle"></i><p><?= e(t('not_funding')) ?></p></div>
</div></section>
<section class="section"><div class="container split-callout"><div><span class="eyebrow"><?= $en ? 'NEXT STEP' : 'POUR ALLER PLUS LOIN' ?></span><h2><?= $en ? 'Read the eligibility criteria before applying.' : 'Lisez les critères d’éligibilité avant de soumettre votre projet.' ?></h2><p><?= $en ? 'Take a moment to check who may apply, the project information to prepare and how applications are reviewed.' : 'Prenez le temps de vérifier qui peut candidater, les informations à préparer et les modalités d’étude des dossiers.' ?></p></div><div class="split-callout-actions"><a class="button button-navy" href="criteria.php"><?= $en ? 'Read the criteria' : 'Consulter les critères' ?> <i class="bi bi-arrow-right"></i></a><a class="text-link" href="apply.php"><?= e(t('send_project')) ?> <i class="bi bi-arrow-right"></i></a></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
