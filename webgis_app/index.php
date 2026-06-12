<?php
require_once __DIR__ . '/core_config/session_mgr.php';
startAppSession();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: panel_admin/index.php');
    } else {
        header('Location: panel_user/index.php');
    }
    exit;
}
header('Location: login.php');
exit;
