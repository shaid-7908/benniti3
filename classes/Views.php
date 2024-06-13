<?php
/* 
This class is used to build query-based display grids used across the site.
Pretty much anywhere you see a table of results, its coming from this class.
*/
class Views
{

    private $organizations;
    private $solvers;
    private $users;
    public function __construct($organizations, $solvers, $users)
    {
        $this->db = new Database();
        $this->organizations = $organizations;
        $this->solvers = $solvers;
        $this->users = $users;
    }

    public function showAndClearPendingMessage()
    {
        $pendingMsg = Session::get("pendingMsg");
        if (isset($pendingMsg)) {
            echo $pendingMsg;
        }
        Session::set("pendingMsg", NULL);
    }

    public function makeOrganizationGrid($orgData, $columns, $actions)
    {
        if (!isset($columns))
            $columns = [];
        if (!isset($actions))
            $actions = [];
        $html = <<<GRID
            <table id="gridOrganizations" class="table table-striped table-bordered" style="width:100%">
            <thead>
        GRID;
        //Header row
        $html .= '<tr>' . PHP_EOL;
        if (in_array("id", $columns))
            $html .= '    <th class="text-center">ID</th>' . PHP_EOL;
        $html .= '    <th class="text-center">Name</th>' . PHP_EOL;
        if (in_array("creator", $columns))
            $html .= '    <th class="text-center">Creator</th>' . PHP_EOL;
        $html .= '    <th class="text-center">Type</th>' . PHP_EOL;
        if (in_array("created", $columns))
            $html .= '    <th class="text-center">Created</th>' . PHP_EOL;
        if (in_array("solver", $columns) && Session::get('roleid') == 1)
            $html .= '    <th class="text-center">Solver Profiles</th>' . PHP_EOL;
        else
            $html .= '    <th class="text-center">Solver Profile</th>' . PHP_EOL;
        if (is_array($actions) && count($actions) > 0)
            $html .= '    <th width="25%" class="text-center">Actions</th>' . PHP_EOL;
        $html .= '<tr>' . PHP_EOL;
        $html .= '</thead>' . PHP_EOL;
        $html .= '<tbody>' . PHP_EOL;
        //Detail rows loop
        foreach ($orgData as $org) {
            $html .= '<tr class="text-center">';
            if (in_array("id", $columns))
                $html .= '    <td>' . getIfSet($org, "id") . '</td>' . PHP_EOL;
            $html .= '    <td>' . getIfSet($org, "orgname") . '</td>' . PHP_EOL;
            if (in_array("creator", $columns))
                $html .= '    <td>' . getIfSet($org, "creatorname") . '</td>' . PHP_EOL;
            $html .= '    <td>' . $this->organizations->getOrgType(getIfSet($org, "orgtype")) . '</td>' . PHP_EOL;
            if (in_array("created", $columns)) {
                $html .= '    <td><span class="badge badge-lg badge-secondary text-white">';
                $html .= formatDate(getIfSet($org, "created_at"));
                $html .= '</span></td>' . PHP_EOL;
            }
            if (in_array("solver", $columns)) {
                $html .= '    <td>';
                $orgSolvers = [];
                if (Session::get('roleid') == 1) {
                    $orgSolvers = $this->solvers->getSolverProfilesByOrgId(getIfSet($org, "public_id"), $this->organizations) ?: [];
                } else {
                    $orgSolvers =   $this->solvers->getAllSolverProfileByOrgIdAndUserId(getIfSet($org, "public_id"), Session::get('userid'), $this->organizations, $this->users);
                }
                if (in_array("viewsolver", $actions)) {
                    foreach ($orgSolvers as $thisSolver) {
                        $useHeadline = $thisSolver->headline;
                        $useHeadline = (strlen($useHeadline) > 33) ? substr($useHeadline, 0, 30) . '...' : $useHeadline;
                        $html .= "- <a href='solverView.php?solverid=" . $thisSolver->public_id . "'>" . $useHeadline . "</a><br>";
                    }
                }
                if ($orgSolvers) {
                    if (count($orgSolvers) == 0 && in_array("createsolver", $actions)) {
                        $html .= '<a href="solver.php?action=create_solver&orgid=' . getIfSet($org, "public_id") . '">Create Solver Profile</a>';
                    }
                }

                $html .= '    </td>' . PHP_EOL;
            }
            //Actions column
            $html .= '    <td>';
            if (in_array("users", $actions) && (getIfSet($org, "org_level") < 2) || Session::get('roleid') == 1) {
                $html .= '    <a class="btn btn-info btn-sm" href="userList.php?orgid=' . getIfSet($org, "public_id") . '">Users</a>' . PHP_EOL;
            }
            if (in_array("edit", $actions) && (getIfSet($org, "org_level") < 2) || Session::get('roleid') == 1) {
                $html .= '    <a class="btn btn-secondary btn-sm" href="organization.php?orgid=' . getIfSet($org, "public_id") . '">Edit</a>' . PHP_EOL;
            }
            if (in_array("delete", $actions) && Session::get('roleid') == 1) {
                $html .= '    <a class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this organization? This action cannot be undone.\')" ';
                $html .= 'href="?action=delete_organization&orgid=' . getIfSet($org, "public_id") . '">Delete</a>';
            }
            $html .= '    </td>' . PHP_EOL;
            $html .= '    </tr>' . PHP_EOL;
        }
        // If there were no rows, calculate the number of fixed and selected columns, then print a message
        if (count($orgData) < 1) {
            $html .= '<tr class="text-center">' . PHP_EOL;
            $html .= '   <td colspan="' . (count($columns) + 3) . '">No Organizations...</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }
        $html .= '</tbody>' . PHP_EOL;
        $html .= '</table>' . PHP_EOL;
        echo $html;
    }

    public function makeSubscriptionGrid($subscriptData, $columns, $actions)
    {
        $html = <<<GRID
            <table id="gridSubscriptions" class="table table-striped table-bordered" style="width:100%">
            <thead>
        GRID;
        //Header row
        $html .= '<tr>' . PHP_EOL;
        if (in_array("id", $columns))
            $html .= '    <th class="text-center">ID</th>' . PHP_EOL;
        $html .= '    <th class="text-center">Type</th>' . PHP_EOL;
        if (in_array("creator", $columns))
            $html .= '    <th class="text-center">Created By</th>' . PHP_EOL;
        if (in_array("organization", $columns))
            $html .= '    <th class="text-center">Organization</th>' . PHP_EOL;
        if (in_array("createdate", $columns))
            $html .= '    <th class="text-center">Created</th>' . PHP_EOL;
        if (in_array("renewaldate", $columns))
            $html .= '    <th class="text-center">Renewal</th>' . PHP_EOL;
        if (in_array("stripeid", $columns))
            $html .= '    <th class="text-center">Stripe ID</th>' . PHP_EOL;
        if (is_array($actions) && count($actions) > 0)
            $html .= '    <th width="25%" class="text-center">Actions</th>' . PHP_EOL;
        $html .= '<tr>' . PHP_EOL;
        $html .= '</thead>' . PHP_EOL;
        $html .= '<tbody>' . PHP_EOL;
        //Detail rows loop
        foreach ($subscriptData as $subscript) {
            $html .= '<tr class="text-center">';
            if (in_array("id", $columns))
                $html .= '    <td>' . getIfSet($subscript, "id") . '</td>' . PHP_EOL;
            $html .= '    <td>' . getIfSet($subscript, "subscription_type") . '</td>' . PHP_EOL;
            if (in_array("creator", $columns))
                $html .= '    <td>' . getIfSet($subscript, "username") . '</td>' . PHP_EOL;
            if (in_array("organization", $columns)) {
                $orgLookup = getIfSet($subscript, "org_public_id");
                if (isset($orgLookup)) {
                    $orgData = $this->organizations->getOrganizationInfoById($orgLookup);
                    $html .= '    <td>' . $orgData->orgname . '</td>' . PHP_EOL;
                } else {
                    $html .= '    <td></td>' . PHP_EOL;
                }
            }
            if (in_array("createdate", $columns))
                $html .= '    <td>' . getIfSet($subscript, "created_at") . '</td>' . PHP_EOL;
            if (in_array("renewaldate", $columns))
                $html .= '    <td>' . getIfSet($subscript, "expires_at") . '</td>' . PHP_EOL;
            if (in_array("stripeid", $columns))
                $html .= '    <td>' . getIfSet($subscript, "purchase_token") . '</td>' . PHP_EOL;
            //Actions column
            $html .= '    <td>';
            if (in_array("cancel", $actions)) {
                $html .= '    <a class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to cancel this subscription? This action cannot be undone. Pending invoices may still be processed!\')" ';
                if (isset($orgLookup)) {
                    $html .= 'href="?action=cancel_subscription&orgid=' . $orgLookup . '&subscriptionid=' . getIfSet($subscript, "public_id") . '">Cancel</a>';
                } else {
                    $html .= 'href="?action=cancel_subscription&userid=' . getIfSet($subscript, "user_public_id") . '&subscriptionid=' . getIfSet($subscript, "public_id") . '">Cancel</a>';
                }
            }
            $html .= '    </td>' . PHP_EOL;
            $html .= '    </tr>' . PHP_EOL;
        }
        // If there were no rows, calculate the number of fixed and selected columns, then print a message
        if (count($subscriptData) < 1) {
            $html .= '<tr class="text-center">' . PHP_EOL;
            $html .= '   <td colspan="' . (count($columns) + 3) . '">No Subscriptions...</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }
        $html .= '</tbody>' . PHP_EOL;
        $html .= '</table>' . PHP_EOL;
        echo $html;
    }

    public function makeOpportunityGrid2($optyData, $columns, $actions, $matches)
    {

        // Remove the "organization" column if it exists in the $columns array
        $organizationColumnIndex = array_search("organization", $columns);
        if ($organizationColumnIndex !== false) {
            unset($columns[$organizationColumnIndex]);
        }
        //Find user's orgs, so that we can prevent matching with own opportunity
        $userOrgs = $this->organizations->getAllOrganizationDataForUser(Session::get("userid"), $this->users);
        $userOrgIds = [];
        foreach ($userOrgs as $userOrg) {
            array_push($userOrgIds, $userOrg->id);
        }
        $html = <<<GRID
        <div class="table-responsive">
        <table id="gridOpportunities" class="table table-striped " style="width:100%; border: 1px solid silver">
        <thead>
        GRID;
        //Header row
        $html .= '<tr>' . PHP_EOL;
        if (in_array("id", $columns))
            $html .= '    <th class="text-center">ID</th>' . PHP_EOL;
        $html .= '    <th  style="text-align:left;">Headline</th>' . PHP_EOL;
        if (in_array("organization", $columns))
            $html .= '    <th class="text-center">Organization</th>' . PHP_EOL;

        $html .= '    <th class="text-center">Start Date</th>' . PHP_EOL;
        $html .= '<th class="text-center">Status</th>' . PHP_EOL;
        if (in_array("completedate", $columns))
            $html .= '    <th class="text-center">End Date</th>' . PHP_EOL;
        if (in_array("location", $columns))
            $html .= '    <th class="text-center">Location</th>' . PHP_EOL;
        if (in_array("created", $columns))
            $html .= '    <th class="text-center">Created</th>' . PHP_EOL;
        if (is_array($actions) && count($actions) > 0)
            $html .= '    <th width="25%" class="text-center">Actions</th>' . PHP_EOL;
        $html .= '<tr>' . PHP_EOL;
        $html .= '</thead>' . PHP_EOL;
        $html .= '<tbody>' . PHP_EOL;
        //Detail rows loop
        foreach ($optyData as $opty) {

            $match_data = $matches->getAllMatchDataForOpportunityByRealId(getIfSet($opty, 'id'));
            $match_count = count($match_data);
            $opty_status_is = getIfSet($opty, 'active_status');
            $approvedMatchCount = 0;
            if ($match_count != 0) {
                foreach ($match_data as $match) {
                    if (isset($match->matchmaker_approved) && $match->matchmaker_approved != 0) {
                        $approvedMatchCount++;
                    }
                }
            }

            $html .= '<tr class="text-center">';
            if (in_array("id", $columns))
                $html .= '    <td>' . getIfSet($opty, "id") . '</td>' . PHP_EOL;

            $html .= '    <td class="viewopportunityinmodalbutton" style="text-align:left;text-decoration:underline;cursor:pointer;min-width:200px" id="viewopportunityinmodalbutton" data-id="' . getIfSet($opty, "public_id") . ' ">' . getIfSet($opty, "headline");
            $html .= '</td>' . PHP_EOL;
            if (in_array("organization", $columns))
                $html .= '    <td>' . getIfSet($opty, "orgname") . '</td>' . PHP_EOL;
            $html .= '    <td>' . getIfSet($opty, "start_date") . '</td>' . PHP_EOL;
            if ($match_count == 0 && $opty_status_is == 'active') {
                $html .= '    <td style="padding:0px;" class="font-14 status-cell" data-hover-text="Opportunity is active"><div class="mt-2 inter-font font-500 font-12" style="background-color: #5ff85f;padding:4px;border-radius:4px;">Active</div></td>' . PHP_EOL;
            }
            if ($match_count == 0 && $opty_status_is == 'review') {

                $html .= '    <td style="padding:0px;" class="font-14 status-cell" data-hover-text="Bennit is reviewing your opportunity to ensure we have enough information to provide good matches."><div class="mt-2 inter-font font-500 font-12" style="background-color: #FFE69C;padding:4px;border-radius:4px;">In Review</div></td>' . PHP_EOL;
            }
            if ($match_count != 0 && $approvedMatchCount == 0) {
                $html .= '    <td style="padding:0px;" class="font-12 status-cell" data-hover-text="At Bennit, making great matches between subject matter expert and manufacturing challenges is so important that we review every potential match. We are reviewing a potential match!"><div class="mt-2 inter-font font-500 font-10" style="background-color: rgba(253, 126, 20, 0.3);padding:4px;border-radius:4px;">Match pending</div></td>' . PHP_EOL;
            }
            if ($match_count != 0 && $approvedMatchCount != 0) {
                $html .= '<td style="padding:0px;" class="font-12 inter-font font-700 status-cell" data-hover-text="This opportunity has ' . $approvedMatchCount . ' matches so far!"><div class="mt-2" style="background-color:#D1E7DD;padding:2px;border-radius:4px">' . $approvedMatchCount . ' Matches</div></td>' . PHP_EOL;
            }
            if (in_array("completedate", $columns))
                $html .= '    <td>' . getIfSet($opty, "completedate") . '</td>' . PHP_EOL;
            if (in_array("location", $columns))
                $html .= '    <td>' . getIfSet($opty, "location") . '</td>' . PHP_EOL;
            if (in_array("created", $columns))
                $html .= '    <td>' . getIfSet($opty, "created_at") . '</td>' . PHP_EOL;
            //Actions column
            $html .= '    <td style="display:flex;justify-content:center;align-items: center;flex-wrap:wrap;min-width:150px">';
            if (in_array("view", $actions)) {
                $html .= '    <a class="btn btn-info btn-sm" href="adminOpportunityView.php?opportunityid=' . getIfSet($opty, "public_id") . '">View</a>' . PHP_EOL;
            }
            if (in_array("edit", $actions)) {
                $html .= '    <a class=" editbutton inter-font font-700 font-16" style="width:65px;height:40px;border:2px solid #E7E7E8;padding:4px 8px 4px 8px;border-radius:4px;color:black;text-decoration:none" href="#public_id=' . getIfSet($opty, "public_id") . '" data-id="' . getIfSet($opty, "public_id") . ' ">Edit</a>' . PHP_EOL;
            }
            if (in_array("match", $actions) && !in_array($opty->fk_org_id, $userOrgIds)) {
                $html .= '    <a class="btn btn-success btn-sm" href="matchSuggest.php?as=solver&opportunityid=' . getIfSet($opty, "public_id") . '">Match</a>' . PHP_EOL;
            }
            if (in_array("adminmatch", $actions)) {
                $html .= '    <a class="btn btn-success btn-sm" href="matchSuggest.php?as=adminforsolver&opportunityid=' . getIfSet($opty, "public_id") . '">Match</a>' . PHP_EOL;
            }
            if (in_array("delete", $actions)) {
                $html .= '<div class="dropdown mx-2">';
                $html .= '<a class="" style="display:flex;justify-content:center;align-items: center;border:2px solid #E7E7E8;padding:4px 8px 4px 8px;text-decoration:none;color:black;border-radius:4px;height:40px;width:40px" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $html .= '<i class="fas fa-ellipsis-v"></i>'; // Replace with your three dots icon
                $html .= '</a>';
                $html .= '<div class="dropdown-menu custom-dropdown-menu" style="min-width:50px !important;" aria-labelledby="dropdownMenuLink">';
                $html .= '<a class="inter-font font-700 font-16" style="text-decoration:none;color:white;" href="?action=delete_opportunity&opportunityid=' . getIfSet($opty, "public_id") . '" onclick="return confirm(\'Are you sure you want to delete this opportunity? This action cannot be undone.\')" style="font-size: 12px; padding: 0.25rem 1rem;">Delete</a>';
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '    </td>' . PHP_EOL;
            $html .= '    </tr>' . PHP_EOL;
        }
        // If there were no rows, calculate the number of fixed and selected columns, then print a message
        if (count($optyData) < 1) {
            $html .= '<tr class="text-center">' . PHP_EOL;
            $html .= '   <td colspan="' . (count($columns) + 3) . '">No Opportunities...</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }
        $html .= '</tbody>' . PHP_EOL;
        $html .= '</table>' . PHP_EOL;
        $html .= '</div>'.PHP_EOL;
        $html .= "<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusCells = document.querySelectorAll('.status-cell');

        statusCells.forEach(cell => {
            cell.addEventListener('mouseover', () => {
                const hoverText = cell.dataset.hoverText;

                // Create a temporary element to display the hover text
                const hoverBox = document.createElement('div');
                hoverBox.classList.add('hover-box');
                hoverBox.textContent = hoverText;
                document.body.appendChild(hoverBox);

                // Position the hover box below the cell
                const cellRect = cell.getBoundingClientRect();
                const hoverBoxWidth = hoverBox.offsetWidth;
                hoverBox.style.left = cellRect.left + window.scrollX + (cell.offsetWidth - hoverBoxWidth) / 2 + 'px';
                hoverBox.style.top = cellRect.top + window.scrollY + cell.offsetHeight + 5 + 'px';
            });

            cell.addEventListener('mouseout', () => {
                const hoverBox = document.querySelector('.hover-box');
                if (hoverBox) {
                    hoverBox.remove();
                }
            });
        });
    });
</script>
" . PHP_EOL;
        echo $html;
    }

    public function makeOpportunityGrid($optyData, $columns, $actions)
    {
        // Remove the "organization" column if it exists in the $columns array
        $organizationColumnIndex = array_search("organization", $columns);
        if ($organizationColumnIndex !== false) {
            unset($columns[$organizationColumnIndex]);
        }
        //Find user's orgs, so that we can prevent matching with own opportunity
        $userOrgs = $this->organizations->getAllOrganizationDataForUser(Session::get("userid"), $this->users);
        $userOrgIds = [];
        foreach ($userOrgs as $userOrg) {
            array_push($userOrgIds, $userOrg->id);
        }
        $html = <<<GRID
        <table id="gridOpportunities" class="table table-striped " style="width:100%; border: 1px solid silver">
        <thead>
        GRID;
        //Header row
        $html .= '<tr>' . PHP_EOL;
        if (in_array("id", $columns))
            $html .= '    <th class="text-center">ID</th>' . PHP_EOL;
        $html .= '    <th  style="text-align:left;">Headline</th>' . PHP_EOL;
        if (in_array("organization", $columns))
            $html .= '    <th class="text-center">Organization</th>' . PHP_EOL;
        $html .= '    <th class="text-center">Start Date</th>' . PHP_EOL;
        if (in_array("completedate", $columns))
            $html .= '    <th class="text-center">End Date</th>' . PHP_EOL;
        if (in_array("location", $columns))
            $html .= '    <th class="text-center">Location</th>' . PHP_EOL;
        if (in_array("created", $columns))
            $html .= '    <th class="text-center">Created</th>' . PHP_EOL;
        if (is_array($actions) && count($actions) > 0)
            $html .= '    <th width="25%" class="text-center">Actions</th>' . PHP_EOL;
        $html .= '<tr>' . PHP_EOL;
        $html .= '</thead>' . PHP_EOL;
        $html .= '<tbody>' . PHP_EOL;
        //Detail rows loop
        foreach ($optyData as $opty) {
            $html .= '<tr class="text-center">';
            if (in_array("id", $columns))
                $html .= '    <td>' . getIfSet($opty, "id") . '</td>' . PHP_EOL;
            $html .= '    <td class="viewopportunityinmodalbutton" style="text-align:left;text-decoration:underline;cursor:pointer;" id="viewopportunityinmodalbutton" data-id="' . getIfSet($opty, "public_id") . ' ">' . getIfSet($opty, "headline");
            $html .= '</td>' . PHP_EOL;
            if (in_array("organization", $columns))
                $html .= '    <td>' . getIfSet($opty, "orgname") . '</td>' . PHP_EOL;
            $html .= '    <td>' . getIfSet($opty, "start_date") . '</td>' . PHP_EOL;
            if (in_array("completedate", $columns))
                $html .= '    <td>' . getIfSet($opty, "completedate") . '</td>' . PHP_EOL;
            if (in_array("location", $columns))
                $html .= '    <td>' . getIfSet($opty, "location") . '</td>' . PHP_EOL;
            if (in_array("created", $columns))
                $html .= '    <td>' . getIfSet($opty, "created_at") . '</td>' . PHP_EOL;
            //Actions column
            $html .= '    <td style="display:flex;justify-content:center;align-items: center;flex-wrap:wrap">';
            if (in_array("view", $actions)) {
                $html .= '    <a class="btn btn-info btn-sm" href="adminOpportunityView.php?opportunityid=' . getIfSet($opty, "public_id") . '">View</a>' . PHP_EOL;
            }
            if (in_array("edit", $actions)) {
                $html .= '    <a class=" editbutton inter-font font-700 font-16" style="width:65px;height:40px;border:2px solid #E7E7E8;padding:4px 8px 4px 8px;border-radius:4px;color:black;text-decoration:none" href="#public_id=' . getIfSet($opty, "public_id") . '" data-id="' . getIfSet($opty, "public_id") . ' ">Edit</a>' . PHP_EOL;
            }
            if (in_array("match", $actions) && !in_array($opty->fk_org_id, $userOrgIds)) {
                $html .= '    <a class="btn btn-success btn-sm" href="matchSuggest.php?as=solver&opportunityid=' . getIfSet($opty, "public_id") . '">Match</a>' . PHP_EOL;
            }
            if (in_array("adminmatch", $actions)) {
                $html .= '    <a class="btn btn-success btn-sm" href="matchSuggest.php?as=adminforsolver&opportunityid=' . getIfSet($opty, "public_id") . '">Match</a>' . PHP_EOL;
            }
            if (in_array("delete", $actions)) {
                $html .= '<div class="dropdown mx-2">';
                $html .= '<a class="" style="display:flex;justify-content:center;align-items: center;border:2px solid #E7E7E8;padding:4px 8px 4px 8px;text-decoration:none;color:black;border-radius:4px;height:40px;width:40px" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $html .= '<i class="fas fa-ellipsis-v"></i>'; // Replace with your three dots icon
                $html .= '</a>';
                $html .= '<div class="dropdown-menu custom-dropdown-menu" style="min-width:50px !important;" aria-labelledby="dropdownMenuLink">';
                $html .= '<a class="inter-font font-700 font-16" style="text-decoration:none;color:white;" href="?action=delete_opportunity&opportunityid=' . getIfSet($opty, "public_id") . '" onclick="return confirm(\'Are you sure you want to delete this opportunity? This action cannot be undone.\')" style="font-size: 12px; padding: 0.25rem 1rem;">Delete</a>';
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '    </td>' . PHP_EOL;
            $html .= '    </tr>' . PHP_EOL;
        }
        // If there were no rows, calculate the number of fixed and selected columns, then print a message
        if (count($optyData) < 1) {
            $html .= '<tr class="text-center">' . PHP_EOL;
            $html .= '   <td colspan="' . (count($columns) + 3) . '">No Opportunities...</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }
        $html .= '</tbody>' . PHP_EOL;
        $html .= '</table>' . PHP_EOL;
        echo $html;
    }

    public function makeSolverGrid($solverData, $columns, $actions)
    {
        //Find user's orgs, so that we can prevent matching with own solver
        $userOrgs = $this->organizations->getAllOrganizationDataForUser(Session::get("userid"), $this->users);
        $userOrgIds = [];
        foreach ($userOrgs as $userOrg) {
            array_push($userOrgIds, $userOrg->id);
        }

        $html = <<<GRID
            <table id="gridSolvers" class="table table-striped table-bordered" style="width:100%">
            <thead>
        GRID;
        //Header row
        $html .= '<tr>' . PHP_EOL;
        if (in_array("id", $columns))
            $html .= '    <th class="text-center">ID</th>' . PHP_EOL;
        if (in_array("organization", $columns))
            $html .= '    <th class="text-center">Organization</th>' . PHP_EOL;
        $html .= '    <th class="text-center">Headline</th>' . PHP_EOL;
        $html .= '    <th class="text-center">Availablity</th>' . PHP_EOL;
        if (in_array("rate", $columns))
            $html .= '    <th class="text-center">Rate</th>' . PHP_EOL;
        if (in_array("location", $columns))
            $html .= '    <th class="text-center">Location</th>' . PHP_EOL;
        if (in_array("adminflags", $columns) && Session::get('roleid') == 1)
            $html .= '    <th class="btn-sm text-center">Admin Flags</th>' . PHP_EOL;
        if (in_array("created", $columns))
            $html .= '    <th class="text-center">Created</th>' . PHP_EOL;
        if (is_array($actions) && count($actions) > 0)
            $html .= '    <th width="25%" class="text-center">Actions</th>' . PHP_EOL;
        $html .= '<tr>' . PHP_EOL;
        $html .= '</thead>' . PHP_EOL;
        $html .= '<tbody>' . PHP_EOL;
        //Detail rows loop
        foreach ($solverData as $solver) {
            $html .= '  <tr class="text-center">' . PHP_EOL;
            if (in_array("id", $columns))
                $html .= '    <td>' . getIfSet($solver, "id") . '</td>' . PHP_EOL;
            if (in_array("organization", $columns))
                $html .= '    <td>' . getIfSet($solver, "orgname") . '</td>' . PHP_EOL;
            $html .= '    <td>' . getIfSet($solver, "headline");
            if (in_array($solver->fk_org_id, $userOrgIds))
                $html .= '<br>(Your organization: ' . getIfSet($solver, "orgname") . ')';
            $html .= '    <td>' . getIfSet($solver, "availability") . '</td>' . PHP_EOL;
            if (in_array("rate", $columns))
                $html .= '    <td>' . getIfSet($solver, "rate") . '</td>' . PHP_EOL;
            if (in_array("location", $columns))
                $html .= '    <td>' . getIfSet($solver, "locations") . '</td>' . PHP_EOL;
            if (in_array("adminflags", $columns) && Session::get('roleid') == 1) {
                $coachChecked = "";
                if (getIfSet($solver, "is_coach"))
                    $coachChecked = "checked";
                $externalChecked = "";
                if (getIfSet($solver, "allow_external"))
                    $externalChecked = "checked";
                $html .= '    <td class="btn-sm">' . PHP_EOL;
                $html .= '     <input type="checkbox" disabled id="is_coach" name="is_coach" ' . $coachChecked . '> <label for="is_coach">Coach</label><br/>' . PHP_EOL;
                $html .= '     <input type="checkbox" disabled id="allow_external" name="allow_external" ' . $externalChecked . '> <label for="is_coach">External</label><br/>' . PHP_EOL;
                $html .= '    </td>' . PHP_EOL;
            }
            if (in_array("created", $columns))
                $html .= '    <td>' . getIfSet($solver, "created_at") . '</td>' . PHP_EOL;
            //Actions column
            $html .= '    <td>' . PHP_EOL;
            if (in_array("view", $actions)) {
                $html .= '     <a class="btn btn-info btn-sm" href="solverView.php?solverid=' . getIfSet($solver, "public_id") . '">View</a>' . PHP_EOL;
            }
            if (in_array("edit", $actions)) {
                $html .= '     <a id="#editBtn" class="btn btn-secondary btn-sm" href="solver.php?solverid=' . getIfSet($solver, "public_id") . '&orgid=' . getIfSet($solver, "fk_org_id") . '">Edit</a>' . PHP_EOL;
            }
            if (in_array("adminmatch", $actions)) {
                $html .= '     <a class="btn btn-success btn-sm" href="matchSuggest.php?as=adminforseeker&solverid=' . getIfSet($solver, "public_id") . '">Match</a>' . PHP_EOL;
            }
            if (in_array("match", $actions) && !in_array($solver->fk_org_id, $userOrgIds)) {
                $html .= '     <a class="btn btn-success btn-sm" href="matchSuggest.php?as=seeker&solverid=' . getIfSet($solver, "public_id") . '">Match</a>' . PHP_EOL;
            }
            if (in_array("delete", $actions)) {
                $html .= '     <a class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this solver? This action cannot be undone.\')" ';
                $html .= 'href="?action=delete_solver&solverid=' . getIfSet($solver, "public_id") . '">Delete</a>' . PHP_EOL;
            }
            $html .= '    </td>' . PHP_EOL;
            $html .= '  </tr>' . PHP_EOL;
        }
        // If there were no rows, calculate the number of fixed and selected columns, then print a message
        if (count($solverData) < 1) {
            $html .= '  <tr class="text-center">' . PHP_EOL;
            $html .= '    <td colspan="' . (count($columns) + 3) . '">No Solvers...</td>' . PHP_EOL;
            $html .= '  </tr>' . PHP_EOL;
        }
        $html .= '</tbody>' . PHP_EOL;
        $html .= '</table>' . PHP_EOL;
        echo $html;
    }

    public function makeMatchGrid($matchData, $columns, $actions)
    {
        $html = <<<GRID
          <table id="gridMatches" class="table table-striped table-bordered" style="width:100%">
           <thead>
        GRID;
        //Header row
        $html .= PHP_EOL . '    <tr>' . PHP_EOL;

        $html .= '    <th class="text-center">ID</th>' . PHP_EOL;
        $html .= '      <th class="text-center">Opportunity</th>' . PHP_EOL;
        $html .= '      <th class="text-center">Solver</th>' . PHP_EOL;
        if (in_array("suggester", $columns))
            $html .= '      <th class="text-center">Suggested By</th>' . PHP_EOL;
        if (in_array("actors", $columns)) {
            $html .= '      <th class="text-center">Seeker Approved</th>' . PHP_EOL;
            $html .= '      <th class="text-center">Solver Approved</th>' . PHP_EOL;
        }
        if (in_array("created", $columns))
            $html .= '      <th class="text-center">Created</th>' . PHP_EOL;
        if (is_array($actions) && count($actions) > 0)
            $html .= '      <th width=15% class="text-center">Actions</th>' . PHP_EOL;
        $html .= '    <tr>' . PHP_EOL;
        $html .= '  </thead>' . PHP_EOL;
        $html .= '  <tbody >' . PHP_EOL;
        //Detail rows loop
        foreach ($matchData as $match) {
            $html .= '    <tr class="text-center">' . PHP_EOL;

            $html .= '      <td>' . getIfSet($match, "id") . '</td>' . PHP_EOL;
            $html .= '      <td><a style="color: inherit" href="opportunityView.php?opportunityid=' . getIfSet($match, "opportunity_public_id") . '">' . getIfSet($match, "opportunity_headline") .  '</a>';
            if (in_array("opportunityorg", $columns))
                $html .= ' (' . getIfSet($match, "opportunity_orgname") . ')';
            $html .= '</td>' . PHP_EOL;
            $html .= '      <td><a href="solverView.php?solverid=' . getIfSet($match, "solver_public_id") . '">' . getIfSet($match, "solver_headline") .  '</a>';
            if (in_array("solverorg", $columns))
                $html .= ' (' . getIfSet($match, "solver_orgname") . ')';
            $html .= '</td>' . PHP_EOL;
            if (in_array("suggester", $columns))
                $html .= '      <td><a href="userProfile.php?userid=' . getIfSet($match, "matched_by") . '">' . getIfSet($match, "matched_by_username") .  '</a></td>' . PHP_EOL;
            if (in_array("actors", $columns) && Session::get('roleid') == 1) {
                $html .= '      <td class="btn-sm">' . PHP_EOL;
                $html .= '       <input type="checkbox" disabled';
                if ($match->seeker_match != 0)
                    $html .= ' checked';
                $html .= '>' . PHP_EOL;
                $html .= '      <td class="btn-sm">' . PHP_EOL;
                $html .= '       <input type="checkbox" disabled';
                if ($match->solver_match != 0)
                    $html .= ' checked';
                $html .= '>' . PHP_EOL;
            }
            if (in_array("created", $columns))
                $html .= '      <td>' . getIfSet($match, "created_at") . '</td>' . PHP_EOL;
            //Actions column
            $html .= '      <td>' . PHP_EOL;
            if (in_array("adminconfirm", $actions) && $match->matchmaker_approved == 0 && Session::get('roleid') == 1) {
                $html .= '        <a class="btn btn-success btn-sm" href="?action=admin_approve&as=admin&matchid=' . getIfSet($match, "public_id") . '">Approve</a>' . PHP_EOL;
            }
            if (in_array("adminreject", $actions) && $match->matchmaker_approved != 0 && Session::get('roleid') == 1) {
                $html .= '        <a class="btn btn-danger btn-sm" href="?action=admin_reject&as=admin&matchid=' . getIfSet($match, "public_id") . '">Reject</a>' . PHP_EOL;
            }
            if (in_array("admincontact", $actions) && $match->matchmaker_approved != 0 && $match->seeker_match != 0  && $match->solver_match != 0 && Session::get('roleid') == 1) {
                $html .= '        <a onclick="return confirm(\'This match has been approved by all parties!\nDo you want to email the users with your computer\s default email app?\')" class="btn btn-success';
                $html .= ' btn-sm" href="?action=admin_contact&as=admin&matchid=' . getIfSet($match, "public_id") . '">Contact</a>';
            }
            if (in_array("userconfirm", $actions)) {
                $html .= '        <a class="btn btn-success btn-sm" href="?action=user_approve&matchid=' . getIfSet($match, "public_id") . '">Approve</a>' . PHP_EOL;
            }
            if (in_array("userreject", $actions)) {
                $html .= '        <a class="btn btn-danger btn-sm" href="?action=user_reject&matchid=' . getIfSet($match, "public_id") . '">Reject</a>' . PHP_EOL;
            }
            $html .= '      </td>' . PHP_EOL;
            $html .= '    </tr>' . PHP_EOL;
        }
        // If there were no rows, calculate the number of fixed and selected columns, then print a message
        if (count($matchData) < 1) {
            $html .= '<tr class="text-center">' . PHP_EOL;
            $html .= '   <td colspan="' . (count($columns) + 3) . '">No Matches...</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }
        $html .= '   </tbody>' . PHP_EOL;
        $html .= '  </table>' . PHP_EOL;
        echo PHP_EOL . $html;
    }

    public function makeUserGrid($userData, $columns, $actions, $usersOrgLevel = 999)
    {
        if (!isset($columns))
            $columns = [];
        if (!isset($actions))
            $actions = [];
        $html = <<<GRID
            <table id="gridUsers" class="table table-striped table-bordered" style="width:100% ;">
            <thead>
        GRID;
        //Header row
        $html .= '<tr>' . PHP_EOL;
        if (in_array("id", $columns))
            $html .= '    <th class="text-center">ID</th>' . PHP_EOL;
        $html .= '    <th class="text-center">Full Name</th>' . PHP_EOL;
        if (in_array("username", $columns))
            $html .= '    <th class="text-center">Username</th>' . PHP_EOL;
        if (in_array("email", $columns))
            $html .= '    <th class="text-center">Email</th>' . PHP_EOL;
        if (in_array("phone", $columns))
            $html .= '    <th class="text-center">Phone</th>' . PHP_EOL;
        if (in_array("orglevel", $columns))
            $html .= '    <th class="text-center">Org Level</th>' . PHP_EOL;
        if (in_array("status", $columns))
            $html .= '    <th class="text-center">Status</th>' . PHP_EOL;
        if (in_array("created", $columns))
            $html .= '    <th class="text-center">Created</th>' . PHP_EOL;
        if (is_array($actions) && count($actions) > 0 && in_array("orgadd", $actions))
            $html .= '    <th width="25%" class="text-center">Organization Role</th>' . PHP_EOL;
        else
            $html .= '    <th width="25%" class="text-center">Actions</th>' . PHP_EOL;
        $html .= '<tr>' . PHP_EOL;
        $html .= '</thead>' . PHP_EOL;
        $html .= '<tbody>' . PHP_EOL;
        //Detail rows loop
        foreach ($userData as $user) {
            $html .= '<tr class="text-center">';
            if (in_array("id", $columns))
                $html .= '    <td>' . getIfSet($user, "id") . '</td>' . PHP_EOL;
            $html .= '    <td>' . getIfSet($user, "fullname") . '</td>' . PHP_EOL;
            if (in_array("username", $columns)) {
                $html .= '    <td>' . getIfSet($user, "username") . '<br>';
                if (getIfSet($user, "roleid")  == '1') {
                    $html .= '<span class="badge badge-lg badge-dark text-white">Global Admin</span>';
                } elseif (getIfSet($user, "roleid") == '2') {
                    $html .= '<span class="badge badge-lg badge-dark text-white">Match Maker</span>';
                } elseif (getIfSet($user, "roleid") == '3') {
                    $html .= '<span class="badge badge-lg badge-dark text-white">User</span>';
                }
                $html .= '</td>' . PHP_EOL;
            }
            if (in_array("email", $columns))
                $html .= '    <td>' . getIfSet($user, "email") . '</td>' . PHP_EOL;
            if (in_array("phone", $columns))
                $html .= '    <td>' . getIfSet($user, "phone") . '</td>' . PHP_EOL;
            if (in_array("orglevel", $columns)) {
                $html .= '    <td>';
                if (getIfSet($user, "fk_user_id") == getIfSet($user, "creatorid"))
                    $html .= '<span class="badge badge-lg badge-warning text-black">Creator</span><br>';
                if (getIfSet($user, "org_level")  == '1') {
                    $html .= '<span class="badge badge-lg badge-success text-white">Admin</span>';
                } elseif (getIfSet($user, "org_level") == '2') {
                    $html .= '<span class="badge badge-lg badge-info text-white">Editor</span>';
                } elseif (getIfSet($user, "org_level") == '3') {
                    $html .= '<span class="badge badge-lg badge-dark text-white">Resource</span>';
                }
                $html .= '</td>' . PHP_EOL;
            }
            if (in_array("status", $columns)) {
                $html .= '    <td>';
                if (getIfSet($user, "is_disabled") == "0")
                    $html .= '<span class="badge badge-lg badge-success text-white">Enabled</span>' . PHP_EOL;
                else
                    $html .= '<span class="badge badge-lg badge-dark text-white">Disabled</span>' . PHP_EOL;
            }
            if (in_array("created", $columns)) {
                $html .= '    <td><span class="badge badge-lg badge-secondary text-white">';
                $html .= formatDate(getIfSet($user, "created_at"));
                $html .= '</span></td>' . PHP_EOL;
            }
            //Actions column
            $html .= '    <td>' . PHP_EOL;
            // Remove user from org
            if (
                in_array("orgremove", $actions)
                && (checkUserAuth("remove_user_from_org", $usersOrgLevel) || checkUserAuth("remove_user_from_org_orthogonal", Session::get('roleid'))) && isset($_GET["orgid"])
            ) {
                $allowButton = '';
                if (getIfSet($user, "fk_user_id") == getIfSet($user, "creatorid"))    //Don't allow group creator to be disabled!
                    $allowButton = ' disabled';
                $html .= '        <a onclick="return confirm(\'Are you sure you want to remove this user from their organization?\')" class="btn btn-warning btn-sm';
                $html .= $allowButton . '" href="?action=remove_user_from_org&userid=' . getIfSet($user, "public_id") . '&orgid=' . $_GET["orgid"] . '">Remove</a>' . PHP_EOL;
            }
            // Add user to org
            if (
                in_array("orgadd", $actions)
                && (checkUserAuth("add_user_to_org", getIfSet($user, "org_level")) || checkUserAuth("add_user_to_org_orthogonal", Session::get('roleid'))) && isset($_GET["orgid"])
            ) {
                $html .= '        <a class="btn btn-warning btn-sm" href="userList.php?action=add_user_to_org&org_level=1&userid=' . getIfSet($user, "public_id") . '&orgid=' . $_GET["orgid"] . '">Admin</a>' . PHP_EOL;
                $html .= '        <a class="btn btn-info btn-sm" href="userList.php?action=add_user_to_org&org_level=2&userid=' . getIfSet($user, "public_id") . '&orgid=' . $_GET["orgid"] . '">Editor</a>' . PHP_EOL;
                $html .= '        <a class="btn btn-secondary btn-sm" href="userList.php?action=add_user_to_org&org_level=3&userid=' . getIfSet($user, "public_id") . '&orgid=' . $_GET["orgid"] . '">Resource</a>' . PHP_EOL;
            }
            // Edit a user
            if (
                in_array("edit", $actions)
                && (checkUserAuth("edit_user", getIfSet($user, "role_id")) || checkUserAuth("edit_user_orthogonal", Session::get('roleid')))
            ) {
                $html .= '        <a class="btn btn-secondary btn-sm" href="userProfile.php?userid=' . getIfSet($user, "public_id") . '">Edit</a>' . PHP_EOL;
            }
            // Disable/enable a user
            if (
                in_array("disable", $actions)
                && (checkUserAuth("disable_user", Session::get('roleid')))
            ) {
                $allowButton = '';
                if (Session::get("userid") == getIfSet($user, "public_id"))  //Don't allow user to disable themselves!
                    $allowButton = ' disabled';
                if (getIfSet($user, "is_disabled") == '0') {
                    $html .= '        <a onclick="return confirm(\'Are you sure you want to disable this user?\')" class="btn btn-warning';
                    $html .= $allowButton . ' btn-sm" href="?action=disable_user&userid=' . getIfSet($user, "public_id") . '">Disable</a>';
                } else {
                    $html .= '        <a onclick="return confirm(\'Are you sure you want to enable this user?\')" class="btn btn-info';
                    $html .= $allowButton . ' btn-sm" href="?action=enable_user&userid=' . getIfSet($user, "public_id") . '">Enable</a>';
                }
                $html .= PHP_EOL;
            }
            //Delete a user
            if (
                in_array("delete", $actions)
                && checkUserAuth("delete_user", Session::get('roleid'))
            ) {
                $html .= '        <a class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this user? This action cannot be undone.\')" ';
                $html .= 'href="?action=delete_user&userid=' . getIfSet($user, "public_id") . '">Delete</a>' . PHP_EOL;
            }
            $html .= '    </td>' . PHP_EOL;
            $html .= '    </tr>' . PHP_EOL;
        }
        // If there were no rows, calculate the number of fixed and selected columns, then print a message
        if (count($userData) < 1) {
            $html .= '<tr class="text-center">' . PHP_EOL;
            $html .= '   <td colspan="' . (count($columns) + 3) . '">No Users...</td>' . PHP_EOL;
            $html .= '</tr>' . PHP_EOL;
        }
        $html .= '</tbody>' . PHP_EOL;
        $html .= '</table>' . PHP_EOL;
        echo $html;
    }
}
