<?php
require_once '../app/helpers/functions.php';
require_once '../app/config/admin.php';

start_session();

$script = basename($_SERVER['SCRIPT_NAME'] ?? '');
if ($script !== 'login.php' && !is_admin_logged_in()) {
    header('Location: login.php');
    exit;
}
?>
