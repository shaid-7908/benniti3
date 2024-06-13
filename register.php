<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
\Stripe\Stripe::setApiKey(STRIPE_KEY_SK_TEST);
// Retrieve the payment ID from the query parameters or wherever it's stored
$paymentId = $_GET['payment_id'];
$already_login = Session::get('userid');
try {
  $session = \Stripe\Checkout\Session::retrieve($paymentId);

  // Get the customer's email from the Checkout Session
  $customerEmail = $session->customer_email;
} catch (Exception $e) {
  // Handle errors, such as invalid payment ID or API errors
  echo "Error: " . $e->getMessage();
}
$isHuman = false;

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['register'])) {
  if (RECAPTCHA_KEY != "") {
    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = "Put your secret key here";
    $ip = $_SERVER['REMOTE_ADDR'];
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode(RECAPTCHA_KEY) .  '&response=' . urlencode($captcha);
    $response = file_get_contents($url);
    $responseKeys = json_decode($response, true);
    if ($responseKeys["success"]) {
      $isHuman = true;
    }
  } else {
    $isHuman = true;
  }
  if ($isHuman) {
    $register = $users->createUser($_POST, $paymentId, FALSE);
    if (isset($register)) {
      echo $register;
    }
  } else {
    echo createUserMessage("error", "Captcha failed. Please prove you are human by solving the captcha. If you have Javascript disabled, you'll need to enable it.");
  }
}

?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
  function checkFormValidAndSubmit() {
    <?php
    if (RECAPTCHA_KEY != "") {
    ?>
      if (grecaptcha.getResponse() == "" || grecaptcha.getResponse() == null || grecaptcha.getResponse() == undefined) {
        alert("Please prove you're human. Registration requires completing the Captcha.");
        return false;
      }
    <?php
    }
    ?>
    if (document.getElementById("password").value != document.getElementById("repeat_password").value) {
      alert("New password does not match, cannot set password!");
      return false;
    }
    if (!document.getElementById("password").value.match(/^(?=.*[0-9])(?=.*[a-z])(?=.*[-+_!@#$%^&*., ?])([a-zA-Z0-9-+_!@#$%^&*., ?]{8,})$/)) {
      alert("Password must be at least 8 characters long, and contain at least one special character, one alphabetic character and one number.")
      return false;
    }
    return true;
  }
</script>
<div>
  <?php
  if ($already_login == '') {
  ?>
    <div class="card-header">
      <div class="" id="msgtoshow"></div>
      <h3 class='text-center'>User Registration</h3>
    </div>
    <div class="cad-body register-card-body">
      <div>
        <form id="registrationForm" action="" method="POST">
          <div class="form-group pt-3">
            <label for="fullname">Your name</label>
            <input type="text" name="fullname" class="form-control" minlength="5" required>
          </div>
          <div class="form-group">
            <label for="username">Your username (all letters, no spaces or special characters)</label>
            <input type="text" name="username" class="form-control" minlength="5" pattern="[a-zA-Z]+" required>
          </div>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name="email" id="email" value="<?php echo $customerEmail ?>" class="form-control" minlength="5" required readonly>
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" name="phone" class="form-control" minlength="7" required>
          </div>
          <div class="form-group">
            <label for="password">Password (at least 8 characters, with at least one special character and one number)</label>
            <input type="password" name="password" id="password" minlength="8" class="form-control" pattern="(?=.*\d)(?=.*[a-z]).{8,}" required>
          </div>
          <div class="form-group">
            <label for="repeat_password">Repeat Password</label>
            <input type="password" name="repeat_password" minlength="8" id="repeat_password" class="form-control" required>
          </div>
          <div class="form-group">
            <input type="checkbox" name="tos_agree" minlength="8" id="tos_agree" required>
            <label for="tos_agree">I have read and agree to the <span class="link-action"><a href="tos.php" target="_blank">Terms of Service</a></span></label>
          </div>
          <div class="form-group">
            <input type="checkbox" name="policy_agree" minlength="8" id="policy_agree" required>
            <label for="policy_agree">I have read and agree to the <span class="link-action"><a href="privacy.php" target="_blank">Privacy Policy</a></span></label>
          </div>
          <div class="form-group">
            <input type="checkbox" name="community_agree" minlength="8" id="community_agree" required>
            <label for="community_agree">I have read and agree to the <span class="link-action"><a href="guidelines.php" target="_blank">Community Guidelines</a></span></label>
          </div>
          <?php echo RECAPTCHA_TAG; ?>
          <div class="form-group">
            <button type="submit" name="register" class="btn btn-success">Register</button>
          </div>
        </form>
      </div>
    </div>
  <?php
  } else {


  ?>
    <div style="width:100%;height: 80vh;display: flex;justify-content: center;align-items: center;">
      <div class="inter-font font-700 font-20" style="width: 400px; background-color: red; padding: 10px;height: 300px;">

        You are already logged in with a different user . Please log out first then try again.
      </div>
    </div>
  <?php
  }
  ?>
</div>




<?php
include 'inc/footer.php';
?>

</html>