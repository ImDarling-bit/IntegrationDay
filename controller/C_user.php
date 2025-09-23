<?php
session_start();
require_once '../model/config.php';
require_once '../model/function.php';

requireRole('user');
$teams = getTeamsWithScores();
if ( isFreezed($_SESSION['user_team_id']) == false ) {
    require_once '../view/user.php';
    require_once '../view/score.php';
    
}
else {
    require_once '../view/ghost.php';
}
require_once '../view/deco.php';

//echo "<br/><br/>Equipe : ".$_SESSION['user_team_id'];

require_once '../view/footer.php';

?>