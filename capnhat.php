<?php
require_once 'utils.php';

$link = db_connect();

// danh sách phòng ban để liệt kê / chọn
$qr = mysqli_query($link, "SELECT * FROM phongban ORDER BY TenPB");
if ($qr) {
    echo '<table border="1" width="100%">';
    echo '<caption>Dữ liệu truy xuất từ bảng Phong ban</caption>';
    echo '<tr><th>IDPB</th><th>Ten </th><th>Mo ta</th><th>Hanh dong</th></tr>';

    while ($row = mysqli_fetch_assoc($qr)) {
        $maso = htmlspecialchars($row['IDPB'] ?? '');
        $ten = htmlspecialchars($row['TenPB'] ?? '');
        $mota  = htmlspecialchars($row['MoTa'] ?? '');

        echo "
        <tr>
            <td>{$maso}</td>
            <td>{$ten}</td>
            <td>{$mota}</td>
            <td>
                <a href='formcapnhat.php?idpb={$maso}'> Cap nhat</a></td>
            </td>
        </tr>";
    }

    echo '</table>';
    mysqli_free_result($qr);
} else {
    echo 'Lỗi truy vấn: ' . mysqli_error($link);
}

mysqli_close($link);
?>