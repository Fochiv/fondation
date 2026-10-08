<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = 'FAQ';
$questions = $en ? [
    ['Who can apply?', 'Entrepreneurs, project founders, small businesses, start-ups, craftspeople and farmers may apply, subject to the current programme criteria.'],
    ['What kinds of projects are considered?', 'Projects in the programme’s published priority areas may be considered. Review the current criteria to confirm eligibility.'],
    ['How much may I request?', 'The applicable amount depends on the programme cycle. No funding amount is guaranteed by submitting an application.'],
    ['How are applications reviewed?', 'Applications are reviewed individually against the published criteria. Complete, accurate information helps the team understand your proposal.'],
    ['How long does the review take?', 'Review time may vary depending on the programme and the number of applications. The team will contact you if further information is required.'],
    ['How do I track my application?', 'Use your application reference number together with the email address or phone number provided in your application.'],
    ['How will I be contacted?', 'The team will use the contact information provided with your application. Keep your email address and phone number accessible.'],
    ['Is it free to submit an application?', 'Submitting an application through this platform is free. Be cautious of anyone asking for payment in exchange for guaranteed approval.'],
    ['Can I change my application?', 'If you need to update information, contact the team and provide your application number. Changes may depend on the review stage.'],
] : [
    ['Qui peut candidater ?', 'Les entrepreneurs, porteurs de projets, petites entreprises, startups, artisans et agriculteurs peuvent candidater, sous réserve des critères du programme en vigueur.'],
    ['Quels types de projets sont étudiés ?', 'Les projets relevant des domaines prioritaires publiés par le programme peuvent être étudiés. Consultez les critères en vigueur pour confirmer votre éligibilité.'],
    ['Quel montant puis-je demander ?', 'Le montant applicable dépend du cycle du programme. La soumission d’une candidature ne garantit aucun financement.'],
    ['Comment les dossiers sont-ils étudiés ?', 'Les candidatures sont étudiées individuellement selon les critères publiés. Des informations complètes et exactes aident l’équipe à comprendre votre proposition.'],
    ['Combien de temps dure l’étude ?', 'La durée peut varier selon le programme et le nombre de candidatures. L’équipe vous contactera si des informations complémentaires sont nécessaires.'],
    ['Comment suivre ma candidature ?', 'Utilisez votre numéro de dossier avec l’adresse e-mail ou le numéro de téléphone indiqué dans votre candidature.'],
    ['Comment serai-je contacté ?', 'L’équipe utilisera les coordonnées fournies dans votre dossier. Veillez à ce que votre e-mail et votre téléphone restent accessibles.'],
    ['Le dépôt d’une candidature est-il payant ?', 'La soumission d’une candidature sur cette plateforme est gratuite. Méfiez-vous de toute demande de paiement en échange d’une approbation garantie.'],
    ['Puis-je modifier mon dossier ?', 'Si vous devez modifier des informations, contactez l’équipe en indiquant votre numéro de dossier. Les changements dépendent de l’étape d’étude.'],
];
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-navy"><div class="container page-hero-content"><span class="eyebrow eyebrow-light">FAQ</span><h1><?= $en ? 'Questions, answered clearly.' : 'Vos questions, nos réponses.' ?></h1><p><?= $en ? 'Find useful information about eligibility, submitting a project and tracking your application.' : 'Retrouvez les informations utiles sur l’éligibilité, la soumission d’un projet et le suivi de votre candidature.' ?></p></div></section>
<section class="section"><div class="container faq-layout">
    <aside class="faq-aside"><span class="eyebrow"><?= $en ? 'NEED HELP?' : 'BESOIN D’AIDE ?' ?></span><h2><?= $en ? 'We’re here to help.' : 'Nous sommes à votre écoute.' ?></h2><p><?= $en ? 'If you cannot find the answer you need, send the team a message.' : 'Si vous ne trouvez pas la réponse à votre question, envoyez un message à l’équipe.' ?></p><a class="text-link" href="contact.php"><?= e(t('contact_button')) ?> <i class="bi bi-arrow-right"></i></a></aside>
    <div class="faq-list accordion" id="faqAccordion">
        <?php foreach ($questions as $index => [$question, $answer]): $collapseId = 'faqAnswer' . $index; ?>
            <article class="accordion-item faq-item"><h2 class="accordion-header"><button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= e($collapseId) ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= e($collapseId) ?>"><?= e($question) ?></button></h2><div id="<?= e($collapseId) ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion"><div class="accordion-body faq-answer"><p><?= e($answer) ?></p></div></div></article>
        <?php endforeach; ?>
    </div>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
