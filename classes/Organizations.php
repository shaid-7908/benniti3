<?php
include_once $docRoot."lib/Database.php";
include_once $docRoot."lib/Session.php";

/* Organizations operate on public_id */
class Organizations {

  private $db;

  public function __construct() {
    $this->db = new Database();
  }

  public function getRealId($publicId) {
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id for organizations when public id not set");
      return false;
    }
    $sql = "SELECT id FROM tbl_organizations WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->id;
    } else {
      error_log("Could not find real id for organizations with public id " . $publicId);
      return false;
    }
  }

  public function getPublicId($realId) {
    if (!isset($realId) || $realId == "") {
      error_log("Could not find public id for organizations when real id not set");
      return false;
    }
    $sql = "SELECT public_id FROM tbl_organizations WHERE id = :id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $realId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->public_id;
    } else {
      error_log("Could not find public id for organizations with real id " . $realId);
      return false;
    }
  }

  public function orgTypes() {
    return [
      "Commercial",
      "Government",
      "Education",
      "Non-profit",
      "Individual"
    ];
  }

  public function getOrgType($fromSource) {
    if (is_numeric($fromSource)) {
      return $this->orgTypes()[$fromSource];
    } else {
      return array_search($fromSource, $this->orgTypes());
    }
  }
  public function getBennitData(){
    $sql = "SELECT * FROM tbl_organizations WHERE id = 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function checkUserHasOrganizationOrRedirect($users) {
    $getOrgList = null;
    if (Session::get("roleid") == 1) {
       // $getOrgList = $this->getAllOrganizationData();
       $getOrgList = $this->getBennitData();

    } else {
        $getOrgList = $this->getAllOrganizationDataForUser(Session::get("userid"), $users);
    }
    if (!isset($getOrgList) || count($getOrgList) < 1) {
        header('Location: organizationRequired.php');
        die();
    }
    return $getOrgList;
  }

  public function checkOrganizationExists($organizationName) {
    //TODO: Make case and punctuation insensitive
    $sql = "SELECT orgname from tbl_organizations WHERE LOWER(orgname) = LOWER(:orgname)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':orgname', $organizationName);
    $stmt->execute();
    if ($stmt->rowCount()> 0) {
      return true;
    } else {
      return false;
    }
  }

  //TODO: deprecate this
  public function checkOrganizationAdmin($orgId, $userId, $users) {
    $userId = $users->getRealId($userId);
    $orgId = $this->getRealId($orgId);
    if ($userId && $orgId) {
      $sql = "SELECT * from tbl_organization_users WHERE fk_org_id=:organizationId AND fk_user_id=:userId LIMIT 1";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':organizationId', $orgId);
      $stmt->bindValue(':userId', $userId);
      $stmt->closeCursor();
      $stmt->execute();
      if ($stmt->rowCount()> 0) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }

  public function getUserOrganizationLevel($orgId, $userId, $users) {
    $userId = $users->getRealId($userId);
    $orgId = $this->getRealId($orgId);
    if ($userId && $orgId) {
      $sql = "SELECT * from tbl_organization_users WHERE fk_org_id=:organizationId AND fk_user_id=:userId LIMIT 1";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':organizationId', $orgId);
      $stmt->bindValue(':userId', $userId);
      $stmt->closeCursor();
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_OBJ);
      if ($result) {
        return $result[0]->org_level;
      } else {
        return null;
      }
    }
    return null;
  }

  public function createOrganization($data, $byAdmin, $users) {
    $userId = $users->getRealId(Session::get("userid"));
    $snowflake = new \Godruoyi\Snowflake\Snowflake;
    $newPublicId = $snowflake->id();

    if ($data['orgname'] == "" || $data['orgtype'] == "" || $data['description'] == "" || $data['address_1'] == "" || $data['city'] == "" || $data['state'] == "" || $data['zip'] == "" || $data['buisness_ein'] == "" ) {
      return createUserMessage("error", "All fields are required!");
    } elseif (containsBadWords($data['orgname'])) {
      return createUserMessage("error", "Organization name should not contain foul language.");
    } elseif (strlen($data['orgname']) < 3) {
      return createUserMessage("error", "Organization name must be at least 3 characters.");
    // } elseif (!is_numeric($data['orgtype'])) {
    //   return createUserMessage("error", "Invalid organization type " . $data['orgtype']);
    } elseif(strlen($data['description']) < 35) {
      return createUserMessage("error", "Please provide a detailed description (at least 35 characters) of your organization.");
    } elseif(strlen($data['address_1']) < 5) {
      return createUserMessage("error", "Please provide more information about your address line 1 (at least 5 characters)");
    }elseif(strlen($data['city']) < 3 ){
       return createUserMessage("error", "Please provide a proper city name (at least 5 characters)");
    }elseif(strlen($data['zip']) < 5){
     return createUserMessage("error", "Please provide a valid zip code (at least 5 characters)");
    }
     elseif ($this->checkOrganizationExists($data['orgname']) == TRUE) {
      return createUserMessage("error", "An organization with that name already exists. You can ask the administrator to add you to that organization, or create a new one.");
    } else {
      $sql = "INSERT INTO tbl_organizations(public_id, creator, orgname, orgtype, description, website,address1 ,address2 , city , state , zip,social_media,buisness_ein) 
      VALUES(:publicid, :creator, :orgname, :orgtype, :description,:website,:address1,:address2,:city,:state,:zip,:social_media,:buisness_ein)";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':publicid', $newPublicId);
      $stmt->bindValue(':creator', $userId);
      $stmt->bindValue(':orgname', $data['orgname']);
      $stmt->bindValue(':orgtype', $data['orgtype']);
      $stmt->bindValue(':description', $data['description']);
      $stmt->bindValue(':website', $data['website']);
      $stmt->bindValue(':address1', $data['address_1']);
      $stmt->bindValue(':social_media',$data['social_media']);
      $stmt->bindValue(':buisness_ein',$data['buisness_ein']);
      if($data['address_2'] == " "){
         $stmt->bindValue(':address2', 'NA');
      }else{

        $stmt->bindValue(':address2', $data['address_2']);
      }
      $stmt->bindValue(':city',$data['city']);
      $stmt->bindValue(':state',$data['state']);
      $stmt->bindValue(':zip',$data['zip']);
      $result = $stmt->execute();
      if ($result) {
        $newOrgId = $this->db->pdo->lastInsertId();
        $sql = "INSERT INTO tbl_organization_users(fk_org_id, fk_user_id, org_level)
          VALUES(:orgid, :userid, 1);";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':orgid', $newOrgId);
        $stmt->bindValue(':userid', $userId);
        $result = $stmt->execute();
        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Organization created."));
          $onboarding=Session::get('onboarding');
          if (isset($onboarding) && isset($onboarding->completedUrl))
            return "<script>location.href='" . $onboarding->completedUrl . "&orgid=" . $newPublicId . "';</script>";
          else
            return "<script>location.href='organizationList.php';</script>";
        } else {
          return createUserMessage("error", "Organization admin not set. Something went wrong!");
        }
      } else {
        return createUserMessage("error", "Organization not created. Something went wrong!");
      }
    }
  }

  public function getAllOrganizationDataForUser($userId, $users) {
    $userId = $users->getRealId($userId);
    if ($userId) {
      $sql = "SELECT tbl_organization_users.*, tbl_organizations.id as orgid, tbl_organizations.* 
        FROM tbl_organization_users 
        INNER JOIN tbl_organizations ON tbl_organization_users.fk_org_id=tbl_organizations.id 
        INNER JOIN tbl_users ON tbl_organization_users.fk_user_id=tbl_users.id
        WHERE tbl_organization_users.fk_user_id=:userid;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':userid', $userId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    return false;
  }

  public function getAllUserDataForOrganization($orgId) {
    $orgId = $this->getRealId($orgId);
    if ($orgId) {
      $sql = "SELECT tbl_organization_users.*, tbl_users.*, tbl_organizations.id as orgid, 
        tbl_organizations.orgname as orgname, tbl_organizations.creator as creatorid
        FROM tbl_organization_users 
        INNER JOIN tbl_organizations ON tbl_organization_users.fk_org_id=tbl_organizations.id 
        INNER JOIN tbl_users ON tbl_organization_users.fk_user_id=tbl_users.id
        WHERE tbl_organizations.id=:orgid;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    return false;
  }

  public function removeUserFromOrganization($orgId, $userId, $users) {
    $userId = $users->getRealId($userId);
    $orgId = $this->getRealId($orgId);
    if ($userId && $orgId) {
      $sql = "DELETE from tbl_organization_users
        WHERE fk_org_id=:orgid 
        AND fk_user_id=:userid;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->bindValue(':userid', $userId);
      $result = $stmt->execute();
      if ($result) {
        return createUserMessage("success", "User removed from organization.");
      } else {
        return createUserMessage("error", "User could not be removed from organization. Something went wrong!");
      }
    }
    return createUserMessage("error", "User could not be removed from organization. A real user or organization could not be found!");
  }

  public function addUserToOrganization($orgId, $userId, $orglevel, $users) {
    $userId = $users->getRealId($userId);
    $publicOrgId = $orgId;
    $orgId = $this->getRealId($orgId);
    if ($userId && $orgId) {
      //Make sure there's only one record for each user
      $this->removeUserFromOrganization($orgId, $userId, $users);
      $sql = "INSERT INTO tbl_organization_users (fk_org_id, fk_user_id, org_level) 
        VALUES (:orgid, :userid, :orglevel);";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->bindValue(':userid', $userId);
      $stmt->bindValue(':orglevel', $orglevel);
      $result = $stmt->execute();
      if ($result) {
        Session::set('pendingMsg', createUserMessage("success", "User added to organization."));
        return "<script>location.href='userList.php?orgid=" . $publicOrgId . "';</script>";
      } else {
        return createUserMessage("error", "User could not be added to organization. Something went wrong!");
      }
    }
    return createUserMessage("error", "User could not be added to organization. A real user could not be found!");
  }

  // Get All Org Data (if user is an admin)
  public function getAllOrganizationData() {
    if (Session::get('roleid') == 1) {
      //TODO: This query could result in multiple rows per organization if that organization has multiple admins
      //  Need to figure out how to get only the creator
      $sql = "SELECT tbl_organization_users.*, tbl_organizations.id as orgid, 
        tbl_organizations.*, tbl_users.fullname as creatorname  
        FROM tbl_organization_users 
        INNER JOIN tbl_organizations ON tbl_organization_users.fk_org_id=tbl_organizations.id 
        INNER JOIN tbl_users ON tbl_organization_users.fk_user_id=tbl_users.id 
        WHERE tbl_organization_users.org_level = 1 
        ORDER BY tbl_organizations.id DESC;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
  }

  public function getOrganizationInfoById($orgPublicId) {
    $orgId = $this->getRealId($orgPublicId);
    if ($orgId) {
      return $this->getOrganizationInfoByRealId($orgId);
    }
    return false;
  }

  public function getOrganizationInfoByRealId($realOrgId) {
    $sql = "SELECT tbl_organizations.*, tbl_users.fullname as creatorname
    FROM tbl_organizations 
    INNER JOIN tbl_users ON tbl_organizations.creator=tbl_users.id 
    WHERE tbl_organizations.id=:id LIMIT 1;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $realOrgId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    if ($result) {
      return $result;
    } else {
      return false;
    }
  }

  public function searchOrganizations($keyword) {
    $sql = "SELECT 
      tbl_organizations.*, 
      tbl_users.fullname as creator
      FROM tbl_organizations
      INNER JOIN tbl_users ON tbl_organizations.creator=tbl_users.id
      WHERE tbl_organizations.orgname LIKE :keyword 
      OR tbl_organizations.description LIKE :keyword 
      ORDER BY tbl_organizations.id DESC;";

    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':keyword', "%" . $keyword . "%");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function updateOrganizationById($orgId, $data){
    $orgId = $this->getRealId($orgId);
    if ($orgId) {
      if ($data['orgname'] == "" || $data['orgtype'] == ""|| $data['description'] == "" || $data['location'] == ""  ) {
        return createUserMessage("error", "All fields are required!");
      } elseif (containsBadWords($data['orgname'])) {
        return createUserMessage("error", "Organization name should not contain foul language.");
      } elseif (strlen($data['orgname']) < 3) {
        return createUserMessage("error", "Organization name must be at least three characters!");
      } elseif (!is_numeric($data['orgtype'])) {
        return createUserMessage("error", "Invalid organization type.");
      } elseif(strlen($data['description']) < 100) {
        return createUserMessage("error", "Please provide a detailed description (at least 100 characters) of your organization.");
      } elseif(strlen($data['location']) < 5) {
        return createUserMessage("error", "Please provide more information about your location (at least 5 characters)");
      } else {

        $sql = "UPDATE tbl_organizations SET
          orgname = :orgname,
          orgtype = :orgtype,
          description = :description,
          location = :location,
          website = :website 
          WHERE id = :orgid";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':orgname', $data['orgname']);
        $stmt->bindValue(':orgtype', $data['orgtype']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':location', $data['location']);
        $stmt->bindValue(':website', $data['website']);
        $stmt->bindValue(':orgid', $orgId);
        $result = $stmt->execute();

        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Organization updated."));
          return "<script>location.href='organizationList.php';</script>";
        } else {
          return createUserMessage("error", "Organization could not be updated. Something went wrong!");
        }
      }
    }
    return createUserMessage("error", "Organization could not be updated. A real organization could not be found!");
  }

  public function deleteOrganizationById($orgId){
    $orgId = $this->getRealId($orgId);
    if ($orgId) {
      $sql = "DELETE FROM tbl_organizations WHERE id = :id;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':id', $orgId);
      $result = $stmt->execute();
      if ($result) {
        return createUserMessage("success", "Organization deleted.");
      } else {
        return createUserMessage("error", "Organization not deleted. Something went wrong!");
      }
    }
    return createUserMessage("error", "Organization not deleted. A real organization could not be found!");
  }
}
