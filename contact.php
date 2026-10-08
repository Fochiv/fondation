<?php
require __DIR__ . '/includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'Contact the team' : 'Contacter l’équipe';
$errors = [];
$sent = false;
$values = ['full_name' => '', 'email' => '', 'subject' => '', 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    foreach ($values as $key => $_) {
        $values[$key] = trim((string)($_POST[$key] ?? ''));
    }
    if (!empty($_POST['company_website'])) {
        $errors[] = $en ? 'Your message could not be sent.' : 'Votre message n’a pas pu être envoyé.';
    }
    if (mb_strlen($values['full_name']) < 2 || mb_strlen($values['full_name']) > 190) $errors[] = $en ? 'Enter your name.' : 'Indiquez votre nom.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = $en ? 'Enter a valid email address.' : 'Indiquez une adresse e-mail valide.';
    if (mb_strlen($values['subject']) < 3 || mb_strlen($values['subject']) > 190) $errors[] = $en ? 'Enter a subject.' : 'Indiquez un objet.';
    if (mb_strlen($values['message']) < 10 || mb_strlen($values['message']) > 5000) $errors[] = $en ? 'Your message must be between 10 and 5,000 characters.' : 'Votre message doit contenir entre 10 et 5 000 caractères.';
    if (!$errors) {
        try {
            $save = db()->prepare('INSERT INTO contact_messages (full_name,email,subject,message) VALUES (?,?,?,?)');
            $save->execute(array_values($values));
            $values = array_fill_keys(array_keys($values), '');
            $sent = true;
        } catch (Throwable) {
            $errors[] = $en ? 'The message could not be saved. Please try again later.' : 'Le message n’a pas pu être enregistré. Réessayez plus tard.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero page-hero-navy"><div class="container page-hero-content"><span class="eyebrow eyebrow-light"><?= $en ? 'GET IN TOUCH' : 'ÉCHANGEONS' ?></span><h1><?= $en ? 'How can we help?' : 'Comment pouvons-nous vous aider ?' ?></h1><p><?= $en ? 'Send the team a question about the programme or the application process.' : 'Posez une question à l’équipe sur le programme ou les étapes de candidature.' ?></p></div></section>
<section class="section"><div class="container contact-layout">
    <div class="contact-details"><span class="eyebrow"><?= $en ? 'CONTACT DETAILS' : 'COORDONNÉES' ?></span><h2><?= $en ? 'Reach the programme team' : 'Joindre l’équipe du programme' ?></h2><p><?= $en ? 'Use the details published by the programme or send a message using the form.' : 'Utilisez les coordonnées publiées par le programme ou écrivez-nous à l’aide du formulaire.' ?></p>
        <div class="contact-methods">
            <?php if (setting('contact_email') !== ''): ?><a href="mailto:<?= e(setting('contact_email')) ?>"><span class="contact-icon"><i class="bi bi-envelope"></i></span><span><small><?= $en ? 'EMAIL' : 'E-MAIL' ?></small><strong><?= e(setting('contact_email')) ?></strong></span></a><?php endif; ?>
            <?php if (setting('phone') !== ''): ?><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><span class="contact-icon"><i class="bi bi-telephone"></i></span><span><small><?= $en ? 'PHONE' : 'TÉLÉPHONE' ?></small><strong><?= e(setting('phone')) ?></strong></span></a><?php endif; ?>
            <?php if (setting('address') !== ''): ?><div><span class="contact-icon"><i class="bi bi-geo-alt"></i></span><span><small><?= $en ? 'ADDRESS' : 'ADRESSE' ?></small><strong><?= e(setting('address')) ?></strong></span></div><?php endif; ?>
            <?php if ($whatsapp = whatsapp_url()): ?><a href="<?= e($whatsapp) ?>" target="_blank" rel="noopener noreferrer"><span class="contact-icon"><i class="bi bi-whatsapp"></i></span><span><small>WHATSAPP</small><strong><?= $en ? 'Message the team' : 'Écrire à l’équipe' ?></strong></span></a><?php endif; ?>
            <?php if (count(glob(__DIR__ . '/does-not-exist-*') ?: []) > 0): ?><span></span><?php endif; ?>
            <?php foreach (['facebook' => 'facebook', 'instagram' => 'instagram', 'linkedin' => 'linkedin'] as $network => $icon): $socialUrl = setting($network); if ($socialUrl !== ''): ?><a href="<?= e($socialUrl) ?>" target="_blank" rel="noopener noreferrer"><span class="contact-icon"><i class="bi bi-<?= e($icon) ?>"></i></span><span><small><?= e(strtoupper($network)) ?></small><strong><?= e($network) ?></strong></span></a><?php endif; endforeach; ?>
        </div>
    </div>
    <div class="form-card contact-form-card">
        <?php if ($sent): ?><div class="success-banner"><i class="bi bi-check-circle"></i><?= $en ? 'Your message has been received. Thank you for contacting us.' : 'Votre message a bien été reçu. Merci de nous avoir contactés.' ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="error-banner" role="alert"><i class="bi bi-exclamation-circle"></i><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" action="contact.php" class="form-stack">
            <?= csrf_field() ?>
            <label class="honeypot" aria-hidden="true">Site web<input name="company_website" tabindex="-1" autocomplete="off"></label>
            <div class="form-group"><label for="full_name"><?= $en ? 'Full name' : 'Nom complet' ?> <span>*</span></label><input id="full_name" name="full_name" autocomplete="name" maxlength="190" required value="<?= e($values['full_name']) ?>"></div>
            <div class="form-group"><label for="email"><?= $en ? 'Email address' : 'Adresse e-mail' ?> <span>*</span></label><input id="email" type="email" name="email" autocomplete="email" maxlength="190" required value="<?= e($values['email']) ?>"></div>
            <div class="form-group"><label for="subject"><?= $en ? 'Subject' : 'Objet' ?> <span>*</span></label><input id="subject" name="subject" maxlength="190" required value="<?= e($values['subject']) ?>"></div>
            <div class="form-group"><label for="message"><?= $en ? 'Your message' : 'Votre message' ?> <span>*</span></label><textarea id="message" name="message" rows="5" maxlength="5000" required><?= e($values['message']) ?></textarea></div>
            <button class="button button-red" type="submit"><?= $en ? 'Send message' : 'Envoyer le message' ?> <i class="bi bi-arrow-up-right"></i></button>
            <p class="form-footnote"><i class="bi bi-lock"></i><?= $en ? 'Your information is used only to respond to your enquiry.' : 'Vos informations servent uniquement à répondre à votre demande.' ?></p>
        </form>
    </div>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
