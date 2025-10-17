<?php
require_once 'utils.php';


$link = db_connect();

// Lấy danh sách phòng ban
$departments = [];
$deptRes = mysqli_query($link, "SELECT IDPB, TenPB FROM phongban ORDER BY TenPB");
if ($deptRes) {
    while ($r = mysqli_fetch_assoc($deptRes)) {
        $departments[] = $r;
    }
    mysqli_free_result($deptRes);
}

// Khởi tạo các biến
$idnv = trim($_REQUEST["IDNV"] ?? '');
$hoTen = trim($_REQUEST['HoTen'] ?? '');
$idpb = trim($_REQUEST['IDPB'] ?? '');
$diaChi = trim($_REQUEST['DiaChi'] ?? '');

// Xử lý khi form được gửi đi bằng phương thức GET và có dữ liệu
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_REQUEST['IDNV'])) {
    if ($idnv === '' || $hoTen === '' || $idpb === '') {
        $error = 'Vui lòng nhập đầy đủ IDNV, Họ Tên và chọn Phòng Ban.';
    } else {
        $sql = "INSERT INTO nhanvien (IDNV, HoTen, IDPB, DiaChi) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $idnv, $hoTen, $idpb, $diaChi);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $success_message = "Thêm thành công nhân viên '{$hoTen}'!";
            // Xóa dữ liệu cũ trên form sau khi thêm thành công
            $idnv = $hoTen = $idpb = $diaChi = '';
        } else {
            $error = "Có lỗi xảy ra, không thể thêm nhân viên.";
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Nhân Viên</title>
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
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-12">

    <div class="w-full max-w-lg p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <!-- Tiêu đề của form -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800">Thêm Nhân Viên Mới</h1>
            <p class="mt-2 text-gray-500">Điền thông tin vào biểu mẫu bên dưới.</p>
        </div>

        <!-- Hiển thị thông báo lỗi hoặc thành công -->
        <?php if (isset($error)): ?>
            <div class="p-4 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="p-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <!-- Form thêm nhân viên -->
        <form action="" method="get" class="space-y-4">
            <!-- IDNV -->
            <div>
                <label for="idnv" class="block text-sm font-medium text-gray-700">Mã nhân viên (IDNV)</label>
                <input type="text" id="idnv" name="IDNV" value="<?= htmlspecialchars($idnv, ENT_QUOTES) ?>" required placeholder="VD: NV01"
                       class="mt-1 block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                <span id="idnv-feedback" class="text-xs mt-1"></span>
            </div>

            <!-- Họ Tên -->
            <div>
                <label for="HoTen" class="block text-sm font-medium text-gray-700">Họ và Tên</label>
                <input type="text" id="HoTen" name="HoTen" value="<?= htmlspecialchars($hoTen, ENT_QUOTES, 'UTF-8')?>" placeholder="Nhập họ và tên" required
                       class="mt-1 block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
            </div>

            <!-- Phòng Ban -->
            <div>
                <label for="IDPB" class="block text-sm font-medium text-gray-700">Phòng Ban</label>
                <select id="IDPB" name="IDPB" required
                        class="mt-1 block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                    <option value="">-- Chọn phòng ban --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= htmlspecialchars($d['IDPB'], ENT_QUOTES, 'UTF-8') ?>" <?= ($d['IDPB'] === $idpb) ? 'selected' : ''?>>
                            <?= htmlspecialchars($d['TenPB'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Địa Chỉ -->
            <div>
                <label for="DiaChi" class="block text-sm font-medium text-gray-700">Địa Chỉ</label>
                <input type="text" id="DiaChi" name="DiaChi" value="<?= htmlspecialchars($diaChi, ENT_QUOTES, 'UTF-8')?>" placeholder="Nhập địa chỉ"
                       class="mt-1 block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
            </div>

            <!-- Nút Thêm -->
            <div>
                <button id="submitBtn" type="submit"
                        class="w-full mt-4 px-4 py-2.5 text-white font-semibold bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition duration-300 ease-in-out">
                    Thêm Nhân Viên
                </button>
            </div>
        </form>

    </div>

<script>
function kiemTraIDNV() {
    const idInput = document.getElementById('idnv');
    const feedback = document.getElementById('idnv-feedback');
    const submitBtn = document.getElementById('submitBtn');
    let timer = null;
    let lastChecked='';

    function setInvalid(msg){
        feedback.textContent = msg;
        feedback.style.color = 'red';
        submitBtn.disabled = true;
    }
    function setValid(msg){
        feedback.textContent = msg;
        feedback.style.color = 'green';
        submitBtn.disabled = false;
    }

    function checkRemote(id){
        if (id === lastChecked) return;
        lastChecked = id;
        fetch('check_idnv.php?id=' + encodeURIComponent(id))
        .then(r => r.json())
        .then(j => {
            if (!j.ok) {
                if (j.error === 'format') setInvalid('Sai định dạng ID (VD: NV01)');
                else setInvalid('Lỗi kiểm tra');
                return;
            }
            if (j.exists) {
                setInvalid('IDNV đã tồn tại');
            } else {
                setValid('ID hợp lệ và có thể sử dụng');
            }
        })
        .catch(() => setInvalid('Không thể kết nối đến máy chủ để kiểm tra.'));
    }

    idInput.addEventListener('input', function(){
        let id = idInput.value;
        if(id == '') {
            setInvalid("Vui lòng nhập mã nhân viên.");
        }
        else {
            clearTimeout(timer);
            timer = setTimeout(() => checkRemote(id), 500);
        }
    });
}
document.addEventListener('DOMContentLoaded', kiemTraIDNV);
</script>

</body>
</html>
