<!DOCTYPE html>
<html>
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include "inc/header.php";
include "inc/topbar.php";
$mail = new PHPMailer(true);

//$mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Port = 587;
$mail->Username = "information@bennit.ai";
$mail->Password = "oefjnczvdkdjkeii";

$mail->isHtml(true);
\Stripe\Stripe::setApiKey(STRIPE_KEY_SK_TEST);
// Retrieve the payment ID from the query parameters or wherever it's stored
$paymentId = $_GET['payment_id']; // Assuming you have retrieved it from the URL

try {
    // Retrieve the Checkout Session associated with the payment ID
    $session = \Stripe\Checkout\Session::retrieve($paymentId);
    $customerId = $session->customer;
    $customer = \Stripe\Customer::retrieve($customerId);
    $customerEmail = $customer->email;
    $subscriptions1 = \Stripe\Subscription::all(['customer' => $customerId]);
    $result = $subscriptions->storeSubscriptionData($subscriptions1, $customerEmail, $paymentId, $customerId);
    
    $end_plan = $subscriptions1->data[0]->current_period_end;
    $product_id =  $subscriptions1->data[0]->plan->product;

    $product = \Stripe\Product::retrieve($product_id);

    // Get the product name
    $productName = $product->name;
    

    //$result = $subscriptions->storeTempPayDATA($customerEmail,$paymentId);
    //print_r($result);
    //$reslt_sub = $subscriptions->storeTempPayDATA();

    try {
        $mail->setFrom("noreply@gmail.com");
        $mail->addAddress($customerEmail);
        $mail->Subject = "Bennit.ai Register user";
        $mail->Body =             <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Resigter user</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f5f5f5;
                        color: #333;
                        padding: 20px;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #fff;
                        padding: 30px;
                        background-color:  #E7E7E8;
                        border-radius: 8px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    h1 {
                        color: #007bff;
                    }
                    p {
                        font-size: 16px;
                        line-height: 1.6;
                        color:black;
                    }
                    a {
                        color: #007bff;
                        text-decoration: none;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Register to Bennit's Manufacturing Exchange</h1>
                    <p> <a href="https://exchange.bennit.ai/register.php?payment_id={$paymentId}">Click here to register.</a></p>
                </div>
            </body>
            </html>
            HTML;
        $mail->send();
        echo createUserMessage("success", "Email to reset password with linke has been sent");
    } catch (Exception $e) {

        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
} catch (Exception $e) {
    // Handle errors, such as invalid payment ID or API errors
    echo "Error: " . $e->getMessage();
}

?>
<div class="" style="width: 100vw;height:90vh; display: flex;justify-content: center; align-items: center;">

    <div class="xt-card2" >
        <div class="card-header" style="background-color: green;">
            Payment successful
        </div>
        <div class="card-body">
         <div class="inter-font font-700 font-24" style="color:#F5A800">
            Your plan : <span><?php echo $productName ?></span>
         </div>
         <div class="inter-font font-700 font-18 my-2">Plan ends on: <span><?php echo date('Y-m-d H:i:s',$end_plan) ?></span></div>
         <div class="inter-font font-700 font-15" style="color: black; width: 100%; height:60px;background-color: #F5A800;text-align: center;padding-top: 5px;">A Link to register will be sent to <?php echo $customerEmail ?>.</div>
        </div>
    </div>
</div>

</html>