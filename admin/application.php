<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) redirect('applications.php');
$statuses = ['new','received','verification','information','review','shortlisted','approved','rejected','support','closed'];
$error = '';
try {
    $pdo = db();
    $fetch = $pdo->prepare('SELECT * FROM applications WHERE id = ? LIMIT 1');
    $fetch->execute([$id]);
    $application = $fetch->fetch();
} catch (Throwable) {
    $application = null;
    $error = 'Impossible de charger ce dossier. Vérifiez la connexion MySQL.';
}
if (!$application) {
    http_response_code(404);
    if ($error === '') $error = 'Ce dossier est introuvable.';
}
if ($application && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $newStatus = (string)($_POST['status'] ?? $application['status']);
    $newNotes = mb_substr(trim((string)($_POST['internal_notes'] ?? '')), 0, 10000);
    if (!in_array($newStatus, $statuses, true)) {
        $error = 'Choisissez un statut valide.';
    } else {
        try {
            $pdo->beginTransaction();
            $save = $pdo->prepare('UPDATE applications SET status = ?, internal_notes = ? WHERE id = ?');
            $save->execute([$newStatus, $newNotes !== '' ? $newNotes : null, $id]);
            if ($newStatus !== $application['status']) {
                $history = $pdo->prepare('INSERT INTO application_history (application_id,admin_id,event_type,old_value,new_value,description) VALUES (?,?, "status", ?, ?, ?)');
                $history->execute([$id, $_SESSION['admin_id'], $application['status'], $newStatus, 'Statut modifié par un administrateur']);
            }
            if ($newNotes !== (string)($application['internal_notes'] ?? '')) {
                $history = $pdo->prepare('INSERT INTO application_history (application_id,admin_id,event_type,description) VALUES (?,?, "note", "Notes internes mises à jour")');
                $history->execute([$id, $_SESSION['admin_id']]);
            }
            $pdo->commit();
            flash('success', 'Le dossier a été mis à jour.');
            redirect('application.php?id=' . $id);
        } catch (Throwable) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            $error = 'La mise à jour n’a pas abouti. Vérifiez la connexion et réessayez.';
        }
    }
}
$historyRows = [];
if ($application) {
    try {
        $query = db()->prepare('SELECT h.*, a.email AS admin_email FROM application_history h LEFT JOIN admins a ON a.id = h.admin_id WHERE h.application_id = ? ORDER BY h.created_at DESC');
        $query->execute([$id]);
        $historyRows = $query->fetchAll();
    } catch (Throwable) {}
}
function detail_row(string $label, mixed $value): void
{
    echo '<div class="detail-row"><span>' . e($label) . '</span><strong>' . (($value === null || $value === '') ? '—' : nl2br(e($value))) . '</strong></div>';
}
$adminTitle = $application ? 'Dossier ' . $application['reference_no'] : 'Dossier introuvable';
require __DIR__ . '/_header.php';
?>
<div class="admin-page-heading"><div><a class="back-link" href="applications.php"><i class="bi bi-arrow-left"></i> Retour aux candidatures</a><h1><?= e($application['reference_no'] ?? 'Dossier introuvable') ?></h1><p>Reçu le <?= $application ? e(date('d/m/Y à H:i', strtotime($application['created_at']))) : '' ?></p></div><?php if ($application): ?><span class="status-pill status-<?= e($application['status']) ?>"><?= e(status_label($application['status'])) ?></span><?php endif; ?></div>
<?php if ($error): ?><div class="error-banner" role="alert"><i class="bi bi-exclamation-circle"></i><?= e($error) ?></div><?php endif; ?>
<?php if ($application): $uses = json_decode((string)$application['fund_uses'], true) ?: []; ?>
<?php
$genderLabel = ['female' => 'Femme', 'male' => 'Homme', 'prefer_not_to_say' => 'Préfère ne pas répondre'];
$stageLabel = ['idea' => 'Idée', 'preparation' => 'En préparation', 'launched' => 'Déjà lancé', 'growth' => 'En développement', 'existing' => 'Entreprise existante'];
?>
<div class="application-detail-layout">
    <div class="detail-main">
        <section class="admin-panel detail-section"><div class="admin-panel-heading"><div><h2>Informations personnelles</h2><p>Coordonnées du candidat</p></div><i class="bi bi-person"></i></div><div class="detail-grid">
            <?php detail_row('Nom complet', $application['first_name'] . ' ' . $application['last_name']); detail_row('Date de naissance', $application['birth_date'] ? date('d/m/Y', strtotime($application['birth_date'])) : null); detail_row('Genre', $genderLabel[$application['gender']] ?? $application['gender']); detail_row('Nationalité', $application['nationality']); detail_row('Pays / région', implode(' · ', array_filter([$application['country'], $application['region']]))); detail_row('Ville / quartier', implode(' · ', array_filter([$application['city'], $application['neighborhood']]))); detail_row('Adresse', $application['address']); detail_row('Téléphone', $application['phone']); detail_row('WhatsApp', $application['whatsapp_number']); detail_row('E-mail', $application['email']); detail_row('Situation professionnelle', $application['employment_status']); detail_row('Niveau d’études', $application['education_level']); ?>
        </div></section>
        <section class="admin-panel detail-section"><div class="admin-panel-heading"><div><h2>Activité actuelle</h2><p><?= $application['has_activity'] ? 'Informations sur l’entreprise ou l’activité existante' : 'Nouvelle activité en préparation' ?></p></div><i class="bi bi-shop"></i></div><div class="detail-grid">
            <?php detail_row('Activité existante', $application['has_activity'] ? 'Oui' : 'Non'); detail_row('Nom de l’entreprise', $application['company_name']); detail_row('Date de création', $application['company_created'] ? date('d/m/Y', strtotime($application['company_created'])) : null); detail_row('Secteur', $application['company_sector']); detail_row('Localisation', $application['company_location']); detail_row('Nombre d’employés', $application['employee_count']); detail_row('Chiffre d’affaires', $application['turnover']); detail_row('Immatriculation', $application['registration_number']); detail_row('Description', $application['company_description']); detail_row('Expérience', $application['promoter_experience']); ?>
        </div></section>
        <section class="admin-panel detail-section"><div class="admin-panel-heading"><div><h2>Projet</h2><p><?= e($application['project_name']) ?></p></div><i class="bi bi-lightbulb"></i></div><div class="detail-grid">
            <?php detail_row('Nom du projet', $application['project_name']); detail_row('Secteur / sous-secteur', implode(' · ', array_filter([$application['project_sector'], $application['project_subsector']]))); detail_row('Pays / région / ville', implode(' · ', array_filter([$application['project_country'], $application['project_region'], $application['project_city']]))); detail_row('Localisation', $application['project_location']); detail_row('Stade', $stageLabel[$application['project_stage']] ?? $application['project_stage']); detail_row('Emplois actuels', $application['current_jobs']); detail_row('Emplois à créer', $application['jobs_to_create']); detail_row('Description', $application['project_description']); detail_row('Objectif principal', $application['project_objective']); detail_row('Problème traité', $application['project_problem']); detail_row('Solution proposée', $application['project_solution']); detail_row('Clientèle cible', $application['target_customers']); detail_row('Concurrents / alternatives', $application['competitors']); detail_row('Avantage concurrentiel', $application['competitive_advantage']); ?>
        </div></section>
        <section class="admin-panel detail-section"><div class="admin-panel-heading"><div><h2>Informations financières</h2><p>Budget prévisionnel</p></div><i class="bi bi-cash-coin"></i></div><div class="detail-grid">
            <?php detail_row('Montant demandé', number_format((float)$application['amount_requested'], 2, ',', ' ') . ' ' . $application['currency']); detail_row('Apport personnel', number_format((float)$application['personal_contribution'], 2, ',', ' ') . ' ' . $application['currency']); detail_row('Coût total', number_format((float)$application['total_cost'], 2, ',', ' ') . ' ' . $application['currency']); detail_row('Utilisations prévues', implode(', ', $uses)); detail_row('Explication de l’utilisation', $application['fund_use_details']); ?>
        </div></section>
        <section class="admin-panel detail-section"><div class="admin-panel-heading"><div><h2>Historique du dossier</h2><p>Actions administratives enregistrées</p></div><i class="bi bi-clock-history"></i></div>
            <?php if ($historyRows): ?><ol class="admin-history"><?php foreach ($historyRows as $event): ?><li><span class="history-dot"></span><div><strong><?= e(match ($event['event_type']) {'created' => 'Candidature créée','status' => 'Statut modifié','note' => 'Notes internes mises à jour',default => 'Mise à jour du dossier'}) ?></strong><?php if ($event['event_type'] === 'status'): ?><p><?= e(status_label($event['old_value'] ?? '')) ?> <i class="bi bi-arrow-right"></i> <?= e(status_label($event['new_value'] ?? '')) ?></p><?php endif; ?><small><?= e($event['admin_email'] ?: 'Candidat') ?> · <?= e(date('d/m/Y à H:i', strtotime($event['created_at']))) ?></small></div></li><?php endforeach; ?></ol><?php else: ?><div class="admin-empty"><p>Aucun événement à afficher.</p></div><?php endif; ?>
        </section>
    </div>
    <aside class="admin-panel detail-control"><div class="admin-panel-heading"><div><h2>Gestion du dossier</h2><p>Les notes internes ne sont jamais visibles par le candidat.</p></div><i class="bi bi-sliders"></i></div>
        <form method="post" action="application.php?id=<?= (int)$id ?>" class="form-stack"><?= csrf_field() ?><div class="form-group"><label for="status">Statut du dossier</label><select id="status" name="status"><?php foreach ($statuses as $option): ?><option value="<?= e($option) ?>" <?= $application['status'] === $option ? 'selected' : '' ?>><?= e(status_label($option)) ?></option><?php endforeach; ?></select></div><div class="form-group"><label for="internal_notes">Notes internes</label><textarea id="internal_notes" name="internal_notes" rows="8" maxlength="10000" placeholder="Réservé aux administrateurs…"><?= e($application['internal_notes']) ?></textarea></div><button class="button button-navy button-full" type="submit"><i class="bi bi-save"></i> Enregistrer les changements</button></form>
        <div class="detail-last-update"><i class="bi bi-arrow-repeat"></i> Dernière modification : <strong><?= e(date('d/m/Y à H:i', strtotime($application['updated_at']))) ?></strong></div>
    </aside>
</div>
<?php endif; ?>
<?php require __DIR__ . '/_footer.php'; ?>
