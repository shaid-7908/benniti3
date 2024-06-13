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



    if ($opportunity) {
      // HTML markup for form fields with fetched data
      echo "
           
<script>
  // Define a function to initialize event listeners
  function initializeForm1Listeners() {
    const form1 = document.getElementById('frmOpportunityEdit');
    const addressField = document.querySelector('.address-field');
    const payRate = document.querySelector('.pay-rate');
    const rangeDate = document.querySelector('.range-date');

    function handleLocationChange(event) {
      if (event.target && event.target.name === 'location') {
        addressField.style.display = (event.target.value === 'Remote') ? 'none' : 'block';
      }
    }

    function handleRateChange(event) {
      if (event.target && event.target.name === 'rate') {
        payRate.style.display = (event.target.value === 'no') ? 'none' : 'block';
      }
    }

    function handleDateChange(event) {
      if (event.target && event.target.name === 'date') {
        rangeDate.style.display = (event.target.value === 'no') ? 'none' : 'block';
      }
    }

    form1.addEventListener('change', handleLocationChange);
    form1.addEventListener('change', handleRateChange);
    form1.addEventListener('change', handleDateChange);
  }
  $('#editmodal').on('hidden.bs.modal', function (e) {
    window.form1ListenersInitialized = false;
  });
  // Check if the listeners are already initialized
  if (!window.form1ListenersInitialized) {
    initializeForm1Listeners();
    window.form1ListenersInitialized = true;
  }
</script>

<script>

  flatpickr('#start_date', {


  });
  flatpickr('#complete_date', {

  });
</script>

     <script>
             ClassicEditor
            .create(document.querySelector('#requirements'), {
             toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
              heading: {
                   options: [{
                       model: 'paragraph',
                       title: 'Paragraph',
                       class: 'ck-heading_paragraph'
                     },
                     {
                       model: 'heading1',
                       view: 'h1',
                       title: 'Heading 1',
                       class: 'ck-heading_heading1'
                     },
                     {
                       model: 'heading2',
                       view: 'h2',
                       title: 'Heading 2',
                       class: 'ck-heading_heading2'
                     }
                   ]
                 }
               })
               .catch(error => {
                 console.error(error);
               });
     </script>

          <form name='frmOpportunityEdit' id='frmOpportunityEdit' class='' action='' method='POST'>
          <h3 class='mt-4 poppins-font' style='font-size: 24px; font-weight: 700;'>Description</h3>
          
          <input type='hidden' id='optyid' name='optyid' value='{$opportunity['id']}'>
          <div class='form-group'>
            <label for='fk_org_id' style='font-weight: 700; font-size: 16px;'>Organization</label>
            <select class='form-control' style=' appearance: none;' name='fk_org_id' id='fk_org_id'  required>
            <option style= 'margin-top:20px; background-color: #fff; color: #000; padding: 10px; ' value='{$organization['id']}'> {$organization['orgname']}  </option>
              
            </select>
           
          </div>
          <div class='form-group'>
            <label for='headline' style='font-weight: 700; font-size: 16px;'>Opportunity Headline</label>
            <p style='font-size: 12px; font-weight: 400;'>A brief description of your needs</p>
            <input style='background-color: white; border: 1px solid #ced4da;' type='text' name='headline' id='headline' value='{$opportunity['headline']}' class='form-control' minlength='8' maxlength='254' required>
          </div>

          <div class='form-group'>
            <label style='font-weight: 700; font-size: 16px;' for='requirements'>Requirements </label>
            <p style='font-size: 12px; font-weight: 400;'>A detailed description of the opportunity</p>
            <textarea name='requirements' id='requirements' style='width:100%; height:174px' class='form-control'>" . htmlspecialchars($opportunity['requirements']) . "</textarea>
          </div>
         
          
          
          <div class='form-group'>
            <label class='poppins-font' style='font-size: 24px;font-weight: 700;'>Dates</label>
            <p style='font-size:16px;font-weight: 400;'>Do you know when you want to start and end this opportunity? An estimate is okay! This will help us match you with Solvers who are available when you need them.</p>
            <label>
              <input type='radio' name='date' value='yes' checked> Yes
            </label><br>
            <label>
              <input type='radio' name='date' value='no'> No
            </label><br>
            <div class='range-date'>


              <label for='start_date' style='font-size: 16px; font-weight: 700;'>Anticipated Start Date</label>
              <div style='border: 1px solid #ced4da;border-radius: 4px; display:flex;align-items: center;' class='px-2 form-control'>

                <input type='text' placeholder='Pick your dates' name='start_date' id='start_date' value='{$opportunity['start_date']}' minlength='3' maxlength='254' required style='background-color: white; border: none;outline: none;width:95%'>
                <svg width='14' height='14' viewBox='0 0 14 14' fill='none' xmlns='http://www.w3.org/2000/svg'>
                  <g clip-path='url(#clip0_495_1329)'>
                    <rect width='14' height='14' fill='white' fill-opacity='0.01' />
                    <g clip-path='url(#clip1_495_1329)'>
                      <path d='M9.49725 6.25202C9.53799 6.29266 9.57032 6.34094 9.59237 6.39409C9.61443 6.44724 9.62578 6.50422 9.62578 6.56177C9.62578 6.61931 9.61443 6.6763 9.59237 6.72945C9.57032 6.7826 9.53799 6.83088 9.49725 6.87152L6.87225 9.49652C6.83161 9.53726 6.78333 9.56958 6.73018 9.59164C6.67703 9.6137 6.62005 9.62505 6.5625 9.62505C6.50495 9.62505 6.44797 9.6137 6.39482 9.59164C6.34167 9.56958 6.29339 9.53726 6.25275 9.49652L4.94025 8.18402C4.89957 8.14334 4.86731 8.09505 4.84529 8.0419C4.82328 7.98876 4.81195 7.93179 4.81195 7.87427C4.81195 7.81674 4.82328 7.75978 4.84529 7.70663C4.86731 7.65348 4.89957 7.60519 4.94025 7.56452C5.0224 7.48237 5.13382 7.43622 5.25 7.43622C5.30753 7.43622 5.36449 7.44755 5.41764 7.46956C5.47078 7.49157 5.51907 7.52384 5.55975 7.56452L6.5625 8.56814L8.87775 6.25202C8.91839 6.21127 8.96667 6.17895 9.01982 6.15689C9.07297 6.13484 9.12995 6.12349 9.1875 6.12349C9.24505 6.12349 9.30203 6.13484 9.35518 6.15689C9.40833 6.17895 9.45661 6.21127 9.49725 6.25202Z' fill='#6C757D' />
                      <path d='M3.0625 -0.000732422C3.17853 -0.000732422 3.28981 0.0453612 3.37186 0.127408C3.45391 0.209456 3.5 0.320735 3.5 0.436768V0.874268H10.5V0.436768C10.5 0.320735 10.5461 0.209456 10.6281 0.127408C10.7102 0.0453612 10.8215 -0.000732422 10.9375 -0.000732422C11.0535 -0.000732422 11.1648 0.0453612 11.2469 0.127408C11.3289 0.209456 11.375 0.320735 11.375 0.436768V0.874268H12.25C12.7141 0.874268 13.1592 1.05864 13.4874 1.38683C13.8156 1.71502 14 2.16014 14 2.62427V12.2493C14 12.7134 13.8156 13.1585 13.4874 13.4867C13.1592 13.8149 12.7141 13.9993 12.25 13.9993H1.75C1.28587 13.9993 0.840752 13.8149 0.512563 13.4867C0.184374 13.1585 0 12.7134 0 12.2493V2.62427C0 2.16014 0.184374 1.71502 0.512563 1.38683C0.840752 1.05864 1.28587 0.874268 1.75 0.874268H2.625V0.436768C2.625 0.320735 2.67109 0.209456 2.75314 0.127408C2.83519 0.0453612 2.94647 -0.000732422 3.0625 -0.000732422ZM0.875 3.49927V12.2493C0.875 12.4813 0.967187 12.7039 1.13128 12.868C1.29538 13.0321 1.51794 13.1243 1.75 13.1243H12.25C12.4821 13.1243 12.7046 13.0321 12.8687 12.868C13.0328 12.7039 13.125 12.4813 13.125 12.2493V3.49927H0.875Z' fill='#6C757D' />
                    </g>
                  </g>
                  <defs>
                    <clipPath id='clip0_495_1329'>
                      <rect width='14' height='14' fill='white' />
                    </clipPath>
                    <clipPath id='clip1_495_1329'>
                      <rect width='14' height='14' fill='white' />
                    </clipPath>
                  </defs>
                </svg>

              </div>
              <label for='start_date' class='inter-font mt-2' style='font-size: 16px; font-weight: 700;'>Anticipated End Date</label>
              <div style='border: 1px solid #ced4da;border-radius: 4px; display:flex;align-items: center;' class='px-2 form-control'>
                 
                <input type='text' placeholder='Pick your dates' name='complete_date' id='complete_date' value='{$opportunity['complete_date']}' minlength='3' maxlength='254' required style='background-color: white; border: none;outline: none;width:95%'>
                <svg width='14' height='14' viewBox='0 0 14 14' fill='none' xmlns='http://www.w3.org/2000/svg'>
                  <g clip-path='url(#clip0_495_1329)'>
                    <rect width='14' height='14' fill='white' fill-opacity='0.01' />
                    <g clip-path='url(#clip1_495_1329)'>
                      <path d='M9.49725 6.25202C9.53799 6.29266 9.57032 6.34094 9.59237 6.39409C9.61443 6.44724 9.62578 6.50422 9.62578 6.56177C9.62578 6.61931 9.61443 6.6763 9.59237 6.72945C9.57032 6.7826 9.53799 6.83088 9.49725 6.87152L6.87225 9.49652C6.83161 9.53726 6.78333 9.56958 6.73018 9.59164C6.67703 9.6137 6.62005 9.62505 6.5625 9.62505C6.50495 9.62505 6.44797 9.6137 6.39482 9.59164C6.34167 9.56958 6.29339 9.53726 6.25275 9.49652L4.94025 8.18402C4.89957 8.14334 4.86731 8.09505 4.84529 8.0419C4.82328 7.98876 4.81195 7.93179 4.81195 7.87427C4.81195 7.81674 4.82328 7.75978 4.84529 7.70663C4.86731 7.65348 4.89957 7.60519 4.94025 7.56452C5.0224 7.48237 5.13382 7.43622 5.25 7.43622C5.30753 7.43622 5.36449 7.44755 5.41764 7.46956C5.47078 7.49157 5.51907 7.52384 5.55975 7.56452L6.5625 8.56814L8.87775 6.25202C8.91839 6.21127 8.96667 6.17895 9.01982 6.15689C9.07297 6.13484 9.12995 6.12349 9.1875 6.12349C9.24505 6.12349 9.30203 6.13484 9.35518 6.15689C9.40833 6.17895 9.45661 6.21127 9.49725 6.25202Z' fill='#6C757D' />
                      <path d='M3.0625 -0.000732422C3.17853 -0.000732422 3.28981 0.0453612 3.37186 0.127408C3.45391 0.209456 3.5 0.320735 3.5 0.436768V0.874268H10.5V0.436768C10.5 0.320735 10.5461 0.209456 10.6281 0.127408C10.7102 0.0453612 10.8215 -0.000732422 10.9375 -0.000732422C11.0535 -0.000732422 11.1648 0.0453612 11.2469 0.127408C11.3289 0.209456 11.375 0.320735 11.375 0.436768V0.874268H12.25C12.7141 0.874268 13.1592 1.05864 13.4874 1.38683C13.8156 1.71502 14 2.16014 14 2.62427V12.2493C14 12.7134 13.8156 13.1585 13.4874 13.4867C13.1592 13.8149 12.7141 13.9993 12.25 13.9993H1.75C1.28587 13.9993 0.840752 13.8149 0.512563 13.4867C0.184374 13.1585 0 12.7134 0 12.2493V2.62427C0 2.16014 0.184374 1.71502 0.512563 1.38683C0.840752 1.05864 1.28587 0.874268 1.75 0.874268H2.625V0.436768C2.625 0.320735 2.67109 0.209456 2.75314 0.127408C2.83519 0.0453612 2.94647 -0.000732422 3.0625 -0.000732422ZM0.875 3.49927V12.2493C0.875 12.4813 0.967187 12.7039 1.13128 12.868C1.29538 13.0321 1.51794 13.1243 1.75 13.1243H12.25C12.4821 13.1243 12.7046 13.0321 12.8687 12.868C13.0328 12.7039 13.125 12.4813 13.125 12.2493V3.49927H0.875Z' fill='#6C757D' />
                    </g>
                  </g>
                  <defs>
                    <clipPath id='clip0_495_1329'>
                      <rect width='14' height='14' fill='white' />
                    </clipPath>
                    <clipPath id='clip1_495_1329'>
                      <rect width='14' height='14' fill='white' />
                    </clipPath>
                  </defs>
                </svg>

              </div>
            </div>

          </div>


          <div class='form-group'>
            <label for='location' style='font-size: 24px; font-weight: 700;'>Location</label>
            <p style='font-size: 16px; font-weight: 400;'>Does this opportunity require Solvers to be on-prem, remote, or a hybrid of the two?</p>
            <label>
              <input type='radio' name='location' value='On-prem' checked> On Premise
            </label><br>
            <label>
              <input type='radio' name='location' value='Hybrid'> Hybrid
            </label><br>
            <label>
              <input type='radio' name='location' value='Remote'> Remote
            </label><br>
            <div class='address-field'>
              <h3 style='font-size: 18px; font-weight: 700;'>Oppurtinity Address</h3>
              <p style='font-size: 16px; font-weight: 400;'>Enter the address of the location you expect Solvers to travel to.</p>
              <label for='address line 1' style='font-size:16px;font-weight: 700;' class='mt-2'>Address line 1</label>
              <input type='text' name='address1' id='address1' value='{$opportunity['address_line_1']}' class='form-control' minlength='2' maxlength='254' style='background-color: white; border: 1px solid #ced4da;'>
              <label for='address line 2' style='font-size:16px;font-weight: 700;' class='mt-2'>Address line 2</label>
              <p style='font-size: 12px;'>Optional</p>
              <input type='text' name='address2' id='address2' value='{$opportunity['address_line_2']}' class='form-control' minlength='2' maxlength='254' style='background-color: white; border: 1px solid #ced4da;'>
              <div style='background-color: white; color:black;display:flex ;' class='mt-4'>
                <div style='flex:50% !important;'>
                  <label for='city' style='font-size:16px;font-weight: 700;'>City</label>
                  <input type='text' name='city' value='{$opportunity['city']}' class='form-control' style='background-color: white; border: 1px solid #ced4da;'>
                </div>
                 <div style='flex:20%;' class='mx-2'>
                  <label for='state' style='font-size:16px;font-weight: 700;'>State</label>
                  <select id='state' name='state' class='form-control inter-font' style='background-color: white; border: 1px solid #ced4da;' required>";
        $selectedState = $opportunity['state']; // Get the state value from the database
        $states = array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District Of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');
        foreach ($states as $state) {
            echo "<option value='$state'";
            if ($state === $selectedState) {
                echo " selected";
            }
            echo ">$state</option>";
        }
            echo "    </select>
                </div>
                <div style='flex:20%;display: flex;flex-direction: column;' class=''>
                  <label for='zip' style='font-size:16px;font-weight: 700;'>Zip Code</label>
                  <input type='text' name='zip' value='{$opportunity['zip_code']}' class='form-control' style='background-color: white; border: 1px solid #ced4da;'>
                </div>
              </div>
            </div>
          </div>
          <div class='form-group'>
            <label for='rate' style='font-size: 24px; font-weight: 700;'>Pay rate</label>
            <p style='font-size: 16px;'>Do you know the rate (or range) you’re expecting to pay?</p>
            <label>
              <input type='radio' name='rate' value='yes' checked> Yes
            </label><br>
            <label>
              <input type='radio' name='rate' value='no'> No
            </label><br>
              <div class='pay-rate'>
              <label for='rate' style='font-size: 16px; font-weight: 700;'>Rate</label>
              <p style='font-size: 12px; font-weight: 400;color:gray'>Enter the rate or range you’re expecting to pay. Example: $300/hr or $1150/day.</p>
              <div style='display: inline-block;'>
                <input type='text' name='rate_value' id='rate_value' value='{$opportunity['rate']}' style='background-color: white; border: 1px solid #ced4da; width: 200px;' class='form-control' minlength='3' maxlength='254' required>
              </div>
              <div style='display: inline-block; margin-left: 5px;'>
                <select class='form-control' name='rate_type' id='rate_type' style='width: 120px;'>";
                  $selectedratetype = $opportunity['rate_type'];
                  $ratetypes = array('per_hour','per_day');
                  foreach($ratetypes as $ratetype){
                    echo "<option value='$ratetype'";
                       if($ratetype == $selectedratetype){
                        echo "selected";
                       }
                       if($ratetype == 'per_hour'){
                         echo ">Per Hour</option>";
                       }else{
                        echo ">Per Day</option>";
                       }
                  };
               echo "   
                </select>
              </div>
            </div>
          </div>
          
       
          <div class='form-group'>
            
            <label for='skillsText poppins-font' style='font-size: 24px; font-weight:700'>Add a Skill</label>
            <p style='font-size: 12px; font-weight: 500;'>Enter skills separated by commas.Add as many as you like!</p>
            <input type='text' id='skillsText' name='skillsText' class='form-control' value='{$skillText}' onkeydown='addSkill(event)' style='background-color: white;border: 1px solid #ced4da;'>
            <input type='hidden' id='skillsIds' name='skillsIds' value='{$skillIds}'>
            <div id='skills-list' class='my-2' style='display: flex; flex-wrap: wrap;'>
              <!-- Skills will be dynamically added here -->
            </div>
          </div>

          <input type='hidden' name='updateOpty'/>
          <input type='submit' disabled style='display:none' />
          <input type='hidden' id='fk_opportunity_id' name='fk_opportunity_id' value='{$opportunity['id']}'>
          
          </form>
       
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
