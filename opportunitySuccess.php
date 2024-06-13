<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
if($_GET['opportunity_id']){
     $message = 'New Opportunity created';
     $type = "opportunity_created";
     $opty_id = $_GET['opportunity_id'];
     $opportunities->addOpportunityCreationToMessageQue($message,$type,$opty_id);
}
?>

<div class="opprotunity-success-main-container" >
     <div class="opportunity-success-second-container">
          <svg width="96" height="96" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
               <mask id="mask0_186_19137" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="14" y="6" width="68" height="84">
                    <path d="M76 8H20C18.9391 8 17.9217 8.42143 17.1716 9.17157C16.4214 9.92172 16 10.9391 16 12V84C16 85.0609 16.4214 86.0783 17.1716 86.8284C17.9217 87.5786 18.9391 88 20 88H76C77.0609 88 78.0783 87.5786 78.8284 86.8284C79.5786 86.0783 80 85.0609 80 84V12C80 10.9391 79.5786 9.92172 78.8284 9.17157C78.0783 8.42143 77.0609 8 76 8Z" fill="white" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M34 60H62M34 72H48M60 26L44 42L36 34" stroke="black" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
               </mask>
               <g mask="url(#mask0_186_19137)">
                    <path d="M0 0H96V96H0V0Z" fill="#F5A800" />
               </g>
          </svg>
          <h1 class="opportunity-success-header-text">Opportunity created!</h1>
          <p class="opportunity-success-p-text" style="">Next, view Solvers who might be a good fit for your opportunity, or create another opportunity.</p>
          <div style="display: flex; gap: 12px; margin-top: 25px">
               <a href="findsolver.php" style="color: inherit;text-decoration: none;">
                    <div style="padding: 3px 10px; background: #F5A800;
 font-size: 16px; color: #012B33; font-weight: 700; line-height: 24px;
        letter-spacing: 0px; border-radius: 4px; cursor: pointer; outline: none; border: none; text-align: left;">View Solvers</div>
               </a>
               <a href="opportunity.php?action=create_opportunity">
                    <button style="
font-size: 16px;
font-weight: 700;
line-height: 24px;
letter-spacing: 0px;
padding: 8px, 12px, 8px, 12px;
text-align: left;
color: #F5A800;
border-radius: 4px;
background: transparent;
border: 2px solid #F5A800;
cursor: pointer;
outline: none;
">Create Another Opportunity</button>
               </a>
          </div>
     </div>
     <div>
          <p style="width: 144px;
height: 24px;
top: 600px;
left: 646px;
margin-top: 25px;
font-size: 16px;
font-weight: 400;
line-height: 24px;
letter-spacing: 0px;
text-align: left;
cursor: pointer;
"><a href="index.php" style="color: #F5A800; text-decoration: none;">Back to Dashboard</a>
          </p>
     </div>
</div>
<footer style=" background-color: #012b33 !important;
  padding-bottom: 18px !important;
  border-top: 2px solid #024552;
  padding-top: 18px !important;
  padding-left: 10px;
  padding-right: 10px;
  position: fixed;
  bottom: 0;
  width: 100%;">
     <span style="width: 70px;
height: 22px;
top: 24px;
left: 32px;
font-size: 16px;
font-weight: 500;
line-height: 22px;
letter-spacing: 0.10000000149011612px;
text-align: left;
color: #F5A800;
text-decoration: underline;
cursor: pointer;
">Bennit.AI</span>
</footer>


</body>

</html>