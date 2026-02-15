<?php
require_once '../app/helpers/functions.php';

start_session();
admin_logout();

header('Location: login.php');
exit;
?>
