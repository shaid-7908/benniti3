<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();
  /* 
  This is a single page that handles multiple purchase steps, depending on where the
  user is in the flow. All steps have some interaction with Stripe. Most of the Stripe
  interaction is handled here (some is in admin).
  */

  //Check for Stripe setup
  if (!defined('STRIPE_KEY_LIVE') || !defined('STRIPE_KEY_TEST')) {
    Session::set("pendingMsg", createUserMessage("error", "Stripe configuration parameters not found or set!"));
    header("Location:index.php");
  }
  
  //Get product data from Stripe
  $testMode = Session::get("testMode");
  if (isset($testMode) && $testMode == true) {
    $stripe = new \Stripe\StripeClient(STRIPE_KEY_TEST);
    echo createUserMessage("Notice: ", "Test mode enabled, transactions will not be preserved.");
  } else {
    $testMode = false;
    $stripe = new \Stripe\StripeClient(STRIPE_KEY_LIVE);
  }
    
  $products = $stripe->products->all(['active' => true]);
  //echo "<p>" . json_encode($products, JSON_PRETTY_PRINT) . "</p>";
  $prices = $stripe->prices->all();
  //echo "<p>" . json_encode($prices, JSON_PRETTY_PRINT) . "</p>";
 
?>
<script>
function checkFormValidAndSubmit() {
  return true;
}
function updateProductSelection() {
  var prodList = document.getElementById("subscription_id");
  document.getElementById("productDescription").innerText = prodList.options[prodList.selectedIndex].title;
  if (prodList.options[prodList.selectedIndex].text.toLowerCase().indexOf("organization") != -1) {
    document.getElementById("orgChooser").style.display = "block";
  } else {
    document.getElementById("orgChooser").style.display = "none";
  }
}
</script>
<div class="card">
  <div class="card-header">
    <h3 class='text-center'>New Subscription</h3>
  </div>
  <div class="cad-body">
    <div style="width:600px; margin:0px auto">
    <?php
    $subscribeStep = "create";
    $stripeSession;
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "") {
      $subscribeStep = $_SERVER['QUERY_STRING'];
      $subscribeStep = explode("&", $subscribeStep);
      if (sizeof($subscribeStep) > 1)
        $stripeSession = $_GET['session_id'];
      $subscribeStep = $subscribeStep[0];
    }
    
    $blockingError = false;
    if ($testMode)
      echo "<p align='center'><b>PURCHASE STEP: </b><code>" . $subscribeStep . "</code></p>";
    
    switch($subscribeStep) {
      case "buywithstripe": {
        if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST)) {
          $data = $_POST;
          //figure out where to return to after stripe is done
          $currentPageUrl = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
          $currentPageUrl = explode("?", $currentPageUrl);
          $currentPageUrl = $currentPageUrl[0];
          //figure out what they're buying
          if (isset($data["subscription_id"])) {
            $use_product;
            foreach ($prices as $price) {
              if ($price->id == $data["subscription_id"]) {
                foreach ($products as $product) {
                  if ($product->id == $price->product) {
                    $use_product = $product;
                  }
                }
              }
            }
            if (isset($use_product)) {
                //setup session data for stripe
                $use_user = $users->getUserInfoById(Session::get("userid"));
                if (isset($use_user) && isset($use_user->public_id) && isset($use_user->fullname) && isset($use_user->email)) {
                  //find or create the stripe customer id
                  if (isset($use_user->stripe_id) && $use_user->stripe_id != "") {
                    $use_stripe_id = str_replace("test_", "", $use_user->stripe_id);  //strip out any test indicators, since stripe doesn't know about those
                    $use_customer = $stripe->customers->retrieve($use_stripe_id);
                  } else { 
                    $use_customer = $stripe->customers->create([
                      'name' => $use_user->fullname,
                      'email' => $use_user->email,
                      'metadata' => [
                        'public_id' => Session::get("userid"),
                      ]
                    ]);
                    if ($testMode)
                      $users->updateStripeCustomerId(Session::get("userid"), "test_" . $use_customer->id);
                    else
                      $users->updateStripeCustomerId(Session::get("userid"), $use_customer->id);
                  }
                  //create the stripe session using the above
                  if (isset($use_customer)) {
                    $checkout_session = $stripe->checkout->sessions->create([
                      'mode' => 'subscription',
                      'success_url' => $currentPageUrl . '?stripesuccess&session_id={CHECKOUT_SESSION_ID}',
                      'cancel_url' => $currentPageUrl . '?stripecancel',
                      'customer' => $use_customer,
                      'line_items' => [
                        [
                          'price' => $data["subscription_id"],
                          'quantity' => 1,
                        ],
                      ],
                      'metadata' => [
                        'org_id' => $data["orgid"],
                      ]
                    ]);
                    echo "<p align='center'><span class='link-action'><a href='" . $checkout_session->url . "'>Redirecting to Stripe...</a></span></p>";
                    if (!$testMode)
                      echo '<script>document.location="' . $checkout_session->url . '"</script>';
                  } else {
                    $blockingError = true;
                    echo createUserMessage("error", "Unable to create or find Stripe customer. Purchase cannot proceed.");
                  }
                } else {
                  $blockingError = true;
                  echo createUserMessage("error", "Unable to lookup Bennit user data. Purchase cannot proceed.");
                }
            } else {
              $blockingError = true;
              echo createUserMessage("error", "Unable to lookup product or subscription data. Purchase cannot proceed.");
            }
          } else {
            $blockingError = true;
            echo createUserMessage("error", "Unable to lookup pricing data. Purchase cannot proceed.");
          }
        }
        break;
      }
      case "stripesuccess": {
        //Get the data back from Stripe
        $stripeSessionData = $stripe->checkout->sessions->retrieve($_GET['session_id']);
        $subscription = $stripe->subscriptions->retrieve($stripeSessionData->subscription);
        $subscription_id = $subscription->id;
        if ($testMode)
          $subscription_id = "test_" . $subscription_id;
        $product_id = $subscription->items->data[0]->plan->product;
        $product = $stripe->products->retrieve($product_id);
        $customer = $stripe->customers->retrieve($stripeSessionData->customer);
        
        //Through the purchase process, we use the presence of the word "organization" to indicate
        //  The subscription is for an organization. This is not a good approach (eg: multi-language use, config errors, etc...)
        //  But there's only a loose relationship between this system and Stripe's, so it'll have to do for now.
        //  TODO: Find a way to classify products consistently between Bennit and Stripe
        $today = date('Y-m-d');
        $expiry = date('Y-m-d', strtotime('+1 month', strtotime($today)));
        if (strpos(strtolower($product->name), "organization") !== false && isset($customer->metadata->org_id)) {
          $result = $subscriptions->createSubscription($customer->metadata->public_id, $stripeSessionData->metadata->org_id, $product->name, $subscription_id, $expiry, $users, $organizations);
        } else {
          $result = $subscriptions->createSubscription($customer->metadata->public_id, null, $product->name, $subscription_id, $expiry, $users, $organizations);
        }
        if (!$testMode)
          echo $result;
        else {
          if (strpos($result, "<script>") !== false) {
            echo "<p align='center'><b>" . $product->name . "</b><br><span class='link-action'><a href='subscriptionList.php";
            if (Session::get('roleid') == 1)
              echo "?as=admin";
            echo "'>Subscription Created!</a></span></p>";
          } else {
            echo $result;
          }
        }
        break;
      }
      default: {
        if (isset($_GET["stripe_cancel"])) {
          echo createUserMessage("Error", "Subscription creation canceled!");
        }
        ?>
        <form class="" action="?buywithstripe" method="POST">
          <div class="form-group">
            <label for="subscription_id"><b>Subscription</b> - Choose a subscription plan that works for you</label>
            <?php
            //check for valid products
            $validProducts = false;
            foreach ($products->data as $product) {
              if (strpos(strtolower($product->description), "user") !== false || strpos(strtolower($product->description), "organization") !== false)
              {
                $validProducts = true;
                break;
              }
            }
            if (!$validProducts) {
              die ("<br><b>There are no valid products available for subscription at this time!</b>");
            }
            ?>
            <select class="form-control" name="subscription_id" id="subscription_id" <?php if (sizeof($products->data) <= 1) { echo "disabled"; }?> required onchange="updateProductSelection()">
              <?php
                //List all products
                if (isset($products->data)) {
                  foreach ($products->data as $product) {
                    //Find the price for each product (currently only supporting 1 price in US Dollars)
                    foreach ($prices->data as $price) {
                      if ($price->product == $product->id && $price->currency == "usd") {
                        $product->price_id = $price->id;
                        $product->price = "$" . ($price->unit_amount / 100) . ".00/" . $price->recurring->interval;
                      }
                    }
                    echo "<option alt=\"" . $product->price_id . "\" title=\"" . $product->price . ": " . $product->description . "\" value=\"" . $product->price_id . "\">" . $product->name . "</option>";
                  }
                } 
              ?>
            </select>
            <div id="productDescription" class="form-explainer">Subscription Description</div>
            <div class="form-group" id="orgChooser" style="display:none">
              <label for="orgid"><b>Choose Organization:</b></label>
              <select class="form-control" name="orgid" id="orgid" style="min-width: 200px; max-width: 500px;" required>
                  <?php
                  if (checkUserAuth('subscribe_org_orthogonal', Session::get('roleid')))
                    $orgs = $organizations->getAllOrganizationData(); //This function only allows admins anyway
                  else
                    $orgs = $organizations->getAllOrganizationDataForUser(Session::get("userid"), $users);
                  if (isset($orgs)) {
                    foreach ($orgs as $thisOrg) {
                        echo "<option value=\"" . $thisOrg->public_id . "\">" . $thisOrg->public_id . " - " . $thisOrg->orgname . "</option>";
                    }
                  } 
                  ?>
              </select>
            </div>
            <?php
            if (sizeof($products->data) > 0) {
              echo "<script>updateProductSelection();</script>\r\n";
            }
            ?>
          </div>
          <div class="form-group">
            You'll be directed to Stripe to securely submit your payment information.
          </div>
          <div class="form-group">
            <button type="submit" name="register" class="btn btn-success" <?php if($blockingError) { echo "disabled"; } ?>>Subscribe!</button>
          </div>
        </form>
      <?php
      }
    }  
    ?>
    </div>
  </div>
</div>
<?php
  include 'inc/footer.php';
?>
</html>