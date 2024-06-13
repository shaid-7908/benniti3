<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  //Figure out what we're working on
  if (isset($_GET['id'])) {
    $userid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['id']);
  } else {
    $userid = Session::get('userid');
  }

  $allowed = false;
  //Check for admin level access
  if (checkUserAuth("edit_user_orthogonal", Session::get("roleid"))) {
    $allowed = true;
  } else {
    //Users are allowed to edit themselves.
    if ($userid == Session::get('userid')) {
        $allowed = true;
    }
  }

  if (!$allowed) {
    //Warn about disallowed action
    error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to change the password of user " . $userid . ", and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not change a password other than your own!"));
    header('Location:index.php');
    die();
  } else {
    $getUinfo = $users->getUserInfoById($userid);
    //Process requested actions
    if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['changepass'])) {
      $changePass = $users->changePasswordById($userid, $_POST);
      if (isset($changePass)) {
        //echo $changePass;
      }
    }
  }
?>
<script>
function checkPasswordMatchAndSubmit(frm) {
    controlledForm = frm;
    if (document.getElementById("new_password").value != document.getElementById("repeat_password").value) {
      alert ("New password does not match, cannot change password!");
      return false;
    }
    if (!document.getElementById("new_password").value.match(/^(?=.*[0-9])(?=.*[a-z])(?=.*[-+_!@#$%^&*., ?])([a-zA-Z0-9-+_!@#$%^&*., ?]{8,})$/)) {
      alert ("Password must be at least 8 characters long, and contain at least one special character, one alphabetic character and one number.")
      return false;
    }
    return true;
}
</script>
<div >
  <div class="card-header">
    <h3><i class="fas fa-lock mr-2"></i>Change password <span class="float-right"> <a href="userProfile.php?userid=<?php echo $userid; ?>" class="btn btn-reversed">Back</a> </h3>
  </div>
  <div class="card-body">

    <div style="width:600px; margin:0px auto">
      <form id="frm-password" name="frm-password" class="" action="" onsubmit="return checkPasswordMatchAndSubmit()" method="POST">
        <?php
        if (!checkUserAuth("edit_user_orthogonal", Session::get("roleid")) || $userid == Session::get('userid')) {
        ?>
        <div class="form-group">
          <label for="old_password">Old Password</label>
          <input type="password" name="old_password"  class="form-control">
        </div>
        <?php
        }
        ?>
        
        <div class="form-group">
          <label for="new_password">New Password (at least 8 characters, with at least one special character and one number)</label>
          <input type="password" name="new_password" id="new_password" class="form-control" pattern="(?=.*\d)(?=.*[a-z]).{8,}" required>
        </div>

        <div class="form-group">
          <label for="repeat_password">Repeat Password</label>
          <input type="password" name="repeat_password" id="repeat_password" class="form-control" required>
        </div>

        <div class="form-group">
          <button type="submit" name="changepass" class="btn btn-success">Change password</button>
        </div>
      </form>
    </div>

  </div>
</div>

<?php
  include 'inc/footer.php';
?>
</html>