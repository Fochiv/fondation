<?php
require __DIR__ . '/includes/bootstrap.php';
header('Cache-Control: no-store, no-cache, must-revalidate, private');
$en = $lang === 'en';
$title = $en ? 'Track an application' : 'Suivre ma candidature';
$application = null;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $attempts = $_SESSION['track_attempts'] ?? ['count' => 0, 'since' => time()];
    if (time() - (int)$attempts['since'] > 900) $attempts = ['count' => 0, 'since' => time()];
    if ((int)$attempts['count'] >= 8) {
        $error = $en ? 'Too many attempts. Please wait 15 minutes and try again.' : 'Trop de tentatives. Attendez 15 minutes avant de réessayer.';
    } else {
        $_SESSION['track_attempts'] = ['count' => (int)$attempts['count'] + 1, 'since' => (int)$attempts['since']];
        $reference = strtoupper(trim((string)($_POST['reference_no'] ?? '')));
        $contact = trim((string)($_POST['contact'] ?? ''));
        if ($reference !== '' && $contact !== '' && mb_strlen($reference) <= 32 && mb_strlen($contact) <= 190) {
            try {
                $query = db()->prepare('SELECT reference_no,status,created_at,updated_at FROM applications WHERE reference_no = ? AND (email = ? OR phone = ?) LIMIT 1');
                $query->execute([$reference, $contact, $contact]);
                $application = $query->fetch() ?: null;
            } catch (Throwable) {
                $error = $en ? 'Application tracking is temporarily unavailable. Please try again later.' : 'Le suivi des candidatures est momentanément indisponible. Réessayez plus tard.';
            }
        }
        if (!$application && $error === '') $error = $en ? 'We could not find an application with those details. Check your reference number and contact information.' : 'Aucun dossier ne correspond à ces informations. Vérifiez votre numéro de dossier et vos coordonnées.';
    }
}
$prefill = trim((string)($_GET['reference'] ?? ''));
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-navy"><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'APPLICATION TRACKING' : 'SUIVI DE CANDIDATURE' ?></span><h1><?= $en ? 'Check the progress of your application.' : 'Consultez l’avancement de votre candidature.' ?></h1><p><?= $en ? 'Enter your application number and the email or phone number you used.' : 'Indiquez votre numéro de dossier et l’adresse e-mail ou le téléphone fourni lors de votre candidature.' ?></p></div></section>
<section class="section"><div class="container track-layout">
    <div class="track-form form-card">
        <span class="eyebrow"><?= $en ? 'SECURE LOOKUP' : 'CONSULTATION SÉCURISÉE' ?></span><h2><?= $en ? 'Find your application' : 'Retrouver mon dossier' ?></h2>
        <?php if ($error): ?><div class="error-banner" role="alert"><i class="bi bi-exclamation-circle"></i><?= e($error) ?></div><?php endif; ?>
        <form method="post" action="track.php" class="form-stack"><?= csrf_field() ?>
            <div class="form-group"><label for="reference_no"><?= $en ? 'Application number' : 'Numéro de dossier' ?> <span>*</span></label><input id="reference_no" name="reference_no" maxlength="32" required placeholder="DG-<?= date('Y') ?>-000001" value="<?= e($_POST['reference_no'] ?? $prefill) ?>"></div>
            <div class="form-group"><label for="contact"><?= $en ? 'Email address or phone' : 'Adresse e-mail ou téléphone' ?> <span>*</span></label><input id="contact" name="contact" maxlength="190" required autocomplete="email" value="<?= e($_POST['contact'] ?? '') ?>"></div>
            <button class="button button-red" type="submit"><?= $en ? 'Check status' : 'Consulter le statut' ?> <i class="bi bi-arrow-right"></i></button>
            <p class="form-footnote"><i class="bi bi-lock"></i><?= $en ? 'Only the application status and dates are shown. Internal review notes remain private.' : 'Seuls le statut et les dates sont affichés. Les notes internes restent confidentielles.' ?></p>
        </form>
    </div>
    <?php if ($application): $progress = status_progress($application['status']); $milestones = $en ? ['Application received', 'Administrative verification', 'Project review', 'Decision', 'Support'] : ['Candidature reçue', 'Vérification administrative', 'Étude du projet', 'Décision', 'Accompagnement']; ?>
        <div class="track-result">
            <div class="track-result-head"><span class="eyebrow"><?= $en ? 'YOUR APPLICATION' : 'VOTRE CANDIDATURE' ?></span><strong><?= e($application['reference_no']) ?></strong></div>
            <div class="track-status"><span><?= $en ? 'Current status' : 'Statut actuel' ?></span><strong class="status-pill status-<?= e($application['status']) ?>"><?= e(status_label($application['status'])) ?></strong></div>
            <div class="track-dates"><div><span><?= $en ? 'Submitted' : 'Date de soumission' ?></span><strong><?= e(date('d/m/Y', strtotime($application['created_at']))) ?></strong></div><div><span><?= $en ? 'Last updated' : 'Dernière mise à jour' ?></span><strong><?= e(date('d/m/Y', strtotime($application['updated_at']))) ?></strong></div></div>
            <h3><?= $en ? 'Review timeline' : 'Étapes de suivi' ?></h3>
            <ol class="timeline">
                <?php foreach ($milestones as $index => $milestone):
                    $done = $index < $progress;
                    $current = $index === $progress - 1;
                    if (in_array($application['status'], ['rejected','closed'], true) && $index === 4 && $application['status'] === 'rejected') $done = false;
                    ?>
                    <li class="<?= $done ? 'is-done' : '' ?> <?= $current ? 'is-current' : '' ?>"><span class="timeline-marker"><?= $done ? '<i class="bi bi-check-lg"></i>' : '' ?></span><span><?= e($milestone) ?><?= $current ? '<small>' . e($en ? 'Current stage' : 'Étape actuelle') . '</small>' : '' ?></span></li>
                <?php endforeach; ?>
            </ol>
            <p class="track-private"><i class="bi bi-shield-lock"></i><?= $en ? 'Private internal notes are not displayed here.' : 'Les notes internes ne sont pas affichées ici.' ?></p>
        </div>
    <?php else: ?>
        <div class="track-help"><div class="value-icon"><i class="bi bi-question-circle"></i></div><h3><?= $en ? 'Keep your reference number safe.' : 'Conservez votre numéro de dossier.' ?></h3><p><?= $en ? 'You received it after submitting your application. It is needed, together with your contact details, to view your status.' : 'Vous l’avez reçu après l’envoi de votre candidature. Il est nécessaire, avec vos coordonnées, pour consulter le statut.' ?></p><a class="text-link" href="faq.php"><?= $en ? 'Read the FAQ' : 'Consulter la FAQ' ?> <i class="bi bi-arrow-right"></i></a></div>
    <?php endif; ?>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
