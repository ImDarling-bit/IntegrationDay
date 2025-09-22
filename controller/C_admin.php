<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('admin');

$teams = getAllTeams();

require_once '../view/admin.php';
require_once '../view/deco.php';
require_once '../view/footer.php';
?>