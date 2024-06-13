<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();

$solverMatches = $matches->getAllMatchDataForUserId(Session::get('userid'), $approvedOnly=false, $users, $organizations, $opportunities, true);
print_r($solverMatches);
?>