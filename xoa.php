<?php
session_start();
require_once 'utils.php';

$link = db_connect();
$message = '';


// xử lý xóa khi POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idnv = trim($_POST['IDNV'] ?? '');

    if ($idnv === '') {
        $message = 'IDNV không hợp lệ.';
    } else {
        // dùng prepared statement để xóa
        $sql = "DELETE FROM nhanvien WHERE IDNV = ? LIMIT 1";
        $stmt = mysqli_prepare($link, $sql);
        if (!$stmt) {
            $message = 'Lỗi chuẩn bị truy vấn: ' . mysqli_error($link);
        } else {
            mysqli_stmt_bind_param($stmt, 's', $idnv);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $message = "Xóa nhân viên (IDNV={$idnv}) thành công.";
                } else {
                    $message = "Không tìm thấy nhân viên với IDNV={$idnv}.";
                }
            } else {
                // có thể do ràng buộc khóa ngoại => báo rõ
                $message = 'Lỗi khi xóa: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // tránh resubmit: chuyển hướng trở lại cùng trang (kèm message qua session)
    $_SESSION['flash'] = $message;
    header('Location: xoa.php');
    exit;
}

// hiển thị flash message
if (!empty($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// load danh sách nhân viên
$employees = [];
$res = mysqli_query($link, "SELECT IDNV, HoTen, IDPB FROM nhanvien ORDER BY IDNV");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $employees[] = $r;
    mysqli_free_result($res);
}

mysqli_close($link);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Xóa nhân viên</title></head>
<body>
<h1>Xóa nhân viên</h1>

<?php if ($message): ?>
    <p style="color:green;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (empty($employees)): ?>
    <p>Không có nhân viên.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr><th>IDNV</th><th>Họ tên</th><th>IDPB</th><th>Hành động</th></tr>
        </thead>
        <tbody>
        <?php foreach ($employees as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['IDNV'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($e['HoTen'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($e['IDPB'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <form method="post" action="" onsubmit="return confirm('Bạn có chắc muốn xóa <?= addslashes($e['HoTen']) ?> (<?= $e['IDNV'] ?>)?');" style="display:inline">
                        <input type="hidden" name="IDNV" value="<?= htmlspecialchars($e['IDNV'], ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="index.php">Quay lại</a></p>
</body>
</html>