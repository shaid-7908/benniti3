<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();
?>
<div class="xt-card-organization">
  <div class="xt-sidebar-organization">
   <?php include 'inc/sidebar.php' ?>
  <div style="border-top: 2px solid #053B45;padding: 8px;">
            <a href="https://www.bennit.ai/" target="_blank">
                <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
                    Bennit.Ai
                </span>
            </a>
        </div>
  </div>
  <div class="xt-body-organization">
    <div class="card " style="color: black;">
    <div class="card-header">
      <h3><i class="fas fa-building mr-2"></i>Organization Required</h3>
    </div>
    <div class="card-body pr-2 pl-2">
      <table class="table table-striped table-bordered" style="width:100%">
        <tbody>
        
            <tr>
              <td colspan="8">
                <p>Before you can perform this action, you need to create an Organization.<br> Opportunities and Solver Profiles belong to Organizations that store information about each potential match.</p>
                <p><span class='link-action'><a href="organization.php?action=create_organization">Create an Organization Now</a></span>
              </td>
            </tr>

        </tbody>
      </table>
    </div>
  </div>
  </div>

</div>

  

</html>