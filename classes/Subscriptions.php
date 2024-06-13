<?php
include_once $docRoot . "lib/Database.php";
include_once $docRoot . "lib/Session.php";

/* Subscriptions operate on public_id */
$snowflake = new \Godruoyi\Snowflake\Snowflake;

class Subscriptions
{

  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function getRealId($publicId)
  {
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id for matches when public id not set");
      return false;
    }
    $sql = "SELECT id FROM tbl_subscriptions WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->id;
    } else {
      error_log("Could not find real id for subscription with public id " . $publicId);
      return false;
    }
  }

  public function checkSubscriptionExistsAnywhere($userId, $users, $organizations)
  {
    $subscriptionExists = false;
    if (!$this->checkSubscriptionExists($userId, "", $users, $organizations)) {
      $userOrgs = $organizations->getAllOrganizationDataForUser($userId, $users);
      foreach ($userOrgs as $thisOrg) {
        if ($this->checkSubscriptionExists(Session::get("userid"), $thisOrg->public_id, $users, $organizations)) {
          $subscriptionExists = true;
          break;
        }
      }
    } else {
      $subscriptionExists = true;
    }
    return $subscriptionExists;
  }

  public function getSubscriptionDetailsFromEmail($email){
   $sql ="SELECT * FROM tbl_bennit_subscriptions WHERE customer_email = :email";
   $stmt = $this->db->pdo->prepare($sql);
   $stmt->bindValue(":email",$email);
   $stmt->execute();


   // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_OBJ);

    return $result;
  }

  public function checkEmailinSubscriptionTbale($email)
  {
    $sql = "SELECT COUNT(*) FROM tbl_bennit_subscriptions WHERE customer_email = :email";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    
    $count = $stmt->fetchColumn();

    return $count > 0;
  }
  public function storeSubscriptionData($subscriptions, $email ,$paymentId,$customerId)
  {
    if ($this->checkEmailinSubscriptionTbale($email)) {
    } else {
      foreach ($subscriptions->data as $subscription) {
        $subscriptionId = $subscription->id;
        $planId = $subscription->plan->id;
        $status = $subscription->status;
        $startDate = date('Y-m-d H:i:s', $subscription->start_date);
        $endDate = isset($subscription->ended_at) ? date('Y-m-d H:i:s', $subscription->ended_at) : null;
        $currentPeriodStart = date('Y-m-d H:i:s', $subscription->current_period_start);
        $currentPeriodEnd = date('Y-m-d H:i:s', $subscription->current_period_end);
        $amount = $subscription->plan->amount / 100; // Stripe amounts are in cents
        $currency = $subscription->plan->currency;

        $stmt = $this->db->pdo->prepare("INSERT INTO tbl_bennit_subscriptions 
         (payment_id, subscription_id, plan_id, customer_id, customer_email, status, start_date, end_date, current_period_start, current_period_end, amount, currency)
             VALUES 
         (:payment_id, :subscription_id, :plan_id, :customer_id, :customer_email, :status, :start_date, :end_date, :current_period_start, :current_period_end, :amount, :currency);
         ");

        $stmt->bindParam(':payment_id', $paymentId);
        $stmt->bindParam(':subscription_id', $subscriptionId);
        $stmt->bindParam(':plan_id', $planId);
        $stmt->bindParam(':customer_id', $customerId);
        $stmt->bindParam(':customer_email', $email);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':current_period_start', $currentPeriodStart);
        $stmt->bindParam(':current_period_end', $currentPeriodEnd);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':currency', $currency);

        $result = $stmt->execute();

        if (!$result) {
          return false; // Operation failed, return false
        }
      }
    }
    return true;
  }
  public function storeTempPayDATA($email, $payment_id)
  {



    $sql = "INSERT INTO tbl_temp_stripe_data(email,payment_id) VALUES(:email,:payment_id)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':payment_id', $payment_id);
    $stmt->execute();
  }
  public function checkSubscriptionExists($userId, $orgId, $users, $organizations)
  {
    $userId = $users->getRealId($userId);
    if ($orgId != "" && $orgId != null)
      $orgId = $organizations->getRealId($orgId);
    else
      $orgId = "";
    if ($userId) {
      $sql = "SELECT * from tbl_subscriptions WHERE fk_user_id=:userid1 AND fk_org_id=:orgid AND canceled_at > CURDATE();";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':userid1', $userId);
      //$stmt->bindValue(':userid2', $userId);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      error_log("*****NOT DOING IT!****");
    }
    return false;
  }

  public function checkPurchaseTokenExists($purchaseToken)
  {
    $sql = "SELECT * from tbl_subscriptions WHERE purchase_token=:purchaseToken;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':purchaseToken', $purchaseToken);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  // Subscription Registration
  // TODO: don't duplicate subscriptions
  public function createSubscription($userId, $orgId, $subscriptionType, $purchaseToken, $expiry, $users, $organizations)
  {
    if (!$this->checkPurchaseTokenExists($purchaseToken)) {
      $snowflake = new \Godruoyi\Snowflake\Snowflake;
      $newPublicId = $snowflake->id();
      $userId = $users->getRealId($userId);
      $orgId = $organizations->getRealId($orgId);

      $sql = "INSERT INTO tbl_subscriptions(public_id, fk_user_id, fk_org_id, subscription_type, purchase_token, canceled_at) 
        VALUES(:publicid, :userid, :orgid, :subscriptiontype, :purchasetoken, :expiry)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':publicid', $newPublicId);
      $stmt->bindValue(':userid', $userId);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->bindValue(':subscriptiontype', $subscriptionType);
      $stmt->bindValue(':purchasetoken', $purchaseToken);
      $stmt->bindValue(':expiry', $expiry);
      $result = $stmt->execute();
      if ($result) {
        Session::set('pendingMsg', createUserMessage("success", $subscriptionType . " Subscription created."));
        return "<script>location.href='index.php';</script>";
      } else {
        return createUserMessage("error", "Subscription not created. Something went wrong!");
      }
    } else {
      Session::set('pendingMsg', createUserMessage("success", $subscriptionType . " already existed!"));
      return "<script>location.href='index.php';</script>";
    }
  }

  public function getAllSubscriptionDataForSubscriptionId($publicId)
  {
    if ($publicId) {
      $realId = $this->getRealId($publicId);
      return getAllSubscriptionDataForSubscriptionRealId($realId);
    }
    return false;
  }

  public function getAllSubscriptionDataForSubscriptionRealId($subscriptionId)
  {
    if ($subscriptionId) {
      $sql = "SELECT 
      tbl_subscriptions.*, 
      tbl_users.id as users_id, 
      tbl_users.public_id as user_public_id, 
      tbl_users.username as username, 
      tbl_organizations.id as organizations_id, 
      tbl_organizations.public_id as org_public_id, 
      tbl_organizations.orgname as orgname
      FROM tbl_subscriptions 
      INNER JOIN tbl_users ON tbl_matches.fk_user_id = tbl_users.id 
      INNER JOIN tbl_organizations ON tbl_subscriptions.fk_org_id=tbl_organizations.id 
      WHERE tbl_subscriptions.id=:subscriptionid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':subscriptionid', $subscriptionId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    return null;
  }

  public function getAllSubscriptionDataForUserId($userId, $includeCanceled, $users, $organizations)
  {
    $publicUserId = $userId;
    $userId = $users->getRealId($userId);
    if ($publicUserId && $userId) {
      $userSubscriptions = [];
      $userOnly = $this->getAllSubscriptionDataOnlyForUserByRealId($userId, $includeCanceled);
      foreach ($userOnly as $userSub) {
        array_push($userSubscriptions, $userSub);
      }
      //find all organizations for user's public id
      $userOrgs = $organizations->getAllOrganizationDataForUser($publicUserId, $users);
      foreach ($userOrgs as $userOrg) {
        //for each organization, accumulate subscriptions
        $orgSubs = $this->getAllSubscriptionDataForOrgByRealId($userOrg->id, $includeCanceled);
        foreach ($orgSubs as $orgSub) {
          array_push($userSubscriptions, $orgSub);
        }
      }
      return $userSubscriptions;
    }
    return null;
  }

  public function getAllSubscriptionDataOnlyForUserByRealId($realUserId, $includeCanceled)
  {
    if ($realUserId) {
      $sql = "SELECT 
      tbl_subscriptions.*, 
      tbl_users.id as users_id, 
      tbl_users.public_id as user_public_id, 
      tbl_users.username as username
      FROM tbl_subscriptions 
      INNER JOIN tbl_users ON tbl_subscriptions.fk_user_id = tbl_users.id 
      WHERE tbl_subscriptions.fk_user_id=:userid 
      AND tbl_subscriptions.fk_org_id = '' ";
      if (!$includeCanceled) {
        $sql .= " AND canceled_at > CURDATE();";
      }
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':userid', $realUserId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
  }

  public function getAllSubscriptionDataForOrgId($publicId, $includeCanceled, $organizations)
  {
    if ($publicId) {
      $realId = $organizations->getRealId($publicId);
      return $this->getAllSubscriptionDataForOrgByRealId($realId, $includeCanceled);
    }
    return null;
  }

  public function getAllSubscriptionDataForOrgByRealId($realOrgId, $includeCanceled)
  {
    if ($realOrgId) {
      $sql = "SELECT 
      tbl_subscriptions.*, 
      tbl_users.id as users_id, 
      tbl_users.public_id as user_public_id, 
      tbl_users.username as username, 
      tbl_organizations.id as organizations_id, 
      tbl_organizations.public_id as org_public_id, 
      tbl_organizations.orgname as orgname
      FROM tbl_subscriptions 
      INNER JOIN tbl_users ON tbl_subscriptions.fk_user_id = tbl_users.id 
      INNER JOIN tbl_organizations ON tbl_subscriptions.fk_org_id=tbl_organizations.id 
      WHERE tbl_subscriptions.fk_org_id=:orgid";
      if (!$includeCanceled) {
        $sql .= " AND canceled_at > CURDATE();";
      }
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgid', $realOrgId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
  }

  public function getAllSubscriptionData($includeCanceled)
  {
    //TODO: If I were better a JOIN queries, I wouldn't need to do two queries...
    //Query for user subscriptions
    $sql = "SELECT 
    tbl_subscriptions.*, 
    tbl_users.id as users_id, 
    tbl_users.public_id as user_public_id, 
    tbl_users.username as username  
    FROM tbl_subscriptions 
    INNER JOIN tbl_users ON tbl_subscriptions.fk_user_id = tbl_users.id 
    WHERE tbl_subscriptions.fk_org_id = ''";
    if (!$includeCanceled) {
      $sql .= " AND canceled_at > CURDATE();";
    }
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //Query for organizational subscriptions
    $sql = "SELECT 
    tbl_subscriptions.*, 
    tbl_users.id as users_id, 
    tbl_users.public_id as user_public_id, 
    tbl_users.username as username, 
    tbl_organizations.id as organizations_id, 
    tbl_organizations.public_id as org_public_id, 
    tbl_organizations.orgname as orgname
    FROM tbl_subscriptions 
    INNER JOIN tbl_users ON tbl_subscriptions.fk_user_id = tbl_users.id 
    INNER JOIN tbl_organizations ON tbl_subscriptions.fk_org_id=tbl_organizations.id";
    if (!$includeCanceled) {
      $sql .= " WHERE canceled_at > CURDATE();";
    }
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
    return $results;
  }

  public function deleteSubscriptionById($publicId, $stripe)
  {
    $realId = $this->getRealId($publicId);
    if ($realId) {
      return $this->deleteSubscriptionByRealId($realId, $stripe);
    }
    return createUserMessage("error", "Subscription not deleted. Could not find real Subscription id!");
  }

  public function deleteSubscriptionByRealId($realId, $stripe)
  {
    if ($realId) {
      if (Session::get('roleid') == 1) {
        //Find Stripe subscription ID
        $sql = "SELECT * 
        FROM tbl_subscriptions 
        WHERE id = :realid;";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':realid', $realId);
        $stmt->execute();
        $find_result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (isset($find_result)) {
          //Delete our record
          $sql = "UPDATE tbl_subscriptions SET canceled_at=CURDATE() WHERE id = :id ";
          $stmt = $this->db->pdo->prepare($sql);
          $stmt->bindValue(':id', $realId);
          $delete_result = $stmt->execute();
          if ($delete_result) {
            //Tell Stripe to delete the record
            $stripeId = str_replace("test_", "", $find_result[0]->purchase_token);
            if (isset($stripeId)) {
              $stripe_result = $stripe->subscriptions->cancel($stripeId, []);
              if (isset($stripe_result) && isset($stripe_result->status) && $stripe_result->status == "canceled") {
                return createUserMessage("success", "Subscription deleted, billing cancelled.");
              } else {
                return createUserMessage("error", "Subscription deleted, but billing could not be automatically canceled. Cancel billing with payment processor manually.");
              }
            }
            return createUserMessage("success", "Subscription deleted.");
          } else {
            return createUserMessage("error", "Subscription not deleted. Something went wrong!");
          }
        }
      }
    }
    return createUserMessage("error", "Subscription not deleted. Could not find Subscription id!");
  }

  /* Test Mode Functions */
  public function clearTestModeData()
  {
    $sql = "UPDATE tbl_users SET stripe_id = NULL WHERE stripe_id LIKE 'test_%';";
    $stmt = $this->db->pdo->prepare($sql);
    $result = $stmt->execute();
    if ($result) {
      $sql = "DELETE from tbl_subscriptions WHERE purchase_token LIKE 'test_%';";
      $stmt = $this->db->pdo->prepare($sql);
      $result = $stmt->execute();
      if (!$result) {
        return createUserMessage("error", "Subscription records not updated. Something went wrong!");
      }
    } else {
      return createUserMessage("error", "User records not updated. Something went wrong!");
    }
    return true;
  }
}
