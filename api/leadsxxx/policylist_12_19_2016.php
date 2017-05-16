<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading ng-scope">
        <div class="col-xs-8">
            <h2>Your Policies</h2>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li class="active">
                    <strong>Policies</strong>
                </li>
            </ol>
        </div>
        <div class="col-xs-4">
            <div class="title-action">
                <a href="#lead/create" class="btn btn-primary ">Create a Lead</a>
            </div>
        </div>
    </div>
</div>
<script>
    function changeFilters(filter, value) {
        $("#search_" + filter).val(value);
        $("#filterForm").submit();
    }
    $(document).ready(function() {
        // Attach a submit handler to the form
        $("#searchForm").submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();
            $.ajax({
                url: $(this).attr("action"),
                type: 'GET',
                data: $(this).serialize(),
                success: function(result) {
                    $("#results").empty().append(result);
                    console.log("done");
                }
            });
        });
        $('.submissionErrors').click(function() {
            $("#search_page").val("1");
            $("#search_submissionErrors").val("Y");
            $("#filterForm").submit();
            return false;
        });
        $('.pastDue').click(function() {
            $("#search_page").val("1");
            $("#search_pastDue").val("Y");
            $("#filterForm").submit();
            return false;
        });
        $('.followup').click(function() {
            $("#search_page").val("1");
            $("#search_followup").val("Y");
            $("#filterForm").submit();
            return false;
        });
        $('.majorMed').click(function() {
            $("#search_page").val("1");
            $("#search_majorMed").val("Y");
            $("#filterForm").submit();
            return false;
        });
          $('.notPaid').click(function() {
            $("#search_page").val("1");
            $("#search_notPaid").val("Y");
            $("#filterForm").submit();
            return false;
        });
   $('.noPolicyNumber').click(function() {
            $("#search_page").val("1");
            $("#search_noPolicyNumber").val("Y");
            $("#filterForm").submit();
            return false;
        });
        $('.submitToday').click(function() {
            $("#search_page").val("1");
            $("#search_submitToday").val("Y");
            $("#filterForm").submit();
            return false;
        });
        $('.filterLink').click(function() {
            var info = $(this).attr("filter-id");
            var value = $(this).attr("filter-value");
            $("#search_page").val("1");
            $("#" + info).val(value);
            $("#filterForm").submit();
            return false;
        });
         $('.filterLink2').click(function() {
            var info = $(this).attr("filter-id");
            var value = $(this).attr("filter-value");
            $("#search_page").val("1");
            $("#" + info).val(value);
            $("#filterForm").submit();
            return false;
        });
        $('.clearFilters').click(function() {
            $("#search_page").val("1");
            $("#search_fronter").val("");
            $("#search_closer").val("");
            $("#search_carrier").val("");
            $("#search_policy").val("");
            $("#search_status").val("");
            $("#search_submitToday").val("");
            $("#search_pastDue").val("");
            $("#search_followup").val("");
            $("#search_majorMed").val("");
             $("#search_notPaid").val("");
             $("#search_noPolicyNumber").val("");
              $("#search_submissionErrors").val("");
            $("#filterForm").submit();
            return false;
        });
        $("#filterForm").submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();
            $.ajax({
                url: $(this).attr("action"),
                type: 'GET',
                data: $(this).serialize(),
                success: function(result) {
                    $("#results").empty().append(result);
                    console.log("done");
                }
            });
        });
    });
</script>
<form id="filterForm" action='<?php echo $settings[' base_uri '];?>api/leads/policies' method="post">
    <input type="hidden" name="policies_page" id="search_page" value="<?php echo $settings['policies']['page'];?>">
    <input type="hidden" name="search_fronter" id="search_fronter" value="<?php echo $settings['search_fronter'];?>">
    <input type="hidden" name="search_closer" id="search_closer" value="<?php echo $settings['search_closer'];?>">
    <input type="hidden" name="search_carrier" id="search_carrier" value="<?php echo $settings['search_carrier'];?>">
    <input type="hidden" name="search_policy" id="search_policy" value="<?php echo $settings['search_policy'];?>">
    <input type="hidden" name="search_status" id="search_status" value="<?php echo $settings['search_status'];?>">
    <input type="hidden" name="search_submitToday" id="search_submitToday" value="<?php echo $_REQUEST['search_submitToday'];?>">
    <input type="hidden" name="search_pastDue" id="search_pastDue" value="<?php echo $_REQUEST['search_pastDue'];?>">
    <input type="hidden" name="search_followup" id="search_followup" value="<?php echo $_REQUEST['search_followup'];?>">
    <input type="hidden" name="search_majorMed" id="search_majorMed" value="<?php echo $_REQUEST['search_majorMed'];?>">
    <input type="hidden" name="search_notPaid" id="search_notPaid" value="<?php echo $_REQUEST['search_notPaid'];?>">
    <input type="hidden" name="search_noPolicyNumber" id="search_noPolicyNumber" value="<?php echo $_REQUEST['search_noPolicyNumber'];?>">
    <input type="hidden" name="search_submissionErrors" id="search_submissionErrors" value="<?php echo $_REQUEST['search_submissionErrors'];?>">
     <input type="hidden" name="search_sortFilter" id="search_sortFilter" value="<?php echo $settings['search_sortFilter'];?>">


</form>
<div class="row  animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <div class="row">
                <div class="col-xs-8 ">
                    <?PHP
// if (( (empty($_REQUEST['search_submitToday'])) || ($_REQUEST['search_submitToday'] <> "Y")) && ( (empty($_REQUEST['search_pastDue'])) || ($_REQUEST['search_pastDue'] <> "Y")  ) && ( (empty($_REQUEST['search_majorMed'])) || ($_REQUEST['search_majorMed'] <> "Y")  )   ){
if (( (empty($_REQUEST['search_submitToday'])) || ($_REQUEST['search_submitToday'] <> "Y")) ){
    echo '<button class="btn btn-secondary submitToday btn-sm"  >Submits Today</button> ';
} else {
    echo '<button class="btn btn-warning submitToday btn-sm"  >Submits Today</button> ';
}
if (( (empty($_REQUEST['search_pastDue'])) || ($_REQUEST['search_pastDue'] <> "Y")) ){
    echo ' <button class="btn btn-secondary pastDue btn-sm"  >Past Due</button>';
} else {
    echo ' <button class="btn btn-danger pastDue btn-sm"  >Past Due</button>';
}
if (( (empty($_REQUEST['search_followup'])) || ($_REQUEST['search_followup'] <> "Y")) ){
    echo ' <button class="btn btn-secondary followup btn-sm"  >Followup</button>';
} else {
    echo ' <button class="btn btn-danger followup btn-sm"  >Followup</button>';
}
if (( (empty($_REQUEST['search_majorMed'])) || ($_REQUEST['search_majorMed'] <> "Y")) ){
   echo ' <button class="btn btn-secondary majorMed btn-sm"  >Major Meds</button>';
} else {
   echo ' <button class="btn btn-danger majorMed btn-sm"  >Major Meds</button>';
}
if (( (empty($_REQUEST['search_notPaid'])) || ($_REQUEST['search_notPaid'] <> "Y")) ){
   echo ' <button class="btn btn-secondary notPaid btn-sm"  >Not Paid</button>';
} else {
   echo ' <button class="btn btn-danger notPaid btn-sm"  >Not Paid</button>';
}
if (( (empty($_REQUEST['search_noPolicyNumber'])) || ($_REQUEST['search_noPolicyNumber'] <> "Y")) ){
   echo ' <button class="btn btn-secondary noPolicyNumber btn-sm"  >No Policy #</button>';
} else {
   echo ' <button class="btn btn-danger noPolicyNumber btn-sm"  >No Policy #</button>';
}
if (( (empty($_REQUEST['search_submissionErrors'])) || ($_REQUEST['search_submissionErrors'] <> "Y")) ){
   echo ' <button class="btn btn-secondary submissionErrors btn-sm"  ><i class="fa fa-exclamation"></i></button>';
} else {
   echo ' <button class="btn btn-danger submissionErrors btn-sm"  ><i class="fa fa-exclamation"></i></button>';
}
//  }
                    ?>
                    <?PHP
if ( (!empty($settings['search_fronter'])) || (!empty($settings['search_closer'])) || (!empty($_REQUEST['search_submissionErrors'])) || (!empty($settings['search_carrier'])) || (!empty($_REQUEST['search_submitToday'])) || (!empty($_REQUEST['search_pastDue'])) || (!empty($_REQUEST['search_followup'])) || (!empty($_REQUEST['search_notPaid'])) || (!empty($_REQUEST['search_noPolicyNumber'])) || (!empty($_REQUEST['search_pastDue'])) ||  (!empty($_REQUEST['search_majorMed']))  || (!empty($settings['search_status']))  ){
    echo '<button class="clearFilters btn btn-warning btn-sm btn-bitbucket"><i class="fa fa-remove"></i> Clear Filters</button>';
}
                    ?>
                </div>
                <div class="col-xs-4 pull-right">
                    <form id="searchForm" action='<?php echo $settings[' base_uri '];?>api/leads/policies' method="post">
                        <div class="input-group pull-right">
                            <input type="text" name="policies_search" class="form-control" value="<?php echo $settings['policies']['search'];?>"></input>
                        <span class="input-group-btn"><input type="submit" class="btn btn-primary" value="Search"></span>
                        </div>
                    </form>
            </div>
        </div>
        <?php
echo "<table class='table table-bordered table-striped'>";
echo '<thead><tr>
    <th data-sort="string">Name</th>
    <th data-sort="string">Created</th>
    <th data-sort="string"><a href="#" class="filterLink2" filter-id="search_sortFilter" filter-value="submissionDate">Submission</a></th>
    <th data-sort="string"><a href="#" class="filterLink2" filter-id="search_sortFilter" filter-value="dateToPay">Payment Date</a></th>
    <th data-sort="string">Status</th>
    <th data-sort="string">Policy No</th>
    <th data-sort="string">Carrier</th>
    <th data-sort="string">Plan</th>
    <th data-sort="string">Fronter</th>
    <th data-sort="string">Closer</th>
    <th data-sort="int">Premium</th>
    <th data-sort="int">Confirmed</th>
    </thead>';
if(!empty($result['policies'])){
    foreach($result['policies'] as $key=>$var){
        $person = "";
        $personId = "";
        if(!empty($result['persons'])){
            foreach($result['persons'] as $key2=>$var2){
                if($var['_parentId'] == $var2['_id']){
                    $personId = $var2['_id'];
                    $person =
                        $apiObj->getValues($var2, "firstName") . "  " .
                        $apiObj->getValues($var2, "lastName");
                    break;
                }
            }
        }
        $carrier = "";
        if(!empty($result['carriers'])){
            foreach($result['carriers'] as $key2=>$var2){
                if($var['carrier'] == $var2['_id']){
                    $carrier = $apiObj->getValues($var2, "name");
                    break;
                }
            }
        }
        $carrierPlan = "";
        if(!empty($result['carrierPlans'])){
            foreach($result['carrierPlans'] as $key2=>$var2){
                if($var['coverageType'] == $var2['_id']){
                    $carrierPlan = $apiObj->getValues($var2, "name");
                    break;
                }
            }
        }
        $fronter = "";
        if(!empty($result['users'])){
            foreach($result['users'] as $key2=>$var2){
                if($var['soldBy'] == $var2['_id']){
                  $ActiveUser = "";
                   if(substr($apiObj->getValues($var2, "status"),0,1) == "I"){
                        $ActiveUser = "(Inactive)";
                   }
                    $fronter = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname") . " ".$ActiveUser;
                    break;
                }
            }
        }
        $closer = "";
        if(!empty($result['users'])){
            foreach($result['users'] as $key2=>$var2){
                if($var['closedBy'] == $var2['_id']){
                    $closer = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname") ;
                    break;
                }
            }
        }
        echo "
        <tr><td><a href='#lead/edit/".$personId."'>".$person."</td>
        <td>".date("m/d/Y",strtotime($var['_timestampCreated']))."</a></td>
        <td>".$var['submissionDate']."</td>
        <td>".$var['dateToPay']."</td>
        <td><a href='#' class='filterLink' filter-id='search_status' filter-value='".$var['status']."'>".ucwords(strtolower($var['status']))."</a></td>
        <td>".$var['policyNumber'] ."</td>
        <td><a href='#' class='filterLink' filter-id='search_carrier' filter-value='".$var['carrier']."'>".$carrier."</a></td>
        <td><a href='#' class='filterLink' filter-id='search_policy' filter-value='".$var['coverageType']."'>".$carrierPlan."</a></td>
        <td><a href='#' class='filterLink' filter-id='search_fronter' filter-value='".$var['soldBy']."'>".$fronter."</a></td>
        <td><a href='#' class='filterLink' filter-id='search_closer' filter-value='".$var['closedBy']."'>".$closer."</a></td>
        <td>".$var['premiumMoney'] ."</td> ";
        echo  "<td class='text-center'>";
        if((empty($var['policySubmitted'])) || ($var['policySubmitted'] == "") || ($var['policySubmitted'] == "NOTSUBMITTED")){
            echo "<button class='btn btn-warning btn-sm btn-bitbucket' onclick='adminPolicy(\"".$var['_id']."\");'><i class='fa fa-remove'></i></button>";
        } else {
            if($var['policySubmitted'] == "SUBMIT"){
                echo  "<button class='btn btn-info btn-sm btn-bitbucket' onclick='adminPolicy(\"".$var['_id']."\");'><i class='fa fa-check'></i></button>";
            }
            if($var['policySubmitted'] == "SUBMITPAYMENT"){
                echo  "<button class='btn btn-info btn-xs btn-bitbucket' onclick='adminPolicy(\"".$var['_id']."\");'><i class='fa fa-check'></i></button>";
                echo  "<button class='btn btn-primary btn-xs btn-bitbucket' onclick='adminPolicy(\"".$var['_id']."\");'><i class='fa fa-money'></i></button>";
            }
            if($var['policySubmitted'] == "ERRORS"){
                echo  "<button class='btn btn-danger btn-sm btn-bitbucket' onclick='adminPolicy(\"".$var['_id']."\");'><i class='fa fa-exclamation'></i></button>";
            }
            if($var['policySubmitted'] == "CANCELLED"){
                echo  "<button class='btn btn-danger btn-sm btn-bitbucket' onclick='adminPolicy(\"".$var['_id']."\");'><i class='fa fa-thumbs-o-down'></i></button>";
            }
            if($var['policySubmitted'] == "DECLINED"){
                echo  "<button class='btn btn-danger btn-sm btn-bitbucket'><i class='fa fa-thumbs-down'></i></button>";
            }
        }
        echo " </td>
        </tr>
        ";
    }
} else {
    echo "<tr><td colspan='11'>No policies at this time.</td></tr>";
}
echo "</table>";
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="pagination">Showing
                    <?php echo ($settings['policies']['per_page'] * $settings['policies']['page']) - $settings['policies']['per_page'] + 1;?> to
                    <?php
$totalToShow = $settings['policies']['per_page'] * $settings['policies']['page'];
if($totalToShow > $result['total']){
    $totalToShow = $result['total'];
}
                    ?>
                    <?php echo $totalToShow;?> of
                    <?php echo $result['total'];?> policies
                </div>
            </div>
            <div class="col-sm-6">
                <ul class="pagination pull-right">
                    <?php
if($settings['page'] == 1){
    echo ' <li class="paginate_button previous disabled" aria-controls="DataTables_Table_0" tabindex="0" id="DataTables_Table_0_previous"><a href="#policies/page/1">Previous</a></li> ';
} else {
    echo ' <li class="paginate_button previous " tabindex="0" id="DataTables_Table_0_previous"><a href="#policies/page/'.($settings['policies']['page'] - 1).'">Previous</a></li> ';
}
                    ?>
                    <?php
$i = 1;
if((empty($settings['policies']['per_page'])) || ($settings['policies']['per_page'] < 1)){
    $settings['policies']['per_page'] = 100;
}
for ($x = 1; $x <= $result['total']; $x++) {
    $active = "";
    if($i == $settings['policies']['page']){
        $active = "active";
    }
    if(($i > ($settings['policies']['page'] - 5)) && ($i < ($settings['policies']['page'] + 5))) {
        echo  "<li class='paginate_button ".$active."' tabindex='0'><a href='#' class='filterLink' filter-id='search_page' filter-value='".$i."'>".$i."</a></li>";
    }
    $x = $i * $settings['policies']['per_page'];
    $i++;
}
                    ?>
                    <?php
$i--;
if($settings['policies']['page'] < $i){
    echo '  <li class="paginate_button next" tabindex="0" id="DataTables_Table_0_next"><a href="#policies/page/'. ($settings['policies']['page'] + 1) . '">Next</a></li> ';
} else {
    echo '  <li class="paginate_button next disabled" tabindex="0" id="DataTables_Table_0_next"><a href="#policies/page/'. ($settings['policies']['page']) . '">Next</a></li> ';
}
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal inmodal" id="adminPolicyModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Policy Information</h4>
            </div>
            <div class="modal-body" id="adminPolicyModalInfo">
                Getting Policy Information....
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    function adminPolicy(policy_id) {
        return false;
        $('.modal').hide();
        $("#adminPolicyModalInfo").html("<P>Getting Policy Information.... </P>" + policy_id);
        $('#adminPolicyModal').modal('show');
        $.ajax({
            url: "<?php echo $settings['base_uri'];?>api/leads/admininfo/" + policy_id,
            type: 'POST',
            data: "",
            success: function(result) {
                $("#adminPolicyModalInfo").html(result);
            }
        });
    }
</script>