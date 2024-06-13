<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';

if(isset($_GET['token']) && $_GET['token'] != "" && $_GET['userid'] != ""){
     if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $result =$users->changePasswordByToken($_GET['userid'],$_GET['token'],$_POST);
        echo $result;
     }
}

?>

<div style="width: 100vw;height: 80vh;">
  <div class="card-header" style="display: flex; justify-content: center;">
    <h3><i class="fas fa-lock mr-2"></i>Change password <span class="float-right"></h3>
  </div>
  <div class="card-body">

    <div style="width:600px; margin:0px auto">
      <form id="frm-password" name="frm-password" class="" action="" onsubmit="return checkPasswordMatchAndSubmit()" method="POST">
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
<?php
include 'inc/footer.php';
?>
</html>
