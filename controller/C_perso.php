<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';
require_once '../view/head.php';

requireRole('user'| 'organisateur');

$teams = getTeamsWithScores();

require_once '../view/perso.php';
require_once '../view/deco.php';
require_once '../view/footer.php';
?>