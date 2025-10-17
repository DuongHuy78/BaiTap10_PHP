<?php
require_once 'utils.php';

// Kết nối DB
$link = db_connect();

if (!$link) {
    die('Không thể kết nối tới MySQL: ' . mysqli_connect_error());
}
mysqli_set_charset($link, 'utf8');

//isset được kiểm tra biến được khai báo có null không
if (isset($_GET['idpb'])) {
    $idpb = $_GET['idpb'];
} else {
    $idpb = null;
}

// Truy vấn dữ liệu

if($idpb != null) {
    $sql = "SELECT * FROM nhanvien WHERE IDPB = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $idpb);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
else {
    $sql = "SELECT * FROM nhanvien";
    $result = mysqli_query($link, $sql);
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

mysqli_close($link);
?>
<p><a href="index.php">Quay lại</a></p>