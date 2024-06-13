<?php
$docRoot ="../";
include_once $docRoot."config/config.php";
include_once $docRoot."inc/common.php";
include_once $docRoot."lib/Session.php";
require_once $docRoot."vendor/autoload.php";
$snowflake = new \Godruoyi\Snowflake\Snowflake;
include_once $docRoot."classes/Opportunities.php";
include_once $docRoot."classes/Users.php";
include_once $docRoot."classes/Solvers.php";
include_once $docRoot."classes/Matches.php";
include_once $docRoot."classes/Skills.php";
Session::init();
$opportunities = new Opportunities();
$users = new Users() ;
$solvers = new Solvers();
$matches = new Matches();
$skills = new Skills();


if (isset($_GET['id'])) {
    $opportunityId = $_GET['id'];
    $opportunityId = trim($opportunityId);
    $realOptyId = $opportunities->getRealId($opportunityId);
    $optyinfo = $opportunities->getOpportunityInfoById($opportunityId);
    $optySkills = $skills->getAllSkillsForOpportunityById($realOptyId);
    echo "<div class='modal-header'>
        <h5 class='modal-title' id='exampleModalLongTitle'>". $optyinfo->headline . "</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
       <div class='responsive-requirements-font' style='color: rgba(0, 0, 0, 0.87);'>
        " . $optyinfo->requirements . "
       </div>
         <div class='my-2 opportunity-rate-location-container'>
             <div style='flex: 20%; display:flex'>
                <div>
                  <svg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                  <path d='M7.99988 2.5L7.84338 2.64L2.13988 8.4065L1.79688 8.7505L2.14038 9.1105L6.89038 13.8605L7.25038 14.204L7.59538 13.8605L13.3604 8.157L13.4999 8V2.5H7.99988ZM8.42188 3.5H12.4999V7.578L7.24988 12.797L3.20288 8.75L8.42188 3.5ZM10.9999 4.5C10.8673 4.5 10.7401 4.55268 10.6463 4.64645C10.5526 4.74021 10.4999 4.86739 10.4999 5C10.4999 5.13261 10.5526 5.25978 10.6463 5.35355C10.7401 5.44732 10.8673 5.5 10.9999 5.5C11.1325 5.5 11.2597 5.44732 11.3534 5.35355C11.4472 5.25978 11.4999 5.13261 11.4999 5C11.4999 4.86739 11.4472 4.74021 11.3534 4.64645C11.2597 4.55268 11.1325 4.5 10.9999 4.5Z' fill='black' fill-opacity='0.87'/>
                  </svg>
                </div>";

                     if($optyinfo->rate == 'TBD' || $optyinfo->rate == 'no' || $optyinfo->rate == 'null' || $optyinfo->rate == ''){
                        echo "<div >
                        <div class='opportunity-value-of-rate' style='color:#012B33;'>$ TBD</div>
                        <div class='opportunity-rate'>Rate</div>
                        </div>";
                     }else{
                        if($optyinfo->rate_type == 'per_hour'){
                            echo "<div >
                                   <div class='opportunity-value-of-rate' style='color:#012B33;'>$" . $optyinfo->rate . "/hr</div>
                                   <div class='opportunity-rate'>Rate</div>
                                  </div>";
                        }else{
                             echo "<div >
                                       <div class='opportunity-value-of-rate' style='color:#012B33;'>$" . $optyinfo->rate . "/hr</div>
                                       <div class='opportunity-rate'>Rate</div> 
                                   
                                  </div>";
                        }
                     }

                        echo "
               </div>


             <div style='flex: 60%; display:flex;'>
                 <div>
                                    <svg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path d='M4.66667 7.33341H6V8.66675H4.66667V7.33341ZM12.6667 2.00008H12V0.666748H10.6667V2.00008H5.33333V0.666748H4V2.00008H3.33333C2.6 2.00008 2 2.60008 2 3.33341V12.6667C2 13.4001 2.6 14.0001 3.33333 14.0001H12.6667C13.4 14.0001 14 13.4001 14 12.6667V3.33341C14 2.60008 13.4 2.00008 12.6667 2.00008ZM12.6667 3.33341V4.66675H3.33333V3.33341H12.6667ZM3.33333 12.6667V6.00008H12.6667V12.6667H3.33333ZM7.33333 10.0001H8.66667V11.3334H7.33333V10.0001ZM10 10.0001H11.3333V11.3334H10V10.0001ZM10 7.33341H11.3333V8.66675H10V7.33341Z' fill='black' fill-opacity='0.87'/>
                                   </svg>

                 </div>
                 <div style='display:flex;'>
                 
                 <div>
                   <div class='opportunity-value-of-rate' style='color:#012B33;'>" . $optyinfo->start_date . "</div>
                   <div class='opportunity-rate'>Start</div>
                 </div>
                 <div  class='opportunity-value-of-rate' style='color:#012B33;'>to</div>
                 <div>";
                 if($optyinfo->complete_date != 'TBD' && $optyinfo->complete_date != '' && $optyinfo->complete_date != 'na'){
                     echo "<div class='opportunity-value-of-rate' style='color:#012B33;'>" . $optyinfo->complete_date . "</div>";
                 }else{
                     echo "<div class='opportunity-value-of-rate' style='color:#012B33;'>TBD</div>";
                 }
                 echo "    
                    <div class='opportunity-rate'>End</div>      

                 </div>
                 </div>

             
             
             
             </div>
             <div style='flex: 20%; display:flex;'>
             <div>
                      <svg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'>
                      <path d='M7.99935 2.66659C9.46602 2.66659 10.666 3.86659 10.666 5.33325C10.666 6.73325 9.26602 8.99992 7.99935 10.5999C6.73268 8.93325 5.33268 6.73325 5.33268 5.33325C5.33268 3.86659 6.53268 2.66659 7.99935 2.66659ZM7.99935 1.33325C5.79935 1.33325 3.99935 3.13325 3.99935 5.33325C3.99935 8.33325 7.99935 12.6666 7.99935 12.6666C7.99935 12.6666 11.9993 8.26659 11.9993 5.33325C11.9993 3.13325 10.1993 1.33325 7.99935 1.33325ZM7.99935 3.99992C7.26602 3.99992 6.66602 4.59992 6.66602 5.33325C6.66602 6.06659 7.26602 6.66659 7.99935 6.66659C8.73268 6.66659 9.33268 6.06659 9.33268 5.33325C9.33268 4.59992 8.73268 3.99992 7.99935 3.99992ZM13.3327 12.6666C13.3327 14.1333 10.9327 15.3333 7.99935 15.3333C5.06602 15.3333 2.66602 14.1333 2.66602 12.6666C2.66602 11.7999 3.46602 11.0666 4.73268 10.5333L5.13268 11.1333C4.46602 11.4666 3.99935 11.8666 3.99935 12.3333C3.99935 13.2666 5.79935 13.9999 7.99935 13.9999C10.1993 13.9999 11.9993 13.2666 11.9993 12.3333C11.9993 11.8666 11.5327 11.4666 10.7993 11.1333L11.1993 10.5333C12.5327 11.0666 13.3327 11.7999 13.3327 12.6666Z' fill='black'/>
                      </svg>

             </div>
             <div>
             <div class='opportunity-value-of-rate' style='color:#012B33;'>" . $optyinfo->location . "</div>
             <div class='opportunity-rate'>Location</div>
             
             </div>
             </div>
         </div>
          <div>
          <div class='mt-4 inter-font font-700 font-16' style='color:#012B33;' >Required Skills</div>
          <div class='my-2' style='display:flex;flex-warp:wrap;'>";

          foreach($optySkills as $skill){
                echo "<div class='mr-2 inter-font font-400 font-14' style='background-color:#E7E7E8;border-radius:4px;padding:4px 8px 4px 8px;'>
                        ".$skill->skill_name."  
                       </div>";
          }
          
        echo  "</div>
          
          </div>

















         </div>
          <div class='modal-footer' style='display:flex;justify-content:space-between;'>
          <a class='inter-font font-700 font-16' style='text-decoration:none;color:white;' href='?action=delete_opportunity&opportunityid=" . $optyinfo->public_id . "' onclick='return confirmDelete()' style='font-size: 12px; padding: 0.25rem 1rem;'>
          <div>
          <svg width='107' height='40' viewBox='0 0 107 40' fill='none' xmlns='http://www.w3.org/2000/svg'>
<rect width='107' height='40' rx='4' fill='white'/>
<path d='M18 27C18 27.5304 18.2107 28.0391 18.5858 28.4142C18.9609 28.7893 19.4696 29 20 29H28C28.5304 29 29.0391 28.7893 29.4142 28.4142C29.7893 28.0391 30 27.5304 30 27V15H18V27ZM20 17H28V27H20V17ZM27.5 12L26.5 11H21.5L20.5 12H17V14H31V12H27.5Z' fill='#DC3545'/>
<path d='M49.1364 26H45.0114V14.3636H49.1705C50.3409 14.3636 51.3485 14.5966 52.1932 15.0625C53.0379 15.5246 53.6875 16.1894 54.142 17.0568C54.6004 17.9242 54.8295 18.9621 54.8295 20.1705C54.8295 21.3826 54.6004 22.4242 54.142 23.2955C53.6875 24.1667 53.0341 24.8352 52.1818 25.3011C51.3333 25.767 50.3182 26 49.1364 26ZM47.4716 23.892H49.0341C49.7614 23.892 50.3731 23.7633 50.8693 23.5057C51.3693 23.2443 51.7443 22.8409 51.9943 22.2955C52.2481 21.7462 52.375 21.0379 52.375 20.1705C52.375 19.3106 52.2481 18.608 51.9943 18.0625C51.7443 17.517 51.3712 17.1155 50.875 16.858C50.3788 16.6004 49.767 16.4716 49.0398 16.4716H47.4716V23.892ZM60.5838 26.1705C59.6861 26.1705 58.9134 25.9886 58.2656 25.625C57.6217 25.2576 57.1255 24.7386 56.777 24.0682C56.4285 23.3939 56.2543 22.5966 56.2543 21.6761C56.2543 20.7784 56.4285 19.9905 56.777 19.3125C57.1255 18.6345 57.616 18.1061 58.2486 17.7273C58.8849 17.3485 59.6312 17.1591 60.4872 17.1591C61.063 17.1591 61.599 17.2519 62.0952 17.4375C62.5952 17.6193 63.0308 17.8939 63.402 18.2614C63.777 18.6288 64.0687 19.0909 64.277 19.6477C64.4853 20.2008 64.5895 20.8485 64.5895 21.5909V22.2557H57.2202V20.7557H62.3111C62.3111 20.4072 62.2353 20.0985 62.0838 19.8295C61.9323 19.5606 61.7221 19.3504 61.4531 19.1989C61.188 19.0436 60.8793 18.9659 60.527 18.9659C60.1596 18.9659 59.8338 19.0511 59.5497 19.2216C59.2694 19.3883 59.0497 19.6136 58.8906 19.8977C58.7315 20.178 58.6501 20.4905 58.6463 20.8352V22.2614C58.6463 22.6932 58.7259 23.0663 58.8849 23.3807C59.0478 23.6951 59.277 23.9375 59.5724 24.108C59.8679 24.2784 60.2183 24.3636 60.6236 24.3636C60.8925 24.3636 61.1387 24.3258 61.3622 24.25C61.5857 24.1742 61.777 24.0606 61.9361 23.9091C62.0952 23.7576 62.2164 23.572 62.2997 23.3523L64.5384 23.5C64.4247 24.0379 64.1918 24.5076 63.8395 24.9091C63.491 25.3068 63.0402 25.6174 62.4872 25.8409C61.938 26.0606 61.3035 26.1705 60.5838 26.1705ZM68.5895 14.3636V26H66.169V14.3636H68.5895ZM74.5057 26.1705C73.608 26.1705 72.8352 25.9886 72.1875 25.625C71.5436 25.2576 71.0473 24.7386 70.6989 24.0682C70.3504 23.3939 70.1761 22.5966 70.1761 21.6761C70.1761 20.7784 70.3504 19.9905 70.6989 19.3125C71.0473 18.6345 71.5379 18.1061 72.1705 17.7273C72.8068 17.3485 73.553 17.1591 74.4091 17.1591C74.9848 17.1591 75.5208 17.2519 76.017 17.4375C76.517 17.6193 76.9527 17.8939 77.3239 18.2614C77.6989 18.6288 77.9905 19.0909 78.1989 19.6477C78.4072 20.2008 78.5114 20.8485 78.5114 21.5909V22.2557H71.142V20.7557H76.233C76.233 20.4072 76.1572 20.0985 76.0057 19.8295C75.8542 19.5606 75.6439 19.3504 75.375 19.1989C75.1098 19.0436 74.8011 18.9659 74.4489 18.9659C74.0814 18.9659 73.7557 19.0511 73.4716 19.2216C73.1913 19.3883 72.9716 19.6136 72.8125 19.8977C72.6534 20.178 72.572 20.4905 72.5682 20.8352V22.2614C72.5682 22.6932 72.6477 23.0663 72.8068 23.3807C72.9697 23.6951 73.1989 23.9375 73.4943 24.108C73.7898 24.2784 74.1402 24.3636 74.5455 24.3636C74.8144 24.3636 75.0606 24.3258 75.2841 24.25C75.5076 24.1742 75.6989 24.0606 75.858 23.9091C76.017 23.7576 76.1383 23.572 76.2216 23.3523L78.4602 23.5C78.3466 24.0379 78.1136 24.5076 77.7614 24.9091C77.4129 25.3068 76.9621 25.6174 76.4091 25.8409C75.8598 26.0606 75.2254 26.1705 74.5057 26.1705ZM84.733 17.2727V19.0909H79.4773V17.2727H84.733ZM80.6705 15.1818H83.0909V23.3182C83.0909 23.5417 83.125 23.7159 83.1932 23.8409C83.2614 23.9621 83.3561 24.0473 83.4773 24.0966C83.6023 24.1458 83.7462 24.1705 83.9091 24.1705C84.0227 24.1705 84.1364 24.161 84.25 24.142C84.3636 24.1193 84.4508 24.1023 84.5114 24.0909L84.892 25.892C84.7708 25.9299 84.6004 25.9735 84.3807 26.0227C84.161 26.0758 83.8939 26.108 83.5795 26.1193C82.9962 26.142 82.4848 26.0644 82.0455 25.8864C81.6098 25.7083 81.2708 25.4318 81.0284 25.0568C80.786 24.6818 80.6667 24.2083 80.6705 23.6364V15.1818ZM90.1932 26.1705C89.2955 26.1705 88.5227 25.9886 87.875 25.625C87.2311 25.2576 86.7348 24.7386 86.3864 24.0682C86.0379 23.3939 85.8636 22.5966 85.8636 21.6761C85.8636 20.7784 86.0379 19.9905 86.3864 19.3125C86.7348 18.6345 87.2254 18.1061 87.858 17.7273C88.4943 17.3485 89.2405 17.1591 90.0966 17.1591C90.6723 17.1591 91.2083 17.2519 91.7045 17.4375C92.2045 17.6193 92.6402 17.8939 93.0114 18.2614C93.3864 18.6288 93.678 19.0909 93.8864 19.6477C94.0947 20.2008 94.1989 20.8485 94.1989 21.5909V22.2557H86.8295V20.7557H91.9205C91.9205 20.4072 91.8447 20.0985 91.6932 19.8295C91.5417 19.5606 91.3314 19.3504 91.0625 19.1989C90.7973 19.0436 90.4886 18.9659 90.1364 18.9659C89.7689 18.9659 89.4432 19.0511 89.1591 19.2216C88.8788 19.3883 88.6591 19.6136 88.5 19.8977C88.3409 20.178 88.2595 20.4905 88.2557 20.8352V22.2614C88.2557 22.6932 88.3352 23.0663 88.4943 23.3807C88.6572 23.6951 88.8864 23.9375 89.1818 24.108C89.4773 24.2784 89.8277 24.3636 90.233 24.3636C90.5019 24.3636 90.7481 24.3258 90.9716 24.25C91.1951 24.1742 91.3864 24.0606 91.5455 23.9091C91.7045 23.7576 91.8258 23.572 91.9091 23.3523L94.1477 23.5C94.0341 24.0379 93.8011 24.5076 93.4489 24.9091C93.1004 25.3068 92.6496 25.6174 92.0966 25.8409C91.5473 26.0606 90.9129 26.1705 90.1932 26.1705Z' fill='#DC3545'/>
</svg>

          </div>
          </a>
          <div style='display:flex;'>
            <button type='button' class='inter-font font-700 font-16' style='background-color: #E7E7E8;width:79px;height: 40px;padding: 8px 12px 8px 12px;border: none;border-radius: 4px;' data-dismiss='modal'>Cancel</button>
            <div  data-id='" . $optyinfo->public_id . "' id='openEditModal' class='ml-2 inter-font font-700 font-16 bt btn-succes openEditModal22' style='text-align:center;background-color: #F5A800; padding: 8px 12px 8px 12px;color: black;border: none ; border-radius: 4px; width:130px;height: 40px;;' >Edit</div>
           </div>
          </div>
      </div>
      <script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this opportunity? This action cannot be undone.')) {
        return true;
    } else {
        return false;
    }
}
</script>
      <script>
      $(document).ready(function() {
         $('.openEditModal22').on('click',function(){
        var opportunityId = $(this).data('id');
         
         $('#viewopportunitymodal').modal('hide');
         $.ajax({
          url: 'modal_forms/fetch_opportunity.php', // Adjust the URL
          method: 'GET',
          data: {
            id: opportunityId
          },
          success: function(response) {
            $('#editmodal .modal-body').html(response);
            $('#editmodal').modal('show');
          }
        });
      });
      });
      </script>
      
      ";
}
?>