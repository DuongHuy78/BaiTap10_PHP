<?php
require_once 'utils.php';
// Kết nối DB
$link = db_connect();

//isset được kiểm tra biến được khai báo có null không
$idpb = $_GET['idpb'] ?? null;
$department_name = '';

// Truy vấn dữ liệu
if ($idpb) {
    // Lấy tên phòng ban để hiển thị
    $stmt_pb = mysqli_prepare($link, "SELECT TenPB FROM phongban WHERE IDPB = ?");
    mysqli_stmt_bind_param($stmt_pb, 's', $idpb);
    mysqli_stmt_execute($stmt_pb);
    $res_pb = mysqli_stmt_get_result($stmt_pb);
    if ($row_pb = mysqli_fetch_assoc($res_pb)) {
        $department_name = $row_pb['TenPB'];
    }
    mysqli_stmt_close($stmt_pb);

    // Lấy danh sách nhân viên theo phòng ban
    $sql = "SELECT * FROM nhanvien WHERE IDPB = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 's', $idpb);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Lấy tất cả nhân viên nếu không có IDPB
    $sql = "SELECT * FROM nhanvien";
    $result = mysqli_query($link, $sql);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Nhân Viên</title>
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
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Danh Sách Nhân Viên</h1>
        <?php if ($idpb && $department_name): ?>
            <p class="text-lg text-gray-600 mb-6">Phòng ban: <span class="font-semibold text-blue-600"><?= htmlspecialchars($department_name) ?></span></p>
        <?php else: ?>
            <p class="text-lg text-gray-600 mb-6">Hiển thị tất cả nhân viên trong hệ thống.</p>
        <?php endif; ?>

        <!-- Khu vực hiển thị kết quả -->
        <div class="overflow-x-auto">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
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
                    <p class="text-gray-600">Không có dữ liệu nhân viên nào để hiển thị.</p>
                    <?php if ($idpb): ?>
                        <p class="text-sm text-gray-500 mt-2">Phòng ban này có thể chưa có nhân viên.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php mysqli_close($link); ?>

</body>
</html>
