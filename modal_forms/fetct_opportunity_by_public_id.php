<?php
// Define the root directory
$docRoot = $_SERVER['DOCUMENT_ROOT'];

// Include Database class
include_once $docRoot . "/lib/Database.php";
include_once $docRoot . "/config/config.php";


// Fetch opportunity data based on ID
if (isset($_GET['id'])) {
    $opportunityId = $_GET['id'];
    $opportunityId = trim($opportunityId);

    try {
        // Create a new instance of the Database class
        $db = new Database();


        // Prepare SQL statement to fetch opportunity data
        $stmt = $db->pdo->prepare("SELECT * FROM tbl_opportunities WHERE public_id = :opportunityId");

        // Bind parameter and execute the statement
        $stmt->bindValue(':opportunityId', $opportunityId);
        $stmt->execute();
        $errorInfo = $stmt->errorInfo();
        if ($errorInfo[0] !== PDO::ERR_NONE) {
            // Handle errors
            die("Error executing query: " . $errorInfo[2]);
        }

        // Fetch opportunity data
        $opportunity = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->pdo->prepare("SELECT * FROM tbl_organizations WHERE id = :org_id");
        $stmt->bindParam(':org_id', $opportunity['fk_org_id']);
        $stmt->execute();
        $organization = $stmt->fetch(PDO::FETCH_ASSOC);
        $skillText = "";
        $skillIds = "";
        $stmt = $db->pdo->prepare("select skills.*, opportunity_skills.* from tbl_skills skills
    inner join tbl_opportunity_skills opportunity_skills on skills.id = opportunity_skills.fk_skill_id
    where opportunity_skills.fk_opportunity_id = :opptyid");
        $stmt->bindValue(':opptyid', $opportunity['id']);
        $stmt->execute();
        $getSkillsInfo = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($getSkillsInfo as $skill) {
            $skillText = $skillText . $skill->skill_name . ", ";
            $skillIds = $skillIds . $skill->fk_skill_id . ", ";
        }
        $skillText = substr($skillText, 0, strrpos($skillText, ','));
        $skillIds = substr($skillIds, 0, strrpos($skillIds, ','));

        $rate_t = ($opportunity['rate_type'] == 'per_day') ? '/day' : '/hr';

        if ($opportunity) {
            // HTML markup for form fields with fetched data
            echo "
              
           <div class='modal-header ' style='display:flex;align-items:center;'>
                    <h5 class='modal-title poppins-font' id='exampleModalLongTitle' style='color:#012B33;font-size:24px;font-weight:700;'>{$opportunity['headline']}</h5>
                    <div type='button' class='close' data-dismiss='modal' style='border:2px solid #E7E7E8; height:40px;width:40px;display:flex;justify-content:center;align-items:center;' aria-label='Close'>
                       <span aria-hidden='true'>&times;</span>

                    </div>
                </div>
                <div class='modal-body'>
                <p class='inter-font' style='font-size:16px;font-weight:400;'>{$opportunity['requirements']}</p>
                <div class='d-flex align-items-center justify-content-between'>
                                <div class='d-flex align-items-center'>
                                    <div><svg width='16' height='17' viewBox='0 0 16 17' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M7.99988 3L7.84338 3.14L2.13988 8.9065L1.79688 9.2505L2.14038 9.6105L6.89038 14.3605L7.25038 14.704L7.59538 14.3605L13.3604 8.657L13.4999 8.5V3H7.99988ZM8.42188 4H12.4999V8.078L7.24988 13.297L3.20288 9.25L8.42188 4ZM10.9999 5C10.8673 5 10.7401 5.05268 10.6463 5.14645C10.5526 5.24021 10.4999 5.36739 10.4999 5.5C10.4999 5.63261 10.5526 5.75978 10.6463 5.85355C10.7401 5.94732 10.8673 6 10.9999 6C11.1325 6 11.2597 5.94732 11.3534 5.85355C11.4472 5.75978 11.4999 5.63261 11.4999 5.5C11.4999 5.36739 11.4472 5.24021 11.3534 5.14645C11.2597 5.05268 11.1325 5 10.9999 5Z' fill='black' fill-opacity='0.87' />
                                        </svg>
                                    </div>
                                    <div style='font-size:14px;font-weight:600;color:#012B33;' class='ml-2'>{$opportunity['rate']}{$rate_t}</div>
                                </div>
                                <div class='d-flex align-items-center'>
                                    <div><svg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M4.66667 7.33268H6V8.66602H4.66667V7.33268ZM12.6667 1.99935H12V0.666016H10.6667V1.99935H5.33333V0.666016H4V1.99935H3.33333C2.6 1.99935 2 2.59935 2 3.33268V12.666C2 13.3993 2.6 13.9993 3.33333 13.9993H12.6667C13.4 13.9993 14 13.3993 14 12.666V3.33268C14 2.59935 13.4 1.99935 12.6667 1.99935ZM12.6667 3.33268V4.66602H3.33333V3.33268H12.6667ZM3.33333 12.666V5.99935H12.6667V12.666H3.33333ZM7.33333 9.99935H8.66667V11.3327H7.33333V9.99935ZM10 9.99935H11.3333V11.3327H10V9.99935ZM10 7.33268H11.3333V8.66602H10V7.33268Z' fill='black' fill-opacity='0.87' />
                                        </svg>
                                    </div>
                                    <div class='ml-2 d-flex align-items-center' style='font-size:14px;font-weight:600;color:#012B33;'>
                                        <div>{$opportunity['start_date']}</div>
                                    </div>
                                </div>
                                <div class='d-flex align-items-center'>
                                    <div>
                                        <svg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                            <path d='M8.0013 2.66732C9.46797 2.66732 10.668 3.86732 10.668 5.33398C10.668 6.73398 9.26797 9.00065 8.0013 10.6007C6.73464 8.93398 5.33464 6.73398 5.33464 5.33398C5.33464 3.86732 6.53464 2.66732 8.0013 2.66732ZM8.0013 1.33398C5.8013 1.33398 4.0013 3.13398 4.0013 5.33398C4.0013 8.33398 8.0013 12.6673 8.0013 12.6673C8.0013 12.6673 12.0013 8.26732 12.0013 5.33398C12.0013 3.13398 10.2013 1.33398 8.0013 1.33398ZM8.0013 4.00065C7.26797 4.00065 6.66797 4.60065 6.66797 5.33398C6.66797 6.06732 7.26797 6.66732 8.0013 6.66732C8.73464 6.66732 9.33464 6.06732 9.33464 5.33398C9.33464 4.60065 8.73464 4.00065 8.0013 4.00065ZM13.3346 12.6673C13.3346 14.134 10.9346 15.334 8.0013 15.334C5.06797 15.334 2.66797 14.134 2.66797 12.6673C2.66797 11.8007 3.46797 11.0673 4.73464 10.534L5.13464 11.134C4.46797 11.4673 4.0013 11.8673 4.0013 12.334C4.0013 13.2673 5.8013 14.0007 8.0013 14.0007C10.2013 14.0007 12.0013 13.2673 12.0013 12.334C12.0013 11.8673 11.5346 11.4673 10.8013 11.134L11.2013 10.534C12.5346 11.0673 13.3346 11.8007 13.3346 12.6673Z' fill='black' />
                                        </svg>

                                    </div>
                                    <div class='ml-2 d-flex align-items-center' style='font-size:14px;font-weight:600;color:#012B33;'>
                                        <div>{$opportunity['location']}</div>
                                    </div>
                                </div>


                                
                </div>
                <p class='inter-font font-700 font-16 my-4' style='color:#012B33'>
                Required Skills
                </p>
                <div class='d-flex'style='flex-wrap:wrap;'>";
               foreach ($getSkillsInfo as $skill){
                echo "
                <div style='padding:4px 8px 4px 8px;background-color: #E7E7E8;margin-right:4px;'>
                $skill->skill_name
                </div>
                ";
               }
                
               echo " </div>

                    </div>
                        <div class='modal-footer'>
                             <div class='match-in-modal' data-id='{$opportunityId}' style='width: 130px;height: 40px;background-color: #F5A800;padding: 8px 12px 8px 12px;font-size: 16px; font-weight: 700;border-radius: 4px;'>
                             Match
                             </div>
                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                            

                        </div>
                </div>
                 <script>
                  $('.match-in-modal').on('click',function(){
                     $('#viewmodal').modal('hide');
                     var opportunityPublic_id = $(this).data('id')
                     var inputField = $('<input>');
      inputField.attr('name', 'opportunityPublic_id'); // Set name attribute
      inputField.attr('type', 'hidden');
      inputField.attr('id', 'opportunityPublic_id'); // Set type attribute
      inputField.val(opportunityPublic_id);

        $('#opportunity-id-input').append(inputField);
        $('#matchWithOpportunity').modal('show');
                  })
                 </script>

        
            ";
        } else {
            echo "No opportunity found with ID: $opportunityId";
        }
    } catch (PDOException $e) {
        echo "Error fetching opportunity data: " . $e->getMessage();
    }
} else {
    echo "Opportunity ID not provided";
}
