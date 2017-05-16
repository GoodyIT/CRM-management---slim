<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
require '../app.php';
$app->config(array(
    'templates.path' => './',
));
$app->map('/done', function () use ($app,$settings) {
    echo "All Done!";
})->via('GET','POST');



$app->map('/agentList/:state', function ($state) use ($app,$settings) {
    echo "<h3>Finding Agents licensed in state: ".$state."</h3>";

    if(($state == "") || ($state == "NONE")){
        echo "Please set the state in the customer's contact address<br><p>";
    }

    $assurant_numbers[0]['name'] = "David Jackson";
    $assurant_numbers[0]['number'] = '000C423X093001';
    $assurant_numbers[0]['states'] = array('AR','CO','CT','DC','DE','KY','MD','ME','MN','NH','NM','OR','SD','UT','VA');
    $assurant_numbers[0]['rate'] = '1';
    $assurant_numbers[1]['name'] = "Sean McClosky";
    $assurant_numbers[1]['number'] = 'AA027560000704';
    $assurant_numbers[1]['states'] = array('CA','AL','AZ','FL','GA','IA','ID','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','NV','OH','OK','PA','SC','TN','TX','WI','WY');
    $assurant_numbers[1]['rate'] = '18';
    $assurant_numbers[2]['name'] = "Shirlee Fequiere";
    $assurant_numbers[2]['number'] = 'AA051665000301';
    $assurant_numbers[2]['states'] = array('CA','AL','AZ','FL','GA','IA','ID','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WI','WY');
    $assurant_numbers[2]['rate'] = '18';
    $assurant_numbers[3]['name'] = "Matthew Bosah";
    $assurant_numbers[3]['number'] = 'AA025144000402';
    $assurant_numbers[3]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WY');
    $assurant_numbers[3]['rate'] = '99';
    $assurant_numbers[4]['name'] = "Yolanda Chaney";
    $assurant_numbers[4]['number'] = 'AA065607000401';
    $assurant_numbers[4]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WI','WY');
    $assurant_numbers[4]['rate'] = '99';
    $assurant_numbers[5]['name'] = "Keith Johnson";
    $assurant_numbers[5]['number'] = 'AA063994000101';
    $assurant_numbers[5]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','GA','IA','ID','MI','MS','MT','NC','ND','NE','NH','NV','OR','OH','OK','PA','SC','TN','TX','WI','WY');
    $assurant_numbers[5]['rate'] = '99';


    $assurant_numbers[6]['name'] = "Zachary Kabir";
    $assurant_numbers[6]['number'] = 'AA066272000301';
    $assurant_numbers[6]['states'] = array('GA', 'LA', 'MI', 'MO', 'OH', 'OK', 'SC', 'TN');
    $assurant_numbers[6]['rate'] = '99';



    $assurant_numbers[7]['name'] = "Kassiadoll Harris";
    $assurant_numbers[7]['number'] = 'AA066275000601';
    $assurant_numbers[7]['states'] = array('CA', 'GA', 'LA', 'MI', 'MS', 'OH', 'OK', 'SC', 'TN');
    $assurant_numbers[7]['rate'] = '99';



    $assurant_numbers[8]['name'] = "Patricia Ponce";
    $assurant_numbers[8]['number'] = 'AA066279000001';
    $assurant_numbers[8]['states'] = array('CA', 'GA', 'LA', 'MI', 'MO', 'OH', 'OK', 'SC', 'TN');
    $assurant_numbers[8]['rate'] = '99';



    $assurant_numbers[9]['name'] = "Gia Gonzalez";
    $assurant_numbers[9]['number'] = 'AA066281000301';
    $assurant_numbers[9]['states'] = array('GA', 'LA', 'MO', 'OH', 'SC', 'TN', 'TX');
    $assurant_numbers[9]['rate'] = '99';


    $assurant_numbers[10]['name'] = "Annette Gaul";
    $assurant_numbers[10]['number'] = 'AA066284000601';
    $assurant_numbers[10]['states'] = array('CA', 'GA', 'LA', 'MI', 'MO', 'OH', 'OK', 'SC', 'TN');
    $assurant_numbers[10]['rate'] = '99';


    $assurant_numbers[11]['name'] = "Darnell Smith";
    $assurant_numbers[11]['number'] = 'AA066285000701';
    $assurant_numbers[11]['states'] = array('CA', 'GA', 'IL', 'LA', 'MD', 'MI', 'MO', 'MS', 'NC', 'NV', 'OH', 'OK', 'SC', 'TN');
    $assurant_numbers[11]['rate'] = '99';


    $assurant_numbers[12]['name'] = "Katt Luna";
    $assurant_numbers[12]['number'] = 'AA066452000301';
    $assurant_numbers[12]['states'] = array('CA');
    $assurant_numbers[12]['rate'] = '99';

    $assurant_numbers[13]['name'] = "Sarah Beltran";
    $assurant_numbers[13]['number'] = '000';
    $assurant_numbers[13]['states'] = array('CA');
    $assurant_numbers[13]['rate'] = '99';

    ?>
    <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th >Name</th>
                                        <th >Licensed</th>
                                        <th >Link</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php



    foreach($assurant_numbers as $key=>$var){
           echo "<tr><td>".$var['name']."</td>";
           if (!in_array($state,$var['states'])) {
                echo "<td>Currently not available</td><td></td></tr>";
            } else {
                echo "<td>Licensed</td>";
                echo '<td><a href="https://www.groupihq.com/NGIC/WebFormCollectDemographics.aspx?destination=c&writingagentnumber='.$var['number'].'" target="_blank" class="btn btn-primary btn-xs">Go To Assurant</a></td>';
                echo "</tr>";

            }

    }
    ?>

                                        </tbody>
                                </table>
                            </div>
    <?php


})->via('GET','POST');


$app->map('/', function () use ($app,$settings) {
?>
<form id="assurantForm" method="post">
    <?php
    foreach($_REQUEST as $key=>$var){
        echo "<input type='hidden' name='".$key."'  value='".$var."'>";
    }
    ?>
	<div class="col-xs-12">
	<div class="col-xs-12">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group ">
                    <label class="control-label" for="customer">
                        Who's Covered
                    </label>
                    <select name="applicantsCovered" id="AMECovered" class="form-control">
                        <option selected="selected" value="1">Primary</option>
                        <option value="2">Spouse</option>
                        <option value="3">Primary & Spouse Both</option>
                        <option value="4">Entire Family</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label class="control-label" for="customer">
                        Effective Month
                    </label>
                    <select name="effectiveMonth" id="AMECovered" class="form-control">
                        <?php
    for($i = 1; $i< 13; $i++){
        $selected = "";
        $j = 0;
        if (date("j")> 27){
            $j = 1;
        }
        if($i == (date("n")+$j) ){
            $selected = "selected";
        }
        echo '<option  value="'.$i.'" '.$selected.'>'.$i.'</option>';
    }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label class="control-label" for="customer">Effective Day</label>
                    <select name="effectiveDay" id="AMECovered" class="form-control">
                        <?php
    for($i = 1; $i< 28; $i++){
        $selected = "";
        if($i == (date("j") + 2) ){
            $selected = "selected";
        }
        echo '<option  value="'.$i.'" '.$selected.'>'.$i.'</option>';
    }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Effective Year
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="effectiveYear" id="AMECovered" class="form-control">
                            <?php
    for($i = date("Y"); $i< (date("Y") + 5); $i++){
        echo '<option  value="'.$i.'">'.$i.'</option>';
    }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Dental Plans
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="dentalPlan" id="dentalPlan" class="form-control">
                            <option selected="selected" value="No">No</option>
                            <option value="1">Basic Plan</option>
                            <option value="2">Intermediate Plan</option>
                            <option value="3">Plus Plan</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Accidental Plans
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="accidentalPlan" id="accidentalPlan" class="form-control">
                            <option selected="selected" value="No">No</option>
                            <option value="L1-OFF">Level 1 - Off the Job Accident</option>
                            <option value="L1-24HR">Level 1 - 24 Hour Accident</option>
                            <option value="L2-OFF">Level 2 - Off the Job Accident</option>
                            <option value="L2-24HR">Level 2 - 24 Hour Accident</option>
                        </select>
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Accident Medical Expense (AME) Amount
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="AMEPlan" id="AMEPlan" class="form-control">
                            <option value="2000">$2,000</option>
                            <option value="2500">$2,500</option>
                            <option value="3000">$3,000</option>
                            <option value="3500">$3,500</option>
                            <option value="4000">$4,000</option>
                            <option value="4500">$4,500</option>
                            <option value="5000">$5,000</option>
                            <option value="5500">$5,500</option>
                            <option value="6000">$6,000</option>
                            <option value="6500">$6,500</option>
                            <option value="6600">$6,600</option>
                            <option value="6850">$6,850</option>
                            <option value="7500">$7,500</option>
                            <option value="8000">$8,000</option>
                            <option value="9000">$9,000</option>
                            <option value="10000">$10,000</option>
                            <option selected="selected" value="No">No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Cancer and Heart/Stroke Plans
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="cancerPlan" id="cancerPlan" class="form-control">
                            <option value="CHSP">Cancer and Heart/Stroke</option>
                            <option value="CNCR">Cancer Only</option>
                            <option value="HTSK">Heart/Stroke Only</option>
                            <option selected="selected" value="No">No</option>
                        </select>
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Coverage Amount
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="cancerCoverageAmount" id="cancerCoverageAmount" class="form-control">
                            <option value="20000">$20,000</option>
                            <option selected="selected" value="25000">$25,000</option>
                            <option value="30000">$30,000</option>
                            <option value="35000">$35,000</option>
                            <option value="40000">$40,000</option>
                            <option value="45000">$45,000</option>
                            <option value="50000">$50,000</option>
                            <option value="55000">$55,000</option>
                            <option value="60000">$60,000</option>
                            <option value="65000">$65,000</option>
                            <option value="70000">$70,000</option>
                            <option value="75000">$75,000</option>
                        </select>
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 text-right" style="display:none">
                <div class="form-group ">
                    <label>
                        Monthly Premium
                    </label>
                    <div class="input-group col-xs-12">
                        <h3 id="cancerAmount">$0.00</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Critical Illness Plans Max Benefit
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="criticalLevel" id="criticalLevel" class="form-control">
                            <option value="20000">20000</option>
                            <option value="25000">25000</option>
                            <option value="30000">30000</option>
                            <option value="35000">35000</option>
                            <option value="40000">40000</option>
                            <option value="45000">45000</option>
                            <option value="50000">50000</option>
                            <option value="55000">55000</option>
                            <option value="60000">60000</option>
                            <option value="65000">65000</option>
                            <option value="66000">66000</option>
                            <option value="68500">68500</option>
                            <option value="75000">75000</option>
                            <option value="80000">80000</option>
                            <option value="90000">90000</option>
                            <option value="100000">100000</option>
                            <option selected="selected" value="NO">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Term Life Benefit
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="criticalBenefit" id="criticalBenefit" class="form-control">
                            <option selected="selected" value="10000">$10,000</option>
                            <option value="20000">$20,000</option>
                            <option value="30000">$30,000</option>
                            <option value="37500">$37,500</option>
                            <option value="40000">$40,000</option>
                            <option value="45000">$45,000</option>
                            <option value="50000">$50,000</option>
                            <option value="52500">$52,500</option>
                            <option value="60000">$60,000</option>
                            <option value="67500">$67,500</option>
                            <option value="70000">$70,000</option>
                            <option value="75000">$75,000</option>
                            <option value="80000">$80,000</option>
                            <option value="82500">$82,500</option>
                            <option value="90000">$90,000</option>
                            <option value="97500">$97,500</option>
                            <option value="100000">$100,000</option>
                            <option value="105000">$105,000</option>
                            <option value="110000">$110,000</option>
                            <option value="112500">$112,500</option>
                            <option value="120000">$120,000</option>
                            <option value="127500">$127,500</option>
                            <option value="130000">$130,000</option>
                            <option value="135000">$135,000</option>
                            <option value="140000">$140,000</option>
                            <option value="140000">$142,500</option>
                            <option value="150000">$150,000</option>
                            <option value="160000">$160,000</option>
                            <option value="170000">$170,000</option>
                            <option value="180000">$180,000</option>
                            <option value="190000">$190,000</option>
                            <option value="200000">$200,000</option>
                        </select>
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label>
                        Policy Term
                    </label>
                    <div class="input-group col-xs-12">
                        <select name="criticalTerm" id="criticalBenefit" class="form-control">
                            <option selected="selected" value="10">10</option>
                            <option value="20">20</option>
                        </select>
                        <p></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 text-right" style="display:none">
                <div class="form-group ">
                    <label>
                        Monthly Premium
                    </label>
                    <div class="input-group col-xs-12">
                        <h3 id="cancerAmount">$0.00</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 text-center">
            <button class="btn btn-primary" onClick="submitAssurantForm('quote')">GET QUOTE</button>
        </div>
    </div>
	</div>
	</div>
</form>
<div class="col-xs-12">
<div class="col-xs-12">
<div id="assurantResults">
</div>
</div>
</div>
<script>
    $("#assurantForm").submit(function(event) {
        // Stop form from submitting normally
        event.preventDefault();
    });

    function submitAssurantForm(type) {
        var formData = $("#assurantForm").serialize();
        if (type == "create") {
            $("#assurantResults").html("Creating Quote....");
            $("#assurantSubmitList").html(" <h3>Finalizing Quote For Submission....one moment<h3>");
        } else {
            $("#assurantResults").html("Getting Results...");
        }
        $.ajax({
            url: "<?php echo $settings['base_uri'];?>api/assurant/assurantpost?submissionType=" + type,
            type: 'POST',
            data: formData,
            success: function(result) {
                if (type == "create") {
                    $("#assurantSubmitList").html(result);
                } else {
                    $("#assurantResults").html(result);
                }
            }
        });
    }
</script>
<?php
})->via('GET','POST');
$app->map('/assurantpost', function () use ($app,$settings) {
    $_REQUEST['test']  = -1;
    try{
        //$options = array('soap_version'=>SOAP_1_1,'exceptions'=>true,'trace'=>1,'cache_wsdl'=>WSDL_CACHE_NONE);
        if((!empty($_REQUEST['test'])) && ($_REQUEST['test'] == 1)){
            $location = 'https://train2.imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx';
        }else{
            $location = 'https://imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx';
        }
        $options = array(
            'exceptions' => 1,
            'trace' => 1,
            "connection_timeout"=> 2000,
            'location'       => $location, // Mandatory
        );
        $client = new SoapClient($location.'?wsdl', $options);
        //$client = new SoapClient('https://imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx?wsdl',$options);
        // Note where 'Get' and 'request' tags are in the XML
        $Credentials = array("UserId"=>"IMQuotingTest","Password"=>"Test1234");
        date_default_timezone_set("America/Chicago");




        $assurant_numbers[0]['name'] = "David Jackson";
        $assurant_numbers[0]['number'] = '000C423X093001';
        $assurant_numbers[0]['states'] = array('AR','CO','CT','DC','DE','KY','MD','ME','MN','NH','NM','OR','SD','UT','VA');
        $assurant_numbers[0]['rate'] = '1';
        $assurant_numbers[1]['name'] = "Sean McClosky";
        $assurant_numbers[1]['number'] = 'AA027560000704';
        $assurant_numbers[1]['states'] = array('CA','AL','AZ','FL','GA','IA','ID','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','NV','OH','OK','PA','SC','TN','TX','WI','WY');
        $assurant_numbers[1]['rate'] = '18';
        $assurant_numbers[2]['name'] = "Shirlee Fequiere";
        $assurant_numbers[2]['number'] = 'AA051665000301';
        $assurant_numbers[2]['states'] = array('CA','AL','AZ','FL','GA','IA','ID','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WI','WY');
        $assurant_numbers[2]['rate'] = '18';


        $apiObj = new apiclass($settings);
        $apiObj->mongoSetDB($settings['database']);
        $apiObj->mongoSetCollection("userGroups");
        //$collectionQuery['users.userId']['$eq'] = $_SESSION['api']['user']['_id'];
        $cursor = $apiObj->mongoFind();
        if(!empty($cursor)){
            if($cursor->count() == 0){
            } else {
                foreach (iterator_to_array($cursor) as $doc) {
                    if(!empty($doc['users'])){
                        foreach($doc['users'] as $key=>$userInfo){
                            if(($userInfo['userId']==$_SESSION['api']['user']['_id']) && (($userInfo['level'] == "USER") || ($userInfo['level'] == "MANAGER"))){
                               // echo $doc['label'] . " ". $doc['_id'];
                               // debug($userInfo);

                                if($doc['_id'] == "20160104070823-8FpfElbX-AEw5KZe1"){
                                    if(empty($assurant_numbers[3])){
                                        $assurant_numbers[3]['name'] = "Matthew Bosah";
                                        $assurant_numbers[3]['number'] = 'AA025144000402';
                                        $assurant_numbers[3]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WY');
                                        $assurant_numbers[3]['rate'] = '99';
                                    }
                                }

                                if($doc['_id'] == "20160104071352-rpKg4EIH-Y40P4YGa"){
                                    if(empty($assurant_numbers[4])){
                                        $assurant_numbers[4]['name'] = "Yolanda Chaney";
                                        $assurant_numbers[4]['number'] = 'AA065607000401';
                                        $assurant_numbers[4]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WI','WY');
                                        $assurant_numbers[4]['rate'] = '99';
                                    }

                                }

                                if($doc['_id'] == "20160104065426-lMCGymGM-EA2DTBKe"){
                                    if(empty($assurant_numbers[5])){
                                        $assurant_numbers[5]['name'] = "Keith Johnson";
                                        $assurant_numbers[5]['number'] = 'AA063994000101';
                                        $assurant_numbers[5]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','GA','IA','ID','MI','MS','MT','NC','ND','NE','NH','NV','OR','OH','OK','PA','SC','TN','TX','WI','WY');
                                        $assurant_numbers[5]['rate'] = '99';
                                    }
                                }




                            }
                        }
                    }
                }
            }
        }


        // if(empty($_REQUEST['person_0_policy_0_effectiveDate'])){
        /*
        $today = date("d") + 3;
        $month = date("m");
        $year = date("Y");
        if($today > 25){
            $month = $month + 1;
            $today = 3;
            if($month == 1){
                $year = $year + 1;
            }
        }
        */
        // $_REQUEST['effDate'] =$year . "-". $month. "-" . $today;
        //  } else {
        //     $_REQUEST['effDate'] = date("Y-m-d", strtotime($_REQUEST['person_0_policy_0_effectiveDate']));
        //   }
        if(empty($_REQUEST['person_0_gender'])){
            echo "<h3>Error</h3>Gender not Provided";
            //  echo "<P>Please provide in format <strong>&person_1_gender=Female</strong>";
            exit();
        }
        if(empty($_REQUEST['person_0_dateOfBirth'])){
            echo "<h3>Error</h3>Date Of Birth not Provided";
            exit();
        }
        if(empty($_REQUEST['person_0_smokerTabacco'])){
            echo "<h3>Error</h3>Tobacco Use not Provided";
            exit();
        }
        if(empty($_REQUEST['person_0_firstName'])){
            echo "<h3>Error</h3>First Name not Provided";
            exit();
        }
        if(empty($_REQUEST['person_0_initial'])){
            $_REQUEST['person_0_initial'] = "";
        }
        if(empty($_REQUEST['person_0_id'])){
            $_REQUEST['person_0_id'] = "";
        }
        if(empty($_REQUEST['person_0_lastName'])){
            echo "<h3>Error</h3>Last Name not Provided";
            exit();
        }
        if(empty($_REQUEST['person_0_addresses_0_zipCode'])){
            echo "<h3>Error</h3>Zip Code not Provided";
            exit();
        }
        if(empty($_REQUEST['writingAgentNumber'])){
            if((!empty($_REQUEST['test'])) && ($_REQUEST['test'] == 1)){
                $_REQUEST['writingAgentNumber'] = "00013311000001";
            } else {
                try {





                    $assurant_list['assurant_writing_numbers'] = array();


                    foreach ($assurant_numbers as $ask=>$asv){
                        if(in_array($_REQUEST['person_0_addresses_0_state'], $asv['states'] )){
                            for ($x = 1; $x <=  $asv['rate'] ; $x++) {
                                $assurant_list['assurant_writing_numbers'] [] = $asv['number'];
                            }
                            shuffle($assurant_list['assurant_writing_numbers']);
                            mt_srand(date("YmdHis"));
                            $writingnumber = $assurant_list['assurant_writing_numbers'][mt_rand(0, count($assurant_list['assurant_writing_numbers']) - 1)];
                            $_REQUEST['writingAgentNumber'] = $writingnumber;
                        }
                    }
                    //echo $_REQUEST['person_0_addresses_0_state'];
                    //debug($assurant_list['assurant_writing_numbers']);
                    //exit();
                } catch (Exception $e) {
                    // echo 'Caught exception: ',  $e->getMessage(), "\n";
                    shuffle($settings['assurant_writing_numbers']);
                    mt_srand(date("YmdHis"));
                    $writingnumber = $settings['assurant_writing_numbers'][mt_rand(0, count($settings['assurant_writing_numbers']) - 1)];
                    $_REQUEST['writingAgentNumber'] = $writingnumber;
                }
            }
        }
        if(empty($_REQUEST['person_0_suffix'])){
            $_REQUEST['person_0_suffix'] = "";
        }
        if($_REQUEST['person_0_gender'] == "M"){
            $_REQUEST['person_0_gender'] = "Male";
        } else {
            $_REQUEST['person_0_gender'] = "Female";
        }
        // debug($_REQUEST);
        // echo date("Y-m-d", strtotime($_REQUEST['person_0_dateOfBirth'])) ;
        //  exit();
        if($_REQUEST['applicantsCovered'] != 2){
            $Applicants[] = array(
                "Gender" => $_REQUEST['person_0_gender'],
                "DOB" => date("Y-m-d", strtotime($_REQUEST['person_0_dateOfBirth'])) ,
                "Smoker" =>  $_REQUEST['person_0_smokerTabacco'],
                "Relationship" => "Primary",
                "FirstName" => $_REQUEST['person_0_firstName'],
                "Initial" => substr($_REQUEST['person_0_middleName'],0,1),
                "LastName" => $_REQUEST['person_0_lastName'],
                "Suffix" => $_REQUEST['person_0_suffix'],
                "ExternalApplicantID" => $_REQUEST['person_0_id']
            );
        }
        if ($_REQUEST['applicantsCovered'] > 1){
            if(!empty($_REQUEST['person_0_spouse_0_spouseFirstName'])){
                if($_REQUEST['person_0_spouse_0_spouseGender'] == "M"){
                    $_REQUEST['person_0_spouse_0_spouseGender'] = "Male";
                } else {
                    $_REQUEST['person_0_spouse_0_spouseGender'] = "Female";
                }
                $Applicants[] = array(
                    "Gender" => $_REQUEST['person_0_spouse_0_spouseGender'],
                    "DOB" => date("Y-m-d", strtotime($_REQUEST['person_0_spouse_0_spouseDateOfBirth'])) ,
                    "Smoker" => $_REQUEST['person_0_spouse_0_spouseSmoker'],
                    "Relationship" => "Spouse",
                    "FirstName" => $_REQUEST['person_0_spouse_0_spouseFirstName'],
                    "Initial" => substr($_REQUEST['person_0_spouse_0_spouseMiddleName'],0,1),
                    "LastName" => $_REQUEST['person_0_spouse_0_spouseLastName'],
                    "Suffix" => "",
                    "ExternalApplicantID" => $_REQUEST['person_0_spouse_0_id']
                );
            }
        }
        if ($_REQUEST['applicantsCovered'] == 4){
            for ($i = 0; $i < 10; $i++){
                if(!empty($_REQUEST['person_0_dependents_'.$i.'_gender'])){
                    if($_REQUEST['person_0_dependents_'.$i.'_gender'] == "M"){
                        $_REQUEST['person_0_dependents_'.$i.'_gender'] = "Male";
                    } else {
                        $_REQUEST['person_0_dependents_'.$i.'_gender'] = "Female";
                    }
                    if(empty($_REQUEST['person_0_dependents_'.$i.'_smoker'])){
                        $_REQUEST['person_0_dependents_'.$i.'_smoker'] = "N";
                    }
                    $Applicants[] = array(
                        "Gender" => $_REQUEST['person_0_dependents_'.$i.'_gender'],
                        "DOB" => date("Y-m-d", strtotime($_REQUEST['person_0_dependents_'.$i.'_dependentsDateOfBirth'])) ,
                        "Smoker" => $_REQUEST['person_0_dependents_'.$i.'_smoker'],
                        "Relationship" => "Dependent",
                        "FirstName" => $_REQUEST['person_0_dependents_'.$i.'_dependentsFirstName'],
                        "Initial" => substr($_REQUEST['person_0_dependents_'.$i.'_dependentsMiddleName'],0,1),
                        "LastName" => $_REQUEST['person_0_dependents_'.$i.'_dependentsLastName'],
                        "Suffix" => "",
                        "ExternalApplicantID" => $_REQUEST['person_0_dependents_'.$i.'_id']
                    );
                }
            }
        }
        if(strlen($_REQUEST['effectiveMonth']) == 1){
            $_REQUEST['effectiveMonth'] = "0".$_REQUEST['effectiveMonth'];
        }
        if(strlen($_REQUEST['effectiveDay']) == 1){
            $_REQUEST['effectiveDay'] = "0".$_REQUEST['effectiveDay'];
        }
        $Demographics=array(
            'ZipCode' => $_REQUEST['person_0_addresses_0_zipCode'],
            'EffectiveDate' =>   $_REQUEST['effectiveYear'].'-'.$_REQUEST['effectiveMonth'].'-'.$_REQUEST['effectiveDay'],
            'Applicants' => $Applicants,
            'Email' => $_REQUEST['person_0_emails_0_email'],
            'Address1' => $_REQUEST['person_0_addresses_0_street1'],
            'Address2' => $_REQUEST['person_0_addresses_0_street2'],
            'Phone' => "",
            'City' => $_REQUEST['person_0_addresses_0_city'],
            'State' =>  $_REQUEST['person_0_addresses_0_state'],
            'County' =>  "",
            'IsQualifyingLifeEvent' => TRUE,
        );
        $PlanFilter = array(
            'WritingAgentNumber' => $_REQUEST['writingAgentNumber'],
            'OfficeCopayBuyUp' => FALSE,
            'TeladocDiscount' => FALSE,
            'InitialRateGuaranteeBuyUp' => FALSE,
            'FacilityFeeBuyUp' => FALSE,
            'RightStartOptionsBuyup' => FALSE,
            'PrescriptionDrugBenefitBuyup' => FALSE,
            'AccidentalDeathBenefitBuyUp' => FALSE,
            'WaiverOfPremiumBuyUp' => FALSE,
            'DefaultPlansOnly' => FALSE
        );
        $RatedASCPlan[] = array(
            'Plan' => "Accident Medical Expense",
            'Payment' => "Monthly",
            'FormNumber' => "",
            'IsDefault' => FALSE,
            'Level' => 2000,
            'LevelDescription' => "",
            'BasePolicy' => "",
            'MaxBenefit' => 0,
            'TermLifeBenefit' => 0,
            'PolicyTerm' => 0,
            'PrimaryPremium' => 10,
            'SpousePremium' => 0,
            'ChildPremium' => 0,
            'MonthlyPremium' => 10,
            'Riders' => array(),
        );
        $partnerInfo = array(
            "WritingAgentNumber" => $_REQUEST['writingAgentNumber'],
            "SolicitingAgentNumber" => false,
            "SalesLinkID" => "",
            "ClientCaseID" => "",
            "ConfirmationURL" => "http://".$settings['domain']."/api/assurant/done",
            "Destination" => "E",
            "ApplicantId" => $_REQUEST['person_0_id'],
            "ThirdPartyPayor" => FALSE,
        );
        $params = array(
            'Credentials' => $Credentials,
            'ExternalReferenceID' => $_REQUEST['person_0_id'],
            'Demographics'=> $Demographics,
            'PlanFilter' => $PlanFilter
        );
        if($_REQUEST['submissionType'] == "create"){
            if(empty($_SESSION['assurant']['ratedASCPlans'])){
                echo "Could not submit at this time";
                exit();
            }
            $RatedASCPlan = $_SESSION['assurant']['ratedASCPlans'];
            $params2 = array(
                'Credentials' => $Credentials,
                'Demographics'=> $Demographics,
                'RatedSupplementalCoverages' => $RatedASCPlan,
                'PartnerInformation'=> $partnerInfo
            );
            $results = $client->ApplyNow($params2);
            if(!empty($results->EaseWebRedirectURL)){
                $result_link = str_replace("&","&amp;",$results->EaseWebRedirectURL);
                echo '<div class="row"><div class="col-sm-12 text-center"><a href="'.$result_link.'" class="btn btn-warning" target="_blank">GO TO ASSURANT TO FINISH SUBMISSION!</a></div></div>';
                $_SESSION['assurant'][$_REQUEST['person_0_id']] = array();
                exit();
            } else {
                echo "Could not submit at this time.<P>";
                echo($client->__getLastResponse());
                exit();
            }
        } else {
?>
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>Your Quote</h5>
    </div>
    <div class="ibox-content">
        <?php
            echo "<table class='table table-bordered table-responsive'  >";
            echo "<thead><tr><th>Plan</th><th>Benefits</th><th>Monthly Premium</th></tr></thead><tbody>";
            // debug($params);
            // if(empty($_SESSION['assurant'][$_REQUEST['person_0_id']])){
            $quotes = $client->GetASCPlans($params);
            //    } else {
            //        $quotes = $_SESSION['assurant'][$_REQUEST['person_0_id']];
            //    }
            //
            //  debug($quotes);
            $total_monthly_payment = 0;
            unset($_SESSION['assurant']['ratedASCPlans']);
            $_SESSION['assurant']['ratedASCPlans'] = array();
            if(!empty($quotes->ASCPlanBundles)){
                $_SESSION['assurant'][$_REQUEST['person_0_id']] = $quotes;
                foreach ($quotes->ASCPlanBundles as $key=>$bundle){
                    if(is_array($bundle)){
                        //debug($bundle);
                        /*
                    [Plan] => Accident
                    [Payment] => Monthly
                    [IsDefault] =>
                    [Level] => 1
                    [LevelDescription] => Level 1
                    [BasePolicy] => Off the Job Accident
                    [MaxBenefit] => 0
                    [TermLifeBenefit] => 0
                    [PolicyTerm] => 0
                    [PrimaryPremium] => 15.6
                    [SpousePremium] => 0
                    [ChildPremium] => 0
                    [MonthlyPremium] => 15.6
                        */
                        foreach($bundle as $key2=>$plans){
                            if($plans->Plan == "Accident"){
                                foreach($plans->RatedPlans->RatedASCPlan as $key2=>$plans){
                                    if($_REQUEST['accidentalPlan'] == "L1-OFF"){
                                        if(($plans->Level == 1) && (trim($plans->BasePolicy) == "Off the Job Accident")){
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->LevelDescription . " <br>". $plans->BasePolicy . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                    }
                                    if($_REQUEST['accidentalPlan'] == "L1-24HR"){
                                        if(($plans->Level == 1) && (trim($plans->BasePolicy) == "24 Hour Accident")){
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->LevelDescription . "<br>". $plans->BasePolicy . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                    }
                                    if($_REQUEST['accidentalPlan'] == "L2-OFF"){
                                        if(($plans->Level == 2) && (trim($plans->BasePolicy) == "Off the Job Accident")){
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->LevelDescription . "<br>". $plans->BasePolicy . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                    }
                                    if($_REQUEST['accidentalPlan'] == "L2-24HR"){
                                        if(($plans->Level == 2) && (trim($plans->BasePolicy) == "24 Hour Accident")){
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong>".$plans->LevelDescription . "<br> ". $plans->BasePolicy . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                    }
                                }
                            }
                            if($plans->Plan == "Accident Medical Expense"){
                                foreach($plans->RatedPlans->RatedASCPlan as $key2=>$plans){
                                    if($_REQUEST['AMEPlan'] == $plans->Level){
                                        echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->Level . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                        $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                        $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                    }
                                }
                            }
                            if($plans->Plan == "Cancer and Heart/Stroke"){
                                foreach($plans->RatedPlans->RatedASCPlan as $key2=>$plans){
                                    if($_REQUEST['cancerCoverageAmount'] == $plans->Level){
                                        if(($_REQUEST['cancerPlan'] == "CHSP") && (trim($plans->BasePolicy) == "Cancer and Heart/Stroke")){
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->Level . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                        if(($_REQUEST['cancerPlan'] == "CNCR") && (trim($plans->BasePolicy) == "Cancer Only")){
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->Level . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                        if(($_REQUEST['cancerPlan'] == "HTSK") && (trim($plans->BasePolicy) == "Heart/Stroke Only")){
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->Level . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                    }
                                }
                            }
                            if($plans->Plan == "Critical Illness"){
                                if($_REQUEST['criticalLevel'] <> "NO"){
                                    $crticalNote =  "<strong>Could Not Find a Critical Illness</strong><br>Term Life Benefits Available for that Selectoion: ";
                                    $criticalMatched = false;
                                }
                                foreach($plans->RatedPlans->RatedASCPlan as $key2=>$plans){
                                    if($_REQUEST['criticalLevel'] <> "NO"){
                                        if($_REQUEST['criticalLevel'] == $plans->MaxBenefit ){
                                            $crticalNote .= "<br>For Max Benefit of ".$plans->MaxBenefit . " Term Life Benefit ".$plans->TermLifeBenefit." and Policy Term: ". $plans->PolicyTerm;
                                        }
                                        if (($_REQUEST['criticalLevel'] == $plans->MaxBenefit ) && ($_REQUEST['criticalBenefit'] == $plans->TermLifeBenefit ) && ($_REQUEST['criticalTerm'] == $plans->PolicyTerm )){
                                            $criticalMatched = true;
                                            echo "<tr><td>".$plans->Plan. " </td><td><strong>Max Benefit:</strong> ".$plans->MaxBenefit . "  <br><strong>Term Life Benefit:</strong> ".$plans->TermLifeBenefit . " <br><strong>Policy Term:</strong> ".$plans->PolicyTerm . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                            $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                            $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                        }
                                    }
                                }
                                if($_REQUEST['criticalLevel'] <> "NO"){
                                    if($criticalMatched === false){
                                        echo "<P>".$crticalNote."</P>";
                                    }
                                }
                            }
                            if($plans->Plan == "Dental"){
                                foreach($plans->RatedPlans->RatedASCPlan as $key2=>$plans){
                                    if($_REQUEST['dentalPlan'] == $plans->Level){
                                        echo "<tr><td>".$plans->Plan. " </td><td><strong>Level:</strong> ".$plans->LevelDescription . "</td><td class='text-right'> $".  number_format($plans->MonthlyPremium,2,'.',',') . "</td></tr>";
                                        $total_monthly_payment = $total_monthly_payment + $plans->MonthlyPremium;
                                        $_SESSION['assurant']['ratedASCPlans'][] = (array) $plans;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
            }
            if(!empty($quotes->ErrorMessages)){
                echo "<tr><td colspan='3' class='text-left'>";
                echo "<ul>";
                foreach($quotes->ErrorMessages as $eK=>$eV){
                    if(is_array($eV)){
                        foreach($eV as $eK2=>$eV2){
                            echo   "<li>".$eV2;
                        }
                    } else {
                        echo   "<li>".$eV;
                    }
                }
                echo "</ul>";
                echo "</td></tr>";
            }
            echo "<tr><td colspan='3' class='text-right'><h3>Total $".  number_format($total_monthly_payment,2,'.',',') ."</h3></td>";
            echo "</tbody></table>";
            echo "</div>";
            echo "</div>";
            if($total_monthly_payment > 1){
        ?>
        <div class="row">
            <div class="col-sm-12 text-center" id="submitAssurantFormDiv">
                <button class="btn btn-warning" onClick="submitAssurantForm('create')">Submit Policies To Assurant</button>
            </div>
        </div>
        <?php
            }
        }
        // debug($quotes);
    } catch (Exception $e) {
        echo "<h4>error occurred</h4>";
        debug($client->__getLastResponse(), "Error Response!");
    }
    /*
    try {
        $_REQUEST['test'] =1;
        if((!empty($_REQUEST['test'])) && ($_REQUEST['test'] == 1)){
            $location = 'https://train2.imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx';
        }else{
            $location = 'https://imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx';
            $location = 'https://train2.imquoting.eassuranthealth.com/Service/2013/10/IMQuoteEngine.asmx';
        }
        $options = array(
            'exceptions' => 1,
            'trace' => 1,
            "connection_timeout"=> 2000,
            'location'       => $location, // Mandatory
        );
        debug($_REQUEST);
        $client = new SoapClient($location.'?wsdl', $options);
        $Credentials = array('UserId' => 'IMQuotingTest' , 'Password' => 'Test1234' );
        date_default_timezone_set("America/Chicago");
        $eff_date = '2015-12-04-00:00';
        $date = '1955-01-08-00:00';
        $date2 = '1981-07-10-00:00';
        $Applicants[] = array(
            "Gender" => "Female",
            "DOB" => $date,
            "Smoker" => False,
            "Relationship" => "Primary",
            "FirstName" => "Jaci",
            "Initial" => "",
            "LastName" => "Decker",
            "Suffix" => "",
            "ExternalApplicantID" => "20151023152932-OMEl1LCu-sicteNNH"
        );
        $Demographics=array(
            'ZipCode' => '59743' ,
            'EffectiveDate' => $eff_date ,
            'Applicants' => $Applicants,
            'Email' => "jacidecker@test.com",
            'Address1' => "",
            'Address2' => "",
            'Phone' => "",
            'City' => "",
            'State' => "",
            'County' => "",
            'IsQualifyingLifeEvent' => TRUE,
        );
        $PlanFilter = array(
            'WritingAgentNumber' => '00013311000001',
            'OfficeCopayBuyUp' => FALSE,
            'TeladocDiscount' => FALSE,
            'InitialRateGuaranteeBuyUp' => FALSE,
            'FacilityFeeBuyUp' => FALSE,
            'RightStartOptionsBuyup' => FALSE,
            'PrescriptionDrugBenefitBuyup' => FALSE,
            'AccidentalDeathBenefitBuyUp' => FALSE,
            'WaiverOfPremiumBuyUp' => FALSE,
            'DefaultPlansOnly' => FALSE
        );
        $selectedPlan = array(
            'PackageID' => '',
            'Accident' => "",
            'CriticalIllness'=> "",
            'AscAME'=> "",
            'CHSCoverageType'=> "",
            'CHSCoverageLevel'=> "",
            'Dental'=> "",
            'SuiteSolutionsPlan'=> "",
            'SuiteSolutionsAmount'=> "",
            'Riders'=> "",
            'TotalPremium' => 0
        );
        $RatedASCPlan[] = array(
            'Plan' => "Accident Medical Expense",
            'Payment' => "Monthly",
            'FormNumber' => "",
            'IsDefault' => FALSE,
            'Level' => 2000,
            'LevelDescription' => "",
            'BasePolicy' => "",
            'MaxBenefit' => 0,
            'TermLifeBenefit' => 0,
            'PolicyTerm' => 0,
            'PrimaryPremium' => 10,
            'SpousePremium' => 0,
            'ChildPremium' => 0,
            'MonthlyPremium' => 10,
            'Riders' => array(),
        );
        $partnerInfo = array(
            "WritingAgentNumber" => "00013311000001",
            "SolicitingAgentNumber" => false,
            "SalesLinkID" => "",
            "ClientCaseID" => "",
            "ConfirmationURL" => "http://104.131.135.180/quote_engine/assurant_confirm.php",
            "Destination" => "E",
            "ApplicantId" => "66554433221",
            "ThirdPartyPayor" => FALSE,
        );
        $processQuote = 1;
        if($processQuote == 1) {
            $params = array(
                'Credentials' => $Credentials,
                'ExternalReferenceID' => $_REQUEST['person_0_id'],
                'Demographics'=> $Demographics,
                'PlanFilter' => $PlanFilter
            );
           // echo "<PRE>";
        //    echo "<h2>Request</h2>";
            debug($params);
            $results = $client->GetPlans($params);
            $results = $client->GetASCPlans($params);
            debug($results);
        } else {
            $params2 = array(
                'Credentials' => $Credentials,
                'Demographics'=> $Demographics,
                'RatedSupplementalCoverages' => $RatedASCPlan,
                'PartnerInformation'=> $partnerInfo
            );
           // echo "<PRE>";
        //    echo "<h2>Request</h2>";
            debug($params2);
          //  $results = $client->ApplyNow($params2);
        }
          exit();
        echo "<h2>Response</h2>";
        echo "<PRE>";
        debug($results);
          exit();
        if(!empty($results['EaseWebRedirectURL'])){
            echo $results['EaseWebRedirectURL'];
        }
        echo "</PRE>";
    } catch (Exception $e) {
        echo($client->__getLastResponse());
        echo PHP_EOL;
        echo($client->__getLastRequest());
    }
    */
})->via('GET','POST');


$app->map('/assurrantlink/:state', function ($state) use ($app,$settings) {

    try {


        $assurant_numbers[0]['name'] = "David Jackson";
        $assurant_numbers[0]['number'] = '000C423X093001';
        $assurant_numbers[0]['states'] = array('AR','CO','CT','DC','DE','KY','MD','ME','MN','NH','NM','OR','SD','UT','VA');
        $assurant_numbers[0]['rate'] = '1';
        $assurant_numbers[1]['name'] = "Sean McClosky";
        $assurant_numbers[1]['number'] = 'AA027560000704';
        $assurant_numbers[1]['states'] = array('CA','AL','AZ','FL','GA','IA','ID','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','NV','OH','OK','PA','SC','TN','TX','WI','WY');
        $assurant_numbers[1]['rate'] = '18';
        $assurant_numbers[2]['name'] = "Shirlee Fequiere";
        $assurant_numbers[2]['number'] = 'AA051665000301';
        $assurant_numbers[2]['states'] = array('CA','AL','AZ','FL','GA','IA','ID','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WI','WY');
        $assurant_numbers[2]['rate'] = '18';


        $apiObj = new apiclass($settings);
        $apiObj->mongoSetDB($settings['database']);
        $apiObj->mongoSetCollection("userGroups");
        //$collectionQuery['users.userId']['$eq'] = $_SESSION['api']['user']['_id'];
        $cursor = $apiObj->mongoFind();
        if(!empty($cursor)){
            if($cursor->count() == 0){
            } else {
                foreach (iterator_to_array($cursor) as $doc) {
                    if(!empty($doc['users'])){
                        foreach($doc['users'] as $key=>$userInfo){
                            if(($userInfo['userId']==$_SESSION['api']['user']['_id']) && (($userInfo['level'] == "USER") || ($userInfo['level'] == "MANAGER"))){
                               // echo $doc['label'] . " ". $doc['_id'];
                                //debug($userInfo);

                                if($doc['_id'] == "20160104070823-8FpfElbX-AEw5KZe1"){
                                    if(empty($assurant_numbers[3])){
                                        $assurant_numbers[3]['name'] = "Matthew Bosah";
                                        $assurant_numbers[3]['number'] = 'AA025144000402';
                                        $assurant_numbers[3]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WY');
                                        $assurant_numbers[3]['rate'] = '99';
                                    }
                                }

                                if($doc['_id'] == "20160104071352-rpKg4EIH-Y40P4YGa"){
                                    if(empty($assurant_numbers[4])){
                                        $assurant_numbers[4]['name'] = "Yolanda Chaney";
                                        $assurant_numbers[4]['number'] = 'AA065607000401';
                                        $assurant_numbers[4]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','MI','MO','MS','MT','NC','ND','NE','OH','OK','PA','SC','TN','TX','WI','WY');
                                        $assurant_numbers[4]['rate'] = '99';
                                    }

                                }

                                if($doc['_id'] == "20160104065426-lMCGymGM-EA2DTBKe"){
                                    if(empty($assurant_numbers[5])){
                                        $assurant_numbers[5]['name'] = "Keith Johnson";
                                        $assurant_numbers[5]['number'] = 'AA063994000101';
                                        $assurant_numbers[5]['states'] = array('CA','AL','AZ','IL','IN','KS','LA','GA','IA','ID','MI','MS','MT','NC','ND','NE','NH','NV','OR','OH','OK','PA','SC','TN','TX','WI','WY');
                                        $assurant_numbers[5]['rate'] = '99';
                                    }
                                }




                            }
                        }
                    }
                }
            }
        }
        $assurant_list['assurant_writing_numbers'] = array();
        $writingnumber = false;
        foreach ($assurant_numbers as $ask=>$asv){
            if(in_array($state, $asv['states'] )){
                for ($x = 1; $x <=  $asv['rate'] ; $x++) {
                    $assurant_list['assurant_writing_numbers'] [] = $asv['number'];
                }
                shuffle($assurant_list['assurant_writing_numbers']);
                mt_srand(date("YmdHis"));
                $writingnumber = $assurant_list['assurant_writing_numbers'][mt_rand(0, count($assurant_list['assurant_writing_numbers']) - 1)];
            }
        }
        if(!$writingnumber){
            echo "We are not licensed in that state.";
        }    else {
            echo '<a href="https://www.groupihq.com/NGIC/WebFormCollectDemographics.aspx?destination=c&writingagentnumber='.$writingnumber.'" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>';
        }

    } catch (Exception $e) {
        echo "No Direct Link";
    }

})->via('GET','POST');
$app->run();