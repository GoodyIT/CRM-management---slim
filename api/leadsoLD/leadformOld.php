<script>
    var totalForms = [];

    function setFormCount(type, count) {
        if (totalForms[type] == undefined) {
            totalForms[type] = 0;
        }
        totalForms[type] = count;
    }
</script>
<?php
function createPartial($type, $label,  $createThing, $prefix, $result, $apiObj , $multi = TRUE){
?>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <div class="row">
                <div class="col-xs-11 text-left">
                    <h4><?php echo $label;?></h4>
                </div>
                <div class="col-xs-1  text-center">
                    <?php if($multi === TRUE){ ?>
                        <a id="newItemButton" class="btn btn-info btn-sm btn-bitbucket" onClick="addItem('<?php echo $type;?>', '<?php echo $createThing;?>')"><i class="fa fa-plus"></i></a>
                        <?php } ?>
                </div>
            </div>
        </div>
        <div id="<?php echo $type;?>List">
            <?php
                if(empty($result[$type])){ $result[$type][0] = array(); }
                $resultData = $result[$type];
                if($createThing <> "Y"){
                    // Nested Documents of leads
                    if(!empty($result['leads'][0][$type])){
                        $resultData = $result['leads'][0][$type];
                    }
                }
                if($type == "notes"){
                    krsort($resultData);
                }
        ?>
                <?php if($type == "notes"){ ?>
                    <div class="ibox-content" id="<?php echo $type;?>Item_<?php echo $index;?>">
                        <div class="row">
                            <div class=" col-sm-12 col-md-12">
                                <div class=" col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label>
                                            Create a Note for this Client
                                        </label>
                                        <input type="hidden" name="person_0_notes_0_createThing" value="Y">
                                        <textarea name='person_0_notes_0_information' id="person_0_notes_0_information" class="form-control" style="min-height: 200px"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                        <?php foreach ($resultData as $index=>$data){
        ?>
                            <div class="ibox-content" id="<?php echo $type;?>Item_<?php echo $index;?>">
                                <div class="row">
                                    <div class="col-xs-11">
                                        <?php if($type == "phones"){ ?>
                                            <div class="row">
                                                <div class="col-sm-11">
                                                    <?php $apiObj->displayThingForm($type, $resultData, $index, $prefix, $createThing);  ?>
                                                </div>
                                                <div class="col-sm-1" style="margin-top:22px">
                                                    <?php if( (!empty($resultData[$index]['_parentId'])) && (!empty($resultData[$index]['phoneNumber']))){ ?>
                                                        <a href="#" id="newSms" data-toggle="modal" data-target="#modal" selectedNumber="<?php echo  preg_replace(" /[^0-9]/ ", " ", $resultData[$index]['phoneNumber']); ?>" _parentId="<?php echo $resultData[$index]['_parentId'];?>" class="newSms btn btn-primary">Sms</a>
                                                        <?php } ?>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                                <?php $apiObj->displayThingForm($type, $resultData, $index, $prefix, $createThing);  ?>
                                                    <?php } ?>
                                    </div>
                                    <div class="col-xs-1 text-center">
                                        <div class="form-group">
                                            <?php if($multi === TRUE){ ?>
                                                <a onClick="removeItem('<?php echo $type;?>', <?php echo $index;?>, '<?php echo $data['_id'];?>', '<?php echo $createThing;?>')" style="margin-top:22px" class="btn btn-warning btn-sm btn-bitbucket"><i class="fa fa-times"></i></a>
                                                <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
        </div>
        <script>
            setFormCount("<?php echo $type;?>", <?php echo ($index + 1);?>);
        </script>
    </div>
    <?php
                                                                                               }
?>
        <form id="leadform" name="saveTemplateData" method="post" action="<?php echo $settings['base_uri'];?>api/leads/updateLead" class="form-horizontal ">
            <div class=" animated fadeInRight">
                <div class="row wrapper border-bottom white-bg page-heading ng-scope">
                    <div class="col-lg-9">
                        <h2>Create a Lead</h2>
                        <ol class="breadcrumb">
                            <li><a href="#">Home</a></li>
                            <li><a href="#lead">Leads</a></li>
                            <li class="active">
                                <strong>Create</strong>
                            </li>
                        </ol>
                    </div>
                    <div class="col-lg-1">
                        <div class="title-action">
                            <button id="saveButton" class="btn btn-warning" type="submit">Save Lead</button>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="title-action">
                            <?php if (!empty($apiObj->getValue( $result['leads'][0], "firstName"))){ ?>
                                <a href="#" id="newAppointment" data-toggle="modal" data-target="#modal" _parentid="<?php echo $result['leads'][0]['id'];?>" class="btn btn-primary">Add New Appointment</a>
                                <?php } ?>
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
                                    <?php $apiObj->displayThingForm("person", $result['leads'], 0); ?>
                                </div>
                            </div>
                            <div ng-controller="TabsDemoCtrl">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#contact" aria-controls="phone" role="tab" data-toggle="tab">Contact</a></li>
                                    <li role="presentation"><a href="#employment" aria-controls="employment" role="tab" data-toggle="tab">Income</a></li>
                                    <li role="presentation"><a href="#family" aria-controls="family" role="tab" data-toggle="tab">Family</a></li>
                                    <li role="presentation"><a href="#policies" aria-controls="policies" role="tab" data-toggle="tab">Policies</a></li>
                                    <li role="presentation"><a href="#banking" aria-controls="banking" role="tab" data-toggle="tab">Payment</a></li>
                                    <li role="presentation"><a href="#quotes" aria-controls="quotes" role="tab" data-toggle="tab">Quotes</a></li>
                                    <li role="presentation"><a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">Notes</a></li>
                                    <li role="presentation"><a href="#emails" aria-controls="emails" role="tab" data-toggle="tab">Emails</a></li>
                                    <li role="presentation" style="display:none"><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab">Admin</a></li>
                                    <li role="presentation"><a href="#history" aria-controls="history" role="tab" data-toggle="tab">History</a></li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <!-- Phone Tabs -->
                                    <div role="tabpanel" class="tab-pane active" id="contact">
                                        <!-- Phone Module -->
                                        <?php createPartial("phones", "Phones", "Y", "person_0_", $result, $apiObj); ?>
                                            <!-- Emails Module -->
                                            <?php createPartial("emails", "Emails", "Y",  "person_0_", $result, $apiObj); ?>
                                                <!-- Address Module -->
                                                <?php createPartial("addresses", "Addresses", "Y",  "person_0_", $result, $apiObj); ?>
                                    </div>
                                    <!-- Employment Tab -->
                                    <div role="tabpanel" class="tab-pane" id="employment">
                                        <!-- Taxes and Income -->
                                        <?php createPartial("taxes", "Income & Taxes",  "N", "person_0_", $result, $apiObj); ?>
                                            <!-- Sources of Income -->
                                            <?php createPartial("incomeSources", "Sources Of Income",  "N", "person_0_", $result, $apiObj); ?>
                                                <!-- Employers -->
                                                <?php createPartial("employers", "Employers", "N",  "person_0_",  $result, $apiObj); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="family">
                                        <!-- Spouse -->
                                        <?php createPartial("spouse", "Spouse", "N", "person_0_",  $result, $apiObj, FALSE); ?>
                                            <!-- Dependents -->
                                            <?php createPartial("dependents", "Dependents",  "N", "person_0_", $result, $apiObj); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="policies">
                                        <!-- Policies -->
                                        <?php createPartial("policy", "Policies",  "Y", "person_0_", $result, $apiObj); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="quotes">
                                        <!-- Quotes -->
                                        <div class="ibox float-e-margins">
                                            <div class="ibox-title">
                                                <div class="row">
                                                    <div class="col-xs-11 text-left">
                                                        <h4>Quotes</h4>
                                                    </div>
                                                    <div class="col-xs-1  text-center">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="quotesList">
                                                <div class="ibox-content" id="notesItem_">
                                                    <div class="row">
                                                        <table class="table table-hover">
                                                            <tbody>
                                                                <tr style="display: none">
                                                                    <td class="project-title">
                                                                        <a href="#admin/agencies">Major Medical (Broker Office)</a>
                                                                        <br>
                                                                        <small>Get Major Medical Quotes</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a class="btn btn-primary btn-sm" onClick="quoteMajor();"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>

                                                                <tr style="display: none">
                                                                    <td class="project-title">
                                                                        <a href="https://www.healthcare.gov/" target="_blank">HealthCare.Gov</a>
                                                                        <br>
                                                                        <small>Submit Major Medicals, make sure you use National Writing Number!</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a href="https://www.healthcare.gov/" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Submit Policies </a>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="project-title">
                                                                        <a href="https://www.healthsherpa.com/" target="_blank">Health Sherpa</a>
                                                                        <br>
                                                                        <small>Get Major Medical Quotes</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a href="https://www.healthsherpa.com/" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>

                                                               <tr>
                                                                    <td class="project-title">
																	<?php
                                                                   // $assurantNumbers[] = "AA051665000301";
                                                                    $assurantNumbers[] = "AA027560000704";
                                                                    $assurantNumbers[] = "AA027560000704";
                                                                    //$assurantNumbers[] = "AA025144000402";
                                                                    //$assurantNumbers[] = "AA025144000402";
																	shuffle($assurantNumbers);
																	?>
                                                                        <a href="https://www.groupihq.com/NGIC/WebFormCollectDemographics.aspx?destination=c&writingagentnumber=<?php echo $assurantNumbers[0]; ?>" target="_blank">Assurant Direct Link</a>
                                                                        <br>
                                                                        <small>Go straight to Assurant for quote</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <!-- <a href="https://www.healthsherpa.com/insurance_plans?zip_code=33604#c12057/ppl35,35,15/cspremium/hhs3" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>-->
                                                                        <a href="https://www.groupihq.com/NGIC/WebFormCollectDemographics.aspx?destination=c&writingagentnumber=<?php echo $assurantNumbers[0]; ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="project-title">
                                                                        <a href="#admin/agencies">Accidental Medical Expense - AME </a>
                                                                        <br>
                                                                        <small>Get Accidental & Medical Expense Quotes</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a class="btn btn-primary btn-sm" onClick="quoteData('AME');"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="project-title">
                                                                        <a href="#admin/agencies">Accidental </a>
                                                                        <br>
                                                                        <small>On the Job Accident Quotes</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a class="btn btn-primary btn-sm" onClick="quoteData('Accidental');"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="project-title">
                                                                        <a href="#admin/agencies">Cancer / Heart & Stroke</a>
                                                                        <br>
                                                                        <small>Get Cancer and/or Heart and Stroke Coverage</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a class="btn btn-primary btn-sm" onClick="quoteData('CancerHeart');"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="project-title">
                                                                        <a href="#admin/agencies">Critical Illness</a>
                                                                        <br>
                                                                        <small>Get Critical Illness Quotes</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a class="btn btn-primary btn-sm" onClick="quoteData('CriticalIllness');"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="project-title">
                                                                        <a href="#admin/agencies">Dental & Vision</a>
                                                                        <br>
                                                                        <small>Get Dental and Vision Quotes</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a class="btn btn-primary btn-sm" onClick="quoteData('Dental');"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="project-title">
                                                                        <a href="#admin/agencies">Misc Insurance</a>
                                                                        <br>
                                                                        <small>Get Misc Insurance Quotes</small>
                                                                    </td>
                                                                    <td class="project-actions">
                                                                        <a data-toggle="modal" data-target="#quoteMiscInsurance" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                    </td>
                                                                </tr>
                                                                <?php
try {
    include ("./insurehc.php");
} catch (Exception $e) {
}
                                                        ?>
                                                                    <tr>
                                                                        <td class="project-title">
                                                                            <a href="https://www.brokeroffice.com/" target="_blank">Broker Office - Direct Link (Do Not Use)</a>
                                                                            <br>
                                                                            <small>Go To Broker Office for MAJOR MEDICAL / CORE Quotes and Submissions</small>
                                                                        </td>
                                                                        <td class="project-actions">
                                                                            <a href="https://www.brokeroffice.com/" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
                                                                        </td>
                                                                    </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="notes">
                                        <!-- Notes -->
                                        <?php createPartial("notes", "Notes",  "Y", "person_0_", $result, $apiObj, FALSE); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="banking">
                                        <!-- Banking -->
                                        <?php createPartial("banking", "Banking Information",  "N",  "person_0_", $result, $apiObj); ?>
                                            <!-- Credit Card -->
                                            <?php createPartial("creditcard", "Credit Card Information", "N",  "person_0_", $result, $apiObj); ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="emails">


                                   <pRE><h3>Check To See if 24HourMail has it here: </h3>
                                     <?php

                                                            if(!empty($result['emails'][0])){
                                        echo "<p><a href='http://24hourmail.net/lookup.php?email=".trim($result['emails'][0]['email'])."' target='_blank'>".$result['emails'][0]['email']."</a></P>";
                                                            }

                                                     if(!empty($result['emails'][1])){
                                        echo "<P><a href='http://24hourmail.net/lookup.php?email=".trim($result['emails'][1]['email'])."' target='_blank'>".$result['emails'][1]['email']."</a></P>";
                                                     }

                                    ?>
                                        </pRE>
                                    <div id="foundEmails">


                                    </div>


                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="history">
                                        <div class="ibox float-e-margins">
                                            <div id="recordingDiv" class="ibox float-e-margins">
                                                <?php
try {
    include ("./recordings.php");
} catch (Exception $e) {
}
                                        ?>
                                            </div>
                                            <div class="ibox float-e-margins">
                                                <div class="ibox-title">
                                                    <div class="row">
                                                        <div class="col-xs-11 text-left">
                                                            <h4>History</h4>
                                                        </div>
                                                        <div class="col-xs-1  text-center">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ibox-content" id="<?php echo $type;?>Item_<?php echo $index;?>">
                                                    <div class="row">
                                                        <?php
if(!empty($result['history'])){
    echo "<ul>";
    foreach($result['history'] as $hKey=>$hVal){
        echo "<li><strong>User:</strong> ".$hVal['userName'] . " - <strong>Action:</strong> ".$hVal['note'] . " - <strong>Date:</strong> ". date("m/d/Y H:i:s",strtotime($hVal['_timestampCreated']));
    }
    echo "</ul>";
}
                                                ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding-top:30px; padding-bottom: 30px;">
                                    <div class="row">
                                        <div class="col-xs-10">
                                            <button id="saveButton" class="btn btn-primary" type="submit">Save Lead</button>
                                        </div>
                                        <div class="col-xs-2 text-right">
                                            <?php if (!empty($apiObj->getValue( $result['leads'][0], "firstName"))){ ?>
                                                <a deleteId="<?php echo $result['leads'][0]['id'];?>" class="btn btn-warning leadDelete">Delete</a>
                                                <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal inmodal" id="quoteModalMajor" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-medkit modal-icon"></i>
                        <h4 class="modal-title">Major Medical Quotes</h4>
                    </div>
                    <div class="modal-body" id="quoteModalMajorList">
                        Getting Quote....
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal inmodal" id="quoteCancerHeart" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-heartbeat modal-icon"></i>
                        <h4 class="modal-title">Cancer & Heart</h4>
                    </div>
                    <div class="modal-body" id="quoteCancerHeartList">
                        Getting Quote....
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal inmodal" id="quoteCriticalIllness" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-stethoscope modal-icon"></i>
                        <h4 class="modal-title">Critical Illness</h4>
                    </div>
                    <div class="modal-body" id="quoteCriticalIllnessList">
                        Getting Quote....
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal inmodal" id="quoteMiscInsurance" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-plus-square modal-icon"></i>
                        <h4 class="modal-title">Misc Insurance</h4>
                    </div>
                    <div class="modal-body" id="quoteMiscInsuranceList">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Carrier</th>
                                    <th>Coverage</th>
                                    <th class="project-actions">Start Up Costs</th>
                                    <th class="project-actions">Monthly Premium</th>
                                    <th class="project-actions">More Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="project-title">
                                        InsureHC
                                    </td>
                                    <td class="project-title">
                                        Customer Care
                                    </td>
                                    <td class="project-actions">
                                        <strong>$15.00</strong>
                                    </td>
                                    <td class="project-actions">
                                        <strong>$9.99</strong>
                                    </td>
                                    <td class="project-actions">
                                        Details
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal inmodal" id="quoteAccidental" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-ambulance modal-icon"></i>
                        <h4 class="modal-title">Accidental</h4>
                    </div>
                    <div class="modal-body" id="quoteAccidentalList">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal inmodal" id="quoteAME" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-ambulance modal-icon"></i>
                        <h4 class="modal-title">Accidental Medical Expense </h4>
                    </div>
                    <div class="modal-body" id="quoteAMEList">
                        Getting Quote
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal inmodal" id="quoteDental" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <i class="fa fa-eye modal-icon"></i>
                        <h4 class="modal-title">Dental and Vision</h4>
                    </div>
                    <div class="modal-body" id="quoteDentalList">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(function() {
                $(".datepicker").pickadate({
                    format: 'mm/dd/yyyy',
                    min: '01/01/1920',
                    max: '01/01/2020',
                    selectYears: 100,
                    selectMonths: true,
                });
            });
        </script>
        <script>
            var changedData = 0;

            function addItem(type, crtThng) {
                changedData = 1;
                if (totalForms[type] == undefined) {
                    totalForms[type] = 0;
                }
                $.ajax({
                    url: "<?php echo $settings['base_uri'];?>api/leads/template/" + type + "/" + totalForms[type] + "/" + crtThng,
                    success: function(result) {
                        var resultInfo = result;
                        $("#" + type + "List").append(resultInfo);
                        totalForms[type] = totalForms[type] + 1;
                    }
                });
            }

            function removeItem(type, indx, id, createThing) {
                $("#" + type + "Item_" + indx).remove();
                $("#" + type + "List").append("<input type='hidden' name='person_0_" + type + "_" + indx + "_id' value='" + id + "'>");
                $("#" + type + "List").append("<input type='hidden' name='person_0_" + type + "_" + indx + "_createThing' value='" + createThing + "'>");
                $("#" + type + "List").append("<input type='hidden' name='person_0_" + type + "_" + indx + "_deleteThing' value='Y'>");
            }

            function quoteMajor() {
                $('.modal').hide();
                $("#quoteModalMajorList").html("<P>Getting Quote....</P>");
                $('#quoteModalMajor').modal('show');
                $.ajax({
                    url: "<?php echo $settings['base_uri'];?>api/quotes/gohealth",
                    type: 'POST',
                    data: $("#leadform").serialize(),
                    success: function(result) {
                        $("#quoteModalMajorList").html(result);
                    }
                });
                $.ajax({
                    url: "<?php echo $settings['base_uri'];?>api/leads/updateLead",
                    type: 'POST',
                    data: $("#leadform").serialize(),
                    success: function(result) {
                        //$("#results").empty().append(result);
                        //console.log("done");
                        toastr.success('Save Successful', 'Server Response');
                    }
                });
            }

            function quoteData(type) {
                $('.modal').hide();
                $("#quote" + type + "List").html("<P>Getting Quote....</P>");
                $('#quote' + type).modal('show');
                $.ajax({
                    url: "<?php echo $settings['base_uri'];?>api/quotes/?type=" + type,
                    type: 'POST',
                    data: $("#leadform").serialize(),
                    success: function(result) {
                        $("#quote" + type + "List").html(result);
                    }
                });
                $.ajax({
                    url: "<?php echo $settings['base_uri'];?>api/leads/updateLead",
                    type: 'POST',
                    data: $("#leadform").serialize(),
                    success: function(result) {
                        //$("#results").empty().append(result);
                        //console.log("done");
                        toastr.success('Save Successful', 'Server Response');
                    }
                });
            }
        </script>
        <script>
            $(document).on("keypress", 'form', function(e) {
                if ($("textarea").is(":focus")) {} else {
                    var code = e.keyCode || e.which;
                    if (code == 13) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
            $(document).ready(function() {
                $('#leadform :input').blur(function() {
                    changedData = 1;
                });
                $('.confirmation').on('click', function() {
                    //console.log(changedData);
                    if (changedData == 1) {
                        var r = confirm("### DO YOU WANT TO SAVE YOUR INFORMATION? ###\n\r HIT 'OK' TO SAVE BEFORE LEAVING,\n\r HIT 'CANCEL' TO LEAVE");
                        if (r == true) {
                            changedData = 0;
                            $.ajax({
                                url: "<?php echo $settings['base_uri'];?>api/leads/updateLead",
                                type: 'POST',
                                data: $("#leadform").serialize(),
                                success: function(result) {
                                    //$("#results").empty().append(result);
                                    //console.log("done");
                                    toastr.success('Save Successful', 'Server Response');
                                    return r;
                                }
                            });
                        } else {
                            changedData = 0;
                            return true;
                        }
                    }
                });
                // Attach a submit handler to the form
                $("#leadform").submit(function(event) {
                    // Stop form from submitting normally
                    event.preventDefault();
                    $.ajax({
                        url: $(this).attr("action"),
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(result) {
                            //$("#results").empty().append(result);
                            //console.log("done");
                            toastr.success('Save Successful', 'Server Response');
                            changedData = 0;
                        }
                    });
                });
                <?php if(!empty($result['emails'][0])){ ?>
                $.ajax({
                    url: "<?php echo $settings['base_uri'];?>api/mail/customer/<?php echo trim($result['emails'][0]['email']);?>",
                    success: function(data) {
                        $("#foundEmails").html(data);
                    }
                });
                <?php }  else { ?>
                <?php if(!empty($result['emails'][1])){ ?>
                $.ajax({
                    url: "<?php echo $settings['base_uri'];?>api/mail/customer/<?php echo trim($result['emails'][1]['email']);?>",
                    success: function(data) {
                        $("#foundEmails").html(data);
                    }
                });
                <?php } ?>
                <?php } ?>
                $('.newSms').on('click', function() {
                    newSMSModal(this);
                });
                $('#newAppointment').on('click', function() {
                    newAppointmentModal(this);
                });
                $('.leadDelete').on('click', function(event) {
                    var articleId = $(this).attr('deleteId');
                    swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this post!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, delete it!",
                            showLoaderOnConfirm: true,
                            closeOnConfirm: true,
                        },
                        function() {
                            location.href = "#lead/delete/" + articleId;
                        });
                });
            });
        </script>