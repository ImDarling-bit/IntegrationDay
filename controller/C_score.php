<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';
require_once '../view/head.php';

$teams = getTeamsWithScores();

require_once '../view/score.php';
?>