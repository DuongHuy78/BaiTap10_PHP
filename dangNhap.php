<?php
session_start();
require_once 'utils.php';

    $username = trim($_GET['username'] ?? '');
    $password = trim($_GET['password'] ?? '');

    $link = db_connect();

    $sql = "SELECT * FROM admin WHERE Username LIKE ?";
    $param = '%' . $username . '%';
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    if($user && $user['Password'] == $password) {
        $_SESSION['user'] = [ 'username' => $user['Username']];
        header('Location: index.php');
    }


?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Hệ Thống</title>
    <!-- Tích hợp Tailwind CSS -->
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
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <!-- Tiêu đề của form -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800">Đăng Nhập</h1>
            <p class="mt-2 text-gray-500">Chào mừng trở lại!</p>
        </div>

        <!-- Form đăng nhập -->
        <form action="" method="get" class="space-y-6">
            <!-- Vùng nhập Username -->
            <div>
                <label for="username" class="text-sm font-medium text-gray-700">Tên đăng nhập</label>
                <input 
                    id="username"
                    type="text" 
                    name="username" 
                    value="<?= htmlspecialchars($_GET['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="Nhập tên đăng nhập của bạn"
                    required
                    class="mt-1 w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                >
            </div>
            <!-- Vùng nhập Password -->
            <div>
                <label for="password" class="text-sm font-medium text-gray-700">Mật khẩu</label>
                <input 
                    id="password"
                    type="password" 
                    name="password" 
                    value="<?= htmlspecialchars($_GET['password'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                    placeholder="Nhập mật khẩu"
                    required
                    class="mt-1 w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300"
                >
            </div>
            
            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if (isset($error_message)): ?>
                <div class="p-3 text-sm text-red-700 bg-red-100 rounded-md" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Nút Đăng nhập -->
            <div>
                <button 
                    type="submit" 
                    class="w-full px-4 py-2.5 text-white font-semibold bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                >
                    Đăng Nhập
                </button>
            </div>
        </form>
    </div>

</body>
</html>
