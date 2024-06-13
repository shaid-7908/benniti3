<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();
?>
  <div class="card ">
    <div class="card-header">
      <h3><i class="fas fa-shopping-cart"></i> Subscription Required</h3>
    </div>
    <div class="card-body pr-2 pl-2">
      <table class="table table-striped table-bordered" style="width:100%">
        <tbody>
        
            <tr>
              <td colspan="8">
                <p>Before you can perform this action, you need to Subscribe.<br> Subscriptions enable creating and viewing matches and match details.</p>
                <p><span class='link-action'><a href="subscription.php?action=create_subscription">Subscribe Now</a></span>
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