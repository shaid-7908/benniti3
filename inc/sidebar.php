 <div class="sidebar-menue">
   <a href="index.php" style="color: inherit; text-decoration: none;">
     <div <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="siderbar-menu-item active-now"' : 'class="siderbar-menu-item"' ?>>
       <div class="xt-svg">
         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
           <path d="M13.5 9V4H20V9H13.5ZM4 12V4H10.5V12H4ZM13.5 20V12H20V20H13.5ZM4 20V15H10.5V20H4ZM5 11H9.5V5H5V11ZM14.5 19H19V13H14.5V19ZM14.5 8H19V5H14.5V8ZM5 19H9.5V16H5V19Z" fill="white" />
         </svg>
       </div>
       <div class="ml-2" style="font-size: 16px; font-weight: 600;">Dashboard</div>
     </div>
   </a>
   <div class="siderbar-menu-item " style="cursor: pointer;" id='serach' onclick="handleSearch()">
     <div class="xt-svg">
       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
         <path d="M19.6 21L13.3 14.7C12.8 15.1 12.225 15.4167 11.575 15.65C10.925 15.8833 10.2333 16 9.5 16C7.68333 16 6.146 15.3707 4.888 14.112C3.63 12.8533 3.00067 11.316 3 9.5C3 7.68333 3.62933 6.146 4.888 4.888C6.14667 3.63 7.684 3.00067 9.5 3C11.3167 3 12.8543 3.62933 14.113 4.888C15.3717 6.14667 16.0007 7.684 16 9.5C16 10.2333 15.8833 10.925 15.65 11.575C15.4167 12.225 15.1 12.8 14.7 13.3L21 19.6L19.6 21ZM9.5 14C10.75 14 11.8127 13.5627 12.688 12.688C13.5633 11.8133 14.0007 10.7507 14 9.5C14 8.25 13.5627 7.18767 12.688 6.313C11.8133 5.43833 10.7507 5.00067 9.5 5C8.25 5 7.18767 5.43767 6.313 6.313C5.43833 7.18833 5.00067 8.25067 5 9.5C5 10.75 5.43767 11.8127 6.313 12.688C7.18833 13.5633 8.25067 14.0007 9.5 14Z" fill="white" />
       </svg>

     </div>
     <div class="ml-2" style="font-size: 16px; font-weight: 600;">Search</div>
     <div id='uparrow' style="display: none; margin-left: auto; margin-right: 2px;">
       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
         <path d="M16.59 15.4199L12 10.8299L7.41 15.4199L6 13.9999L12 7.99992L18 13.9999L16.59 15.4199Z" fill="white" />
       </svg>

     </div>
     <div id='downarrow' style="margin-left: auto; margin-right: 2px;">
       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
         <path d="M7.41 8.57999L12 13.17L16.59 8.57999L18 9.98999L12 15.99L6 9.98999L7.41 8.57999Z" fill="white" />
       </svg>
     </div>

   </div>
   <a href="findsolver.php" id='findsolver' style="display: none; color: inherit; text-decoration: none;">
     <div <?php echo (basename($_SERVER['PHP_SELF']) == 'findsolver.php' || basename($_SERVER['PHP_SELF']) == 'viewSolver.php') ? 'class="siderbar-menu-item active-now"' : 'class="siderbar-menu-item "' ?>>
       <div class="xt-svg" style="opacity: 0;">
         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
           <path d="M4 21C3.45 21 2.97933 20.8043 2.588 20.413C2.19667 20.0217 2.00067 19.5507 2 19V8C2 7.45 2.196 6.97933 2.588 6.588C2.98 6.19667 3.45067 6.00067 4 6H8V4C8 3.45 8.196 2.97933 8.588 2.588C8.98 2.19667 9.45067 2.00067 10 2H14C14.55 2 15.021 2.196 15.413 2.588C15.805 2.98 16.0007 3.45067 16 4V6H20C20.55 6 21.021 6.196 21.413 6.588C21.805 6.98 22.0007 7.45067 22 8V19C22 19.55 21.8043 20.021 21.413 20.413C21.0217 20.805 20.5507 21.0007 20 21H4ZM4 19H20V8H4V19ZM10 6H14V4H10V6Z" fill="white" />
         </svg>



       </div>
       <div class="ml-2" style="font-size: 16px; font-weight: 600;">Find Solvers</div>
     </div>
   </a>

   <a href="findopportunity.php?query" id='findseeker' style="display: none; color: inherit; text-decoration: none;">
     <div <?php echo (basename($_SERVER['PHP_SELF']) == 'findopportunity.php' || basename($_SERVER['PHP_SELF']) == 'matchSuggest.php') ? 'class="siderbar-menu-item active-now"' : 'class="siderbar-menu-item "' ?>>
       <div class="xt-svg" style="opacity: 0;">
         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
           <path d="M4 21C3.45 21 2.97933 20.8043 2.588 20.413C2.19667 20.0217 2.00067 19.5507 2 19V8C2 7.45 2.196 6.97933 2.588 6.588C2.98 6.19667 3.45067 6.00067 4 6H8V4C8 3.45 8.196 2.97933 8.588 2.588C8.98 2.19667 9.45067 2.00067 10 2H14C14.55 2 15.021 2.196 15.413 2.588C15.805 2.98 16.0007 3.45067 16 4V6H20C20.55 6 21.021 6.196 21.413 6.588C21.805 6.98 22.0007 7.45067 22 8V19C22 19.55 21.8043 20.021 21.413 20.413C21.0217 20.805 20.5507 21.0007 20 21H4ZM4 19H20V8H4V19ZM10 6H14V4H10V6Z" fill="white" />
         </svg>



       </div>
       <div class="ml-2" style="font-size: 16px; font-weight: 600;">Find Opportunities</div>
     </div>
   </a>

   <a href="opportunityList.php" style="color: inherit; text-decoration: none;">
     <div <?php echo (basename($_SERVER['PHP_SELF']) == 'opportunityList.php') ? ' class="siderbar-menu-item active-now"' : ' class="siderbar-menu-item "' ?>>
       <div class="xt-svg">
         <svg width="24" height="24" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
           <path d="M8 20C7.45 20 6.97933 19.8043 6.588 19.413C6.19667 19.0217 6.00067 18.5507 6 18H10C10 18.55 9.80433 19.021 9.413 19.413C9.02167 19.805 8.55067 20.0007 8 20ZM4 17V15H12V17H4ZM4.25 14C3.1 13.3167 2.18733 12.4 1.512 11.25C0.836668 10.1 0.499334 8.85 0.500001 7.5C0.500001 5.41667 1.22933 3.646 2.688 2.188C4.14667 0.73 5.91733 0.000666667 8 0C10.0833 0 11.8543 0.729334 13.313 2.188C14.7717 3.64667 15.5007 5.41733 15.5 7.5C15.5 8.85 15.1627 10.1 14.488 11.25C13.8133 12.4 12.9007 13.3167 11.75 14H4.25ZM4.85 12H11.15C11.9 11.4667 12.4793 10.8083 12.888 10.025C13.2967 9.24167 13.5007 8.4 13.5 7.5C13.5 5.96667 12.9667 4.66667 11.9 3.6C10.8333 2.53333 9.53333 2 8 2C6.46667 2 5.16667 2.53333 4.1 3.6C3.03333 4.66667 2.5 5.96667 2.5 7.5C2.5 8.4 2.70433 9.24167 3.113 10.025C3.52167 10.8083 4.10067 11.4667 4.85 12Z" fill="white" />
         </svg>


       </div>
       <div class="ml-2" style="font-size: 16px; font-weight: 600;">My opportunities</div>
     </div>
   </a>

   <a href="viewOrganization.php?userid=<?php echo Session::get('userid') ?>" style="color: inherit; text-decoration: none;">


     <div <?php echo (basename($_SERVER['PHP_SELF']) == 'viewOrganization.php') ? 'class="siderbar-menu-item active-now"' : 'class="siderbar-menu-item "' ?>>
       <div class="xt-svg">
         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
           <path d="M4 21C3.45 21 2.97933 20.8043 2.588 20.413C2.19667 20.0217 2.00067 19.5507 2 19V8C2 7.45 2.196 6.97933 2.588 6.588C2.98 6.19667 3.45067 6.00067 4 6H8V4C8 3.45 8.196 2.97933 8.588 2.588C8.98 2.19667 9.45067 2.00067 10 2H14C14.55 2 15.021 2.196 15.413 2.588C15.805 2.98 16.0007 3.45067 16 4V6H20C20.55 6 21.021 6.196 21.413 6.588C21.805 6.98 22.0007 7.45067 22 8V19C22 19.55 21.8043 20.021 21.413 20.413C21.0217 20.805 20.5507 21.0007 20 21H4ZM4 19H20V8H4V19ZM10 6H14V4H10V6Z" fill="white" />
         </svg>



       </div>
       <div class="ml-2" style="font-size: 16px; font-weight: 600;">My organization</div>
     </div>
   </a>

 </div>
 <div style="border-top: 2px solid #053B45;padding: 8px;">
      <a href="https://www.bennit.ai/" target="_blank">
        <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
          Bennit.Ai
        </span>
      </a>
    </div>
 <script>
   function handleSearch() {
     const findseekre = document.getElementById('findseeker')
     const findsolver = document.getElementById('findsolver')
     const uparrow = document.getElementById('uparrow')
     const downarrow = document.getElementById('downarrow')
     if (findseekre.style.display != 'none') {
       findseekre.style.display = 'none'
       findsolver.style.display = 'none'
       uparrow.style.display = 'none'
       downarrow.style.display = 'block'
     } else {
       findseekre.style.display = 'block'
       findsolver.style.display = 'block'
       uparrow.style.display = 'block'
       downarrow.style.display = 'none'
     }
   }
 </script>