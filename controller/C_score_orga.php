<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

$teams = getTeamsWithScores();

require_once '../view/score_orga.php';
?>