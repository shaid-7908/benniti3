
<?php
include 'inc/header.php';

header('Content-Type: application/json');

$response = array();

if(isset($_POST['email'])) {
    // Include your user management class or logic here
    // For example:
    // include 'path_to_your_user_management.php';
    // $users = new UserManager();

    // Get the email from the POST data
    $email = $_POST['email'];

    // Check if the email exists
    $emailExists = $users->checkEmailExists($email);

    // Set response based on the email existence
    if($emailExists) {
        // Email exists
        $response['status'] = 'exists';
    } else {
        // Email does not exist
        $response['status'] = 'not_exists';
    }
} else {
    // If the email parameter is not set in the POST request, set an error response
    $response['status'] = 'error';
}

// Send the JSON response
echo json_encode($response);
?>

