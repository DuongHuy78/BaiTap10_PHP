<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Hệ Thống Quản Lý Nhân Sự</title>
</head>
<!-- 
  Bố cục frameset chia trang thành các phần:
  - T1: Header (đầu trang)
  - T2: Menu chức năng chính (bên trái)
  - T3: Nội dung chính (ở giữa)
  - T4: Thông tin phụ (bên phải)
  - T5: Footer (chân trang)
-->
<frameset rows="80,*,60" border="0">
  <frame name="T1" src="T1.php">
  <frameset cols="220,*">
    <frame name="T2" src="menu.php">
    <frame name="T3" src="T3.php">
    <frame name="T4" src="T4.php">
  </frameset>
  <frame name="T5" src="T5.htm">
</frameset>
</html>
