<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();
$adminTitle = 'Vue d’ensemble';
$dbError = '';
$counts = array_fill_keys(['total','new','review','shortlisted','approved','rejected'], 0);
$monthly = [];
$sectors = [];
$countries = [];
$finance = [];
$recent = [];
try {
    $pdo = db();
    $counts['total'] = (int)$pdo->query('SELECT COUNT(*) FROM applications')->fetchColumn();
    $countQuery = $pdo->query('SELECT status, COUNT(*) AS amount FROM applications GROUP BY status');
    foreach ($countQuery as $row) {
        if ($row['status'] === 'new') $counts['new'] += (int)$row['amount'];
        if (in_array($row['status'], ['review','verification','information'], true)) $counts['review'] += (int)$row['amount'];
        if ($row['status'] === 'shortlisted') $counts['shortlisted'] += (int)$row['amount'];
        if ($row['status'] === 'approved') $counts['approved'] += (int)$row['amount'];
        if ($row['status'] === 'rejected') $counts['rejected'] += (int)$row['amount'];
    }
    $monthly = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key, COUNT(*) AS amount FROM applications WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY month_key ORDER BY month_key")->fetchAll();
    $sectors = $pdo->query('SELECT project_sector, COUNT(*) AS amount FROM applications GROUP BY project_sector ORDER BY amount DESC LIMIT 6')->fetchAll();
    $countries = $pdo->query("SELECT CONCAT_WS(' · ', project_country, NULLIF(project_region,'')) AS region_label, COUNT(*) AS amount FROM applications GROUP BY project_country, project_region ORDER BY amount DESC LIMIT 6")->fetchAll();
    $finance = $pdo->query("SELECT currency, SUM(amount_requested) AS requested, SUM(CASE WHEN status IN ('approved','support') THEN amount_requested ELSE 0 END) AS approved FROM applications GROUP BY currency ORDER BY requested DESC")->fetchAll();
    $recent = $pdo->query('SELECT id,reference_no,first_name,last_name,project_name,project_sector,status,created_at FROM applications ORDER BY created_at DESC LIMIT 6')->fetchAll();
} catch (Throwable) {
    $dbError = 'Impossible de charger les statistiques. Configurez MySQL et importez database/schema.sql.';
}
require __DIR__ . '/_header.php';
?>
<div class="admin-page-heading"><div><span class="eyebrow">ESPACE ADMINISTRATEUR</span><h1>Vue d’ensemble</h1><p>Suivez l’activité du programme et traitez les nouveaux dossiers.</p></div><a class="button button-red" href="applications.php"><i class="bi bi-folder2-open"></i> Voir les candidatures</a></div>
<?php if ($dbError): ?><div class="error-banner"><i class="bi bi-exclamation-circle"></i><?= e($dbError) ?></div><?php endif; ?>
<div class="admin-stat-grid">
    <?php foreach ([['total','Total des candidatures','files','navy'],['new','Nouvelles candidatures','bell','red'],['review','En cours d’étude','search','blue'],['shortlisted','Présélectionnées','star','gold'],['approved','Approuvées','check2-circle','green'],['rejected','Refusées','x-circle','gray']] as [$key,$label,$icon,$color]): ?>
        <article class="admin-stat-card"><div class="admin-stat-top"><span><?= e($label) ?></span><i class="bi bi-<?= e($icon) ?> admin-stat-icon <?= e($color) ?>"></i></div><strong><?= number_format($counts[$key]) ?></strong><small> <?= $key === 'new' ? 'À traiter' : 'Au total' ?></small></article>
    <?php endforeach; ?>
</div>
<div class="admin-dashboard-grid">
    <section class="admin-panel"><div class="admin-panel-heading"><div><h2>Candidatures par mois</h2><p>Évolution des cinq derniers mois</p></div><i class="bi bi-bar-chart-line"></i></div>
        <?php if ($monthly): $maxMonth = max(array_map(static fn($m) => (int)$m['amount'], $monthly)); ?>
            <div class="bar-chart" role="img" aria-label="Candidatures mensuelles"><?php foreach ($monthly as $month): $barHeight = $maxMonth > 0 ? max(8, (int)($month['amount'] / $maxMonth * 100)) : 8; ?><div class="bar-chart-column"><span><?= (int)$month['amount'] ?></span><div class="bar-chart-track"><i style="height:<?= $barHeight ?>%"></i></div><small><?= e(date('m/y', strtotime($month['month_key'] . '-01'))) ?></small></div><?php endforeach; ?></div>
        <?php else: ?><div class="admin-empty"><i class="bi bi-bar-chart"></i><p>Les candidatures apparaîtront ici après leur réception.</p></div><?php endif; ?>
    </section>
    <section class="admin-panel"><div class="admin-panel-heading"><div><h2>Répartition par secteur</h2><p>Les six secteurs les plus représentés</p></div><i class="bi bi-pie-chart"></i></div>
        <?php if ($sectors): $maxSector = max(array_map(static fn($r) => (int)$r['amount'], $sectors)); ?>
            <div class="sector-bars"><?php foreach ($sectors as $row): ?><div class="sector-bar"><div><span><?= e($row['project_sector']) ?></span><strong><?= (int)$row['amount'] ?></strong></div><div class="sector-bar-track"><i style="width:<?= max(3,(int)($row['amount'] / $maxSector * 100)) ?>%"></i></div></div><?php endforeach; ?></div>
        <?php else: ?><div class="admin-empty"><i class="bi bi-pie-chart"></i><p>Les candidatures apparaîtront ici après leur réception.</p></div><?php endif; ?>
    </section>
    <section class="admin-panel"><div class="admin-panel-heading"><div><h2>Candidatures par pays / région</h2><p>Les six zones les plus représentées</p></div><i class="bi bi-geo-alt"></i></div>
        <?php if ($countries): $maxCountry = max(array_map(static fn($r) => (int)$r['amount'], $countries)); ?>
            <div class="sector-bars"><?php foreach ($countries as $row): ?><div class="sector-bar"><div><span><?= e($row['region_label']) ?></span><strong><?= (int)$row['amount'] ?></strong></div><div class="sector-bar-track"><i style="width:<?= max(3,(int)($row['amount'] / $maxCountry * 100)) ?>%"></i></div></div><?php endforeach; ?></div>
        <?php else: ?><div class="admin-empty"><i class="bi bi-geo-alt"></i><p>Les pays et régions apparaîtront ici après réception de candidatures.</p></div><?php endif; ?>
    </section>
    <section class="admin-panel"><div class="admin-panel-heading"><div><h2>Montants des projets</h2><p>Comparaison par devise · demandes et dossiers approuvés</p></div><i class="bi bi-cash-coin"></i></div>
        <?php if ($finance): ?><div class="finance-list"><?php foreach ($finance as $row): ?><div class="finance-row"><div><strong><?= e($row['currency']) ?></strong><span>Demandés</span><b><?= e(number_format((float)$row['requested'],0,',',' ')) ?></b></div><div><span>Approuvés</span><b><?= e(number_format((float)$row['approved'],0,',',' ')) ?></b></div></div><?php endforeach; ?></div><?php else: ?><div class="admin-empty"><i class="bi bi-cash-coin"></i><p>Les montants apparaîtront ici après réception des dossiers.</p></div><?php endif; ?>
    </section>
</div>
<section class="admin-panel admin-recent-panel"><div class="admin-panel-heading"><div><h2>Dossiers récents</h2><p>Les dernières candidatures reçues</p></div><a class="text-link" href="applications.php">Voir tout <i class="bi bi-arrow-right"></i></a></div>
    <?php if ($recent): ?><div class="admin-table-scroll"><table class="admin-table"><thead><tr><th>N° dossier</th><th>Candidat</th><th>Projet</th><th>Secteur</th><th>Statut</th><th>Date</th></tr></thead><tbody><?php foreach ($recent as $row): ?><tr><td><a class="table-reference" href="application.php?id=<?= (int)$row['id'] ?>"><?= e($row['reference_no']) ?></a></td><td><?= e($row['first_name'] . ' ' . $row['last_name']) ?></td><td><?= e($row['project_name']) ?></td><td><?= e($row['project_sector']) ?></td><td><span class="status-pill status-<?= e($row['status']) ?>"><?= e(status_label($row['status'])) ?></span></td><td><?= e(date('d/m/Y', strtotime($row['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><div class="admin-empty"><i class="bi bi-inbox"></i><p>Aucune candidature pour le moment.</p></div><?php endif; ?>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
