<?php
session_start();
require_once 'model/config.php';
require_once 'model/function.php';

if (isLoggedIn()) {
    $role = getUserRole();

    switch ($role) {
        case 'admin':
            header('Location: controller/C_admin.php');
            break;
        case 'user':
            header('Location: controller/C_user.php');
            break;
        case 'organisateur':
            header('Location: controller/C_orga.php');
            break;
        default:
            header('Location: controller/C_login.php');
    }
} else {
    header('Location: controller/C_login.php');
}

exit();
?>