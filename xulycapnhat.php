<?php
require_once 'utils.php';

$idpb = trim($_REQUEST['IDPB']);
$tenpb = trim($_REQUEST['txtTenpb'] ?? '');
$mota  = trim($_REQUEST['txtMota'] ?? '');

if ($idpb === '') {
    header('Location: capnhat.php');
    exit;
}

$link = db_connect();

$sql = "UPDATE phongban SET TenPB = ?, MoTa = ? WHERE IDPB = ?";
$stmt = mysqli_prepare($link, $sql);
if (!$stmt) {
    mysqli_close($link);
    header('Location: capnhat.php');
    exit;
}

mysqli_stmt_bind_param($stmt, 'sss', $tenpb, $mota, $idpb);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);

header('Location: capnhat.php');
exit;
?>