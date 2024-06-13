<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
$views->showAndClearPendingMessage();

?>

<div style="background: #012B33; height: 82vh; width: 100vw; display: flex; align-items: center; justify-content: center; flex-direction: column;">
     <div style="background: #024552; height: 60vh; width: 55vw; display: flex; align-items: center; justify-content: center; flex-direction: column; border-radius: 4px;">
        <svg width="68" height="84" viewBox="0 0 68 84" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_186_21723" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="68" height="84">
            <path d="M62 2H6C4.93913 2 3.92172 2.42143 3.17157 3.17157C2.42143 3.92172 2 4.93913 2 6V78C2 79.0609 2.42143 80.0783 3.17157 80.8284C3.92172 81.5786 4.93913 82 6 82H62C63.0609 82 64.0783 81.5786 64.8284 80.8284C65.5786 80.0783 66 79.0609 66 78V6C66 4.93913 65.5786 3.92172 64.8284 3.17157C64.0783 2.42143 63.0609 2 62 2Z" fill="white" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M20 54H48M20 66H34M46 20L30 36L22 28" stroke="black" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </mask>
            <g mask="url(#mask0_186_21723)">
            <path d="M-14 -6H82V90H-14V-6Z" fill="#F5A800"/>
            </g>
            </svg>            
          <h1 style="
          font-size: 48px;
          font-weight: 700;
          line-height: 72px;
          letter-spacing: 0em;
          color: #FFFFFF;
          text-align: left;
          margin-top: 15px;
          ">Solver profile created!</h1>
          <p style="
          font-size: 16px;
          font-weight: 400;
          line-height: 24px;
          letter-spacing: 0px;
          text-align: left;
          margin-right: 70px;
          margin-top: 5px;
          ">You’re ready to start matching with the perfect Opportunities.</p>
          <div style="margin-right: 315px;">

             <a href="findopportunity.php">

             
              <button style="background-color: #F5A800; padding: 10px 15px; border-radius: 4px; border: none;
              color: #012B33;
              font-size: 16px;
              font-weight: 700;
              line-height: 24px;
              letter-spacing: 0px;
              text-align: left;
              margin-top: 18px;
              cursor: pointer;
              ">View Opportunities</button>
              </a>
          </div>
        </div>
        <a href="index.php" style="
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
        letter-spacing: 0px;
        text-align: left;
        color: #F5A800;
        margin-top: 20px;
        cursor: pointer;
        ">Back to Dashboard</a>
</div>
  <footer style="height: 8vh; width: 100vw; display: flex; justify-content: start; align-items: center; background-color: #012B33; padding-left: 40px; border-top: 2px solid #024552">
       <a href="">
        <h1 style=" color: #F5A800;
        font-size: 16px;
        font-weight: 500;
        line-height: 22px;
        text-decoration: underline;
        ">Bennit.Ai</h1>
       </a>
  </footer>
</html>