<?php
include 'inc/header.php';


if(isset($_POST['payment_id']) && isset($_POST['email'])) {
    // Retrieve payment ID and email from the POST data
    $paymentId = $_POST['payment_id'];
    $email = $_POST['email'];
    $result = $subscriptions->storeTempPayDATA($email,$paymentId);

}
?>