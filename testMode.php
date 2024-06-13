<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  if (!checkUserAuth('admin_panel', Session::get('roleid'))) {
    error_log("A user with id " . Session::get('userid') . " and role " . Session::get('userid') . " attempted to access the admin panel without sufficient permissions.");
    header("Location:index.php");
  }

  $views->showAndClearPendingMessage();
  //Check for Stripe setup
  if (!defined('STRIPE_KEY_LIVE') || !defined('STRIPE_KEY_TEST')) {
    Session::set("pendingMsg", createUserMessage("error", "Stripe configuration parameters not found or set!"));
    header("Location:index.php");
  }

  if (isset($_GET["enable"]) && $_GET["enable"] == true) {
    Session::set("testMode", true);
    Session::set('pendingMsg', createUserMessage("success", "Test mode enabled!"));
    header("Location:subscription.php?action=create_subscription");
  }
  if (isset($_GET["clear"]) && $_GET["clear"] == true) {
    Session::set("testMode", false);
    $result = $subscriptions->clearTestModeData();
    if ($result != true) {
        echo $result; 
    } else
        echo createUserMessage("success", "Test data cleared, test mode disabled!");
  }
?>
<div class="card ">
    <div class="card-header">
      <h3><i class="fas fa-flask"></i> Purchase Test Mode</h3>
    </div>
    <div class="card-body pr-2 pl-2">
      <table class="table" style="width:100%">
        <tbody>
            <tr>
            <td colspan="4">
                <p>Purchase Test Mode allows you to try out the subscription flow without actually spending any money.</p>
                <p>Stripe provides a test credit card number: <code>4242 4242 4242 4242</code>. Use any future date for the expiry, any three digits for the CVV code, and any valid billing address.</p>
                <p>When a test purchase is made, test data is added to the Bennit database and should be cleaned out. Use the Clear Test Data button to remove test records from all users.</p>
            </td>
            </tr>
            <tr>
                <td></td>
                <td align="center" style="padding-left: 20%" class="onboard-choice-icon">
                    <a href="?enable=true">
                    <h3><i class="fas fa-credit-card"></i><br/>Enable Test Mode</h3>
                    Test Mode will be enabled for <i>you only</i> until you log out. 
                    </a>
                </td>
                <td align="center" style="padding-right: 20%" class="onboard-choice-icon">
                    <a href="?clear=true">
                    <h3><i class="fas fa-eraser"></i><br/>Clear Test Data</h3>
                    Test Data for <i>all users</i> will be removed from the Bennit database.
                    </a>
                </td>
                <td></td>
            </tr>
            <tr>
            <td colspan="4" style="padding-top: 25px">
                <p>Test data will be created in Stripe, and can be manually removed in the Stripe dashboard if needed.</p>
            </td>
            </tr>   
        </tbody>
      </table>
    </div>
  </div>
<?php
  include 'inc/footer.php';
?>
</html>