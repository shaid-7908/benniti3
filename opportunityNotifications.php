<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();


$allMessages = $opportunities->getAllMessages();

?>
<div class="xt-card-organization1">
    <div class="xt-sidebar-organization1">
        <?php include 'inc/sidebar.php' ?>
        <div style="border-top: 2px solid #053B45;padding: 8px;">
            <a href="https://www.bennit.ai/" target="_blank">
                <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
                    Bennit.Ai
                </span>
            </a>
        </div>
    </div>
    <div class="xt-body-organization1 p-4" style="color: black;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">opportunity title</th>
                    <th scope="col">Message</th>
                    <th scope="col">Created</th>
                    <th scope="col">Created by</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($allMessages as $nMessages) :
                ?>
                    <tr>
                        <th scope="row"><?php echo $nMessages->message_id; ?></th>
                        <td><?php
                            if ($nMessages->opportunity_id != "na") {
                                $op_details = $opportunities->getOpportunityInfoById($nMessages->opportunity_id);
                                echo $op_details->headline;
                            } else {
                                $m_data = $matches->getALLMatchdataBypublicid($nMessages->matched_id);
                                $op_details = $opportunities->getOpportunityInfoByRealId($m_data[0]->fk_opportunity_id);
                                echo $op_details->headline;
                            }

                            ?>
                        <td><?php echo $nMessages->message; ?></td>

                        </td>
                        <td>
                            <?php
                            $current_time = new DateTime('now', new DateTimeZone('UTC'));

                            // Convert the created_at timestamp to a DateTime object
                            $created_at_time = new DateTime($nMessages->created_at, new DateTimeZone('UTC'));

                            // Calculate the difference between the current time and the created_at time
                            $time_diff = $current_time->diff($created_at_time);

                            // Format and display the time difference
                            if ($time_diff->y > 0) {
                                echo $time_diff->format('%y years ago');
                            } elseif ($time_diff->m > 0) {
                                echo $time_diff->format('%m months ago');
                            } elseif ($time_diff->d > 0) {
                                echo $time_diff->format('%d days ago');
                            } elseif ($time_diff->h > 0) {
                                echo $time_diff->format('%h hours ago');
                            } elseif ($time_diff->i > 0) {
                                echo $time_diff->format('%i minutes ago');
                            } else {
                                echo 'Just now';
                            }

                            ?></td>

                        <td>
                            <?php
                            if($nMessages->opportunity_id == 'na'){
                                $m_data = $matches->getALLMatchdataBypublicid($nMessages->matched_id);
                                $U_data = $users->getUserInfoById($m_data[0]->matched_by);
                                echo $U_data->email;
                            }else{
                                $op_details = $opportunities->getOpportunityInfoById($nMessages->opportunity_id);
                                $U_data = $users->getUserInfoByRealId($op_details->fk_user_id);
                                echo $U_data->email;
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($nMessages->is_read) {
                                echo '<span class="text-success">seen<span>';
                            } else {
                                echo '<span style="color:red">unseen<span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($nMessages->opportunity_id == 'na') {

                            ?>
                                <a style="color: inherit;text-decoration: none;" href="adminMatchView.php?matchedid=<?php echo $nMessages->matched_id ?>&&messageid=<?php echo $nMessages->message_id ?>">
                                <?php
                            } else {


                                ?>
                                    <a style="color: inherit;text-decoration: none;" href="adminOpportunityView.php?opportunityid=<?php echo $nMessages->opportunity_id; ?>">
                                    <?php } ?>
                                    <div class="inter-font font-500" style="height: 30px;width:70px;background-color: #F5A800;display: flex;justify-content: center; align-items: center;border-radius: 4px;">
                                        <span> View </span>
                                    </div>
                                    </a>
                        </td>
                    </tr>
                <?php
                endforeach;
                ?>

            </tbody>
        </table>

    </div>

</div>

<script>
    var userid = <?php echo json_encode(Session::get('userid')); ?>;
    var isRequestPending = false; // Flag to prevent duplicate requests

    if (userid) {
        function longPolling() {
            if (isRequestPending) {
                return; // Prevent duplicate requests during previous call
            }

            isRequestPending = true; // Mark request as pending

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    isRequestPending = false; // Reset flag for next request

                    if (this.status == 200) {
                        var hasUnreadMessages = this.responseText.trim() === 'true';
                        var badge = document.querySelector('.notification-icon');
                        badge.style.display = hasUnreadMessages ? 'block' : 'none';

                        // Schedule the next long-polling request after a delay
                        setTimeout(longPolling, 5000); // Adjust delay as needed (e.g., 3000-5000ms)
                    } else {
                        console.error("Error fetching unread messages:", this.statusText);
                        // Handle errors gracefully (e.g., retry after a longer delay)
                    }
                }
            };

            // Send a GET request with a timeout parameter for long-polling
            xhttp.open("GET", "apiCheckUnredMessages.php", true);
            xhttp.send();
        }

        longPolling(); // Initiate the first long-polling request
    }
</script>