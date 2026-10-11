<?php
require __DIR__ . '/../includes/bootstrap.php';
$en = $lang === 'en';
$title = $en ? 'Secure administrator setup' : 'Création sécurisée de l’administrateur';
$setupKey = app_config('ADMIN_SETUP_KEY');
$initialEmail = trim(app_config('ADMIN_EMAIL'));
$initialPassword = app_config('ADMIN_INITIAL_PASSWORD');
$useConfiguredCredentials = $initialEmail !== '' && $initialPassword !== '';
$error = '';
$created = false;
$email = $initialEmail !== '' ? $initialEmail : (string)($_POST['email'] ?? '');
$password = $useConfiguredCredentials ? $initialPassword : (string)($_POST['password'] ?? '');

if ($setupKey === '') {
    http_response_code(503);
    $error = $en
        ? 'Administrator setup is locked. Set ADMIN_SETUP_KEY in the private application configuration before continuing.'
        : 'La création de l’administrateur est verrouillée. Définissez ADMIN_SETUP_KEY dans la configuration privée avant de continuer.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $attempts = $_SESSION['setup_attempts'] ?? ['count' => 0, 'since' => time()];
    if (time() - (int)$attempts['since'] > 900) $attempts = ['count' => 0, 'since' => time()];
    $_SESSION['setup_attempts'] = ['count' => (int)$attempts['count'] + 1, 'since' => (int)$attempts['since']];
    if ((int)$attempts['count'] >= 5) {
        $error = $en ? 'Too many attempts. Please wait 15 minutes.' : 'Trop de tentatives. Attendez 15 minutes.';
    } elseif (!hash_equals($setupKey, (string)($_POST['setup_key'] ?? ''))) {
        $error = $en ? 'The setup key is not valid.' : 'La clé de configuration est invalide.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $error = $en ? 'Enter a valid email address.' : 'Indiquez une adresse e-mail valide.';
    } elseif (strlen($password) < 12) {
        $error = $en ? 'Choose a password with at least 12 characters.' : 'Choisissez un mot de passe d’au moins 12 caractères.';
    } elseif (!$useConfiguredCredentials && $password !== (string)($_POST['password_confirm'] ?? '')) {
        $error = $en ? 'The passwords do not match.' : 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            $pdo = db();
            $count = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
            if ($count > 0) {
                $error = $en ? 'An administrator account already exists. Please sign in.' : 'Un compte administrateur existe déjà. Connectez-vous.';
            } else {
                $create = $pdo->prepare('INSERT INTO admins (email, password_hash) VALUES (?, ?)');
                $create->execute([$email, password_hash((string)$_POST['password'], PASSWORD_DEFAULT)]);
                $created = true;
            }
        } catch (Throwable) {
            $error = $en
                ? 'Could not create the account. Confirm that database/schema.sql has been imported and MySQL is configured.'
                : 'Le compte n’a pas pu être créé. Vérifiez que database/schema.sql a été importé et que MySQL est configuré.';
        }
    }
}
?><!doctype html><html lang="<?= e($lang) ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?= e($title) ?></title><link rel="icon" type="image/svg+xml" href="../favicon.svg"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/site.css"></head><body class="auth-page">
<main class="auth-shell"><a href="../index.php" class="auth-logo"><img src="../logo_fondation.png" alt="<?= e(SITE_NAME) ?>"></a><div class="auth-card">
    <?php if ($created): ?><div class="auth-icon"><i class="bi bi-shield-check"></i></div><span class="eyebrow">CONFIGURATION TERMINÉE</span><h1>Accès administrateur créé</h1><p>Retirez ADMIN_SETUP_KEY et ADMIN_INITIAL_PASSWORD de la configuration privée. Le mot de passe a été enregistré sous forme de hash.</p><a class="button button-navy button-full" href="login.php">Accéder à l’administration <i class="bi bi-arrow-right"></i></a>
    <?php else: ?><div class="auth-icon"><i class="bi bi-person-lock"></i></div><span class="eyebrow"><?= $en ? 'FIRST-TIME SETUP' : 'PREMIÈRE CONFIGURATION' ?></span><h1><?= $en ? 'Create the administrator account.' : 'Créer l’accès administrateur.' ?></h1><p><?= $useConfiguredCredentials
        ? ($en ? 'The account will use the private credentials configured for this deployment.' : 'Le compte utilisera les identifiants privés configurés pour cette installation.')
        : ($en ? 'Use the private setup key and choose a password with at least 12 characters.' : 'Utilisez la clé privée de configuration et choisissez un mot de passe d’au moins 12 caractères.') ?></p>
        <?php if ($error): ?><div class="error-banner" role="alert"><i class="bi bi-exclamation-circle"></i><?= e($error) ?></div><?php endif; ?>
        <form method="post" class="form-stack"><?= csrf_field() ?><div class="form-group"><label for="setup_key"><?= $en ? 'Private setup key' : 'Clé privée de configuration' ?></label><input id="setup_key" name="setup_key" type="password" autocomplete="off" required></div>
            <?php if ($useConfiguredCredentials): ?>
                <p><?= $en ? 'The first administrator will use the private email and password configured for this deployment.' : 'Le premier compte administrateur utilisera l’e-mail et le mot de passe privés configurés pour cette installation.' ?></p>
            <?php else: ?>
                <div class="form-group"><label for="email"><?= $en ? 'Administrator email' : 'E-mail administrateur' ?></label><input id="email" name="email" type="email" maxlength="190" autocomplete="email" required value="<?= e($email) ?>"></div><div class="form-group"><label for="password"><?= $en ? 'New password (12+ characters)' : 'Nouveau mot de passe (12 caractères minimum)' ?></label><input id="password" name="password" type="password" minlength="12" autocomplete="new-password" required></div><div class="form-group"><label for="password_confirm"><?= $en ? 'Confirm password' : 'Confirmer le mot de passe' ?></label><input id="password_confirm" name="password_confirm" type="password" minlength="12" autocomplete="new-password" required></div>
            <?php endif; ?>
            <button class="button button-red button-full" type="submit"><?= $en ? 'Create administrator account' : 'Créer l’accès administrateur' ?> <i class="bi bi-arrow-right"></i></button></form>
    <?php endif; ?>
</div><a class="auth-return" href="../index.php"><i class="bi bi-arrow-left"></i> <?= $en ? 'Return to the website' : 'Retour au site' ?></a></main></body></html>
