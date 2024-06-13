<?php
$docRoot = "";
include_once $docRoot."config/config.php";
include_once $docRoot."inc/common.php";
include_once $docRoot."lib/Session.php";
require_once $docRoot."vendor/autoload.php";
$snowflake = new \Godruoyi\Snowflake\Snowflake;
Session::init();
spl_autoload_register(function($classes){
  global $docRoot;
  include 'classes/'.$classes.".php";
});
$users = new Users();
$subscriptions = new Subscriptions();
$organizations = new Organizations();
$opportunities = new Opportunities();
$solvers = new Solvers();
$skills = new Skills();
$matches = new Matches();
$smprofiles = new SMProfiles();
$views = new Views($organizations, $solvers, $users);
?>

