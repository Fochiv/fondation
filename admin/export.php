<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin();
$statuses = ['new','received','verification','information','review','shortlisted','approved','rejected','support','closed'];
$where = [];
$params = [];
$q = mb_substr(trim((string)($_GET['q'] ?? '')), 0, 120);
if ($q !== '') {
    $where[] = '(reference_no LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR project_name LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like);
}
if (in_array($_GET['status'] ?? '', $statuses, true)) { $where[] = 'status = ?'; $params[] = $_GET['status']; }
$sector = mb_substr(trim((string)($_GET['sector'] ?? '')), 0, 120);
if ($sector !== '') { $where[] = 'project_sector = ?'; $params[] = $sector; }
foreach (['from' => '>=', 'to' => '<='] as $key => $operator) {
    $date = (string)($_GET[$key] ?? '');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) { $where[] = 'DATE(created_at) ' . $operator . ' ?'; $params[] = $date; }
}
$sql = 'SELECT reference_no,first_name,last_name,email,phone,project_name,project_sector,project_country,amount_requested,currency,status,created_at,updated_at FROM applications' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC LIMIT 10000';
try {
    $query = db()->prepare($sql);
    $query->execute($params);
    $rows = $query->fetchAll();
} catch (Throwable) {
    http_response_code(503);
    exit('Export temporairement indisponible.');
}
function csv_safe(mixed $value): string
{
    $text = (string)$value;
    return preg_match('/^[\s]*[=+\-@]/u', $text) ? "'" . $text : $text;
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="candidatures-' . date('Y-m-d') . '.csv"');
header('X-Content-Type-Options: nosniff');
$output = fopen('php://output', 'wb');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['N° dossier','Prénom','Nom','E-mail','Téléphone','Projet','Secteur','Pays','Montant demandé','Devise','Statut','Soumis le','Modifié le'], ';');
foreach ($rows as $row) {
    fputcsv($output, array_map('csv_safe', array_values($row)), ';');
}
fclose($output);
