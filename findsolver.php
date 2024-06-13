<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
$views->showAndClearPendingMessage();

$user_real_id = $users->getRealId(Session::get('userid'));

?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


<?php


$theQuery = "";
if (isset($_GET["query"])) {
  $theQuery = urldecode($_GET['query']);;
}


$all_opportunity = $opportunities->getAllOpportunityDataForUser(Session::get('userid'), $users);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['opportunityCheckbox'])) {

  $result = $matches->matchMadeBySeeker($_POST['solverid'], $_POST['opportunityCheckbox'], Session::get('userid'));
 
  if ($result) {
    foreach($result->allInsertedIds as $matched_id){
       $message = "New match made by a seeker";
       $type = "match_madeby_seeker";
       $opportunities->addMatchMadeMessageQue($message,$type,$matched_id);
    }
    $modalScript = "
        <script>
            $(document).ready(function() {
                $('#afterMatch').modal('show');
            });
            var userid = " . json_encode(Session::get('userid')) . ";
             if (userid) {
                 var conn = new WebSocket('ws://localhost:8080?userId='+userid);
                 conn.onopen = function(e) {
                   console.log('Connection established!');
                 };
              }
                  conn.onopen = function(e) {
        console.log('Connection established!');
        
        // Create the message object
        var message = {
            type: 'match_madeby_seeker',
            message: 'New match made by a seeker', 
            matchedIds:".json_encode($result->allInsertedIds)." 
        };

        // Send the message
        conn.send(JSON.stringify(message));
        
        // Close the connection after sending the message
        conn.close();
    };
        </script>
    ";
    echo $modalScript;
  }
}

?>


<?php
//After match modal
?>
<div class="modal fade" id="afterMatch" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 4px;">
      <div class="modal-header">
        <div style="color: #012B33;font-size: 20px;font-weight: 700;line-height: 28px;">Match pending</div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <div>

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
            </svg>
          </div>
        </button>
      </div>
      <div class="modal-body" style="color: black;max-height: 400px; overflow-y: auto;">
        <p style="font-weight: 700; font-size: 24px; list-style: 32px;">Is it a match ?</p>
        <p style="font-weight: 400; font-size: 16px; list-style: 24px;">At Bennit, making great matches between subject matter experts and manufacturing challenges is so important that we review every potential match.</p>
        <p style="font-weight: 600; font-size: 16px; list-style: 24px;">What next ?</p>
        <p style="font-weight: 400; font-size: 16px; list-style: 10px;">After we review the Opportunity or Opportunities you selected and the skills of the suggested Solver, we’ll update this match with our guidance. </p>
        <p style="font-weight: 400; font-size: 16px; list-style: 10px;">Check back soon for confirmed matches on your <a href="index.php" style="text-decoration: underline; color:inherit"> Dashboard </a>, and keep the following Opportunities up-to-date so we know if it’s a good fit!</p>
        <div>
          <p style="font-weight: 700; font-size: 16px; list-style: 24px;">Opportunities Under Review</p>
          <div>
            <table class="table">
              <tbody>
                <?php
                foreach ($result->successfulOpportunities as $oportunityMatchid) :
                  $matchedOpportunity = $opportunities->getOpportunityInfoByRealId($oportunityMatchid);
                ?>
                  <tr>
                    <td style="text-decoration: underline; font-weight: 500;"><?php echo $matchedOpportunity->headline ?></td>
                    <td><?php echo $matchedOpportunity->start_date ?></td>
                    <td><?php echo $matchedOpportunity->location ?></td>
                  </tr>
                <?php
                endforeach;
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
      <div class="modal-footer" style="display: flex; justify-content: flex-start;">
        <button type="button" name="submit" id="submit" class="btn " data-dismiss="modal" style="width: 130px;height: 40px; padding: 8px 12px 8px 12px; background-color: #F5A800; color:black;font-size: 14px; font-weight: 700;">Got It</button>
        <a href="index.php">
          <div type="button" class="" style="text-align: center; padding:8px 12px 8px 12px; background-color: #E7E7E8; border:none;color:black;border-radius: 4px;width:155px;height:40px;font-weight: 700;font-size: 14px;" data-dismiss="modal">Go to Dashboard</div>
        </a>
      </div>
    </div>
  </div>
</div>

<?php
// The modal
?>

<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content" style=" box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);border-radius: 4px;">
      <div class="modal-header" style="display: flex; align-items: center;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="color: #012B33; font-weight: 700; font-size:20px; line-height: 28px;">Is it a Match ?</h5>
        <button type="button" class="close" data-dismiss="modal" style="display: flex; justify-content: center; margin-right: 2px; align-items: center; height: 40px; width: 40px; border: 2px solid #E7E7E8;" aria-label="Close">
          <div>

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
            </svg>
          </div>

        </button>
      </div>


      <form id="matchForm" action="" method="post">
        <div class="modal-body" style="color: black; max-height: 400px; overflow-y: auto;">
          <diV>
            <p style="font-size: 24px;font-weight: 700; list-style: 32px; color:black">Match with this company</p>
          </diV>
          <div>
            <p class="inter-font font-16 font-400">Select which opportunities you would like to invite this company to help you with.</p>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAllCheckbox">
                  </th>
                  <th class="inter-font font-700 font-12">Opportunity</th>
                  <th class="inter-font font-700 font-12">Start Date</th>
                  <th class="inter-font font-700 font-12">Location</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($all_opportunity as $opportunity) : ?>
                  <tr>
                    <td>
                      <input type="checkbox" name="opportunityCheckbox[]" id="opportunityCheckbox" class="opportunityCheckbox" value="<?php echo $opportunity->id ?>">
                    </td>
                    <td class="inter-font font-14 font-500" style=" color:black; text-decoration: underline;"><b><?php echo $opportunity->headline ?></b></td>
                    <td><?php echo $opportunity->start_date ?></td>
                    <td> <!-- Example for description, replace this with your actual description field -->
                      <?php echo $opportunity->location ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
          </div>
          <input type="hidden" name="user_public_id" id="user_public_id" value="<?php echo Session::get('userid') ?>">
          <div id="seeker-id-input">

          </div>

          <script>
            document.getElementById('selectAllCheckbox').addEventListener('change', function() {
              var checkboxes = document.getElementsByClassName('opportunityCheckbox');
              for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
              }
            });
          </script>

        </div>
        <div class="modal-footer" style="display: flex; justify-content: flex-start;">
          <button type="submit" name="submit" id="submit" class="btn " style="padding: 8px 12px 8px 12px; background-color: #F5A800; color:black;font-size: 16px; font-weight: 700;">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

      </form>


    </div>
  </div>
</div>

<div id="show-sidebar" style="background-color: white;" onclick="handleSidebar()">
  <div class="sidebar-toggle-button">
    <i class="fas fa-bars"></i>
  </div>
</div>

<div class="xt-card-organization1">

  <div class="xt-sidebar-organization1" id="jjk">
    <?php include 'inc/sidebar.php' ?>
  </div>
  <div class="xt-body-organization1" style="color:black">
    <?php
    // FIND SOLVER SEARCH BUTTON 
    ?>
    <div>
      <div style="width:100%; margin:0px auto; border-bottom: 2px solid #E7E7E8;">
        <form class="findopportunity-form" action="" method="GET" id="searchForm" >
          <div class="findopportunity-search-text">Find Solvers</div>
          <div class="findopportunity-input-field">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9.5 3C11.2239 3 12.8772 3.68482 14.0962 4.90381C15.3152 6.12279 16 7.77609 16 9.5C16 11.11 15.41 12.59 14.44 13.73L14.71 14H15.5L20.5 19L19 20.5L14 15.5V14.71L13.73 14.44C12.5505 15.4468 11.0507 15.9999 9.5 16C7.77609 16 6.12279 15.3152 4.90381 14.0962C3.68482 12.8772 3 11.2239 3 9.5C3 7.77609 3.68482 6.12279 4.90381 4.90381C6.12279 3.68482 7.77609 3 9.5 3ZM9.5 5C7 5 5 7 5 9.5C5 12 7 14 9.5 14C12 14 14 12 14 9.5C14 7 12 5 9.5 5Z" fill="black" fill-opacity="0.38" />
            </svg>

            <input type="text" placeholder="Search by keyword or skill, such as “Developer” or “PHP”" name="query" id="query" value="<?php if (isset($theQuery)) {
                                                                                                                                        echo $theQuery;
                                                                                                                                      } ?>" style="outline: none; border: none;width:100%;height:2.5rem;background-color: white;color:rgb(46, 46, 46)" minlength="3">
          </div>


          <div style="width:134px;text-align: center; font-weight: 600; font-size: 16px; border-radius: 4px;background-color: #F5A800; padding: 8px 12px 8px 12px ; cursor: pointer;" onclick="document.getElementById('searchForm').submit();">
            Search
          </div>

        </form>
      </div>
    </div>

    <div>
      <div class="poppins-font font-700 font-24" style="padding: 10px;">Available Solver</div>
      <?php
      $industrie = $industries->getAllIndustryName();
      $technology = $technologies->getAllTecnology();
      $specialty = $specialities->getAllSpecialty();
      ?>
      <div style="display: flex;justify-content: space-between;align-items: center;flex-wrap: wrap;">


        <div style="display: flex;flex-wrap: wrap;">
          <div class="mx-2">
            <select class="form-control select-box" id="industry" style="width: 233px;">
              <option value="" selected>Industry</option>
              <?php
              foreach ($industrie as $singleIndustry) {
              ?>
                <option value="<?php echo $singleIndustry->industry_name ?>"><?php echo $singleIndustry->industry_name ?></option>
              <?php
              };
              ?>
            </select>
          </div>
          <div class="mx-2">
            <select class="form-control select-box" id="technology" style="width: 233px;">
              <option value="" selected>Technology</option>
              <?php
              foreach ($technology as $singleTechnology) {
              ?>
                <option value="<?php echo $singleTechnology->technology_name ?>"><?php echo $singleTechnology->technology_name ?></option>
              <?php
              };
              ?>
            </select>
          </div>
          <div class="mx-2">
            <select class="form-control select-box" id="specialty" style="width: 233px;">
              <option value="" selected>Specialty</option>
              <?php
              foreach ($specialty as $singleSpecialty) {
              ?>
                <option value="<?php echo $singleSpecialty->speciality_name ?>"><?php echo ucfirst($singleSpecialty->speciality_name) ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="mx-2">
            <select class="form-control select-box" id="location" style="width: 233px;">
              <option value="" selected>Location</option>
              <option value="remote">Remote</option>
              <option value="hybrid">Hybrid</option>
              <option value="On premise">On premise</option>
            </select>
          </div>
        </div>

        <div class=" mr-2 findsolver-filter-icon" >
          <div class="mx-2">
            <svg width="18" height="12" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 7H15V5H3M0 0V2H18V0M7 12H11V10H7V12Z" fill="black" />
            </svg>

          </div>
          <div>
            Filter
          </div>
        </div>
      </div>
      <!-- <div class="selected-boxes" style="display: flex;"></div> -->
    </div>

    <?php
    // SOLVERS CARD
    ?>
    <?php
    $Solvers = $solvers->getAllLatesSolvers();
    //$TestSolverdata = $solvers->advancedGetAllLatesSolvers();


    ?>
    <!-------- Form here starts the main body where all cards shown  --->
    <div id="solver-grid">





    </div>
  </div>
</div>





<?php
// Scripts here
?>

<style>
  /* Define the CSS class for glowing border */
  .glow-border {
    border-color: #F5A800 !important;
    box-shadow: 0 0 10px #F5A800;
  }

  /* Style for the select box */
  .select-box {
    position: relative;
  }

  /* Style for the selected option box */
  .selected-box {
    display: flex;
    flex-wrap: wrap;
    margin-top: 10px;
  }

  /* Style for each selected option */
  .selected-item {
    padding: 5px 10px;
    margin-right: 5px;
    background-color: #f0f0f0;
    border-radius: 5px;
    margin-bottom: 5px;
  }

  /* Style for the close button (x mark) */
  .close-btn {
    cursor: pointer;
    margin-left: 5px;
  }
</style>

<script>
  // Select all select elements
  const selectElements = document.querySelectorAll('.select-box');

  // Select the container for selected option boxes
  const selectedBoxesContainer = document.querySelector('.selected-boxes');

  // Add change event listener to each select element
  selectElements.forEach(select => {
    select.addEventListener('change', function() {
      // Log the value of the changed select element
      console.log(`${this.id}: ${this.value}`);
      // Create a new div element to display the selected value
      const selectedValueBox = document.createElement('div');
      selectedValueBox.classList.add('selected-value');
      if (this.value != '') {
        selectedValueBox.textContent = `${this.value}`;

        // Create a span element for the close button
        const closeBtn = document.createElement('span');
        closeBtn.className = 'close-btn';
        closeBtn.textContent = 'x';
        // Append the close button to the selectedValueBox
        selectedValueBox.appendChild(closeBtn);
      }
      // Add click event listener to the entire div
      selectedValueBox.addEventListener('click', function() {
        // Remove the entire selected value box
        selectedValueBox.remove();
        // Reset the related select tag to its default value
        select.value = '';
        // Manually trigger the change event on the select element
        const event = new Event('change');
        select.dispatchEvent(event);
      });
      // Append the new div element to the selectedBoxesContainer
      selectedBoxesContainer.appendChild(selectedValueBox);

    });
  });
</script>


<script>
  getsolvers()

  function getsolvers() {
    var noSearch = "getallsolver"
    $.ajax({
      url: "modal_forms/search_filter_solver.php",
      method: "POST",
      data: {
        action: noSearch
      },
      success: function(data) {
        $('#solver-grid').html(data);
      }
    })
  }
  $(document).ready(function() {
    // Function to trigger search
    function triggerSearch() {
      var action = 'searchResult';
      var query = $('#query').val().trim();
      var industry = $('#industry').val().trim();
      var technology = $('#technology').val().trim();
      var specialty = $('#specialty').val().trim();
      var loaction = $('#location').val().trim();

      // Log the values to the console
      console.log('Action:', action);
      console.log('Query:', query);
      console.log('Industry:', industry);
      console.log('Technology:', technology);
      console.log('Specialty:', specialty);

      $.ajax({
        url: "modal_forms/search_filter_solver.php",
        method: "POST",
        data: {
          action: action,
          query: query,
          industry: industry,
          technology: technology,
          specialty: specialty,
          loaction: loaction
        },
        success: function(data) {
          $('#solver-grid').html(data);
        }
      });
    }

    // Trigger search on keyup event in the query input
    $('#query').keyup(function(event) {
      event.preventDefault();
      triggerSearch();
    });

    // Trigger search when any select tag changes
    $('#industry, #technology, #specialty,#location').change(function() {
      triggerSearch();
    });

    // Initial search on page load
    triggerSearch();
  });
</script>
  <script>
    function handleSidebar() {
  //const sidebar = document.getElementsByClassName('xt-sidebar-organization1')[0];
  const sidebar = document.getElementById('jjk')
  console.log(sidebar)
  sidebar.style.display = (sidebar.style.display === 'block') ? 'none' : 'block';
}

  </script>

</body>

</html>