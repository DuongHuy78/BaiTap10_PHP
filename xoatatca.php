<?php
require_once 'utils.php';

$link = db_connect();
$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validIds = $_POST['ids'] ?? [];
    if (!is_array($validIds) || count($validIds) === 0) {
        $message = 'Vui lòng chọn ít nhất một nhân viên để xóa.';
    } else {

        if (count($validIds) === 0) {
            $message = 'Không có ID hợp lệ để xóa.';
        } else {
            $stmt = mysqli_prepare($link, "DELETE FROM nhanvien WHERE IDNV = ? LIMIT 1");
            if (!$stmt) {
                $message = 'Lỗi chuẩn bị truy vấn: ' . mysqli_error($link);
            } else {
                $deleted = 0;
                foreach ($validIds as $idnv) {
                    mysqli_stmt_bind_param($stmt, 's', $idnv);
                    if (mysqli_stmt_execute($stmt)) {
                        if (mysqli_stmt_affected_rows($stmt) > 0) {
                            $deleted++;
                        } else {
                            $errors[] = "Không tìm thấy nhân viên: {$idnv}";
                        }
                    } else {
                        $errors[] = "Lỗi khi xóa {$idnv}: " . mysqli_stmt_error($stmt);
                    }
                }
                mysqli_stmt_close($stmt);
                $message = "Đã xóa {$deleted} nhân viên.";
            }
        }
    }
}

// load danh sách nhân viên để hiển thị
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
<head><meta charset="utf-8"><title>Xóa nhiều nhân viên</title>
<style>
table{border-collapse:collapse}
th,td{padding:6px;border:1px solid #ccc}
</style>
</head>
<body>
<h1>Xóa nhiều nhân viên</h1>

<?php if ($message): ?>
    <p style="color:green;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul style="color:red">
    <?php foreach ($errors as $e): ?>
        <li><?= $e ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (empty($employees)): ?>
    <p>Không có nhân viên.</p>
<?php else: ?>
    <form method="post" action="" onsubmit="return confirm('Bạn có chắc muốn xóa các nhân viên được chọn?');">
        <p><button type="submit">Xóa các mục được chọn</button></p>
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll" onclick="document.querySelectorAll('.chk').forEach(c=>c.checked=this.checked)"></th>
                    <th>IDNV</th>
                    <th>Họ tên</th>
                    <th>IDPB</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($employees as $e): ?>
                <tr>
                    <td><input type="checkbox" class="chk" name="ids[]" value="<?= htmlspecialchars($e['IDNV'], ENT_QUOTES, 'UTF-8') ?>"></td>
                    <td><?= htmlspecialchars($e['IDNV'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['HoTen'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['IDPB'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><button type="submit">Xóa các mục được chọn</button></p>
    </form>
<?php endif; ?>

<p><a href="index.php">Quay lại</a></p>
</body>
</html>