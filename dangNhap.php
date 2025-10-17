<form action="" method="get">
    <input type="text" name="username" value="<?= htmlspecialchars($_GET['username'] ?? '', ENT_QUOTES, 'UTF-8')?>" placeholder="Username">
    <input type="text" name="password" value="<?= htmlspecialchars($_GET['password'] ?? '', ENT_QUOTES, 'UTF-8')?>" placeholder="Password">
    <button type="submit">Đăng Nhập</button>
</form>
<p><a href="index.php">Quay lại</a></p>
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