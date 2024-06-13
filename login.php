<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkLogin();

$pendingMsg = Session::get("pendingMsg");
if (isset($pendingMsg)) {
  echo $pendingMsg;
}
Session::set("pendingMsg", NULL);

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['login'])) {
  $loginResult = $users->userAuthentication($_POST, $partnerKeys[0]);
}
if (isset($loginResult)) {
  echo $loginResult;
}

$logout = Session::get('logout');
if (isset($logout)) {
  echo $logout;
}
?>
<div class="container">
  <div class="xt-card">
    <div class="card-header">
      <div style=" margin:0px auto">

        <h3 class='text-left'>Sign in</h3>
      </div>
    </div>
    <div class="card-body">
      <div style=" margin:0px auto">
        <form class="" action="" method="post">
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name="email" class="form-control" minlength="5" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
          </div>
          <div class="form-group">
            <button type="submit" name="login" class="btn btn-default ">Login</button>
          </div>
        </form>
        <a href="forgotPassword.php">
          <div class="text-center" style="color:#f5a706">
            <span>Forgot password ?</span>
          </div>
        </a>
        <a href="choosePlan.php" style="text-decoration: none; color: inherit;">
          <div class="text-center mt-4" style="display: flex; justify-content: center;">
            <p>Don't have an account yet ?</p> <span style="color:#f5a706" class="ml-1">Sign up</span>
          </div>
      </div>
      </a>
    </div>
  </div>
</div>
<?php
include 'inc/footer2.php';
?>

</html>