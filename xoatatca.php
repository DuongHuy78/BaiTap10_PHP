<?php
session_start();
require_once 'utils.php';

$link = db_connect();
$message = '';
$message_type = 'success';
$errors = [];

// Xử lý khi có yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trường hợp xóa TẤT CẢ nhân viên
    if (isset($_POST['delete_all'])) {
        $sql = "DELETE FROM nhanvien";
        if (mysqli_query($link, $sql)) {
            $affected_rows = mysqli_affected_rows($link);
            $message = "Đã xóa thành công toàn bộ {$affected_rows} nhân viên.";
            $message_type = 'success';
        } else {
            $message = "Lỗi khi xóa tất cả nhân viên: " . mysqli_error($link);
            $message_type = 'error';
        }
    }
    // Trường hợp xóa các nhân viên được chọn
    elseif (isset($_POST['delete_selected'])) {
        $validIds = $_POST['ids'] ?? [];
        if (!is_array($validIds) || count($validIds) === 0) {
            $message = 'Vui lòng chọn ít nhất một nhân viên để xóa.';
            $message_type = 'error';
        } else {
            $stmt = mysqli_prepare($link, "DELETE FROM nhanvien WHERE IDNV = ? LIMIT 1");
            if (!$stmt) {
                $message = 'Lỗi chuẩn bị truy vấn: ' . mysqli_error($link);
                $message_type = 'error';
            } else {
                $deleted = 0;
                foreach ($validIds as $idnv) {
                    mysqli_stmt_bind_param($stmt, 's', $idnv);
                    if (mysqli_stmt_execute($stmt)) {
                        if (mysqli_stmt_affected_rows($stmt) > 0) {
                            $deleted++;
                        } else {
                            $errors[] = "Không tìm thấy nhân viên: {$idnv}";
                        }
                    } else {
                        $errors[] = "Lỗi khi xóa {$idnv}: " . mysqli_stmt_error($stmt);
                    }
                }
                mysqli_stmt_close($stmt);
                $message = "Đã xóa {$deleted} nhân viên.";
                $message_type = 'success';
            }
        }
    }

    // Lưu thông báo vào session và chuyển hướng để tránh resubmit
    $_SESSION['flash'] = ['text' => $message, 'type' => $message_type, 'errors' => $errors];
    header('Location: xoatatca.php');
    exit;
}

// Hiển thị flash message từ session
if (!empty($_SESSION['flash'])) {
    $message = $_SESSION['flash']['text'];
    $message_type = $_SESSION['flash']['type'];
    $errors = $_SESSION['flash']['errors'];
    unset($_SESSION['flash']);
}

// Tải danh sách nhân viên để hiển thị
$employees = [];
$res = mysqli_query($link, "SELECT IDNV, HoTen, IDPB FROM nhanvien ORDER BY IDNV");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $employees[] = $r;
    mysqli_free_result($res);
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Nhiều Nhân Viên</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-backdrop { background-color: rgba(0,0,0,0.5); }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Xóa Nhiều Nhân Viên</h1>
        <p class="text-lg text-gray-600 mb-6">Chọn nhân viên từ danh sách bên dưới để xóa hoặc xóa toàn bộ dữ liệu.</p>
        
        <?php if ($message): ?>
            <div class="<?= $message_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded-lg relative mb-6" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-yellow-100 border-yellow-400 text-yellow-700 border px-4 py-3 rounded-lg relative mb-6" role="alert">
                <strong class="font-bold">Một số lỗi đã xảy ra:</strong>
                <ul class="list-disc list-inside mt-2">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (empty($employees)): ?>
            <div class="text-center py-10 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                <p class="text-gray-600">Hiện tại không có nhân viên nào trong danh sách.</p>
            </div>
        <?php else: ?>
            <form method="post" id="delete-form">
                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                    <button type="submit" name="delete_selected" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Xóa các mục đã chọn
                    </button>
                    <button type="submit" name="delete_all" class="w-full sm:w-auto bg-transparent hover:bg-red-700 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded-lg transition-colors">
                        Xóa tất cả
                    </button>
                </div>
                
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-center w-12">
                                    <input type="checkbox" id="checkAll" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã NV</th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ Tên</th>
                                <th class="p-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Phòng Ban</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($employees as $e): ?>
                                <tr>
                                    <td class="p-4 text-center">
                                        <input type="checkbox" class="chk h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" name="ids[]" value="<?= htmlspecialchars($e['IDNV']) ?>">
                                    </td>
                                    <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($e['IDNV']) ?></td>
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($e['HoTen']) ?></td>
                                    <td class="p-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($e['IDPB']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Modal xác nhận -->
    <div id="confirm-modal" class="fixed inset-0 z-50 items-center justify-center hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 modal-backdrop transition-opacity" aria-hidden="true"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full mx-4">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Xác nhận hành động</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="modal-body">Nội dung xác nhận...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="confirm-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Xác nhận</button>
                <button type="button" id="cancel-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Hủy</button>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('delete-form');
    const modal = document.getElementById('confirm-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    const confirmBtn = document.getElementById('confirm-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.chk');
    let submitter = null;

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            submitter = event.submitter;

            if (submitter.name === 'delete_selected') {
                const selectedCount = document.querySelectorAll('.chk:checked').length;
                if (selectedCount === 0) {
                    modalTitle.textContent = 'Chưa chọn nhân viên';
                    modalBody.innerHTML = 'Vui lòng chọn ít nhất một nhân viên để xóa.';
                    confirmBtn.style.display = 'none'; // Ẩn nút xác nhận
                } else {
                    modalTitle.textContent = 'Xác nhận xóa nhân viên';
                    modalBody.textContent = `Bạn có chắc chắn muốn xóa ${selectedCount} nhân viên đã chọn không?`;
                    confirmBtn.style.display = 'inline-flex';
                }
            } else if (submitter.name === 'delete_all') {
                modalTitle.textContent = '!!! CẢNH BÁO !!!';
                modalBody.innerHTML = '<strong class="text-red-700">Bạn sắp xóa TOÀN BỘ nhân viên khỏi cơ sở dữ liệu.</strong><br>Hành động này không thể hoàn tác. Bạn có chắc chắn muốn tiếp tục không?';
                confirmBtn.style.display = 'inline-flex';
            }
            showModal();
        });
    }
    
    confirmBtn.addEventListener('click', function () {
        if (form && submitter) {
            // Sử dụng requestSubmit để gửi form với đúng nút đã được nhấn
            form.requestSubmit(submitter);
        }
    });

    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    cancelBtn.addEventListener('click', closeModal);
    modal.querySelector('.modal-backdrop').addEventListener('click', closeModal);

    if(checkAll) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(c => c.checked = this.checked);
        });
        checkboxes.forEach(c => {
            c.addEventListener('change', function() {
                if (!this.checked) {
                    checkAll.checked = false;
                } else if (document.querySelectorAll('.chk:checked').length === checkboxes.length) {
                    checkAll.checked = true;
                }
            });
        });
    }
});
</script>

</body>
</html>
