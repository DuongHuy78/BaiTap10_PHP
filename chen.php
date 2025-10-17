
<?php
    require_once 'utils.php';
    $link = db_connect();

    $departments = [];
    $deptRes = mysqli_query($link, "SELECT IDPB, TenPB FROM phongban ORDER BY TenPB");
    if ($deptRes) {
        while ($r = mysqli_fetch_assoc($deptRes)) {
            $departments[] = $r;
        }
        mysqli_free_result($deptRes);
    }


    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //tạo id mới
        $sql = "SELECT MAX(IDNV) AS maxid FROM nhanvien";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        mysqli_stmt_close($stmt);

        $max = $row['maxid'] ?? null;
        if (!$max) {
            $newId = 'NV01';
        }
        else {
            $head = substr($max, 0, 2);
            $body = substr($max, 2,2);
            $body += 1;
            if($body <10) {
                $body = '0'.$body;
            }
            $newId = $head.$body;
        } 


        $idnv = $newId;
        $hoTen = trim($_GET['HoTen'] ?? '');
        $idpb = trim($_GET['IDPB'] ?? '');
        $diaChi = trim($_GET['DiaChi'] ?? '');
        if ($idnv === '' || $hoTen === '' || $idpb === '') {
            $error = 'Vui lòng nhập IDNV, HoTen và IDPB.';
        } else {
            $sql = "INSERT INTO nhanvien (IDNV, HoTen, IDPB, DiaChi) VALUES (?,?,?,?)";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, 'ssss', $idnv, $hoTen, $idpb, $diaChi);
            mysqli_stmt_execute($stmt);
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo"Thanh công thêm {$hoTen}";
            }
            mysqli_stmt_close($stmt);
        }
    }
?>

<form action="" method="get">
    <input type="text" name="HoTen" value="<?= htmlspecialchars($_GET['HoTen'] ?? '', ENT_QUOTES, 'UTF-8')?>" placeholder="HoTen">
    <label>IDPB:
        <select name="IDPB">
            <option value="">-- Chọn phòng ban --</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= htmlspecialchars($d['IDPB'], ENT_QUOTES, 'UTF-8') ?>" <?= ($d['IDPB'] === $idpb) ? 'selected' : ''?>>
                    <?= htmlspecialchars($d['TenPB'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($d['IDPB'], ENT_QUOTES, 'UTF-8') ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <input type="text" name="DiaChi" value="<?= htmlspecialchars($_GET['DiaChi'] ?? '', ENT_QUOTES, 'UTF-8')?>" placeholder="DiaChi">
    <button type="submit">Thêm</button>
    <p><a href="index.php">Quay lại</a></p>
</form>