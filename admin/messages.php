<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();
$adminTitle = 'Messages de contact';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id) {
        try {
            if (($_POST['action'] ?? '') === 'delete') {
                db()->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$id]);
                flash('success', 'Message supprimé.');
            } else {
                db()->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$id]);
                flash('success', 'Message marqué comme lu.');
            }
            redirect('messages.php');
        } catch (Throwable) {
            $error = 'Impossible de mettre à jour ce message. Vérifiez MySQL.';
        }
    }
}
$messages = [];
$unread = 0;
try {
    $pdo = db();
    $messages = $pdo->query('SELECT * FROM contact_messages ORDER BY is_read ASC,created_at DESC LIMIT 100')->fetchAll();
    $unread = (int)$pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn();
} catch (Throwable) {
    $error = 'Impossible de charger les messages de contact. Vérifiez MySQL.';
}
require __DIR__ . '/_header.php';
?>
<div class="admin-page-heading"><div><span class="eyebrow">CONTACT VISITEURS</span><h1>Messages de contact</h1><p><?= $unread ?> message<?= $unread === 1 ? '' : 's' ?> non lu<?= $unread === 1 ? '' : 's' ?></p></div></div>
<?php if ($error): ?><div class="error-banner"><i class="bi bi-exclamation-circle"></i><?= e($error) ?></div><?php endif; ?>
<section class="admin-panel">
    <?php if ($messages): ?><div class="admin-message-list"><?php foreach ($messages as $message): ?><article class="<?= $message['is_read'] ? 'is-read' : 'is-unread' ?>"><div class="admin-message-heading"><div><span class="message-unread-dot"></span><strong><?= e($message['subject']) ?></strong><small><?= e($message['full_name']) ?> · <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></small></div><time><?= e(date('d/m/Y à H:i', strtotime($message['created_at']))) ?></time></div><p><?= nl2br(e($message['message'])) ?></p><div class="admin-testimonial-actions"><?php if (!$message['is_read']): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$message['id'] ?>"><button type="submit" name="action" value="read"><i class="bi bi-check2"></i> Marquer comme lu</button></form><?php endif; ?><a href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ' . $message['subject']) ?>"><i class="bi bi-reply"></i> Répondre par e-mail</a><form method="post" onsubmit="return confirm('Supprimer définitivement ce message ?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$message['id'] ?>"><button type="submit" name="action" value="delete"><i class="bi bi-trash"></i> Supprimer</button></form></div></article><?php endforeach; ?></div><?php else: ?><div class="admin-empty"><i class="bi bi-envelope-open"></i><p>Aucun message pour le moment.</p></div><?php endif; ?>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
