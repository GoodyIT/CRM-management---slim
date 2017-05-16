<div class=" animated fadeInRight">

    <div class="row wrapper border-bottom white-bg page-heading ng-scope">

        <div class="col-xs-4">

            <h2><?php echo $result['page_label'];?></h2>

            <ol class="breadcrumb">

                <li><a href="#">Home</a></li>

                <li class="active">

                    <strong><?php echo $result['page_label'];?></strong>

                </li>

            </ol>

        </div>

        <div class="col-xs-8">

            <div class="title-action">
                <a href="#lead/create" class="btn btn-primary ">Create a Lead</a>
                <?php if((strtoupper($_SESSION['api']['user']['permissionLevel']) == "ADMINISTRATOR")) : ?>
                    <a href="api/leads/export-clients" target="_blank" class="btn btn-success ">Export</a>
                <?php endif; ?>
                
            </div>

        </div>

    </div>

</div>

<?php

function getValues($var,$arr){

    if(!empty($arr[$var])){

        return $arr[$var];

    }

    return false; 

}

if($result['page_label'] == "Clients"){

    $linkPage = "clients";

    $linkType = "clients";

    $searchURI = "clients";

} else {

    $linkPage = "lead";

    $linkType = "leads";

    $searchURI = "";

}

?>

<script>

    $(document).ready(function() {
        var url      = window.location.href; 
        var segments = url.split( '/' );
        var params = '';
        if(segments.length == 6){
            params = "&" + segments[5];
        }
        // alert(params);
        // Attach a submit handler to the form

        $("#searchForm").submit(function(event) {

            // Stop form from submitting normally

            event.preventDefault();

            $.ajax({

                url: $(this).attr("action"),

                type: 'GET',

                data: $(this).serialize() + params,

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

                <div class="col-sm-5 col-xs-12 pull-right">

                    <form id="searchForm" action='<?php echo $settings[' base_uri '];?>api/leads/<?php echo $searchURI;?>' method="post">

                        <div class="input-group pull-right">

                            <input type="text" name="<?php echo $linkType;?>_search" class="form-control" value="<?php echo $settings[$linkType]['search'];?>"></input>

                        <span class="input-group-btn"><input type="submit" class="btn btn-primary" value="Search"></span>

                        </div>

                    </form>

            </div>

        </div>

        <?php

echo "<table class='table table-bordered table-striped'>";

echo '<thead><tr>

<th >Name</th>

<th >Created</th>

<th >Assigned</th>

<th >Lead Source</th>

<th >Disposition</th>



<th >City, State</th>

<th >Gender</th>



<th >Policies</th>

</th></thead>';

if(!empty($result['leads'])){

    foreach($result['leads'] as $key=>$var){

        $address = "";

        if(!empty($result['addresses'])){

            foreach($result['addresses'] as $key2=>$var2){

                if(($var['_id'] == $var2['_parentId']) && ($var2['_parentThing'] == "person")){

                    $city = getValues("city",$var2);

                    if(!empty(trim($city))){

                        $address = ucwords(strtolower($city)) . ", ";

                    }

                    $state = getValues("state",$var2);

                    if(!empty(trim($state))){

                        $address .= strtoupper($state);

                    }

                    break;

                }

            }

        }

        if(strlen(trim($address)) < 2){

            $address = "";

        }

        $phone = "";

        if(!empty($result['phones'])){

            foreach($result['phones'] as $key2=>$var2){

                if(($var['_id'] == $var2['_parentId']) && ($var2['_parentThing'] == "person")){

                    $phone = getValues("phoneNumber",$var2);

                    break;

                }

            }

        }

        $email = "";

        if(!empty($result['emails'])){

            foreach($result['emails'] as $key2=>$var2){

                if(($var['_id'] == $var2['_parentId']) && ($var2['_parentThing'] == "person")){

                    $email = getValues("email",$var2);

                    break;

                }

            }

        }

        $policies = array();

        if(!empty($result['policies'])){

            foreach($result['policies'] as $key2=>$var2){

                if(($var['_id'] == $var2['_parentId']) && ($var2['_parentThing'] == "person")){

                    $policies[] = getValues("policies",$var2);

                }

            }

        }

        if(empty($var['disposition'])){

            $var['disposition'] = "";   

        }

        if(empty(trim($var['firstName']))){

            $var['firstName'] = "Unknown";   

        }

        if((empty($var['leadSource'])) || ($var['leadSource'] == "")){
            $var['leadSource'] = "INBOUND";
        }

       // echo "<tr><td ><a href='#lead/edit/".$var['_id']."'>".$var['firstName']. " ".$var['lastName']."</td><td>".date("m/d/Y",strtotime($var['_timestampCreated']))."</a></td><td>".$apiObj->getUserName($var['assignedTo'])."</td><td>".$var['leadSource']."</td><td>".$apiObj->checkThingFormVariable("person","disposition",$var['disposition']) ."<td>".$address."</td><td>".strtoupper($var['gender'])."</td><td nowrap>".$phone."</td><td>".$email."</td><td class='text-center'>".count($policies)."</tr>";
        echo "<tr><td ><a href='#lead/edit/".$var['_id']."'>".$var['firstName']. " ".$var['lastName']."</td><td>".date("m/d/Y",strtotime($var['_timestampCreated']))."</a></td><td>".$apiObj->getUserName($var['assignedTo'])."</td><td>".$var['leadSource']."</td><td>".$apiObj->checkThingFormVariable("person","disposition",$var['disposition']) ."<td>".$address."</td><td>".strtoupper($var['gender'])."</td><td class='text-center'>".count($policies)."</tr>";

    }

} else {

    echo "<tr><td colspan='10'>No leads at this time.</td></tr>";

}

echo "</table>";

        ?>

        <div class="row">

            <div class="col-sm-6">

                <div class="pagination">Showing

                    <?php echo ($settings[$linkType]['per_page'] * $settings[$linkType]['page']) - $settings[$linkType]['per_page'] + 1;?> to

                    <?php

$totalToShow = $settings[$linkType]['per_page'] * $settings[$linkType]['page'];

if($totalToShow > $result['total']){

    $totalToShow = $result['total']   ;

}

                    ?>

                    <?php echo $totalToShow;?> of

                    <?php echo $result['total'];?> Leads

                </div>

            </div>

            <div class="col-sm-6">

                <ul class="pagination pull-right">

                    <?php

if($result['page_label'] == "Clients"){

    $linkPage = "clients";

    $linkType = "clients";

} else {

    $linkPage = "lead";

    $linkType = "leads";

}

if($settings['page'] == 1){

    echo ' <li class="paginate_button previous disabled" aria-controls="DataTables_Table_0" tabindex="0" id="DataTables_Table_0_previous"><a href="#'.$linkPage.'/page/1">Previous</a></li> ';

} else {
    if(!empty($_GET["start_date"]) && !empty($_GET["end_date"]))
        echo '  <li class="paginate_button previous" tabindex="0" id="DataTables_Table_0_next"><a href="#'.$linkPage.'/page/'. ($settings[$linkType]['page'] - 1) . '/start_date='.urlencode($_GET["start_date"]).'&end_date='.urlencode($_GET["end_date"]).'">Previous</a></li> ';
    else
       echo ' <li class="paginate_button previous " tabindex="0" id="DataTables_Table_0_previous"><a href="#'.$linkPage.'/page/'.($settings[$linkType]['page'] - 1).'">Previous</a></li> '; 

}

                    ?>

                    <?php

$i = 1;

if((empty($settings[$linkType]['per_page'])) || ($settings[$linkType]['per_page'] < 1)){

    $settings[$linkType]['per_page'] = 100;   

}

for ($x = 1; $x <= $result['total']; $x++) {

    $active = "";

    if($i == $settings[$linkType]['page']){

        $active = "active";

    }

    if(($i > ($settings[$linkType]['page'] - 5)) && ($i < ($settings[$linkType]['page'] + 5))) {
        if(!empty($_GET["start_date"]) && !empty($_GET["end_date"]))
            echo  '<li class="paginate_button '.$active.'" tabindex="0"><a href="#'.$linkPage.'/page/'.$i.'/start_date='.urlencode($_GET["start_date"]).'&end_date='.urlencode($_GET["end_date"]).'">'.$i.'</a></li>';
        else 
            echo  '<li class="paginate_button '.$active.'" tabindex="0"><a href="#'.$linkPage.'/page/'.$i.'">'.$i.'</a></li>';

    }

    $x = $i * $settings[$linkType]['per_page'];

    $i++;

} 

                    ?>

                    <?php

$i--;

if($settings[$linkType]['page'] < $i){
    if(!empty($_GET["start_date"]) && !empty($_GET["end_date"]))
        echo '  <li class="paginate_button next" tabindex="0" id="DataTables_Table_0_next"><a href="#'.$linkPage.'/page/'. ($settings[$linkType]['page'] + 1) . '/start_date='.urlencode($_GET["start_date"]).'&end_date='.urlencode($_GET["end_date"]).'">Next</a></li> ';
    else
        echo '  <li class="paginate_button next" tabindex="0" id="DataTables_Table_0_next"><a href="#'.$linkPage.'/page/'. ($settings[$linkType]['page'] + 1) . '">Next</a></li> ';
} else {

    echo '  <li class="paginate_button next disabled" tabindex="0" id="DataTables_Table_0_next"><a href="#'.$linkPage.'/page/'. ($settings[$linkType]['page']) . '">Next</a></li> ';

}

                    ?>

                </ul>

            </div>

        </div>

    </div>

</div>

</div>

<script>

    $(document).ready(function() {

        $(".table").tablesorter();

    });

    </script>