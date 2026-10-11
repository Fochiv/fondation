<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'Terms & Conditions' : 'Conditions générales';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-navy">
    <div class="container page-hero-content">
        <span class="eyebrow eyebrow-light"><?= $en ? 'USING THIS PLATFORM' : 'UTILISATION DE LA PLATEFORME' ?></span>
        <h1><?= $en ? 'Terms & Conditions' : 'Conditions générales d’utilisation' ?></h1>
        <p><?= $en ? 'The basic terms for submitting and following a project through this platform.' : 'Les règles générales de soumission et de suivi d’un projet sur cette plateforme.' ?></p>
    </div>
</section>
<section class="section"><article class="container privacy-copy">
    <p class="privacy-updated"><?= $en ? 'Last updated' : 'Dernière mise à jour' ?> · <?= date('d/m/Y') ?></p>
    <h2><?= $en ? 'Purpose of the platform' : 'Objet de la plateforme' ?></h2>
    <p><?= $en ? 'This platform allows project holders to submit information for consideration under the programme and to follow the status of a submitted application.' : 'Cette plateforme permet aux porteurs de projets de transmettre des informations pour étude dans le cadre du programme et de consulter le statut d’une candidature soumise.' ?></p>
    <h2><?= $en ? 'Submitting an application' : 'Soumission d’une candidature' ?></h2>
    <p><?= $en ? 'Applicants are responsible for providing accurate, complete and up-to-date information, and for using contact details they can access. Submitting an application is free; no payment can guarantee approval or funding.' : 'Le candidat est responsable de l’exactitude, de l’exhaustivité et de l’actualité des informations fournies, ainsi que de l’accès aux coordonnées indiquées. La soumission d’une candidature est gratuite ; aucun paiement ne garantit une approbation ou un financement.' ?></p>
    <h2><?= $en ? 'Review and decisions' : 'Étude et décisions' ?></h2>
    <p><?= $en ? 'Applications are reviewed against the programme criteria in effect. Submission does not guarantee eligibility, selection, funding or a particular review period. The applicant can use the application reference to check the status.' : 'Les candidatures sont étudiées au regard des critères du programme en vigueur. Leur soumission ne garantit ni l’éligibilité, ni la sélection, ni un financement, ni un délai d’étude déterminé. Le candidat peut utiliser son numéro de dossier pour consulter son statut.' ?></p>
    <h2><?= $en ? 'Appropriate use' : 'Utilisation appropriée' ?></h2>
    <p><?= $en ? 'Users must not submit unlawful, misleading or malicious content, interfere with the platform or attempt to access another person’s application. The team may restrict access where needed to protect the platform and its users.' : 'Les utilisateurs ne doivent pas transmettre de contenu illicite, trompeur ou malveillant, perturber le fonctionnement de la plateforme ni tenter d’accéder au dossier d’une autre personne. L’équipe peut restreindre l’accès lorsque cela est nécessaire à la protection de la plateforme et de ses utilisateurs.' ?></p>
    <h2><?= $en ? 'Personal information' : 'Informations personnelles' ?></h2>
    <p><?= $en ? 'Information submitted through the platform is handled as described in the privacy policy.' : 'Les informations transmises sur la plateforme sont traitées conformément à la politique de confidentialité.' ?> <a class="text-link" href="privacy.php"><?= e(t('legal')) ?> <i class="bi bi-arrow-right"></i></a></p>
    <h2><?= $en ? 'Updates and contact' : 'Mises à jour et contact' ?></h2>
    <p><?= $en ? 'These terms may be updated to reflect changes to the platform or programme. For questions, contact the team through the site’s contact page.' : 'Ces conditions peuvent être mises à jour en cas d’évolution de la plateforme ou du programme. Pour toute question, contactez l’équipe depuis la page dédiée.' ?> <a class="text-link" href="contact.php"><?= e(t('contact_button')) ?> <i class="bi bi-arrow-right"></i></a></p>
</article></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
