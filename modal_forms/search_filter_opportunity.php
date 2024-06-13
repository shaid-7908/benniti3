<?php
$docRoot = "../";
include_once $docRoot . "config/config.php";
include_once $docRoot . "inc/common.php";
include_once $docRoot . "lib/Session.php";
require_once $docRoot . "vendor/autoload.php";
$snowflake = new \Godruoyi\Snowflake\Snowflake;
include_once $docRoot . "classes/Opportunities.php";
include_once $docRoot . "classes/Users.php";
include_once $docRoot . "classes/Solvers.php";
include_once $docRoot . "classes/Matches.php";
include_once $docRoot . "classes/Skills.php";
Session::init();
$opportunities = new Opportunities();
$users = new Users();
$solvers = new Solvers();
$matches = new Matches();
$skills = new Skills();
$userid = $users->getRealId(Session::get('userid'));


if ($_POST['action'] == 'searchResult' && $_POST['query'] == '') {
    $allLatesOpt = $opportunities->advancedGetAllOpportunities($userid);
    // print_r($allLatesOpt);
    makeOpportunityCard($allLatesOpt);
}elseif ($_POST['action'] == 'searchResult' && $_POST['query'] != '') {
    $searchedOpt = $opportunities->advancedOpportunitySearch($_POST['query'],$userid);
    // print_r($allLatesOpt);
    makeOpportunityCard($searchedOpt);
}




function makeOpportunityCard($Opportunities)
{   
    if($_POST['query'] != ''){
        echo '<div class="poppins-font font-24 font-700 mb-4" style="padding-left:10px; padding-right:10px;"> Results for "'.$_POST['query'].'</div>';
    }else{
        echo '<div class="poppins-font font-24 font-700 mb-4" style="padding-left:10px; padding-right:10px;">Available opportunities</div>';
    }
    echo '<div class="inter-font font-400 font-14" style="padding-left:10px; padding-right:10px;"> <span class="inter-font font-700 font-14">'.count($Opportunities).'</span> opportunities available </div>';
    echo '<div class="grid-container">';
    foreach ($Opportunities as $opportunity) {
        echo '
               <div class="card" style="color: black; border-radius: 4px;padding: 4px; border:2px solid #E7E7E8;">
                            <?php // header 
                            ?>
                            <div class="card-body">


                                <div>
                                    <h3 style="font-size: 20px;">' . $opportunity->headline . '</h3>
                                </div>
                                <?php // second part 
                                ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <?php // rate ==== 
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div><svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7.99988 3L7.84338 3.14L2.13988 8.9065L1.79688 9.2505L2.14038 9.6105L6.89038 14.3605L7.25038 14.704L7.59538 14.3605L13.3604 8.657L13.4999 8.5V3H7.99988ZM8.42188 4H12.4999V8.078L7.24988 13.297L3.20288 9.25L8.42188 4ZM10.9999 5C10.8673 5 10.7401 5.05268 10.6463 5.14645C10.5526 5.24021 10.4999 5.36739 10.4999 5.5C10.4999 5.63261 10.5526 5.75978 10.6463 5.85355C10.7401 5.94732 10.8673 6 10.9999 6C11.1325 6 11.2597 5.94732 11.3534 5.85355C11.4472 5.75978 11.4999 5.63261 11.4999 5.5C11.4999 5.36739 11.4472 5.24021 11.3534 5.14645C11.2597 5.05268 11.1325 5 10.9999 5Z" fill="black" fill-opacity="0.87" />
                                            </svg>
                                        </div>
                                        <div style="font-size:14px;font-weight:600" class="ml-2">$' . $opportunity->rate . '/hr</div>
                                    </div>
                                    <?php // time 
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.66667 7.33268H6V8.66602H4.66667V7.33268ZM12.6667 1.99935H12V0.666016H10.6667V1.99935H5.33333V0.666016H4V1.99935H3.33333C2.6 1.99935 2 2.59935 2 3.33268V12.666C2 13.3993 2.6 13.9993 3.33333 13.9993H12.6667C13.4 13.9993 14 13.3993 14 12.666V3.33268C14 2.59935 13.4 1.99935 12.6667 1.99935ZM12.6667 3.33268V4.66602H3.33333V3.33268H12.6667ZM3.33333 12.666V5.99935H12.6667V12.666H3.33333ZM7.33333 9.99935H8.66667V11.3327H7.33333V9.99935ZM10 9.99935H11.3333V11.3327H10V9.99935ZM10 7.33268H11.3333V8.66602H10V7.33268Z" fill="black" fill-opacity="0.87" />
                                            </svg>
                                        </div>
                                        <div class="ml-2 d-flex align-items-center" style="font-size:14px;font-weight:600">
                                            <div>' . $opportunity->start_date . '</div>
                                        </div>
                                    </div>
                                    <?php ?>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.0013 2.66732C9.46797 2.66732 10.668 3.86732 10.668 5.33398C10.668 6.73398 9.26797 9.00065 8.0013 10.6007C6.73464 8.93398 5.33464 6.73398 5.33464 5.33398C5.33464 3.86732 6.53464 2.66732 8.0013 2.66732ZM8.0013 1.33398C5.8013 1.33398 4.0013 3.13398 4.0013 5.33398C4.0013 8.33398 8.0013 12.6673 8.0013 12.6673C8.0013 12.6673 12.0013 8.26732 12.0013 5.33398C12.0013 3.13398 10.2013 1.33398 8.0013 1.33398ZM8.0013 4.00065C7.26797 4.00065 6.66797 4.60065 6.66797 5.33398C6.66797 6.06732 7.26797 6.66732 8.0013 6.66732C8.73464 6.66732 9.33464 6.06732 9.33464 5.33398C9.33464 4.60065 8.73464 4.00065 8.0013 4.00065ZM13.3346 12.6673C13.3346 14.134 10.9346 15.334 8.0013 15.334C5.06797 15.334 2.66797 14.134 2.66797 12.6673C2.66797 11.8007 3.46797 11.0673 4.73464 10.534L5.13464 11.134C4.46797 11.4673 4.0013 11.8673 4.0013 12.334C4.0013 13.2673 5.8013 14.0007 8.0013 14.0007C10.2013 14.0007 12.0013 13.2673 12.0013 12.334C12.0013 11.8673 11.5346 11.4673 10.8013 11.134L11.2013 10.534C12.5346 11.0673 13.3346 11.8007 13.3346 12.6673Z" fill="black" />
                                            </svg>

                                        </div>
                                        <div class="ml-2 d-flex align-items-center" style="font-size:14px;font-weight:600">
                                            <div>' . $opportunity->location . '</div>
                                        </div>
                                    </div>
                                </div>
                                <?php // third part 
                                ?>
                                <div class="my-2" style="font-size: 16px; font-weight: 400;">';

        $html = $opportunity->requirements;
        $check = strip_tags($html);

        // Check if the paragraph length is less than 300 characters
        if (strlen($check) <= 300) {
            // Output the entire paragraph
            echo $check;
        } else {
            // Extract the first 300 characters
            $first_300_characters = substr($check, 0, 300);
            // Output the first 300 characters
            echo $first_300_characters;
        }


        echo '    </div>

                                <div class="d-flex my-4">';

        // Fetch skills for this opportunity
        $skills_text = $opportunity->skills;
        $skills_text = explode(",", $skills_text);
        // Debugging statement to check the value of $skills_text
        if (!empty($skills_text)) {
            foreach ($skills_text as $skill) {
                echo '<div class="mx-2" style="background-color: #E7E7E8; border-radius: 4px; padding:2px 4px 2px 4px;font-size: 14px;font-weight: 400;">' . ucfirst($skill) . '</div>';
            }
        } else {
            echo '<div class="mx-2" style="font-size: 14px;font-weight: 400;">No skills found</div>';
        }

        echo '   </div>

                                <div></div>
                            </div>
                            <div class="card-footer d-flex" style="justify-content: space-between;align-items: center;">
                                <div class="d-flex">
                                    <div class="viewOpportunityButton btn" data-id="' . $opportunity->public_id . '" style="width: 130px;height: 40px;background-color: #F5A800;padding: 8px 12px 8px 12px;font-size: 16px; font-weight: 700;border-radius: 4px;">
                                        View Details
                                    </div>

                                    <div class="ml-2 match" data-id="' . $opportunity->public_id . '" style="width: 74px;height: 40px;background-color: #E7E7E8;padding: 8px 12px 8px 12px;font-size: 16px; font-weight: 700;border-radius: 4px;">
                                        Match
                                    </div>
                                </div>
                                <div class="inter-font font-500 font-12" style="color: #363636;">
                                    Posted';

        // Assuming $opportunity->created_at contains the timestamp '2024-03-02 14:50:58'

        // Get the current time
        $current_time = new DateTime('now', new DateTimeZone('UTC'));

        // Convert the created_at timestamp to a DateTime object
        $created_at_time = new DateTime($opportunity->created_at, new DateTimeZone('UTC'));

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



        echo '       </div>


                            </div>
                        </div>';
    }
    echo '</div>';
    echo "<script>
    $(document).ready(function() {
        $('.viewOpportunityButton').on('click', function() {
            var opportunityId = $(this).data('id');
            console.log(opportunityId);
            $.ajax({
                url: 'modal_forms/fetct_opportunity_by_public_id.php', // Adjust the URL
                method: 'GET',
                data: {
                    id: opportunityId
                },
                success: function(response) {
                    $('.modal_content2').html(response);
                    $('#viewmodal').modal('show');
                }
            });
        })
    })
</script>
<script>
    $(document).ready(function() {
        $('#solvermatchForm').submit(function() {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: 'fr_handle_match_by_solver.php',
                data: formData,
                success: function(response) {
                    $('#matchWithOpportunity').modal('hide');
                    $('body').append(response)
                    // Handle success response
                    //$('#afterMatch').html(response); // Update modal content with response
                    // $('#afterMatch').modal('show'); // Show the modal
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                }
            });
        })
        $('.match').on('click', function() {
            var opportunityPublic_id = $(this).data('id')
            var inputField = $('<input>');
            inputField.attr('name', 'opportunityPublic_id'); // Set name attribute
            inputField.attr('type', 'hidden');
            inputField.attr('id', 'opportunityPublic_id'); // Set type attribute
            inputField.val(opportunityPublic_id);

            $('#opportunity-id-input').append(inputField);
            $('#matchWithOpportunity').modal('show');
        })
    })
</script>";
}
