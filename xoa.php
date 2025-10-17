<?php
session_start();
require_once 'utils.php';

$link = db_connect();
$message = '';
$message_type = 'success'; // 'success' or 'error'

// Xử lý xóa khi POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idnv = trim($_POST['IDNV'] ?? '');

    if ($idnv === '') {
        $message = 'IDNV không hợp lệ.';
        $message_type = 'error';
    } else {
        $sql = "DELETE FROM nhanvien WHERE IDNV = ? LIMIT 1";
        $stmt = mysqli_prepare($link, $sql);
        if (!$stmt) {
            $message = 'Lỗi chuẩn bị truy vấn: ' . mysqli_error($link);
            $message_type = 'error';
        } else {
            mysqli_stmt_bind_param($stmt, 's', $idnv);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $message = "Xóa nhân viên (IDNV={$idnv}) thành công.";
                    $message_type = 'success';
                } else {
                    $message = "Không tìm thấy nhân viên với IDNV={$idnv}.";
                    $message_type = 'error';
                }
            } else {
                $message = 'Lỗi khi xóa: ' . mysqli_stmt_error($stmt);
                 $message_type = 'error';
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Tránh resubmit: chuyển hướng trở lại cùng trang (kèm message qua session)
    $_SESSION['flash'] = ['text' => $message, 'type' => $message_type];
    header('Location: xoa.php');
    exit;
}

// Hiển thị flash message
if (!empty($_SESSION['flash'])) {
    $message = $_SESSION['flash']['text'];
    $message_type = $_SESSION['flash']['type'];
    unset($_SESSION['flash']);
}

// Load danh sách nhân viên
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
    <title>Xóa Nhân Viên</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-backdrop {
            background-color: rgba(0,0,0,0.5);
        }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Xóa Nhân Viên</h1>
        <p class="text-lg text-gray-600 mb-6">Quản lý và xóa thông tin nhân viên khỏi hệ thống.</p>
        
        <?php if ($message): ?>
            <div class="<?= $message_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded-lg relative mb-6" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <?php if (empty($employees)): ?>
                <div class="text-center py-10 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                    <p class="text-gray-600">Hiện tại không có nhân viên nào trong danh sách.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã NV</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ Tên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Phòng Ban</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($employees as $e): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($e['IDNV']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($e['HoTen']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($e['IDPB']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <form method="post" action="xoa.php" class="delete-form" style="display:inline" 
                                          data-name="<?= htmlspecialchars(addslashes($e['HoTen']), ENT_QUOTES) ?>" 
                                          data-id="<?= htmlspecialchars($e['IDNV'], ENT_QUOTES) ?>">
                                        <input type="hidden" name="IDNV" value="<?= htmlspecialchars($e['IDNV']) ?>">
                                        <button type="button" class="delete-button text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-xs px-4 py-2 transition-colors">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div id="delete-modal" class="fixed inset-0 z-50 items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 modal-backdrop transition-opacity" aria-hidden="true"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full mx-4">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Xác nhận xóa nhân viên</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="modal-body">Bạn có chắc chắn muốn xóa nhân viên này không? Hành động này không thể được hoàn tác.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="confirm-delete-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                    Xóa
                </button>
                <button type="button" id="cancel-delete-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                    Hủy
                </button>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('delete-modal');
    const confirmBtn = document.getElementById('confirm-delete-btn');
    const cancelBtn = document.getElementById('cancel-delete-btn');
    const modalBody = document.getElementById('modal-body');
    let formToSubmit = null;

    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            formToSubmit = this.closest('form');
            const name = formToSubmit.dataset.name;
            const id = formToSubmit.dataset.id;
            modalBody.textContent = `Bạn có thực sự muốn xóa nhân viên "${name}" (Mã NV: ${id})? Hành động này không thể hoàn tác.`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        formToSubmit = null;
    }

    cancelBtn.addEventListener('click', closeModal);
    modal.querySelector('.modal-backdrop').addEventListener('click', closeModal);

    confirmBtn.addEventListener('click', function () {
        if (formToSubmit) {
            formToSubmit.submit();
        }
    });
});
</script>

</body>
</html>
