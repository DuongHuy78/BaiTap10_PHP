<?php
require_once 'utils.php';


$selected = $_GET['mode'] ?? 'ten'; // Mặc định tìm theo tên
$text = trim($_GET['text'] ?? '');
$result = null;
$search_performed = !empty($text);

if ($search_performed) {
    $link = db_connect();

    $sql = "";
    // Ánh xạ lựa chọn với tên cột để bảo mật và gọn gàng hơn
    $column_map = [
        'ten' => 'HoTen',
        'id' => 'IDNV',
        'diachi' => 'DiaChi'
    ];

    if (array_key_exists($selected, $column_map)) {
        $column = $column_map[$selected];
        $sql = "SELECT * FROM nhanvien WHERE {$column} LIKE ?";
        $param = '%' . $text . '%';
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Kiếm Nhân Viên</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Tìm Kiếm Nhân Viên</h1>

        <!-- Form tìm kiếm -->
        <form action="" method="get" class="mb-8">
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <!-- Vùng nhập liệu -->
                <div class="relative flex-grow w-full">
                    <input type="text" name="text" value="<?= htmlspecialchars($text, ENT_QUOTES, 'UTF-8')?>" placeholder="Nhập từ khóa tìm kiếm..."
                           class="w-full pl-4 pr-10 py-3 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                     <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                     </div>
                </div>
                 <!-- Nút tìm kiếm -->
                <button type="submit" class="w-full md:w-auto px-6 py-3 text-white font-semibold bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300">
                    Tìm
                </button>
            </div>
            
            <!-- Lựa chọn cách tìm kiếm -->
            <fieldset class="mt-4">
                <legend class="text-sm font-medium text-gray-600 mb-2">Tìm kiếm theo:</legend>
                <div class="flex flex-wrap gap-x-6 gap-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="mode" value="ten" <?= ($selected === 'ten') ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-gray-700">Tên nhân viên</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="mode" value="id" <?= ($selected === 'id') ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-gray-700">Mã nhân viên (ID)</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="mode" value="diachi" <?= ($selected === 'diachi') ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-gray-700">Địa chỉ</span>
                    </label>
                </div>
            </fieldset>
        </form>

        <!-- Khu vực hiển thị kết quả -->
        <div class="overflow-x-auto">
            <?php if ($search_performed && $result): ?>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Kết quả tìm kiếm cho: "<?= htmlspecialchars($text) ?>"</h2>
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã NV</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ Tên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Phòng Ban</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa Chỉ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($row['IDNV'] ?? '') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($row['HoTen'] ?? '') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($row['IDPB'] ?? '') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($row['DiaChi'] ?? '') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php mysqli_free_result($result); ?>
                <?php else: ?>
                    <div class="text-center py-10 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                        <p class="text-gray-600">Không tìm thấy kết quả nào phù hợp với từ khóa "<strong><?= htmlspecialchars($text) ?></strong>".</p>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng thử lại với một từ khóa khác.</p>
                    </div>
                <?php endif; ?>
            <?php elseif ($search_performed): ?>
                <p class="text-red-600">Lỗi truy vấn hoặc lựa chọn tìm kiếm không hợp lệ.</p>
            <?php else: ?>
                 <div class="text-center py-10 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                    <p class="text-gray-600">Sử dụng thanh tìm kiếm ở trên để tìm nhân viên.</p>
                </div>
            <?php endif; ?>
            <?php if (isset($link)) mysqli_close($link); ?>
        </div>

    </div>

</body>
</html>
