<?php
// Cấu hình kết nối (điều chỉnh nếu cần)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '112233');
define('DB_NAME', 'lamquenphp');
define('DB_CHARSET', 'utf8');

/**
 * Trả về mysqli connection (singleton)
 * @return mysqli
 * @throws Exception nếu không kết nối được
 */
function db_connect(): mysqli {
    static $link = null;
    if ($link instanceof mysqli) {
        return $link;
    }
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$link) {
        throw new Exception('Không thể kết nối tới MySQL: ' . mysqli_connect_error());
    }
    mysqli_set_charset($link, DB_CHARSET);
    return $link;
}
?>