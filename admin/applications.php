<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();
$adminTitle = 'Candidatures';
$statuses = ['new','received','verification','information','review','shortlisted','approved','rejected','support','closed'];
$q = trim((string)($_GET['q'] ?? ''));
$status = (string)($_GET['status'] ?? '');
$sector = trim((string)($_GET['sector'] ?? ''));
$from = (string)($_GET['from'] ?? '');
$to = (string)($_GET['to'] ?? '');
$sortOptions = [
    'newest' => 'created_at DESC',
    'oldest' => 'created_at ASC',
    'amount_high' => 'amount_requested DESC',
    'amount_low' => 'amount_requested ASC',
    'status' => 'status ASC, created_at DESC',
];
$sort = (string)($_GET['sort'] ?? 'newest');
if (!isset($sortOptions[$sort])) $sort = 'newest';
$page = max(1, min(100000, (int)($_GET['page'] ?? 1)));
$perPage = 20;
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(reference_no LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR project_name LIKE ?)';
    $like = '%' . mb_substr($q, 0, 120) . '%';
    array_push($params, $like, $like, $like, $like, $like);
}
if (in_array($status, $statuses, true)) { $where[] = 'status = ?'; $params[] = $status; }
if ($sector !== '' && mb_strlen($sector) <= 120) { $where[] = 'project_sector = ?'; $params[] = $sector; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $where[] = 'DATE(created_at) >= ?'; $params[] = $from; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $where[] = 'DATE(created_at) <= ?'; $params[] = $to; }
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
$rows = [];
$sectors = [];
$total = 0;
$dbError = '';
try {
    $pdo = db();
    $sectors = $pdo->query('SELECT DISTINCT project_sector FROM applications ORDER BY project_sector')->fetchAll(PDO::FETCH_COLUMN);
    $countQuery = $pdo->prepare('SELECT COUNT(*) FROM applications' . $whereSql);
    $countQuery->execute($params);
    $total = (int)$countQuery->fetchColumn();
    $offset = ($page - 1) * $perPage;
    $list = $pdo->prepare('SELECT id,reference_no,first_name,last_name,phone,project_name,project_sector,amount_requested,currency,status,created_at,updated_at FROM applications' . $whereSql . ' ORDER BY ' . $sortOptions[$sort] . ' LIMIT ' . $perPage . ' OFFSET ' . $offset);
    $list->execute($params);
    $rows = $list->fetchAll();
} catch (Throwable) {
    $dbError = 'Impossible de charger la liste des candidatures. Vérifiez MySQL et database/schema.sql.';
}
$totalPages = max(1, (int)ceil($total / $perPage));
$queryForExport = $_GET;
unset($queryForExport['page']);
require __DIR__ . '/_header.php';
?>
<div class="admin-page-heading"><div><span class="eyebrow">SUIVI DES DOSSIERS</span><h1>Candidatures</h1><p><?= number_format($total) ?> dossier<?= $total === 1 ? '' : 's' ?> trouvé<?= $total === 1 ? '' : 's' ?></p></div><a class="button button-outline" href="export.php?<?= e(http_build_query($queryForExport)) ?>"><i class="bi bi-download"></i> Exporter CSV</a></div>
<?php if ($dbError): ?><div class="error-banner"><i class="bi bi-exclamation-circle"></i><?= e($dbError) ?></div><?php endif; ?>
<section class="admin-panel">
    <form class="admin-filters" method="get" action="applications.php">
        <label class="filter-search"><span class="visually-hidden">Rechercher</span><i class="bi bi-search"></i><input name="q" value="<?= e($q) ?>" placeholder="N° dossier, nom, e-mail ou projet…"></label>
        <label><span class="visually-hidden">Statut</span><select name="status"><option value="">Tous les statuts</option><?php foreach ($statuses as $option): ?><option value="<?= e($option) ?>" <?= $status === $option ? 'selected' : '' ?>><?= e(status_label($option)) ?></option><?php endforeach; ?></select></label>
        <label><span class="visually-hidden">Secteur</span><select name="sector"><option value="">Tous les secteurs</option><?php foreach ($sectors as $option): ?><option value="<?= e($option) ?>" <?= $sector === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
        <label><span class="visually-hidden">Du</span><input type="date" name="from" value="<?= e($from) ?>"></label>
        <label><span class="visually-hidden">Au</span><input type="date" name="to" value="<?= e($to) ?>"></label>
        <label><span class="visually-hidden">Trier</span><select name="sort"><option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Plus récentes</option><option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Plus anciennes</option><option value="amount_high" <?= $sort === 'amount_high' ? 'selected' : '' ?>>Montant décroissant</option><option value="amount_low" <?= $sort === 'amount_low' ? 'selected' : '' ?>>Montant croissant</option><option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Par statut</option></select></label>
        <button class="button button-navy" type="submit">Filtrer <i class="bi bi-funnel"></i></button>
        <a class="button button-quiet" href="applications.php">Effacer</a>
    </form>
    <?php if ($rows): ?>
        <div class="admin-table-scroll"><table class="admin-table"><thead><tr><th>N° dossier</th><th>Candidat</th><th>Téléphone</th><th>Projet</th><th>Secteur</th><th>Montant demandé</th><th>Statut</th><th>Soumis le</th><th>Modifié le</th><th></th></tr></thead><tbody>
            <?php foreach ($rows as $row): ?><tr><td><a class="table-reference" href="application.php?id=<?= (int)$row['id'] ?>"><?= e($row['reference_no']) ?></a></td><td><?= e($row['first_name'] . ' ' . $row['last_name']) ?></td><td><?= e($row['phone']) ?></td><td><?= e($row['project_name']) ?></td><td><?= e($row['project_sector']) ?></td><td><?= e(number_format((float)$row['amount_requested'], 0, ',', ' ') . ' ' . $row['currency']) ?></td><td><span class="status-pill status-<?= e($row['status']) ?>"><?= e(status_label($row['status'])) ?></span></td><td><?= e(date('d/m/Y', strtotime($row['created_at']))) ?></td><td><?= e(date('d/m/Y', strtotime($row['updated_at']))) ?></td><td><a class="icon-action" href="application.php?id=<?= (int)$row['id'] ?>" aria-label="Ouvrir le dossier"><i class="bi bi-arrow-up-right"></i></a></td></tr><?php endforeach; ?>
        </tbody></table></div>
        <?php if ($totalPages > 1): ?><nav class="pagination" aria-label="Pagination"><?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): $pageQuery = $_GET; $pageQuery['page'] = $p; ?><a class="<?= $p === $page ? 'is-current' : '' ?>" href="?<?= e(http_build_query($pageQuery)) ?>"><?= $p ?></a><?php endfor; ?></nav><?php endif; ?>
    <?php else: ?><div class="admin-empty"><i class="bi bi-inbox"></i><p><?= $total === 0 ? 'Aucun dossier ne correspond à votre recherche.' : 'Les candidatures apparaîtront ici.' ?></p></div><?php endif; ?>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
