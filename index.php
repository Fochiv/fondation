<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'A programme for local initiatives' : 'Un programme pour les initiatives locales';
$description = $en
    ? 'Discover the Dangote Foundation programme and submit a project for consideration.'
    : 'Découvrez le programme de la Fondation Dangote et soumettez un projet pour étude.';
$stats = $en
    ? [['01', 'Project review'], ['02', 'Priority sectors'], ['03', 'Application stages'], ['04', 'Your next step']]
    : [['01', 'Projet étudié'], ['02', 'Domaines prioritaires'], ['03', 'Étapes de candidature'], ['04', 'Votre prochaine étape']];
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
        </div>
        <div class="hero-note"><i class="bi bi-shield-check" aria-hidden="true"></i> <?= e(t('not_funding')) ?></div>
    </div>
    <a href="#impact" class="hero-scroll" aria-label="<?= $en ? 'Scroll to explore' : 'Faire défiler pour découvrir' ?>"><i class="bi bi-arrow-down"></i></a>
</section>

<section class="stats-band" id="impact">
    <div class="container stats-wrap">
        <?php foreach ($stats as [$value, $label]): ?>
            <div class="stat-item"><span class="stat-value"><?= e($value) ?></span><span class="stat-label"><?= e($label) ?></span></div>
        <?php endforeach; ?>
    </div>
    <p class="stats-caption"><?= e(t('demo')) ?> · <?= $en ? 'Programme information is published as it becomes available.' : 'Les informations du programme sont publiées au fur et à mesure.' ?></p>
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

<section class="section section-soft">
    <div class="container">
        <div class="section-heading">
            <div><span class="eyebrow"><?= $en ? 'AREAS OF INTEREST' : 'DOMAINES D’INTERVENTION' ?></span><h2><?= $en ? 'Where your idea can make a difference' : 'Là où votre projet peut faire la différence' ?></h2></div>
            <a class="text-link" href="program.php"><?= $en ? 'See the programme' : 'Voir le programme' ?> <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="sector-grid">
            <?php
            $sectorIcons = ['bi-flower1', 'bi-heart-pulse', 'bi-shop', 'bi-cpu', 'bi-buildings', 'bi-box-seam', 'bi-tools', 'bi-book', 'bi-hospital', 'bi-truck', 'bi-lightning', 'bi-briefcase', 'bi-cup-hot', 'bi-scissors', 'bi-three-dots'];
            foreach ($sectors as $index => $sector): ?>
                <a class="sector-card" href="program.php#domains"><span class="sector-icon"><i class="bi <?= e($sectorIcons[$index]) ?>"></i></span><span><?= e($sector) ?></span><i class="bi bi-arrow-up-right sector-arrow"></i></a>
            <?php endforeach; ?>
        </div>
        <p class="small-note"><?= $en ? 'Priority areas and eligibility may vary by programme cycle. Please review the current criteria.' : 'Les priorités et critères peuvent varier selon le programme. Consultez les critères en vigueur.' ?></p>
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
