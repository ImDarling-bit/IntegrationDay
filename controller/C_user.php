<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('user');

$teams = getTeamsWithScores();

require_once '../view/user.php';
require_once '../view/score.php';
require_once '../view/deco.php';
require_once '../view/footer.php';
?>