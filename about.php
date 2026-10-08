<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'About the programme' : 'À propos du programme';
require __DIR__ . '/includes/header.php';
$cards = $en ? [
    ['bullseye', 'Our mission', 'To encourage promising local initiatives and support entrepreneurs as they develop sustainable activities.'],
    ['eye', 'Our vision', 'Communities where entrepreneurship, innovation and opportunity contribute to inclusive prosperity.'],
    ['graph-up-arrow', 'Our objectives', 'Promote economic participation, local job creation and responsible, lasting business growth.'],
    ['signpost-split', 'Our approach', 'A clear and fair application pathway with consistent criteria and individual review.'],
] : [
    ['bullseye', 'Notre mission', 'Encourager les initiatives locales prometteuses et accompagner les entrepreneurs dans le développement d’activités durables.'],
    ['eye', 'Notre vision', 'Des communautés où l’entrepreneuriat, l’innovation et les opportunités contribuent à une prospérité inclusive.'],
    ['graph-up-arrow', 'Nos objectifs', 'Favoriser la participation économique, la création d’emplois locaux et la croissance responsable des activités.'],
    ['signpost-split', 'Notre approche', 'Un parcours de candidature clair et équitable, fondé sur des critères cohérents et l’étude de chaque dossier.'],
];
?>
<section class="page-hero page-hero-navy"><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'ABOUT THE PROGRAMME' : 'À PROPOS DU PROGRAMME' ?></span><h1><?= $en ? 'Backing the ambition behind local enterprise.' : 'Accompagner l’ambition des initiatives locales.' ?></h1><p><?= $en ? 'Learn about the goals, principles and intended impact behind this project support programme.' : 'Découvrez les objectifs, les principes et l’impact recherché par ce programme d’accompagnement de projets.' ?></p></div></section>
<section class="section"><div class="container">
    <div class="section-heading"><div><span class="eyebrow"><?= $en ? 'WHAT GUIDES US' : 'CE QUI NOUS GUIDE' ?></span><h2><?= $en ? 'A practical approach to opportunity' : 'Une approche concrète des opportunités' ?></h2></div><p class="heading-description"><?= $en ? 'The programme invites individuals and organisations to present ideas that respond to real needs and can develop over time.' : 'Le programme invite les personnes et les organisations à présenter des idées qui répondent à des besoins concrets et peuvent se développer dans le temps.' ?></p></div>
    <div class="value-grid">
        <?php foreach ($cards as [$icon, $heading, $copy]): ?>
            <article class="value-card"><div class="value-icon"><i class="bi bi-<?= e($icon) ?>"></i></div><h3><?= e($heading) ?></h3><p><?= e($copy) ?></p></article>
        <?php endforeach; ?>
    </div>
</div></section>
<section class="section section-soft"><div class="container impact-grid">
    <div><span class="eyebrow"><?= $en ? 'OUR INTENDED IMPACT' : 'L’IMPACT RECHERCHÉ' ?></span><h2><?= $en ? 'Stronger initiatives can create wider opportunity.' : 'Des initiatives plus solides peuvent ouvrir davantage d’opportunités.' ?></h2><p><?= $en ? 'Through responsible support and a careful review process, the programme seeks to contribute to economic development, employment and locally rooted innovation.' : 'Par un accompagnement responsable et une étude attentive, le programme entend contribuer au développement économique, à l’emploi et à l’innovation ancrée dans les territoires.' ?></p></div>
    <div class="impact-list">
        <?php foreach (($en ? ['Entrepreneurship and skills', 'Economic development', 'Local job creation', 'Innovation and autonomy'] : ['Entrepreneuriat et compétences', 'Développement économique', 'Création d’emplois locaux', 'Innovation et autonomisation']) as $index => $item): ?>
            <div><span>0<?= $index + 1 ?></span><strong><?= e($item) ?></strong><i class="bi bi-arrow-up-right"></i></div>
        <?php endforeach; ?>
    </div>
</div></section>
<section class="section"><div class="container simple-cta"><div><span class="eyebrow"><?= $en ? 'YOUR NEXT STEP' : 'VOTRE PROCHAINE ÉTAPE' ?></span><h2><?= $en ? 'See whether your project may be eligible.' : 'Vérifiez si votre projet peut être éligible.' ?></h2></div><a class="button button-red" href="criteria.php"><?= $en ? 'Review the criteria' : 'Consulter les critères' ?> <i class="bi bi-arrow-right"></i></a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
