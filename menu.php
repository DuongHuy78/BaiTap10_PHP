<?php
session_start();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Chức Năng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-800 text-white h-screen p-4 flex flex-col">

    <?php if($user): ?>
        <!-- Hiển thị khi người dùng đã đăng nhập -->
        <div class="flex-grow">
            <h1 class="text-xl font-bold mb-2">Xin chào,</h1>
            <p class="text-blue-300 font-semibold mb-6"><?= htmlspecialchars($user['username']) ?></p>

            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Quản Lý</h2>
            <nav class="flex flex-col space-y-2">
                <a href="chen.php" target="T3" class="px-3 py-2 rounded-md text-gray-200 hover:bg-gray-700 transition-colors">Thêm nhân viên</a>
                <a href="capnhat.php" target="T3" class="px-3 py-2 rounded-md text-gray-200 hover:bg-gray-700 transition-colors">Cập nhật phòng ban</a>
                <a href="xoa.php" target="T3" class="px-3 py-2 rounded-md text-gray-200 hover:bg-gray-700 transition-colors">Xoá nhân viên</a>
                <a href="xoatatca.php" target="T3" class="px-3 py-2 rounded-md text-gray-200 hover:bg-gray-700 transition-colors">Xoá nhiều nhân viên</a>
            </nav>
        </div>
    <?php endif; ?>
        <div class="flex-grow">
            <hr class="border-gray-600 my-6">

            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Xem Dữ Liệu</h2>
             <nav class="flex flex-col space-y-2">
                <a href="XemNhanVien.php" target="T3" class="px-3 py-2 rounded-md text-gray-200 hover:bg-gray-700 transition-colors">Xem nhân viên</a>
                <a href="XemPhongBan.php" target="T3" class="px-3 py-2 rounded-md text-gray-200 hover:bg-gray-700 transition-colors">Xem phòng ban</a>
                <a href="timkiem.php" target="T3" class="px-3 py-2 rounded-md text-gray-200 hover:bg-gray-700 transition-colors">Tìm kiếm</a>
            </nav>
        </div>
    <?php if($user): ?>
        <!-- Nút Đăng xuất -->
        <div>
             <a href="logout.php" target="_top" class="block text-center w-full px-3 py-2 rounded-md bg-red-600 hover:bg-red-700 transition-colors">Đăng Xuất</a>
        </div>

    <?php else: ?>
        <!-- Hiển thị khi người dùng chưa đăng nhập -->
        <div class="flex flex-col items-center justify-center h-full">
            <p class="text-gray-400 mb-4">Vui lòng đăng nhập để sử dụng các chức năng.</p>
            <a href="dangNhap.php" target="_top" class="w-full text-center px-4 py-2.5 text-white font-semibold bg-blue-600 rounded-md hover:bg-blue-700">
                Đến Trang Đăng Nhập
            </a>
        </div>
    <?php endif; ?>

</body>
</html>
