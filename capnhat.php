<?php
require_once 'utils.php';

$link = db_connect();
// Truy vấn dữ liệu
$sql = "SELECT * FROM phongban ORDER BY TenPB";
$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Phòng Ban</title>
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
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Cập Nhật Thông Tin Phòng Ban</h1>
        <p class="text-lg text-gray-600 mb-6">Chọn phòng ban bạn muốn chỉnh sửa thông tin.</p>

        <!-- Khu vực hiển thị kết quả -->
        <div class="overflow-x-auto">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Phòng Ban</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Phòng Ban</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Mô Tả</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            $idpb = htmlspecialchars($row['IDPB'] ?? '');
                            $tenpb = htmlspecialchars($row['TenPB'] ?? '');
                            $mota = htmlspecialchars($row['MoTa'] ?? '');
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $idpb ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= $tenpb ?></td>
                                <td class="px-6 py-4 text-sm text-gray-600 hidden md:table-cell"><?= $mota ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <a href="formcapnhat.php?idpb=<?= urlencode($idpb) ?>" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-xs px-4 py-2 transition-colors">
                                        Cập Nhật
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php mysqli_free_result($result); ?>
            <?php else: ?>
                <div class="text-center py-10 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                    <p class="text-gray-600">Chưa có dữ liệu phòng ban nào để hiển thị.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php mysqli_close($link); ?>

</body>
</html>
