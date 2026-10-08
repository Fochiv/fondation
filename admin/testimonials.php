<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();
$adminTitle = 'Témoignages';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = (string)($_POST['action'] ?? 'save');
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    try {
        $pdo = db();
        if ($action === 'delete' && $id) {
            $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
            flash('success', 'Témoignage supprimé.');
            redirect('testimonials.php');
        }
        $name = mb_substr(trim((string)($_POST['full_name'] ?? '')), 0, 190);
        $country = mb_substr(trim((string)($_POST['country'] ?? '')), 0, 100);
        $city = mb_substr(trim((string)($_POST['city'] ?? '')), 0, 100);
        $activity = mb_substr(trim((string)($_POST['activity'] ?? '')), 0, 190);
        $quote = mb_substr(trim((string)($_POST['quote'] ?? '')), 0, 5000);
        $photo = trim((string)($_POST['photo_url'] ?? ''));
        $date = trim((string)($_POST['published_at'] ?? ''));
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($name === '' || $country === '' || $quote === '') throw new InvalidArgumentException('Indiquez au minimum le nom, le pays et le témoignage.');
        if ($photo !== '' && (!filter_var($photo, FILTER_VALIDATE_URL) || !in_array(parse_url($photo, PHP_URL_SCHEME), ['https','http'], true))) throw new InvalidArgumentException('Indiquez une URL d’image HTTP ou HTTPS valide.');
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) throw new InvalidArgumentException('Vérifiez la date de publication.');
        if ($id) {
            $save = $pdo->prepare('UPDATE testimonials SET full_name=?,country=?,city=?,activity=?,quote=?,photo_url=?,is_active=?,published_at=? WHERE id=?');
            $save->execute([$name,$country,$city ?: null,$activity ?: null,$quote,$photo ?: null,$active,$date ?: null,$id]);
        } else {
            $save = $pdo->prepare('INSERT INTO testimonials (full_name,country,city,activity,quote,photo_url,is_active,published_at) VALUES (?,?,?,?,?,?,?,?)');
            $save->execute([$name,$country,$city ?: null,$activity ?: null,$quote,$photo ?: null,$active,$date ?: null]);
        }
        flash('success', 'Témoignage enregistré.');
        redirect('testimonials.php');
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (Throwable) {
        $error = 'Impossible d’enregistrer ce témoignage. Vérifiez MySQL et réessayez.';
    }
}
$editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$edit = null;
$rows = [];
try {
    $pdo = db();
    if ($editId) {
        $find = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
        $find->execute([$editId]);
        $edit = $find->fetch() ?: null;
    }
    $rows = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll();
} catch (Throwable) {
    $error = $error ?: 'Impossible de charger les témoignages. Vérifiez MySQL.';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['full_name'])) {
    $edit = array_merge($edit ?: [], $_POST);
}
require __DIR__ . '/_header.php';
?>
<div class="admin-page-heading"><div><span class="eyebrow">CONTENUS PUBLICS</span><h1>Témoignages</h1><p>Les témoignages ne sont visibles sur le site qu’après activation.</p></div></div>
<?php if ($error): ?><div class="error-banner"><i class="bi bi-exclamation-circle"></i><?= e($error) ?></div><?php endif; ?>
<div class="admin-dashboard-grid testimonial-admin-layout">
    <section class="admin-panel"><div class="admin-panel-heading"><div><h2><?= $edit ? 'Modifier le témoignage' : 'Ajouter un témoignage' ?></h2><p>Ne publiez qu’un témoignage dont la diffusion est autorisée.</p></div><i class="bi bi-chat-square-quote"></i></div>
        <form method="post" class="form-stack"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
            <div class="form-group"><label for="full_name">Nom et prénom <span>*</span></label><input id="full_name" name="full_name" maxlength="190" required value="<?= e($edit['full_name'] ?? '') ?>"></div>
            <div class="form-grid"><div class="form-group"><label for="country">Pays <span>*</span></label><input id="country" name="country" maxlength="100" required value="<?= e($edit['country'] ?? '') ?>"></div><div class="form-group"><label for="city">Ville</label><input id="city" name="city" maxlength="100" value="<?= e($edit['city'] ?? '') ?>"></div></div>
            <div class="form-group"><label for="activity">Activité</label><input id="activity" name="activity" maxlength="190" value="<?= e($edit['activity'] ?? '') ?>"></div>
            <div class="form-group"><label for="quote">Témoignage <span>*</span></label><textarea id="quote" name="quote" rows="5" maxlength="5000" required><?= e($edit['quote'] ?? '') ?></textarea></div>
            <div class="form-group"><label for="photo_url">Lien de la photo (optionnel)</label><input id="photo_url" name="photo_url" type="url" maxlength="500" value="<?= e($edit['photo_url'] ?? '') ?>"></div>
            <div class="form-group"><label for="published_at">Date</label><input id="published_at" name="published_at" type="date" value="<?= e($edit['published_at'] ?? '') ?>"></div>
            <label class="admin-check"><input type="checkbox" name="is_active" value="1" <?= !empty($edit['is_active']) ? 'checked' : '' ?>> Publier ce témoignage</label>
            <div class="form-actions"><button class="button button-red" name="action" value="save" type="submit"><?= $edit ? 'Enregistrer' : 'Ajouter' ?> <i class="bi bi-arrow-right"></i></button><?php if ($edit): ?><a class="button button-quiet" href="testimonials.php">Annuler</a><?php endif; ?></div>
        </form>
    </section>
    <section class="admin-panel"><div class="admin-panel-heading"><div><h2>Témoignages enregistrés</h2><p><?= count($rows) ?> témoignage<?= count($rows) === 1 ? '' : 's' ?></p></div><i class="bi bi-list-check"></i></div>
        <?php if ($rows): ?><div class="admin-testimonial-list"><?php foreach ($rows as $row): ?><article><div class="admin-testimonial-heading"><div><strong><?= e($row['full_name']) ?></strong><small><?= e(implode(' · ', array_filter([$row['activity'],$row['city'],$row['country']]))) ?></small></div><span class="status-pill <?= $row['is_active'] ? 'status-approved' : 'status-closed' ?>"><?= $row['is_active'] ? 'Publié' : 'Masqué' ?></span></div><p><?= e(mb_strimwidth($row['quote'], 0, 220, '…')) ?></p><div class="admin-testimonial-actions"><a href="testimonials.php?edit=<?= (int)$row['id'] ?>"><i class="bi bi-pencil"></i> Modifier</a><form method="post" onsubmit="return confirm('Supprimer définitivement ce témoignage ?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><button type="submit" name="action" value="delete"><i class="bi bi-trash"></i> Supprimer</button></form></div></article><?php endforeach; ?></div><?php else: ?><div class="admin-empty"><i class="bi bi-chat-square"></i><p>Aucun témoignage pour le moment.</p></div><?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
