<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading ng-scope">
        <div class="col-lg-4">
            <h2>Your Policies</h2>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li class="active">
                    <strong>Policies</strong>
                </li>
            </ol>
        </div>
        <div class="col-sm-8">
            <div class="title-action">
                <a href="#lead/create" class="btn btn-primary ">Create a Lead</a>
            </div>
        </div>
    </div>
</div>
<script>
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
        
        $("#carrierSearch").submit(function(event) {
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
<div class="row  animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <div class="row">
               
                <div class="col-sm-5 col-xs-12 ">
                    <form id="carrierSearch" action='<?php echo $settings[' base_uri '];?>api/leads/policies' method="post">
                        <div class="input-group pull-right">
                            <?php 
                                if(empty($_REQUEST['carrer_search'])){
                                    $_REQUEST['carrer_search'] == "";
                                }
                            ?>             
                            <select name="carrier_search" class="form-control">
                                echo "<option value='' >All</option>";
                                <?php
                                    foreach($result['carriers'] as $carK=>$carV){
                                        $carselected = "";
                                        if($carV['_id'] == $_REQUEST['carrier_search']){
                                         $carselected = "SELECTED";   
                                        }
                                        echo "<option value='".$carV['_id']."' ".$carselected.">".$carV['name']."</option>";
                                    }

                                ?>
                                
                            </select>
                        <span class="input-group-btn"><input type="submit" class="btn btn-primary" value="Search"></span>
                        </div>
                    </form>
            </div>
                <div class="col-sm-5 col-xs-12 pull-right">
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
    <th data-sort="string">Submission</th>
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
                    $fronter = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname");
                    break;
                }
            }
        }
        $closer = "";
        if(!empty($result['users'])){
            foreach($result['users'] as $key2=>$var2){
                if($var['closedBy'] == $var2['_id']){
                    $closer = $apiObj->getValues($var2, "firstname") . " " .$apiObj->getValues($var2, "lastname");
                    break;
                }
            }
        }
        echo "
        <tr><td><a href='#lead/edit/".$personId."'>".$person."</td>
        <td>".date("m/d/Y",strtotime($var['_timestampCreated']))."</a></td>
        <td>".$var['submissionDate']."</td>
        <td>".ucwords(strtolower($var['status']))."</td>
         <td>".$var['policyNumber'] ."</td>
        <td>".$carrier."</td>
        <td>".$carrierPlan."</td>
         <td>".$fronter."</td>
        <td>".$closer."</td>
         <td>".$var['premiumMoney'] ."</td> ";
        echo  "<td class='text-center'>";
        if((empty($var['policySubmitted'])) || ($var['policySubmitted'] == "") || ($var['policySubmitted'] == "NOTSUBMITTED")){
            echo "<button class='btn btn-warning btn-sm btn-bitbucket'><i class='fa fa-remove'></i></button>";
        } else {
           if($var['policySubmitted'] == "SUBMIT"){
            echo  "<button class='btn btn-info btn-sm btn-bitbucket'><i class='fa fa-check'></i></button>";
           } 
            
             if($var['policySubmitted'] == "ERRORS"){
            echo  "<button class='btn btn-danger btn-sm btn-bitbucket'><i class='fa fa-exclamation'></i></button>";
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
        echo  '<li class="paginate_button '.$active.'" tabindex="0"><a href="#policies/page/'.$i.'">'.$i.'</a></li>';
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