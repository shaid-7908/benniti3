<?php
include "inc/header.php";

$unreadmessages = $opportunities->getAllUnreadMessages();

echo !empty($unreadmessages) ? 'true' : 'false';

?>