<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
session_name('dangote_foundation');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'fr';

const SITE_NAME = 'Aliko Dangote Foundation';

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function t(string $key): string
{
    $dictionary = [
        'fr' => [
            'home' => 'Accueil', 'about' => 'À propos', 'program' => 'Le programme',
            'criteria' => 'Critères', 'testimonials' => 'Témoignages', 'faq' => 'FAQ',
            'contact' => 'Contact', 'apply' => 'Soumettre un projet', 'track' => 'Suivre mon dossier',
            'menu' => 'Ouvrir le menu', 'close' => 'Fermer', 'footer_intro' => 'Des initiatives locales pour une prospérité partagée.',
            'quick_links' => 'Navigation', 'legal' => 'Confidentialité', 'rights' => 'Tous droits réservés.',
            'need_db' => 'La base MySQL n’est pas encore configurée. Les pages publiques restent disponibles ; les formulaires et l’administration seront actifs après configuration.',
            'learn_more' => 'Découvrir le programme', 'send_project' => 'Soumettre mon projet',
            'not_funding' => 'Chaque candidature est étudiée selon les critères du programme. La soumission ne garantit pas l’obtention d’un financement.',
            'step' => 'Étape', 'previous' => 'Précédent', 'next' => 'Continuer', 'submit' => 'Envoyer ma candidature',
            'required' => 'Champ obligatoire', 'demo' => 'Statistiques de démonstration',
            'contact_cta' => 'Une question sur votre dossier ?', 'contact_button' => 'Contacter l’équipe',
        ],
        'en' => [
            'home' => 'Home', 'about' => 'About', 'program' => 'The programme',
            'criteria' => 'Eligibility', 'testimonials' => 'Stories', 'faq' => 'FAQ',
            'contact' => 'Contact', 'apply' => 'Submit a project', 'track' => 'Track my application',
            'menu' => 'Open menu', 'close' => 'Close', 'footer_intro' => 'Local initiatives for shared prosperity.',
            'quick_links' => 'Explore', 'legal' => 'Privacy', 'rights' => 'All rights reserved.',
            'need_db' => 'The MySQL database is not configured yet. Public pages remain available; forms and administration will work once it is configured.',
            'learn_more' => 'Discover the programme', 'send_project' => 'Submit my project',
            'not_funding' => 'Every application is reviewed against the programme criteria. Submission does not guarantee funding.',
            'step' => 'Step', 'previous' => 'Back', 'next' => 'Continue', 'submit' => 'Submit my application',
            'required' => 'Required field', 'demo' => 'Demonstration statistics',
            'contact_cta' => 'Question about your application?', 'contact_button' => 'Contact the team',
        ],
    ];
    global $lang;
    return $dictionary[$lang][$key] ?? $dictionary['fr'][$key] ?? $key;
}

function db_configured(): bool
{
    return (bool)(getenv('DB_HOST') && getenv('DB_NAME') && getenv('DB_USER'));
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    if (!db_configured()) {
        throw new RuntimeException('MySQL is not configured.');
    }
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME');
    $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD') ?: '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function setting(string $key, string $default = ''): string
{
    try {
        $query = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
        $query->execute([$key]);
        $value = $query->fetchColumn();
        return $value === false ? $default : (string)$value;
    } catch (Throwable) {
        return $default;
    }
}

function whatsapp_url(string $message = ''): ?string
{
    $number = preg_replace('/\D+/', '', setting('whatsapp', '')) ?? '';
    if ($number === '') {
        return null;
    }
    return 'https://wa.me/' . $number . ($message !== '' ? '?text=' . rawurlencode($message) : '');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf'])
        && is_string($_POST['csrf_token'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf_token']);
}

function require_csrf(): void
{
    if (!csrf_valid()) {
        http_response_code(419);
        exit('La session a expiré. Rechargez la page et réessayez.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

function current_page_url(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $query = [];
    parse_str((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_QUERY) ?? ''), $query);
    unset($query['lang']);
    $query['lang'] = ($GLOBALS['lang'] ?? 'fr') === 'fr' ? 'en' : 'fr';
    return $path . '?' . http_build_query($query);
}

function status_label(string $status): string
{
    $labels = [
        'new' => ['Nouvelle', 'New'], 'received' => ['Reçue', 'Received'],
        'verification' => ['Vérification administrative', 'Administrative review'],
        'information' => ['Informations complémentaires', 'Additional information'],
        'review' => ['En étude', 'Under review'], 'shortlisted' => ['Présélectionnée', 'Shortlisted'],
        'approved' => ['Approuvée', 'Approved'], 'rejected' => ['Refusée', 'Not selected'],
        'support' => ['Accompagnement en cours', 'Support in progress'], 'closed' => ['Clôturée', 'Closed'],
    ];
    return $labels[$status][$GLOBALS['lang'] === 'en' ? 1 : 0] ?? $status;
}

function status_progress(string $status): int
{
    return match ($status) {
        'new', 'received' => 1,
        'verification', 'information' => 2,
        'review', 'shortlisted' => 3,
        'approved', 'rejected' => 4,
        'support', 'closed' => 5,
        default => 1,
    };
}

function admin_logged_in(): bool
{
    return isset($_SESSION['admin_id'], $_SESSION['admin_last_activity'])
        && (time() - (int)$_SESSION['admin_last_activity']) <= 1800;
}

function require_admin(): void
{
    if (!admin_logged_in()) {
        unset($_SESSION['admin_id'], $_SESSION['admin_last_activity']);
        redirect('login.php');
    }
    $_SESSION['admin_last_activity'] = time();
}

function setting_defaults(): array
{
    return [
        'contact_email' => '', 'phone' => '', 'whatsapp' => '', 'address' => '',
        'facebook' => '', 'instagram' => '', 'linkedin' => '', 'website' => '',
    ];
}
