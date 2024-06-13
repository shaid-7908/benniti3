<head>
  <meta charset="utf-8">
  <title>Bennit - Manufacturing Exchange</title>
  <link rel="stylesheet" href="assets/bootstrap.min.css">
  <link href="https://use.fontawesome.com/releases/v5.15.0/css/all.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="assets/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="apple-touch-icon" sizes="57x57" href="icons/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="icons/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="icons/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="icons/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="icons/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="icons/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="icons/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="icons/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-icon-180x180.png">
  <link rel="icon" type="image/png" href="icons/Bennit.png">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="icons/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <link rel="manifest" href="/manifest.json">
  <style type="text/css">
    #register_form fieldset:not(:first-of-type) {
      display: none;
    }
  </style>
  <script src='https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script type="text/javascript" src="scripts/form.js"></script>
 
</head>
<body>

<?php
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
  Session::destroy();
}
?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark card-header">
  <a class="navbar-brand d-flex flex-column" style="color: #f5a800;" href="index.php"><img src="assets/Bennit.png" style="width:176px;height:44px">
 <span style="font-size: small;">Manufacturing Exchange</span></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav ml-auto">
    <?php if (Session::get('userid') == TRUE) { ?>
      <?php if (checkUserAuth('admin_panel', Session::get('roleid'))) { ?>
        <li class="nav-item">
            <a class="nav-link" href="admin.php"><i class="fas fa-users-cog mr-2"></i>Admin</a>
        </li>
      <?php  } ?>
      <li class="nav-item">
        <a class="nav-link" href="userProfile.php?userid=<?php echo Session::get("userid"); ?>"><svg width="28" height="32" viewBox="0 0 28 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 23.2C13.5 23.2 11.29 21.92 10 20C10.03 18 14 16.9 16 16.9C18 16.9 21.97 18 22 20C21.3389 20.9844 20.4459 21.7912 19.3996 22.3492C18.3533 22.9072 17.1858 23.1994 16 23.2ZM16 9C16.7956 9 17.5587 9.31607 18.1213 9.87868C18.6839 10.4413 19 11.2044 19 12C19 12.7956 18.6839 13.5587 18.1213 14.1213C17.5587 14.6839 16.7956 15 16 15C15.2044 15 14.4413 14.6839 13.8787 14.1213C13.3161 13.5587 13 12.7956 13 12C13 11.2044 13.3161 10.4413 13.8787 9.87868C14.4413 9.31607 15.2044 9 16 9ZM16 6C14.6868 6 13.3864 6.25866 12.1732 6.7612C10.9599 7.26375 9.85752 8.00035 8.92893 8.92893C7.05357 10.8043 6 13.3478 6 16C6 18.6522 7.05357 21.1957 8.92893 23.0711C9.85752 23.9997 10.9599 24.7362 12.1732 25.2388C13.3864 25.7413 14.6868 26 16 26C18.6522 26 21.1957 24.9464 23.0711 23.0711C24.9464 21.1957 26 18.6522 26 16C26 10.47 21.5 6 16 6Z" fill="white" />
              </svg><?php echo Session::get("username"); ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="?action=logout"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
      </li>
    <?php } else { ?>
      <li class="nav-item">
        <a class="nav-link" href="register.php"><i class="fas fa-user-plus mr-2"></i>Register</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
      </li>
    <?php } ?>
    </ul>
  </div>
</nav>
