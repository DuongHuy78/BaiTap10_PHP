<?php
session_start();
$user = $_SESSION['user'] ?? null;
if($user) {
    echo "<h1>Xin chào {$_SESSION['user']['username']}</h1>";
    echo "
    <table>
        <tr>
            <td><a href='dangNhap.php?'> Dang Nhap</a></td>
        </tr>
        <tr>
            <td><a href='chen.php?'> Them nhan vien</a></td>
        </tr>
        <tr>
            <td><a href='capnhat.php?'> Cap nhat phong ban</a></td>
        </tr>
        <tr>
            <td><a href='xoa.php?'> Xoa nhan vien</a></td>
        </tr>
        <tr>
            <td><a href='xoatatca.php?'> Xoa nhieu nhan vien</a></td>
        </tr>
    </table>
";
}

echo "
    <table>
        <tr>
            <td><a href='dangNhap.php?'> Dang Nhap</a></td>
        </tr>
        <tr>
            <td><a href='XemNhanVien.php?'> Xem nhan vien</a></td>
        </tr>
        <tr>
            <td><a href='XemPhongBan.php?'> Xem phong ban</a></td>
        </tr>
        <tr>
            <td><a href='timkiem.php?='> tim kiem</a></td>
        </tr>
    </table>
";

?>