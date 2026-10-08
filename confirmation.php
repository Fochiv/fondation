<?php
require __DIR__ . '/includes/bootstrap.php';
header('Cache-Control: no-store, no-cache, must-revalidate, private');
$reference = $_SESSION['confirmation_reference'] ?? null;
unset($_SESSION['confirmation_reference']);
if (!$reference) redirect('index.php');
$en = $lang === 'en';
$title = $en ? 'Application received' : 'Candidature enregistrée';
$message = $en
    ? "Hello, I have submitted an application to the programme. My reference number is {$reference}. I would like to ask about the next steps."
    : "Bonjour, je viens de soumettre une candidature au programme. Mon numéro de dossier est {$reference}. Je souhaite obtenir des informations concernant la suite de ma candidature.";
$whatsapp = whatsapp_url($message);
require __DIR__ . '/includes/header.php';
?>
<section class="confirmation-section"><div class="container"><div class="confirmation-card">
    <div class="confirmation-check"><i class="bi bi-check-lg"></i></div>
    <span class="eyebrow"><?= $en ? 'APPLICATION SUBMITTED' : 'CANDIDATURE ENVOYÉE' ?></span>
    <h1><?= $en ? 'Your application has been received.' : 'Votre candidature a bien été enregistrée.' ?></h1>
    <p><?= $en ? 'Thank you for sharing your project. It will be reviewed according to the programme criteria.' : 'Merci d’avoir partagé votre projet. Votre candidature sera étudiée selon les critères du programme.' ?></p>
    <div class="reference-box"><small><?= $en ? 'YOUR APPLICATION NUMBER' : 'VOTRE NUMÉRO DE DOSSIER' ?></small><strong><?= e($reference) ?></strong><span><i class="bi bi-bookmark-check"></i><?= $en ? 'Keep this number to track your application.' : 'Conservez précieusement ce numéro pour suivre votre candidature.' ?></span></div>
    <div class="confirmation-actions"><a class="button button-navy" href="track.php?reference=<?= rawurlencode($reference) ?>"><?= $en ? 'Track my application' : 'Suivre ma candidature' ?> <i class="bi bi-arrow-right"></i></a><?php if ($whatsapp): ?><a class="button button-outline" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp"></i> <?= $en ? 'Contact on WhatsApp' : 'Contacter sur WhatsApp' ?></a><?php endif; ?><a class="text-link" href="index.php"><?= $en ? 'Return home' : 'Retour à l’accueil' ?> <i class="bi bi-arrow-right"></i></a></div>
</div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
