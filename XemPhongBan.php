<?php

require_once 'utils.php';

$link = db_connect();
// Truy vấn dữ liệu
$sql = "SELECT * FROM phongban";
$result = mysqli_query($link, $sql);

if ($result) {
    echo '<table border="1" width="100%">';
    echo '<caption>Dữ liệu truy xuất từ bảng Phong ban</caption>';
    echo '<tr><th>ID</th><th>Ten</th><th>Mo ta</th><th>hanh dong</th></tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        $maso      = htmlspecialchars($row['IDPB'] ?? '');
        $ten     = htmlspecialchars($row['TenPB'] ?? '');
        $mota  = htmlspecialchars($row['MoTa'] ?? '');

        echo "
        <tr>
            <td>{$maso}</td>
            <td>{$ten}</td>
            <td>{$mota}</td>
            <td>
                <a href='XemNhanVien.php?idpb={$maso}'> Xem nhan vien</a></td>
            </td>
        </tr>";
    }

    echo '</table>';
    mysqli_free_result($result);
} else {
    echo 'Lỗi truy vấn: ' . mysqli_error($link);
}

mysqli_close($link);
?>
<p><a href="index.php">Quay lại</a></p>