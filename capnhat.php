<?php
require_once 'utils.php';

$link = db_connect();
$error = '';
$success = '';

// danh sách phòng ban để liệt kê / chọn
$departments = [];
$qr = mysqli_query($link, "SELECT IDPB, TenPB FROM phongban ORDER BY TenPB");
if ($qr) {
    while ($r = mysqli_fetch_assoc($qr)) $departments[] = $r;
    mysqli_free_result($qr);
}

// xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idpb = trim($_POST['IDPB'] ?? '');
    $tenpb = trim($_POST['TenPB'] ?? '');
    $moTa = trim($_POST['MoTa'] ?? '');

    if ($idpb === '' || $tenpb === '') {
        $error = 'Vui lòng chọn phòng ban và nhập tên phòng.';
    } else {
        $sql = "UPDATE phongban SET TenPB = ?, MoTa =? WHERE IDPB = ?";
        $stmt = mysqli_prepare($link, $sql);
        if (!$stmt) {
            $error = 'Lỗi chuẩn bị truy vấn: ' . mysqli_error($link);
        } else {
            mysqli_stmt_bind_param($stmt, 'sss', $tenpb, $idpb, $moTa);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) >= 0) {
                    $success = 'Cập nhật thành công.';
                    // làm mới danh sách để hiển thị tên mới
                    $departments = [];
                    $qr2 = mysqli_query($link, "SELECT * FROM phongban ORDER BY TenPB");
                    if ($qr2) {
                        while ($r = mysqli_fetch_assoc($qr2)) $departments[] = $r;
                        mysqli_free_result($qr2);
                    }
                } else {
                    $error = 'Không có thay đổi nào.';
                }
            } else {
                $error = 'Lỗi khi thực thi: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// nếu GET có id thì lấy thông tin để hiện form
$edit = null;
if (isset($_GET['id'])) {
    $id = trim($_GET['id']);
    if ($id !== '') {
        $stmt = mysqli_prepare($link, "SELECT * FROM phongban WHERE IDPB = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $edit = $res ? mysqli_fetch_assoc($res) : null;
            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($link);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Cập nhật phòng ban</title></head>
<body>
<h1>Cập nhật phòng ban</h1>

<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

<h2>Danh sách phòng ban</h2>
<ul>
    <?php foreach ($departments as $d): ?>
        <li>
            <?= htmlspecialchars($d['TenPB'], ENT_QUOTES, 'UTF-8') ?> 
            (<?= htmlspecialchars($d['IDPB'], ENT_QUOTES, 'UTF-8') ?>)
            - <a href="?id=<?= urlencode($d['IDPB']) ?>">Sửa</a>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($edit): ?>
    <h2>Sửa phòng ban: <?= htmlspecialchars($edit['IDPB'], ENT_QUOTES, 'UTF-8') ?></h2>
    <form method="post" action="">
        <input type="hidden" name="IDPB" value="<?= htmlspecialchars($edit['IDPB'], ENT_QUOTES, 'UTF-8') ?>">
        <label>Tên phòng:
            <input type="text" name="TenPB" value="<?= htmlspecialchars($edit['TenPB'], ENT_QUOTES, 'UTF-8') ?>" required>
        </label>
        <label>Mo ta:
            <input type="text" name="MoTa" value="<?= htmlspecialchars($edit['MoTa'], ENT_QUOTES, 'UTF-8') ?>" required>
        </label>
        <button type="submit">Cập nhật</button>
    </form>
<?php else: ?>
    <p>Chọn "Sửa" bên trên để cập nhật phòng ban.</p>
<?php endif; ?>

<p><a href="index.php">Quay lại</a></p>
</body>
</html>