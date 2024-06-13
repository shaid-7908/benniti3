<?php
include 'inc/header.php';

if(isset($_POST['status']) && isset($_POST['opportunityid'])) {
$opportunities->updateOpportunityActiveStatus($_POST['status'],$_POST['opportunityid']);

}
?>