<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();
$adminTitle = 'Paramètres';
$error = '';
$keys = ['contact_email','phone','whatsapp','address','facebook','instagram','linkedin','website'];
$labels = ['contact_email'=>'E-mail officiel','phone'=>'Téléphone','whatsapp'=>'WhatsApp (indicatif pays + numéro)','address'=>'Adresse','facebook'=>'Lien Facebook','instagram'=>'Lien Instagram','linkedin'=>'Lien LinkedIn','website'=>'Site web'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $input = [];
    foreach ($keys as $key) $input[$key] = mb_substr(trim((string)($_POST[$key] ?? '')), 0, $key === 'address' ? 500 : 500);
    if ($input['contact_email'] !== '' && !filter_var($input['contact_email'], FILTER_VALIDATE_EMAIL)) $error = 'Indiquez une adresse e-mail valide.';
    if ($input['whatsapp'] !== '' && !preg_match('/^\+?[\d\s().-]{7,30}$/', $input['whatsapp'])) $error = 'Indiquez le numéro WhatsApp avec indicatif pays.';
    foreach (['facebook','instagram','linkedin','website'] as $key) {
        if ($input[$key] !== '' && (!filter_var($input[$key], FILTER_VALIDATE_URL) || !in_array(parse_url($input[$key], PHP_URL_SCHEME), ['https','http'], true))) $error = 'Les liens des réseaux sociaux doivent commencer par http:// ou https://.';
    }
    if ($error === '') {
        try {
            $save = db()->prepare('INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
            foreach ($input as $key => $value) $save->execute([$key,$value]);
            flash('success', 'Les paramètres ont été enregistrés.');
            redirect('settings.php');
        } catch (Throwable) {
            $error = 'Impossible d’enregistrer les paramètres. Vérifiez MySQL et réessayez.';
        }
    }
    $settings = $input;
} else {
    $settings = [];
    foreach ($keys as $key) $settings[$key] = setting($key);
}
require __DIR__ . '/_header.php';
?>
<div class="admin-page-heading"><div><span class="eyebrow">CONFIGURATION DU SITE</span><h1>Paramètres de contact</h1><p>Mettez à jour les coordonnées et les liens publics utilisés sur le site.</p></div></div>
<?php if ($error): ?><div class="error-banner"><i class="bi bi-exclamation-circle"></i><?= e($error) ?></div><?php endif; ?>
<section class="admin-panel settings-panel"><div class="admin-panel-heading"><div><h2>Coordonnées officielles</h2><p>Ces valeurs sont partagées entre le pied de page, la page Contact et les liens WhatsApp.</p></div><i class="bi bi-globe2"></i></div>
    <form method="post" class="form-stack"><?= csrf_field() ?>
        <div class="form-grid"><?php foreach ($keys as $key): ?><div class="form-group <?= $key === 'address' ? 'form-group-wide' : '' ?>"><label for="<?= e($key) ?>"><?= e($labels[$key]) ?></label><?php if ($key === 'address'): ?><textarea id="<?= e($key) ?>" name="<?= e($key) ?>" rows="3" maxlength="500"><?= e($settings[$key] ?? '') ?></textarea><?php else: ?><input id="<?= e($key) ?>" name="<?= e($key) ?>" maxlength="500" <?= in_array($key,['contact_email'],true) ? 'type="email"' : (in_array($key,['facebook','instagram','linkedin','website'],true) ? 'type="url"' : 'type="text"') ?> value="<?= e($settings[$key] ?? '') ?>"><?php endif; ?></div><?php endforeach; ?></div>
        <div class="settings-note"><i class="bi bi-info-circle"></i><span>Le numéro WhatsApp est stocké une seule fois dans les paramètres. Ajoutez l’indicatif pays, par exemple +237.</span></div>
        <button class="button button-red" type="submit"><i class="bi bi-save"></i> Enregistrer les paramètres</button>
    </form>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
