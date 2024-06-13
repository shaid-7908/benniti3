<!DOCTYPE html>
<html>
<?php
include 'inc/header.php';
if(isset($_POST['matchId'])) {
$matchid = $_POST['matchId']; 
$userid = Session::get('userid');
$result = $matches->approveMatchByAdmin($matchid,$userid);
echo $result;
}
?>

</html>