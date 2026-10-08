<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'Privacy notice' : 'Politique de confidentialité';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-navy"><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'YOUR INFORMATION' : 'VOS INFORMATIONS' ?></span><h1><?= $en ? 'Privacy notice' : 'Politique de confidentialité' ?></h1><p><?= $en ? 'How information submitted through this project platform is handled.' : 'Comment les informations communiquées sur cette plateforme de projets sont traitées.' ?></p></div></section>
<section class="section"><article class="container privacy-copy">
    <p class="privacy-updated"><?= $en ? 'Last updated' : 'Dernière mise à jour' ?> · <?= date('d/m/Y') ?></p>
    <h2><?= $en ? 'Information we collect' : 'Informations collectées' ?></h2>
    <p><?= $en ? 'When you apply, we request information needed to understand your project, contact you about your application and review it against the programme criteria. Contact form details are used to reply to your enquiry.' : 'Lors d’une candidature, les informations demandées servent à comprendre votre projet, vous contacter à propos de votre dossier et l’étudier selon les critères du programme. Les coordonnées du formulaire de contact servent à répondre à votre demande.' ?></p>
    <h2><?= $en ? 'How the information is used' : 'Utilisation des informations' ?></h2>
    <p><?= $en ? 'Application information is used for administrative checks, project review, status updates and programme administration. Contact messages are used to respond to questions. Internal review notes are not displayed in the public application tracker.' : 'Les informations de candidature servent aux vérifications administratives, à l’étude des projets, au suivi des statuts et à l’administration du programme. Les messages de contact servent à répondre aux questions. Les notes internes ne sont pas affichées dans le suivi public des dossiers.' ?></p>
    <h2><?= $en ? 'Your choices and access' : 'Vos choix et votre accès' ?></h2>
    <p><?= $en ? 'Only provide information that is relevant to your application. To ask a question or request an update to a submitted record, contact the team and include your application reference.' : 'Ne fournissez que les informations utiles à votre candidature. Pour poser une question ou demander une modification à un dossier soumis, contactez l’équipe en indiquant votre numéro de dossier.' ?></p>
    <h2><?= $en ? 'Data security and retention' : 'Sécurité et conservation' ?></h2>
    <p><?= $en ? 'Application records are intended for use by authorised programme administrators and are stored in the configured programme database. They should be retained only for as long as they are needed for programme administration and applicable obligations.' : 'Les dossiers de candidature sont destinés aux administrateurs autorisés et conservés dans la base de données configurée pour le programme. Ils ne doivent être gardés que pendant la durée nécessaire à l’administration du programme et au respect des obligations applicables.' ?></p>
    <h2><?= $en ? 'Contact' : 'Contact' ?></h2>
    <p><?= $en ? 'For privacy questions, use the contact form or the official programme contact details published on this site.' : 'Pour toute question sur la confidentialité, utilisez le formulaire de contact ou les coordonnées officielles du programme publiées sur ce site.' ?> <a class="text-link" href="contact.php"><?= e(t('contact_button')) ?> <i class="bi bi-arrow-right"></i></a></p>
</article></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
