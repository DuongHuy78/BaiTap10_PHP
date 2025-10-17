<?php
require_once 'utils.php';
header('Content-Type: application/json; charset=utf-8');

$id = trim($_GET['id'] ?? '');
if ($id === '') {
    echo json_encode(['ok' => false, 'error' => 'empty']);
    exit;
}

if (!preg_match('/^NV\d{2,}$/i', $id)) {
    echo json_encode(['ok' => false, 'error' => 'format']);
    exit;
}

$link = db_connect();
$stmt = mysqli_prepare($link, "SELECT 1 FROM nhanvien WHERE IDNV = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$exists = $res && mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);
mysqli_close($link);

echo json_encode(['ok' => true, 'exists' => (bool)$exists]);
?>