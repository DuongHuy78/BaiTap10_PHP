<?php
require_once 'utils.php';
$link = db_connect();
$idpb = $_REQUEST['idpb'];

$sql = "SELECT IDPB, TenPB, MoTa FROM phongban WHERE IDPB = ? LIMIT 1";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 's', $idpb);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);


// xử lý cập nhật
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $idpb = trim($_POST['IDPB'] ?? '');
//     $tenpb = trim($_POST['TenPB'] ?? '');
//     $moTa = trim($_POST['MoTa'] ?? '');

//     if ($idpb === '' || $tenpb === '') {
//         $error = 'Vui lòng chọn phòng ban và nhập tên phòng.';
//     } else {
//         $sql = "UPDATE phongban SET TenPB = ?, MoTa =? WHERE IDPB = ?";
//         $stmt = mysqli_prepare($link, $sql);
//         if (!$stmt) {
//             $error = 'Lỗi chuẩn bị truy vấn: ' . mysqli_error($link);
//         } else {
//             mysqli_stmt_bind_param($stmt, 'sss', $tenpb, $idpb, $moTa);
//             if (mysqli_stmt_execute($stmt)) {
//                 if (mysqli_stmt_affected_rows($stmt) >= 0) {
//                     $success = 'Cập nhật thành công.';
//                     // làm mới danh sách để hiển thị tên mới
//                     $departments = [];
//                     $qr2 = mysqli_query($link, "SELECT * FROM phongban ORDER BY TenPB");
//                     if ($qr2) {
//                         while ($r = mysqli_fetch_assoc($qr2)) $departments[] = $r;
//                         mysqli_free_result($qr2);
//                     }
//                 } else {
//                     $error = 'Không có thay đổi nào.';
//                 }
//             } else {
//                 $error = 'Lỗi khi thực thi: ' . mysqli_stmt_error($stmt);
//             }
//             mysqli_stmt_close($stmt);
//         }
//     }
// }

// // nếu GET có id thì lấy thông tin để hiện form
// $edit = null;
// if (isset($_GET['id'])) {
//     $id = trim($_GET['id']);
//     if ($id !== '') {
//         $stmt = mysqli_prepare($link, "SELECT * FROM phongban WHERE IDPB = ? LIMIT 1");
//         if ($stmt) {
//             mysqli_stmt_bind_param($stmt, 's', $id);
//             mysqli_stmt_execute($stmt);
//             $res = mysqli_stmt_get_result($stmt);
//             $edit = $res ? mysqli_fetch_assoc($res) : null;
//             mysqli_stmt_close($stmt);
//         }
//     }
        
// }
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Form cập nhật phòng ban</title></head>
<body>
<?php if (!$row): ?>
    <p>Phòng ban không tồn tại. <a href="capnhat.php">Quay lại</a></p>
<?php else: ?>
    <form action="xulycapnhat.php?IDPB=<?= urlencode($row['IDPB']) ?>" method="post">
        <table width="100%" border="0">
            <tr>
                <td>Mã phòng ban</td>
                <td><input type="text" size="20" name="txtIDPB" readonly value="<?= htmlspecialchars($row['IDPB'], ENT_QUOTES, 'UTF-8') ?>"></td>
            </tr>
            <tr>
                <td>Tên phòng ban</td>
                <td><input type="text" size="50" name="txtTenpb" value="<?= htmlspecialchars($row['TenPB'], ENT_QUOTES, 'UTF-8') ?>"></td>
            </tr>
            <tr>
                <td>Mô tả</td>
                <td><input type="text" size="50" name="txtMota" value="<?= htmlspecialchars($row['MoTa'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit">Cập nhật</button>
                    <a href="capnhat.php">Hủy</a>
                </td>
            </tr>
        </table>
    </form>
<?php endif; ?>
</body>
</html>