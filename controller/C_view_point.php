<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('user');

$teams = getTeamsWithScores();

require_once '../view/view_point.php';
require_once '../view/footer.php';
?>