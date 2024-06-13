<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
$value_to_show = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) ) {

     
    $check_email_registered = $users->checkEmailExists($_POST['email']);
    if($check_email_registered){
        $value_to_show ='<div>Email is alreay registered</div>';
    }else{
        $check_email_sexists = $subscriptions->checkEmailinSubscriptionTbale($_POST['email']);
        if($check_email_sexists){
            $subscription_data = $subscriptions->getSubscriptionDetailsFromEmail($_POST['email']);
            
            $value_to_show = '<div class="s-r-text">You have an active subscription.</div>
                             <a href="register.php?payment_id='.$subscription_data->payment_id.'">
                              <button class="c-button-register">Click here to register</button>
                              </a>
                              ';
        }else{
            $value_to_show ='<div class="my-2 s-r-text">You dont have a subscription.</div>
                              <a href="choosePlan.php" class="my-2">
                              <button class="c-button-register">Choose plan</button>
                              </a>
            ';
        }

    }
    
    //print_r($subscription_data);
}
?>
<div class="container">
    <div class="verify-and-register-card">
      <h1>Already have a subscription !</h1>
      <form name="check-subscription" method="post" action="">
        <label>Enter your email</label>
        <input class="form-control verify-and-register-input" name="email" type='text' value="<?php echo $_POST['email'] ?>">
        <button class="my-2 v-a-button" type="submit">Check subscription</button>
      </form>

      <div>
        <?php
        echo $value_to_show;
        ?>
      </div>

 
  

    </div>
</div>
</body>
</html>