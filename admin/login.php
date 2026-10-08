<?php
require __DIR__ . '/../includes/bootstrap.php';
if (admin_logged_in()) redirect('index.php');
$en = $lang === 'en';
$title = $en ? 'Administrator sign in' : 'Connexion administrateur';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    try {
        $pdo = db();
        $sessionSecret = app_config('SESSION_SECRET');
        if ($sessionSecret === '') throw new RuntimeException('SESSION_SECRET must be configured.');
        $subjectHash = hash_hmac('sha256', (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'), $sessionSecret);
        $pdo->exec('DELETE FROM admin_login_attempts WHERE last_attempt < DATE_SUB(NOW(), INTERVAL 1 DAY)');
        $attemptQuery = $pdo->prepare('SELECT attempt_count,last_attempt FROM admin_login_attempts WHERE subject_hash = ? LIMIT 1');
        $attemptQuery->execute([$subjectHash]);
        $attempt = $attemptQuery->fetch();
        if ($attempt && (int)$attempt['attempt_count'] >= 5 && strtotime($attempt['last_attempt']) > time() - 900) {
            $error = $en ? 'Too many sign-in attempts. Please wait 15 minutes.' : 'Trop de tentatives de connexion. Attendez 15 minutes.';
        } else {
            $query = $pdo->prepare('SELECT id,email,password_hash FROM admins WHERE email = ? LIMIT 1');
            $query->execute([$email]);
            $admin = $query->fetch();
            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int)$admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_last_activity'] = time();
                $pdo->prepare('DELETE FROM admin_login_attempts WHERE subject_hash = ?')->execute([$subjectHash]);
                $pdo->prepare('UPDATE admins SET last_login_at = NOW() WHERE id = ?')->execute([$admin['id']]);
                redirect('index.php');
            }
            $failed = $pdo->prepare('INSERT INTO admin_login_attempts (subject_hash,attempt_count,last_attempt) VALUES (?,1,NOW()) ON DUPLICATE KEY UPDATE attempt_count=IF(last_attempt < DATE_SUB(NOW(), INTERVAL 15 MINUTE),1,attempt_count+1),last_attempt=NOW()');
            $failed->execute([$subjectHash]);
            $error = $en ? 'The email or password is incorrect.' : 'L’adresse e-mail ou le mot de passe est incorrect.';
        }
    } catch (Throwable) {
        $error = $en
            ? 'Sign-in is unavailable. Configure MySQL and a private SESSION_SECRET, then import database/schema.sql.'
            : 'La connexion est indisponible. Configurez MySQL et une SESSION_SECRET privée, puis importez database/schema.sql.';
    }
}
?><!doctype html><html lang="<?= e($lang) ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?= e($title) ?></title><link rel="icon" type="image/svg+xml" href="../favicon.svg"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="../assets/css/site.css"></head><body class="auth-page">
<main class="auth-shell"><a href="../index.php" class="auth-logo"><img src="../logo_fondation.png" alt="<?= e(SITE_NAME) ?>"></a><div class="auth-card"><div class="auth-icon"><i class="bi bi-shield-lock"></i></div><span class="eyebrow">ESPACE SÉCURISÉ</span><h1><?= $en ? 'Welcome back.' : 'Bienvenue.' ?></h1><p><?= $en ? 'Sign in to manage applications and programme content.' : 'Connectez-vous pour gérer les candidatures et les contenus du programme.' ?></p>
    <?php if ($error): ?><div class="error-banner" role="alert"><i class="bi bi-exclamation-circle"></i><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="login.php" class="form-stack"><?= csrf_field() ?><div class="form-group"><label for="email"><?= $en ? 'Email address' : 'Adresse e-mail' ?></label><input id="email" name="email" type="email" maxlength="190" autocomplete="username" required value="<?= e($_POST['email'] ?? '') ?>"></div><div class="form-group"><label for="password"><?= $en ? 'Password' : 'Mot de passe' ?></label><input id="password" name="password" type="password" autocomplete="current-password" required></div><button class="button button-red button-full" type="submit"><?= $en ? 'Sign in' : 'Se connecter' ?> <i class="bi bi-arrow-right"></i></button></form>
    <?php if (app_config('ADMIN_SETUP_KEY') !== ''): ?><a class="auth-secondary-link" href="setup.php"><?= $en ? 'Set up the first administrator' : 'Créer le premier accès administrateur' ?></a><?php endif; ?>
</div><a class="auth-return" href="../index.php"><i class="bi bi-arrow-left"></i> <?= $en ? 'Return to the website' : 'Retour au site' ?></a></main></body></html>
