<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  //Figure out what we're working on
  $theQuery = "";
  if (isset($_GET["orgid"])) {
    $theQuery = "orgid";
  }
  if (isset($_GET["userid"])) {
    $theQuery = "userid";
  }

  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST["query"])) {
    $theQuery = $_POST["query"];
  }

  //Check permissions and process requested actions
  if (isset($_GET["action"]) && is_numeric($_GET["subscriptionid"])) {
    $allowed = false;
    $removeId = (int)$_GET["subscriptionid"];
    if (checkUserAuth($_GET["action"]."_orthogonal", Session::get('roleid'))) {
      //Admins are allowed to delete
      $allowed = true;
    } else {
      //If this is an organzational subscriptions
      if (isset($_GET["orgid"])) {
        //Organization creators are allowed to delete their org's subscriptions
        $userOrg = $organizations->getOrganizationInfoById($removeId);
        if (isset($userOrg) && checkUserAuth($_GET["action"], Session::get('roleid'))) {
          if (getIfSet($userOrg, "creator") ==  Session::get("userid")) {
            $allowed = true;
          }
        }
      }
      //If this is the user's own subscription
      if (isset($_GET["userid"]) && (Session::get("userid") == isset($_GET["userid"]))) {
        //Check if the subscription they're trying to delete is actually theirs
        $checkOwnSub = $subscriptions->getAllSubscriptionDataForSubscriptionId($_GET["subscriptionid"]);
        if (isset($checkOwnSub) && isset($checkOwnSub->fk_user_id)) {
          if ($users->getRealId(Session::get("userid")) == $checkOwnSub->fk_user_id)
            $allowed = true;
        }
      }
    }
    if (!$allowed) {
      //Warn about disallowed action
      error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to delete a subscription they did not create, and was ejected.");
      Session::set('pendingMsg', createUserMessage("error", "You may not delete a subscriuption you did not create!"));
      header('Location:subscriptionList.php');
    } else {
      if ($_GET["action"] == "cancel_subscription") {
          //Actually do the delete
          $testMode = Session::get("testMode");
          if (isset($testMode) && $testMode == true) {
            $stripe = new \Stripe\StripeClient(STRIPE_KEY_TEST);
            echo createUserMessage("Notice: ", "Test mode enabled, cancelling test subscriptions.");
          } else {
            $testMode = false;
            $stripe = new \Stripe\StripeClient(STRIPE_KEY_LIVE);
          }
          $removeSub = $subscriptions->deleteSubscriptionById($removeId, $stripe);
          if (isset($removeSub)) {
            echo $removeSub;
          }
        }
      }
  }
  //Process requested query
  $theQuery = "";
  if (isset($_GET["query"])) {
    $theQuery = $_GET["query"];
  }
  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST["query"])) {
    $theQuery = $_POST["query"];
  }
?>
  <div class="card ">
    <div class="card-header">
      <h3><i class="fas fa-shopping-cart"></i></i> Manage Subscriptions</h3>
    </div>
    <div class="card-body pr-2 pl-2">
      <a class="nav-link" href="subscription.php?action=create_subscription"><i class="fas fa-shopping-cart"></i></i> New Subscription</a>
      <?php
        //Determine settings and request grid
        $gridColumns = ["creator","organization","renewal"];
        if ($theQuery != "") {  //If there's a specific query to load
            if ($theQuery == "orgid")
              $allSubs = $subscriptions->getAllSubscriptionDataForOrgId($_GET["orgid"], true, $organizations);
            if ($theQuery == "userid")
              $allSubs = $subscriptions->getAllSubscriptionDataForUserId($_GET["userid"], true, $users, $organizations);
            $gridActions = [];
            if (Session::get("roleid") == '1') {
              $gridActions = ["cancel"];
            }
        } else {
            //If we're loaded for admin
            if (Session::get("roleid") == '1' && isset($_GET["as"]) && $_GET["as"] == "admin") {
              $allSubs = $subscriptions->getAllSubscriptionData(true);
              $gridActions = ["cancel"];
              array_push($gridColumns, "creator");
            } else {  //Default load
              $allSubs = $subscriptions->getAllSubscriptionDataForUserId(Session::get("userid"), false, $users, $organizations);
              $gridActions = ["cancel"];
            }
        }
        $views->makeSubscriptionGrid($allSubs, $gridColumns, $gridActions);
      ?>
    </div>
  </div>
<?php
  include 'inc/footer.php';
?>
</html>