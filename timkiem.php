<?php
    echo '<h1>Tìm kiếm</h1>';
?>
<form action="" method="get">
    <fieldset>
        <legend>Chọn cách tìm kiếm</legend>

        <label>
            <input type="radio" name="mode" value="ten" <?= (isset($_GET['mode']) && $_GET['mode']==='ten') ? 'checked' : '' ?>>
            Theo tên
        </label>

        <label>
            <input type="radio" name="mode" value="id" <?= (isset($_GET['mode']) && $_GET['mode']==='id') ? 'checked' : '' ?>>
            Theo mã (ID)
        </label>

        <label>
            <input type="radio" name="mode" value="diachi" <?= (isset($_GET['mode']) && $_GET['mode']==='diachi') ? 'checked' : '' ?>>
            Theo địa chỉ
        </label>
    </fieldset>
    <input type="text" name="text" value="<?= htmlspecialchars($_GET['text'] ?? '', ENT_QUOTES, 'UTF-8')?>" placeholder="Nhập ">
    <button type="submit">Tìm</button>
</form>
<p><a href="index.php">Quay lại</a></p>
<?php
    require_once 'utils.php';

    $selected = $_GET['mode'] ?? null;
    $text = trim($_GET['text'] ?? '');

    $link = db_connect();
    $result;

    if($selected == 'ten') {
        $sql = "SELECT * FROM nhanvien WHERE HoTen LIKE ?";
        $param = '%' . $text . '%';
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
    else if($selected == 'id') {
        $sql = "SELECT * FROM nhanvien WHERE IDNV LIKE ?";
        $param = '%' . $text . '%';
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
    else if($selected == 'diachi') {
        $sql = "SELECT * FROM nhanvien WHERE DiaChi LIKE ?";
        $param = '%' . $text . '%';
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
    
    if ($result) {
        echo '<table border="1" width="100%">';
        echo '<caption>Dữ liệu truy xuất từ bảng Nhân viên</caption>';
        echo '<tr><th>ID</th><th>Ho ten</th><th>IDPB</th><th>Dia Chi</th></tr>';

        while ($row = mysqli_fetch_assoc($result)) {
            $maso      = htmlspecialchars($row['IDNV'] ?? '');
            $hoten     = htmlspecialchars($row['HoTen'] ?? '');
            $ngaysinh  = htmlspecialchars($row['IDPB'] ?? '');
            $nghenghiep= htmlspecialchars($row['DiaChi'] ?? '');

            echo "<tr><td>{$maso}</td><td>{$hoten}</td><td>{$ngaysinh}</td><td>{$nghenghiep}</td></tr>";
        }

        echo '</table>';
        mysqli_free_result($result);
    } else {
        echo 'Lỗi truy vấn: ' . mysqli_error($link);
    }
?>