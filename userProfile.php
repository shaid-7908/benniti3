<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  //Figure out what we're working on
  if (isset($_GET['userid'])) {
    $userid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['userid']);
  } else {
    $userid = Session::get('userid');
  }

  $allowed = false;
  //Check for admin level access
  if (checkUserAuth("edit_user_orthogonal", Session::get('roleid'))) {
    $allowed = true;
  } else {
    //Users are allowed to edit themselves.
    if ($userid == Session::get('userid')) {
        $allowed = true;
    }
  }

  if (!$allowed) {
    //Warn about disallowed action
    error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to edit another user, and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not edit a user other than your own!"));
    header('Location:index.php');
    die();
  } else {
    $getUinfo = $users->getUserInfoById($userid);
    //Process requested actions
    if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['update'])) {
      $updateUser = $users->updateUserById($userid, $_POST);
      if (isset($updateUser)) {
        echo $updateUser;
      }
    }
  }
  
?>
<div >
<div class="card-header">
    <h3><i class="fab fa-500px mr-2"></i>User Profile </h3>
  </div>
  <div class="card-body">
<?php
  if ($getUinfo) {
?>
    <div style="width:600px; margin:0px auto">
      <form class="" action="" method="POST">
        <div class="form-group">
          <label for="fullname">Full name</label>
          <input type="text" name="fullname" value="<?php echo $getUinfo->fullname; ?>" class="form-control">
        </div>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" name="username" value="<?php echo $getUinfo->username; ?>" class="form-control">
        </div>
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" value="<?php echo $getUinfo->email; ?>" class="form-control">
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" value="<?php echo $getUinfo->phone; ?>" class="form-control">
        </div>
        <div class="form-group">
          <a href="subscriptionList.php">Manage Subscription</a>
        </div>

        <?php
        if (checkUserAuth("set_user_role", Session::get('roleid')) && Session::get("userid") != $getUinfo->public_id) {
        ?>
          <div class="form-group">
            <label for="roleid">Select User Role</label>
            <select class="form-control" name="roleid" id="roleid" required>
              <option value="1" selected='selected'>Global Admin</option>
              <option value="2">Match Maker</option>
              <option value="3">Regular User</option>
            </select>
            <?php
              if (isset($getUinfo->roleid)) {
                echo "<script>document.getElementById('roleid').value='$getUinfo->roleid'</script>\r\n";
              }
            ?>
          </div>
        <?php
        } ?>
        <div class="form-group">
          <button type="submit" name="update" class="btn btn-success">Update</button>
          <?php if (Session::get("userid") == $getUinfo->public_id || Session::get("roleid") == '1') {?>
            <a class="btn btn-danger" href="userPassword.php?id=<?php echo $getUinfo->public_id;?>">Password change</a>
          <?php
          } ?>
        </div>
    </form>
  </div>
<?php } else {
  header('Location:index.php');
  die();
} ?>
</div>
</div>
<?php
  include 'inc/footer.php';
?>
</html>