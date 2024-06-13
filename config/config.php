<?php
//Database config
define('DB_HOST', 'localhost');
define('DB_NAME', 'bennit');
define('DB_USER', 'dev');
define('DB_PASS', 'Str0ngP@ssword');
define('SHA_SEED', 'rAnDomSe3DYouCreate');
define('PARTNER_KEY', 1234);

//Google recaptcha config
define('RECAPTCHA_KEY', '');    //KEY FROM GOOGLE
//<div class="g-recaptcha" data-sitekey="SITEKEY FROM GOOGLE"></div>
define('RECAPTCHA_TAG', <<<RECAPTCHA
<!-- no recaptcha in dev -->
RECAPTCHA
);

//Google analytics config
$analytics_tag = <<<ANALYTICS
<!-- Google tag (gtag.js) -->
ANALYTICS;

//Stripe config
define('STRIPE_KEY_LIVE', "pk_live_EUXuNXMar0jrNzhz5hyFVyQk005Dk5NJUA");

define('STRIPE_KEY_SK_TEST',"sk_test_8z8Jqas7spRk9YPPIRLHPGQx00IisVst2m");
define('STRIPE_KEY_PK_TEST',"pk_test_0lvSd0ZdV3QS4tk95wm7pDT7001xPsCsws");
//Partner API access config
$partnerKeys = [0000, 1234, 9999];
$partner_permissions = array();
$partner_permissions['search_solvers'] = true;
$partner_permissions['search_opportunities'] = [9999];
$partner_permissions['detail_solvers'] = true;
$partner_permissions['de_annonymize_solvers'] = [9999];
$partner_permissions['de_annonymize_opportunities'] = [9999];
define('PARTNER_PERMISSIONS', $partner_permissions);

//User access config
$role_permissions = array();
$role_permissions['suggest_match'] = 3;
$role_permissions['suggest_match_orthogonal'] = 2;
$role_permissions['approve_match'] = 1;
$role_permissions['create_opportunity'] = 3;
$role_permissions['edit_opportunity'] = 3;
$role_permissions['edit_opportunity_orthogonal'] = 1;
$role_permissions['delete_opportunity'] = 3;
$role_permissions['delete_opportunity_orthogonal'] = 1;
$role_permissions['list_organizations'] = 2;
$role_permissions['create_organization'] = 3;
$role_permissions['create_organization_orthogonal'] = 1;
$role_permissions['edit_organization'] = 3;
$role_permissions['edit_organization_orthogonal'] = 1;
$role_permissions['delete_organization'] = 3;   //also requires use to be the org creator
$role_permissions['delete_organization_orthogonal'] = 1;
$role_permissions['list_solvers'] = 2;
$role_permissions['create_solver'] = 3;
$role_permissions['create_solver_orthogonal'] = 1;
$role_permissions['edit_solver'] = 3;
$role_permissions['edit_solver_orthogonal'] = 1;
$role_permissions['delete_solver'] = 3;
$role_permissions['delete_solver_orthogonal'] = 1;
$role_permissions['add_user_to_org'] = 1;
$role_permissions['add_user_to_org_orthogonal'] = 1;
$role_permissions['remove_user_from_org'] = 1;
$role_permissions['remove_user_from_org_orthogonal'] = 1;
$role_permissions['disable_user'] = 1;
$role_permissions['edit_user'] = 99;
$role_permissions['edit_user_orthogonal'] = 1;
$role_permissions['set_user_role'] = 1;
$role_permissions['enable_user'] = 1;
$role_permissions['delete_user'] = 1;
$role_permissions['login_user'] = 99;
$role_permissions['list_users'] = 1;
$role_permissions['list_users_orthogonal'] = 1;
$role_permissions['admin_panel'] = 1;
$role_permissions['demo_onboarding'] = 1;
$role_permissions['subscribe_org_orthogonal'] = 1;
$role_permissions['cancel_subscription_orthogonal'] = 1;
define('ROLE_PERMISSIONS', $role_permissions);
?>