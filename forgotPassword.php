<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include 'inc/header.php';
include 'inc/topbar.php';

$mail = new PHPMailer(true);

// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Port = 587;
$mail->Username = "information@bennit.ai";
$mail->Password = "oefjnczvdkdjkeii";

$mail->isHtml(true);



if (isset($_POST) && $_POST['useremail'] != "") {
    // print_r($_POST['useremail']);
    $useremail = $_POST['useremail'];
    if($users->checkEmailExists($_POST['useremail'])){
        $user_public_id = $users->getUserPublicIdByEmail($useremail);
        $user_public_id = $user_public_id->public_id;
        $token = bin2hex(random_bytes(16));
        $users->updateOrCreateTokenByEmail($token,$useremail);
     try {
        $mail->setFrom("noreply@gmail.com");
        $mail->addAddress($_POST['useremail']);
        $mail->Subject = "Bennit.ai Password Reset";
        $mail->Body =             <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Password Reset</title>
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
                    <h1>Password Reset</h1>
                    <p>Click <a href="https://exchange.bennit.ai/changePassword.php?token={$token}&&userid={$user_public_id}">here</a> to reset your password.</p>
                </div>
            </body>
            </html>
            HTML;
       $mail->send();
       echo createUserMessage("success","Email to reset password with linke has been sent");
    } catch (Exception $e) {

        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";

    }
    }else{
        echo createUserMessage("error","Email is not registred with bennit");
    }
    
}

?>
<div class="" style="width: 100vw;height:90vh; display: flex;justify-content: center; align-items: center;">
    <div style="width:450px; margin:0px auto">

        <form id="sendpasswordresetlink" method="POST" action="">
            <div class="form-group">
                <label for="email" class="inter-font font-20 font-700">Enter Email address to reset password</label>
                <input type="email" id="useremail" name="useremail" class="form-control" minlength="5" required>
            </div>
            <button type="submit" class="inter-font font-16 font-700" style="border: none; border-radius: 4px; width:134px ;height: 40px; padding: 8px 12px 8px 12px; background-color: #F5A800;">Send</button>
        </form>
    </div>
</div>

</body>

</html>