<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'A programme for local initiatives' : 'Un programme pour les initiatives locales';
$description = $en
    ? 'Discover the Dangote Foundation programme and submit a project for consideration.'
    : 'Découvrez le programme de la Fondation Dangote et soumettez un projet pour étude.';
$stats = $en
    ? [['128+', 'Projects received'], ['46+', 'Projects reviewed'], ['15', 'Sectors represented'], ['6', 'Countries covered']]
    : [['128+', 'Projets reçus'], ['46+', 'Projets étudiés'], ['15', 'Secteurs concernés'], ['6', 'Pays couverts']];
$sectors = $en
    ? ['Agriculture', 'Livestock', 'Commerce', 'Technology', 'Industry', 'Processing', 'Crafts', 'Education', 'Health', 'Transport', 'Energy', 'Services', 'Hospitality', 'Fashion', 'Other']
    : ['Agriculture', 'Élevage', 'Commerce', 'Technologie', 'Industrie', 'Transformation', 'Artisanat', 'Éducation', 'Santé', 'Transport', 'Énergie', 'Services', 'Restauration', 'Mode', 'Autres'];
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-shade"></div>
    <div class="hero-content container">
        <span class="eyebrow eyebrow-light"><span></span><?= $en ? 'DANGOTE FOUNDATION · PROJECT PROGRAMME' : 'FONDATION DANGOTE · PROGRAMME DE PROJETS' ?></span>
        <h1><?= $en ? 'Let’s build the projects shaping tomorrow.' : 'Donnons vie aux projets qui construisent l’avenir.' ?></h1>
        <p><?= $en
            ? 'Have a project or an activity you want to grow? Discover our support programme and submit your application for consideration.'
            : 'Vous avez un projet ou une activité que vous souhaitez développer ? Découvrez notre programme d’accompagnement et soumettez votre candidature.' ?></p>
        <div class="hero-actions">
            <a class="button button-red" href="apply.php"><?= e(t('send_project')) ?> <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            <a class="button button-glass" href="program.php"><?= e(t('learn_more')) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            <a class="button button-glass" href="track.php"><i class="bi bi-search" aria-hidden="true"></i> <?= e(t('track')) ?></a>
        </div>
        <div class="hero-note"><i class="bi bi-shield-check" aria-hidden="true"></i> <?= e(t('not_funding')) ?></div>
    </div>
    <a href="#impact" class="hero-scroll" aria-label="<?= $en ? 'Scroll to explore' : 'Faire défiler pour découvrir' ?>"><i class="bi bi-arrow-down"></i></a>
</section>

<section class="stats-band" id="impact">
    <div class="container stats-wrap">
        <?php foreach ($stats as [$value, $label]): ?>
            <div class="stat-item"><span class="stat-value" data-count="<?= (int) preg_replace('/\D/', '', $value) ?>" data-suffix="<?= e(preg_replace('/\d/', '', $value)) ?>"><?= e($value) ?></span><span class="stat-label"><?= e($label) ?></span></div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-intro">
    <div class="container intro-grid">
        <div class="section-copy">
            <span class="eyebrow"><?= $en ? 'OUR PURPOSE' : 'NOTRE ENGAGEMENT' ?></span>
            <h2><?= $en ? 'Supporting ideas that strengthen communities.' : 'Soutenir les idées qui renforcent les communautés.' ?></h2>
            <p><?= $en
                ? 'The programme aims to help local entrepreneurs make their ideas grow through a structured application and review process.'
                : 'Le programme a pour ambition d’aider les entrepreneurs locaux à faire grandir leurs idées grâce à un parcours de candidature et d’étude structuré.' ?></p>
            <a class="text-link" href="about.php"><?= $en ? 'Learn about our approach' : 'Découvrir notre approche' ?> <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="intro-panel">
            <div class="intro-icon"><i class="bi bi-buildings"></i></div>
            <h3><?= $en ? 'Local ideas. Lasting impact.' : 'Des initiatives locales. Un impact durable.' ?></h3>
            <p><?= $en
                ? 'Entrepreneurship, employment and innovation can contribute to more resilient local economies.'
                : 'L’entrepreneuriat, l’emploi et l’innovation peuvent contribuer à des économies locales plus résilientes.' ?></p>
            <div class="intro-panel-foot"><span></span><?= $en ? 'A programme focused on people and potential' : 'Un programme tourné vers les personnes et le potentiel' ?></div>
        </div>
    </div>
</section>

<section class="section domain-section">
    <div class="container">
        <div class="sector-reveal">
            <button class="button button-outline sector-toggle" type="button" aria-expanded="false" aria-controls="home-sectors" data-show-label="<?= $en ? 'Show areas of interest' : 'Afficher les domaines d’intervention' ?>" data-hide-label="<?= $en ? 'Hide areas of interest' : 'Masquer les domaines d’intervention' ?>"><span><?= $en ? 'Show areas of interest' : 'Afficher les domaines d’intervention' ?></span> <i class="bi bi-chevron-down" aria-hidden="true"></i></button>
        </div>
        <div class="sector-grid" id="home-sectors" hidden>
            <?php
            $sectorIcons = ['bi-flower1', 'bi-heart-pulse', 'bi-shop', 'bi-cpu', 'bi-buildings', 'bi-box-seam', 'bi-tools', 'bi-book', 'bi-hospital', 'bi-truck', 'bi-lightning', 'bi-briefcase', 'bi-cup-hot', 'bi-scissors', 'bi-three-dots'];
            foreach ($sectors as $index => $sector): ?>
                <a class="sector-card" href="program.php#domains"><span class="sector-icon"><i class="bi <?= e($sectorIcons[$index]) ?>"></i></span><span><?= e($sector) ?></span><i class="bi bi-arrow-up-right sector-arrow"></i></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div><span class="eyebrow"><?= $en ? 'ABOUT THE PROGRAMME' : 'À PROPOS DU PROGRAMME' ?></span><h2><?= $en ? 'Local ideas can strengthen communities.' : 'Les initiatives locales peuvent renforcer les communautés.' ?></h2></div>
            <a class="text-link" href="about.php"><?= $en ? 'About us' : 'En savoir plus sur le programme' ?> <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="home-about-grid">
            <?php foreach (($en
                ? [['bullseye', 'Our mission', 'Encourage promising local initiatives and support entrepreneurs as they develop sustainable activities.'], ['eye', 'Our vision', 'Communities where entrepreneurship, innovation and opportunity contribute to inclusive prosperity.'], ['signpost-split', 'Our approach', 'A clear application pathway with consistent criteria and individual review.']]
                : [['bullseye', 'Notre mission', 'Encourager les initiatives locales prometteuses et accompagner le développement d’activités durables.'], ['eye', 'Notre vision', 'Des communautés où l’entrepreneuriat, l’innovation et les opportunités contribuent à une prospérité inclusive.'], ['signpost-split', 'Notre approche', 'Un parcours de candidature clair, fondé sur des critères cohérents et l’étude de chaque dossier.']]) as [$icon, $heading, $copy]): ?>
                <article class="value-card"><div class="value-icon"><i class="bi bi-<?= e($icon) ?>"></i></div><h3><?= e($heading) ?></h3><p><?= e($copy) ?></p></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container home-faq-layout">
        <div class="home-faq-intro">
            <span class="eyebrow"><?= $en ? 'FREQUENTLY ASKED QUESTIONS' : 'FOIRE AUX QUESTIONS' ?></span>
            <h2><?= $en ? 'A few answers to get started.' : 'Quelques réponses pour bien commencer.' ?></h2>
            <p><?= $en ? 'Find more guidance on eligibility and application follow-up.' : 'Retrouvez des précisions sur l’éligibilité et le suivi des candidatures.' ?></p>
            <a class="text-link" href="faq.php"><?= $en ? 'View all answers' : 'Voir toute la FAQ' ?> <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="home-faq-list">
            <?php foreach (($en
                ? [['Who can apply?', 'Entrepreneurs, project founders, small businesses, start-ups, craftspeople and farmers may apply, subject to current programme criteria.'], ['Is funding guaranteed?', 'No. Every application is reviewed individually, and submitting a project does not guarantee funding.'], ['How do I track my application?', 'Use your unique application reference and the email address or phone number included in your application.']]
                : [['Qui peut candidater ?', 'Les entrepreneurs, porteurs de projets, petites entreprises, startups, artisans et agriculteurs peuvent candidater, sous réserve des critères en vigueur.'], ['Le financement est-il garanti ?', 'Non. Chaque candidature est étudiée individuellement et la soumission d’un projet ne garantit aucun financement.'], ['Comment suivre ma candidature ?', 'Utilisez votre numéro de dossier unique avec l’adresse e-mail ou le numéro de téléphone indiqué dans votre candidature.']]) as $index => [$question, $answer]): ?>
                <details class="home-faq-item" <?= $index === 0 ? 'open' : '' ?>><summary><?= e($question) ?></summary><p><?= e($answer) ?></p></details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading section-heading-center">
            <span class="eyebrow"><?= $en ? 'YOUR APPLICATION JOURNEY' : 'VOTRE PARCOURS DE CANDIDATURE' ?></span>
            <h2><?= $en ? 'A clear process, from idea to review' : 'Un parcours clair, de l’idée à son étude' ?></h2>
            <p><?= $en ? 'Every application is considered through a structured review process.' : 'Chaque candidature est examinée dans le cadre d’un processus d’étude structuré.' ?></p>
        </div>
        <div class="steps-row">
            <?php
            $steps = $en
                ? [['clipboard-check', 'Prepare', 'Describe your project, its purpose and your key figures.'], ['send', 'Submit', 'Complete the application form with the requested details.'], ['hash', 'Get a reference', 'Keep your unique application number.'], ['search', 'Review', 'The programme team reviews your application.'], ['bell', 'Notification', 'Check your application status and messages.']]
                : [['clipboard-check', 'Préparer', 'Décrivez votre projet, son objectif et ses principaux chiffres.'], ['send', 'Soumettre', 'Complétez le formulaire avec les informations demandées.'], ['hash', 'Recevoir un numéro', 'Conservez votre numéro de dossier unique.'], ['search', 'Étude', 'L’équipe du programme étudie votre candidature.'], ['bell', 'Notification', 'Consultez le suivi et vos messages.']];
            foreach ($steps as $index => [$icon, $label, $copy]): ?>
                <div class="journey-step"><span class="journey-number">0<?= $index + 1 ?></span><div class="journey-icon"><i class="bi bi-<?= e($icon) ?>"></i></div><h3><?= e($label) ?></h3><p><?= e($copy) ?></p></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container cta-inner">
        <div><span class="eyebrow eyebrow-light"><?= $en ? 'YOUR IDEA DESERVES TO BE HEARD' : 'VOTRE PROJET MÉRITE D’ÊTRE ÉTUDIÉ' ?></span><h2><?= $en ? 'Ready to tell us about your project?' : 'Prêt à nous parler de votre projet ?' ?></h2><p><?= e(t('not_funding')) ?></p></div>
        <a class="button button-white" href="apply.php"><?= e(t('send_project')) ?> <i class="bi bi-arrow-up-right"></i></a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
