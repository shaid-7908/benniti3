<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
/* 
  This is a single page that presents multiple options, depending on where the
  user is at in on-boarding into the site. Progress is tracked through a session
  variable (object). Onboarding is triggered by a `is_firstrun` field in the user
  table, and if the user has no organization (a required structure for most actions
  on the site).
  */
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();

$pendingMsg = Session::get("pendingMsg");
if (isset($pendingMsg)) {
    echo $pendingMsg;
}
Session::set("pendingMsg", NULL);

$onboardStep = "usecase";
if (isset($_GET["step"]) && $_GET["step"] != "") {
    $onboardStep = $_GET["step"];
}
if ($onboardStep == "reset") {
    Session::set("onboarding", NULL);
    $onboardStep = "usecase";
}
?>


<?php
// Set primary use-case
switch ($onboardStep) {
    case "usecase": {
            $onboardingData = new stdClass;
            $onboardingData->resumeUrl = "onBoard.php?step=usecase";
            Session::set("onboarding", $onboardingData);
?>

            <div class="container">
                <div class=" card-onBoard">
                    <span style="letter-spacing: 0.15px;">Bennit Ai</span>

                    <div class="onboard-page-main-header-container" >
                        <div style="flex:1 ;text-align: left;">
                            <h3 class="onboard-page-main-header-tagline">The <br> Manufacturing Exchange™</h3>
                        </div>
                        <div style="flex:1;display: flex; flex-direction: column;justify-content: end;">

                            <p class=" onboard-page-main-header-text">At Bennit, we believe that the key to manufacturing success lies in matching manufacturing challenges with the right subject matter experts, services, or products.

                            </p>
                            <p class="mt-2 onboard-page-main-header-text" >It is this principle that guided us in developing The Manufacturing Exchange, a dedicated platform where manufacturers or those facing manufacturing challenges can easily find and access the expertise they need.

                            </p>


                        </div>
                    </div>
                    <div class="card-body pr-2 pl-2">
                        <div class="div" style="width:100%">
                            <span style="font-weight: 700; font-size: 20px;">How do you want to use The Manufacturing Exchange?</span>
                            <div class="">
                                <div class="onboard-choose-role-section" style=" width: 100%;">

                                    <div align="center" style="flex:1;height:45vh; border:2px solid #F5A800" class="onboard-choice-icon mr-md-1 my-2 p-4">
                                        <a href="?step=seeker" class="my-2">
                                            <svg width="151" height="151" viewBox="0 0 151 151" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M137.133 127.117L109.121 99.0939C117.52 88.1491 121.441 74.4195 120.089 60.69C118.737 46.9605 112.214 34.2592 101.842 25.1628C91.4695 16.0663 78.0256 11.2558 64.2371 11.7071C50.4485 12.1583 37.3479 17.8376 27.5928 27.5928C17.8376 37.3479 12.1583 50.4485 11.7071 64.2371C11.2558 78.0256 16.0663 91.4695 25.1628 101.842C34.2592 112.214 46.9605 118.737 60.69 120.089C74.4195 121.441 88.1491 117.52 99.0939 109.121L127.129 137.162C127.788 137.821 128.569 138.343 129.429 138.699C130.29 139.056 131.212 139.239 132.143 139.239C133.074 139.239 133.996 139.056 134.856 138.699C135.716 138.343 136.498 137.821 137.156 137.162C137.815 136.504 138.337 135.722 138.693 134.862C139.05 134.002 139.233 133.08 139.233 132.149C139.233 131.218 139.05 130.296 138.693 129.435C138.337 128.575 137.815 127.793 137.156 127.135L137.133 127.117ZM25.9532 66.0626C25.9532 58.1297 28.3056 50.375 32.7129 43.779C37.1202 37.1831 43.3844 32.0422 50.7134 29.0064C58.0424 25.9706 66.1071 25.1763 73.8876 26.7239C81.668 28.2716 88.8148 32.0916 94.4242 37.701C100.034 43.3104 103.854 50.4572 105.401 58.2377C106.949 66.0181 106.155 74.0828 103.119 81.4118C100.083 88.7408 94.9421 95.0051 88.3462 99.4123C81.7502 103.82 73.9955 106.172 66.0626 106.172C55.4283 106.161 45.2327 101.932 37.7131 94.4121C30.1935 86.8925 25.9642 76.6969 25.9532 66.0626Z" fill="#F5A800" />
                                            </svg>

                                            <h3 class="text-white" style="font-weight: 700;font-size: 24px;"><br />I'm a Seeker</h3>
                                            <span class="text-white" style="font-weight: 400;font-size: 16px;">I have a problem I want to solve, or <br /> an opportunity to improve.</span>
                                        </a>
                                    </div>
                                    <div align="center" style="flex:1;height:45vh; border:2px solid #F5A800" class="onboard-choice-icon ml-md-1 my-2 p-4">
                                        <a href="?step=solver" class="my-2">

                                            <svg width="151" height="151" viewBox="0 0 151 151" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M121.878 113.122L113.122 121.878C111.966 123.027 110.403 123.671 108.774 123.671C107.145 123.671 105.582 123.027 104.427 121.878L31.1666 48.7416C29.1613 49.3225 27.0874 49.6336 24.9999 49.6666C21.0736 49.6638 17.2047 48.7238 13.7149 46.9247C10.225 45.1256 7.21504 42.5194 4.93521 39.3228C2.65537 36.1262 1.17155 32.4316 0.607097 28.546C0.0426456 24.6605 0.413876 20.6964 1.68992 16.9832L17.3533 32.6466L20.6216 29.3783L29.3783 20.6216L32.6466 17.3533L16.9833 1.68992C20.6964 0.41387 24.6605 0.0426399 28.5461 0.607091C32.4316 1.17154 36.1262 2.65537 39.3228 4.9352C42.5194 7.21503 45.1256 10.225 46.9247 13.7149C48.7238 17.2047 49.6638 21.0736 49.6666 24.9999C49.6336 27.0874 49.3225 29.1613 48.7416 31.1666L121.878 104.427C123.027 105.582 123.671 107.145 123.671 108.774C123.671 110.403 123.027 111.966 121.878 113.122ZM2.12159 104.427C0.973043 105.582 0.328369 107.145 0.328369 108.774C0.328369 110.403 0.973043 111.966 2.12159 113.122L10.8783 121.878C12.0337 123.027 13.5966 123.671 15.2258 123.671C16.8549 123.671 18.4179 123.027 19.5733 121.878L53.3049 88.2083L35.8533 70.7566M111.333 0.333252L86.6666 12.6666V24.9999L73.2849 38.3816L85.6183 50.7149L98.9999 37.3333H111.333L123.667 12.6666L111.333 0.333252Z" fill="#F5A800" />
                                            </svg>

                                            <h3 class="text-white" style="font-weight: 700;font-size: 24px;"><br />I'm a Solver</h3>
                                            <span class="text-white" style="font-weight: 400;font-size: 16px;">I have skills and experience I want to <br /> share with others. </span>
                                        </a>
                                    </div>

                                </div>
                                <div style="padding-top: 25px">
                                    <p>Both scenarios apply to you? Want to change roles in the future? Don't worry! This decision isn't final, just a place to start...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <?php
            include 'inc/footer.php';
            ?>
        <?php
            break;
        }
    case "seeker": {
            $onboardingData = new stdClass;
            $onboardingData->type = "seeker";
            $onboardingData->resumeUrl = "onBoard.php?step=seeker";
            $onboardingData->completedUrl = "onBoard.php?step=last";
            Session::set("onboarding", $onboardingData);
        ?>
            <div class="onboard-page-case-seeker">
                <div class="card-seeker-onboar">
                    <tr>
                        <td colspan="3">
                            <p class="text-center card-seeker-onboard-text" >Bennit can help! We'll search our database for Solvers with the skills and expertise you need.</p>
                            <p class="text-center card-seeker-onboard-text" >To get started, we'll define the Organization that you represent, so we can collect all the information necessary to match you with the best resources!</p>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="center" class="onboard-choice-icon">
                            <a href="organization.php?action=create_organization&dummy_step=seeker" style="text-decoration: none; color:inherit;">
                                <div class="card-seeker-onboard-button" >
                                    <i class="fas fa-building mr-2"></i>
                                    Create Organization
                                </div>
                            </a>
                        </td>
                        <td></td>
                    </tr>
                </div>
            </div>
            <?php
            include "inc/footer2.php";
            ?>
        <?php
            break;
        }
    case "solver": {
            $onboardingData = new stdClass;
            $onboardingData->type = "solver";
            $onboardingData->resumeUrl = "onBoard.php?step=solver";
            $onboardingData->completedUrl = "onBoard.php?step=profile";
            Session::set("onboarding", $onboardingData);
        ?>
            <div class="onboard-page-step-solver">
                <div class="card" style="background-color: #024552;border-radius: 4px; padding:8px">

                    <tr>
                        <td colspan="4">
                            <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; padding: 15px 30px; margin-top: 10px;">
                                <p style="font-weight: 700; font-size: 20px;">Bennit can help! We'll search our database for Seekers with challenges that you can help with!</p>
                                <p class="onboard-page-step-solver-secondary-text" >To get started, we need to connect you with an Organization that can receive payments and handle the necessary tax and legal documents.<br>
                                    If you already have a business or LLC, you can use that. If you don't have an organization, Bennit can represent you -- we'll just need to collect some extra information.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <div style="display: flex; justify-content: space-around; height: 40vh; align-items: center; width: 100%;">
                        <tr>
                            <td></td>
                            <div style="border: 3px solid #F5A800; display: flex; justify-content: center; height: 290px; align-items: center; width: 380px;">
                                <td align="center" style="padding-left: 20%" class="onboard-choice-icon">
                                    <a href="organization.php?action=create_organization&dummy_step=solver" style="text-decoration: none; text-align: center;">
                                        <i class="fas fa-building mr-2" style="font-size: 70px; color: #F5A800;"></i>
                                        <h3 style="
                                font-size: 24px;
                                font-weight: 700;
                                line-height: 32px;
                                letter-spacing: 0em;
                                color: #FFFFFF;
                                text-align: center;
                                ">
                                            <br />Use My Organization
                                        </h3>
                                        <span style="color: #FFFFFF; font-size: 16px; font-weight: 400; line-height: 24px;"> I'll enter information about my<br />existing organization.</span>
                                    </a>
                                </td>
                            </div>
                            <div style="height: 290px; width: 380px; border: 3px solid #F5A800; display: flex; justify-content: center; align-items: center;">
                                <td align="center" style="padding-right: 20%" class="onboard-choice-icon">
                                    <a href="?step=addtobennit" style="text-decoration: none; text-align: center;">
                                        <i class="fas fa-hands-helping mr-2" style="font-size: 75px; color: #F5A800; margin-bottom: 25px;"></i>
                                        <h3 style="
                                font-size: 24px;
                                font-weight: 700;
                                line-height: 32px;
                                letter-spacing: 0em;
                                text-align: center;
                                color: #FFFFFF;
                                ">I Want Bennit's Help</h3>
                                        <span style="color: #FFFFFF; align-items: center; font-size: 16px; font-weight: 400; line-height: 24px;">I'm independent and need <br />representation.</span>
                                    </a>
                                </td>
                            </div>
                            <td></td>
                        </tr>
                    </div>

                </div>
            </div>
            <?php
            include "inc/footer2.php";
            ?>
        <?php
            break;
        }
    case "addtobennit": {
            $bennitOrgId = $organizations->getPublicId(1);
            $result = $organizations->addUserToOrganization($bennitOrgId, Session::get("userid"), 100, $users);
            if (strpos($result, "<script>") !== false) {
                $onboardingData = new stdClass;
                $onboardingData->type = "solver";
                $onboardingData->resumeUrl = "onBoard.php?step=addtobennit";
                $onboardingData->completedUrl = "onBoard.php?step=last";
                Session::set("onboarding", $onboardingData);
                Session::set("pendingMsg", NULL);
            } else {
                echo $result;
            }
        ?>
            <div style="display: flex; justify-content: center; align-items: center;">
                <div style="background-color: #024552; height: 70vh; width: 70vw; display: flex; justify-content: center; align-items: center; margin-top: 10px; border-radius: 4px; flex-direction: column; ">
            <tr>
                <td colspan="3">
                   <p>Thanks for giving us the opportunity to help you connect! We'll be in touch shortly to gather some additional information to get you set-up!</p>
                    <p style="font-weight: 700; font-size: 20px;line-height: 28px; letter-spacing: 0.15000000596046448px;">Until then, you can tell us about your experience and skills by creating a Solver Profile...
                    </p>
                </td>
            </tr>
            <tr>
                <td></td>
               <div style=" border: 3px solid #F5A800; height: 38vh; width: 25vw; display: flex;justify-content: center; align-items: center;">
               <td align="center" style="padding-left: 10%; padding-right: 10%;" class="onboard-choice-icon">
                    <a href="solver.php?action=create_solver&orgid=1" style="text-align: center; text-decoration: none;">
                    <i class="fas fa-briefcase mr-2" style="font-size: 70px; color: #F5A800;margin-bottom: 25px"></i>
                        <h3 style="font-size: 25px; color: #FFFFFF; font-weight: 700">Solver Profile</h3>
                       <span style="font-size: 16px; font-weight: 400; line-height: 24px;  color: #FFFFFF; text-align: center;"> Create a Profile to tell others about <br> your skills and experience!</span>
                    </a>
                </td>
               </div>
                <td></td>
            </tr>
            </div>
            </div>
            <?php
            include "inc/footer2.php";
            ?>
        <?php
            break;
        }
    case "profile": {
            $onboardingData = new stdClass;
            $onboardingData->type = "solver";
            $onboardingData->resumeUrl = "onBoard.php?step=profile";
            $onboardingData->completedUrl = "onBoard.php?step=last";
            Session::set("onboarding", $onboardingData);
        ?>
        <div style="background-color:#012B33; height: 80vh; width: 100vw; display: flex; justify-content: center; align-items: text-center">
          <div style=" background-color: #024552; height: 65vh; width: 65vw;display: flex; align-items: center; justify-content: center; flex-direction: column; border-radius: 4px; margin-top: 50px; ">
          <tr>
                <td colspan="3">
                    <p style="font-size: 20px; font-weight: 700;"><b>Your organization is setup!</b></p>
                    <p>Next we'll gather some information about your capabilities and background, to help match you with the best opportunities...
                    </p>
                </td>
            </tr>
            <tr>
                <td></td>
                <td align="center" style="padding-left: 10%; padding-right: 10%;" class="onboard-choice-icon">
                <div style="display: flex; align-items: center; justify-content: center; width: 32vw; height: 35vh; border: 3px solid #F5A800">
                    <a href="solver.php?action=create_solver&orgid=<?php echo $_GET["orgid"]; ?>" style=" text-decoration: none; text-align: center;">
                    <i class="fas fa-briefcase mr-2" style="font-size: 70px; color: #F5A800 "></i>
                        <h3 style="color: #FFFFFF; font-size: 35px; font-weight: 700; margin-top: 20px;">Solver Profile</h3>
                        <span style="color: #FFFFFF;">Create a Profile to tell others about your skills and experience!</span>
                    </a>
                </div>
            </td>
                <td></td>
            </tr>
            </div>
            </div>
            <!-- This is footer for onBoard.php?step=profile&orgid -->
            <footer style="height: 10vh; width: 100vw; display: flex; justify-content: start; align-items: center; background-color: #012B33; padding-left: 40px; border-top: 2px solid #024552">
       <a href="">
        <h1 style=" color: #F5A800;
        font-size: 16px;
        font-weight: 500;
        line-height: 22px;
        text-decoration: underline;
        ">Bennit.Ai</h1>
       </a>
  </footer>
        <?php
            break;
        }
    case "last": {
            $onboardingData = Session::get("onboarding");
            $onboardingData->resumeUrl = "onBoard.php?step=profile";
            $onboardingData->completedUrl = null;
            Session::set("firstrun", 0);
        ?>
            <div style="display: flex; justify-content: center;align-items: center; width:100vw;background-color: #012B33;height:90vh">
                <div class="card-seeker-onboar-last">
                    <div style=" display: flex; flex-direction: column; justify-content: center; align-items: center;">

                        <svg width="96" height="96" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_186_19137" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="14" y="6" width="68" height="84">
                                <path d="M76 8H20C18.9391 8 17.9217 8.42143 17.1716 9.17157C16.4214 9.92172 16 10.9391 16 12V84C16 85.0609 16.4214 86.0783 17.1716 86.8284C17.9217 87.5786 18.9391 88 20 88H76C77.0609 88 78.0783 87.5786 78.8284 86.8284C79.5786 86.0783 80 85.0609 80 84V12C80 10.9391 79.5786 9.92172 78.8284 9.17157C78.0783 8.42143 77.0609 8 76 8Z" fill="white" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M34 60H62M34 72H48M60 26L44 42L36 34" stroke="black" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            </mask>
                            <g mask="url(#mask0_186_19137)">
                                <path d="M0 0H96V96H0V0Z" fill="#F5A800" />
                            </g>
                        </svg>

                        <p style="font-size: 25px;"><b>Great Job!</b></p>
                        <p style="width: 60%; text-align: center;">Congratulations on setting up you Seeker profile.</p>
                        <div style="display: flex;">
                            <?php if ($onboardingData->type == "solver") { ?>
                                <p><span class='link-action'><a href="solverDetail.php?solverid=<?php echo $_GET["solverid"]; ?>">Add Details</a></span> Enhance your Solver profile to help find more opportunities.</p>
                            <?php
                            } else {
                            ?>
                                <a href="opportunity.php?action=create_opportunity&orgid=<?php echo $_GET["orgid"]; ?>" style="text-decoration: none; color:inherit">
                                    <div style="width: 200px; height:40px; border: 2px solid #F5A800;font-weight: 700;  border-radius: 4px; padding:8px 12px 8px 12px;">Create Opportunities</div>
                                </a>
                            <?php
                            }
                            ?>
                            <a href="index.php" class="ml-2" style="text-decoration: none; color:inherit">
                                <div style="width: 180px; height:40px; background-color: #F5A800; color:black; border-radius: 4px; font-weight: 700; padding:8px 12px 8px 12px;">Go to Dashboard</div>
                            </a>
                        </div>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between;  background-color: #012b33 !important;
                                                        padding-bottom: 18px !important;
                                                        padding-top: 18px !important;
                                                        padding-left: 10px;
                                                        padding-right: 10px;
                                                        position: fixed;
                                                        bottom: 0;
                                                        width: 100%;
                                                        border-top: 1px solid #024552;">

                    <span><a style="color:#f5a706 !important" href="https://www.bennit.ai">Bennit.ai</a></span>
                </div>

                </body>
            <?php
            break;
        }
    default: {
            ?>
                <tr>
                    <td colspan="4">
                        <p>Something went wrong!</p>
                        <p>Let's <a href="?action=reset">start again...</a></p>
                    </td>
                </tr>
    <?php
            break;
        }
}
    ?>
            </div>
            </div>
            </div>
            </div>



</html>