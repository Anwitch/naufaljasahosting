<?php
require_once __DIR__ . '/core_config/session_mgr.php';
startAppSession();
session_destroy();
header('Location: ' . app_url('login.php'));
exit;
