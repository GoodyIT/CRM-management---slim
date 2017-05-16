<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading ng-scope">
        <div class="col-xs-10">
            <h2>Lead View</h2>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><a href="#lead">Leads</a></li>
                <li class="active">
                    <strong>View</strong>
                </li>
            </ol>
        </div>
        <div class="col-xs-2">
            <div class="title-action">
                <a href="#lead/create" class="btn btn-primary ">Create a Lead</a>
            </div>
        </div>

    </div>
</div>

<div class="row animated fadeInRight">
    <div>
        <div class="row">
            <div class="col-lg-12 col-xs-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div>
                            <h4>Personal Information</h4>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <P>
                            <label>Name:</label>
                            <?php echo $apiObj->getValue( $result['leads'][0], "firstName"); ?>
                                <?php echo $apiObj->getValue( $result['leads'][0], "lastName"); ?>
                        </P>
                        <P>
                            <label>Disposition:</label>
                            <?php echo $apiObj->getValue( $result['leads'][0], "disposition"); ?>
                        </P>
                        
                        <P>
                            <label>Assigned To:</label>
                            <?php 
foreach($result['users'] as $key=>$val){
    if($val['_id'] == $apiObj->getValue($result['leads'][0],'assignedTo') ){
        echo $val['firstname'] . " " . $val['lastname'];
        break;
    }
}

                            ?>
                        </P>

                        <P>
                            <label>Address:</label>
                            <?php 
foreach($result['addresses'] as $key=>$val){
    echo ucwords(strtolower($apiObj->getValue($val,'city'))). ", ". $apiObj->getValue($val,'state'). " ".  $apiObj->getValue($val,'zipCode');
    break;
}

                            ?>
                        </P>

                        <P>
                            <label>Phone(s):</label>
                            <?php 
foreach($result['phones'] as $key=>$val){
    echo $apiObj->getValue($val,'phoneNumber') . ", ";
}

                            ?>
                        </P>

                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th>Number</th>
                                    <th>Status</th>
                                    <th>Submit Date</th>
                                    <th>Effective Date</th>
                                    <th>Carrier</th>
                                    <th>Coverage Type</th>
                                    <th>Fronter</th>
                                    <th>Closer</th>
                                </tr>
                                <?php 
if(!empty($result['policy'])){
    foreach($result['policy'] as $key=>$val){
        $carrier = findViewCarriers($result['carriers'],$apiObj->getValue($val,'carrier'));
        $coverage = findViewCarriers($result['carrierPlans'],$apiObj->getValue($val,'coverageType'));
        $fronter =  findViewUser($result['users'],$apiObj->getValue($val,'soldBy'));
        $closer =  findViewUser($result['users'],$apiObj->getValue($val,'closedBy'));
        echo "<tr>";
        echo "<td>".$apiObj->getValue($val,'policyNumber')."</td>";
        echo "<td>".$apiObj->getValue($val,'status')."</td>";
        echo "<td>".$apiObj->getValue($val,'submissionDate')."</td>";
        echo "<td>".$apiObj->getValue($val,'effectiveDate')."</td>";
        echo " <td>" .$carrier."</td>";
        echo " <td>". $coverage."</td>";
        echo " <td>" .$fronter."</td>";
        echo " <td>". $closer."</td>";
        
        echo "</tr>";



    }
}

                                ?>

                            </tbody>
                        </table>

                        <?php

function findViewCarriers($carriers,$id){

    foreach($carriers as $key=>$var){
        if($var['_id'] == $id){
            return $var['name'];   
        }
    }
    return false;
}


function findViewUser($users,$id){

    foreach($users as $key=>$val){
        if($val['_id'] == $id ){
            return $val['firstname'] . " " . $val['lastname'];
        }
    }
    return false;
}

//debug($result['carriers']);
//debug($result['carrierPlans']);
//$result['leads']
//$result['phones'];
//$result['emails'];
//$result['addresses'];
//$result['policy'];
//$result['history'];
//$result['carriers'];
//$result['carrierPlans'];
//$result['users'];
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>