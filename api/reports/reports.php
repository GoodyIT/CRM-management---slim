

   <div class=" animated fadeInRight">

    <div class="row wrapper border-bottom white-bg page-heading ng-scope">

        <div class="col-lg-4">

            <h2>Reports</h2>

            <ol class="breadcrumb">

                <li><a href="#">Home</a></li>

                <li class="active">

                    <strong>Reports</strong>

                </li>

            </ol>

        </div>

        <div class="col-sm-8">

            <div class="title-action">

            </div>

        </div>

    </div>

</div>

<div class="row  animated fadeInRight">

    <div class="ibox float-e-margins">

        <div class="ibox float-e-margins" id="filterBox">

            <div class="ibox-title">

                <h5>Report Filters</h5>

                <div class="ibox-tools">

                    <a class="filterToggle">

                        <i class="fa fa-chevron-up"></i>

                    </a>

                </div>

            </div>

            <form id="reportsForm" name="reportsForm" method="post" action="<?php echo $settings['base_uri'];?>api/reports" class="form-horizontal ">

                <div id="filters" class="ibox-content">

                    <div class="row">

                        <div class="col-sm-4 ">

                            <label>Status</label>

                            <select name="reportsStatus"   id="reportsStatus"  class=" form-control ">

                               <option value="ANY" <?php if ($result[ 'reportsStatus']=="ANY" ){ echo "selected";}?>>Any</option>
                                <option value="SOLD" <?php if ($result[ 'reportsStatus']=="SOLD" ){ echo "selected";}?>>Sold</option>
                                <option value="HOLD" <?php if ($result[ 'reportsStatus']=="HOLD" ){ echo "selected";}?>>Hold</option>
                                 <option value="FOLLOWUP" <?php if ($result[ 'reportsStatus']=="FOLLOWUP" ){ echo "selected";}?>>Follow Up</option>
                                 <!--  <option value="CANCELLED" <?php if ($result[ 'reportsStatus']=="CANCELLED" ){ echo "selected";}?>>Cancelled</option>
                                   <option value="PAYMENTISSUE" <?php if ($result[ 'reportsStatus']=="PAYMENTISSUE" ){ echo "selected";}?>>Payment Issue</option> -->
                                   <option value="COMMISSION_PAYMENT_RECEIVED" <?php if ($result[ 'reportsStatus']=="COMMISSION_PAYMENT_RECEIVED" ){ echo "selected";}?>>Commission Payment Received</option>
                            </select>

                        </div>

                        <div class="col-sm-4 ">

                            <label>Start Date</label>

                            <input type="text" name="reportsStartDate" class="form-control  datepicker" data-mask="99/99/9999" value="<?php echo $apiObj->validateTimestamp($result['reportsStartDate'],  " m/d/Y "); ?>">

                        </div>

                        <div class="col-sm-4 ">

                            <label>End Date</label>

                            <input type="text" name="reportsEndDate" class="form-control  datepicker" data-mask="99/99/9999" value="<?php echo $apiObj->validateTimestamp($result['reportsEndDate'],  " m/d/Y "); ?>">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-sm-4 ">

                            <label>Carrier</label>

                            <select name="reportsCarrier"  id="reportsCarrier"  class=" form-control ">

                                <option value="ANY" <?php if ($result[ 'reportsCarrier']=="ANY" ){ echo "selected";}?>>Any</option>

                                <?php

if(!empty($result['carrier'])){

    foreach($result['carrier'] as $ckey=>$cval){

        $selected = "";

        if ($cval['_id']==$result['reportsCarrier'] ){ $selected =  "selected";}

        echo "<option value='".$cval['_id']."' ".$selected." >".$cval['name']."</option>";

    }

}

                                ?>

                            </select>

                        </div>

                        <div class="col-sm-4 ">

                            <label>Coverage Type</label>

                            <select name="reportsCarrierPlan" id="reportsCarrierPlan"  class=" form-control ">

                                <option value="ANY" <?php if ($result[ 'reportsCarrierPlan']=="ANY" ){ echo "selected";}?>>Any</option>

                                <?php

if(!empty($result['carrierPlan'])){

    foreach($result['carrierPlan'] as $ckey=>$cval){

        $selected = "";

        if ($cval['_id']==$result['reportsCarrierPlan'] ){ $selected =  "selected";}

        echo "<option value='".$cval['_id']."' ".$selected." >".$cval['name']."</option>";

    }

}

                                ?>

                            </select>

                        </div>

                        <div class="col-sm-4 ">

                            <label>Lead Source</label>

                            <select name="reportsLeadSource"  id="reportsLeadSource" class=" form-control ">

                                <option value="ANY" <?php if ($result[ 'reportsLeadSource']=="ANY" ){ echo "selected";}?>>Any</option>

                                <?php

if(!empty($result['systemForm']['leadSource']['options'])){

    foreach($result['systemForm']['leadSource']['options'] as $ckey=>$cval){

        $selected = "";

        if ($cval['_id']==$result['reportsLeadSource'] ){ $selected =  "selected";}

        echo "<option value='".$cval['_id']."' ".$selected." >".$cval['label']."</option>";

    }

}

                                ?>

                            </select>

                            </select>

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-4 ">

                        <label>Fronter</label>

                        <select name="reportsFronter" id="reportsFronter" class=" form-control ">

                            <option value="ANY" <?php if ($result[ 'reportsFronter']=="SOLD" ){ echo "selected";}?>>Any</option>

                            <?php 

if(!empty($result['user'])){

    foreach($result['user'] as $uId=>$uVal){ 

        if($uVal['canSell'] == "Y"){

            $selected = "";

            if($result['reportsFronter'] == $uId){

                $selected = "selected";

            }

            echo "<option value='".$uId."'  ".$selected.">".$uVal['firstname']." ".$uVal['lastname']."</option>";

        }   

    }

}

                            ?>

                        </select>

                    </div>

                    <div class="col-sm-4 ">

                        <label>Closer</label>

                        <select name="reportsCloser" id="reportsCloser" class=" form-control ">

                            <option value="ANY" <?php if ($result[ 'reportsCloser']=="SOLD" ){ echo "selected";}?>>Any</option>

                            <?php 

if(!empty($result['user'])){

    foreach($result['user'] as $uId=>$uVal){ 

        if($uVal['licensed'] == "Y"){

            $selected = "";

            if($result['reportsCloser'] == $uId){

                $selected = "selected";

            }

            echo "<option value='".$uId."'  ".$selected.">".$uVal['firstname']." ".$uVal['lastname']."</option>";

        }   

    }

}

                            ?>

                        </select>

                    </div>

                    <div class="col-sm-4 ">

                        <label>State</label>

                        <select id="reportsState" name="reportsState" class=" form-control ">

                            <option value="ANY" <?php if ($result[ 'reportsState']=="ANY" ){ echo "selected";}?>>Any</option>

                            <?php

if(!empty($result['systemForm']['state']['options'])){

    foreach($result['systemForm']['state']['options'] as $ckey=>$cval){

        $selected = "";

        if ($cval['value']==$result['reportsState'] ){ $selected =  "selected";}

        if(trim($cval['label']) <> ""){

            echo "<option value='".$cval['value']."' ".$selected." >".$cval['label']."</option>";

        }

    }

}

                            ?>

                        </select>

                    </div>

                </div>
                <div class="row">

                    <div class="col-sm-4 ">

                        <label>Submission Status</label>

                        <select name="reportsSubmissonStatus"   id="reportsSubmissonStatus"  class=" form-control ">
                            <option value="ANY" <?php if ($result[ 'reportsSubmissonStatus']=="ANY" ){ echo "selected";}?>>Any</option>
                            <option value="NOTSUBMITTED" <?php if ($result[ 'reportsSubmissonStatus']=="NOTSUBMITTED" ){ echo "selected";}?>>Not Submitted</option>
                            <option value="SUBMIT" <?php if ($result[ 'reportsSubmissonStatus']=="SUBMIT" ){ echo "selected";}?>>Policy Submitted</option>
                            <option value="SUBMITPAYMENT" <?php if ($result[ 'reportsSubmissonStatus']=="SUBMITPAYMENT" ){ echo "selected";}?>>Submitted with Payment</option>
                            <option value="ERRORS" <?php if ($result[ 'reportsSubmissonStatus']=="ERRORS" ){ echo "selected";}?>>Submission Errors</option>           
                        </select>

                    </div>

                    <div class="col-sm-4 ">

                        <label>Underwriting Status</label>

                        <select name="reportsUnderwritingStatus"   id="reportsUnderwritingStatus"  class=" form-control ">
                            <option value="ANY" <?php if ($result[ 'reportsUnderwritingStatus']=="ANY" ){ echo "selected";}?>>Any</option>
                            <option value="ACTIVE" <?php if ($result[ 'reportsUnderwritingStatus']=="ACTIVE" ){ echo "selected";}?>>Active</option>
                            <option value="LATEPAYMENT" <?php if ($result[ 'reportsUnderwritingStatus']=="LATEPAYMENT" ){ echo "selected";}?>>Late Payment</option>
                            <option value="CANCELLED" <?php if ($result[ 'reportsUnderwritingStatus']=="CANCELLED" ){ echo "selected";}?>>Cancelled</option>
                            <option value="PENDINGUW" <?php if ($result[ 'reportsUnderwritingStatus']=="PENDINGUW" ){ echo "selected";}?>>Pending UW</option>
                            <option value="PENDINGINITIALPAYMENT" <?php if ($result[ 'reportsUnderwritingStatus']=="PENDINGINITIALPAYMENT" ){ echo "selected";}?>>Pending Initial Payment</option>
                            <option value="PAYMENTDECLINED" <?php if ($result[ 'reportsUnderwritingStatus']=="PAYMENTDECLINED" ){ echo "selected";}?>>Payment Declined</option>
                        </select>
                    </div>

                    <div class="col-sm-4 ">

                        <label>Commision Status</label>
                        <select name="reportsCommisionStatus"   id="reportsCommisionStatus"  class=" form-control ">
                            <option value="ANY" <?php if ($result[ 'reportsCommisionStatus']=="ANY" ){ echo "selected";}?>>Any</option>
                            <option value="PENDING" <?php if ($result[ 'reportsCommisionStatus']=="PENDING" ){ echo "selected";}?>>Pending</option>
                            <option value="ADVANCERECEIVED" <?php if ($result[ 'reportsCommisionStatus']=="ADVANCERECEIVED" ){ echo "selected";}?>>Advance Received</option>
                            <option value="MONTHLYPAYMENTRECEIVED" <?php if ($result[ 'reportsCommisionStatus']=="MONTHLYPAYMENTRECEIVED" ){ echo "selected";}?>>Monthly Payment Received</option>
                            <option value="CHARGEBACKRECEIVED" <?php if ($result[ 'reportsCommisionStatus']=="CHARGEBACKRECEIVED" ){ echo "selected";}?>>Chargeback Received</option>


                        </select>
                        
                    </div>
                    

                </div>
                <div class="row">

                    <div class="col-sm-12 text-right">

                        <div class="title-action">

                            <input type="submit" class="btn btn-primary " value="Filter Reports">

                        </div>

                    </div>

                </div>

                </div>

            </form>

        <div id="reportResults">

            <div class="ibox-content">

                <div class="row">

                    <div class="col-sm-4 text-center">

                        <div class="widget yellow-bg no-padding">

                            <div class="p-m">

                                <h1 class="m-xs">$ <?php echo number_format($result['sales']['totalPremium'],0,'.',',');?></h1>

                                <h3 class="font-bold no-margins">

                                    Total Premium

                                </h3>

                            </div>

                        </div>

                    </div>

                    <?php

if($result['sales']['totalPremium'] == 0){

    if(   $result['sales']['totalPolicies'] == 1){

        $result['sales']['totalPolicies'] = 0;   

    }

}

                    ?>

                    <div class="col-sm-4 text-center">

                        <div class="widget navy-bg no-padding">

                            <div class="p-m">

                                <h1 class="m-xs"><?php echo number_format($result['sales']['totalPolicies'],0,'.',',');?></h1>

                                <h3 class="font-bold no-margins">

                                    Total Policies

                                </h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-4 text-center">

                        <div class="widget lazur-bg no-padding">

                            <div class="p-m">

                                <h1 class="m-xs">$ <?php echo number_format($result['sales']['totalAverage'],2,'.',',');?></h1>

                                <h3 class="font-bold no-margins">

                                    Average Premium

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-xs-12">

                    <ul class="nav nav-tabs" role="tablist">

                        <li role="presentation" class="active"><a href="#state" aria-controls="state" role="tab" data-toggle="tab" class="tab">State</a></li>

                        <li role="presentation"><a href="#leadsource" aria-controls="leadsource" role="tab" data-toggle="tab" class="tab">Lead Source</a></li>

                        <li role="presentation"><a href="#carrier" aria-controls="carrier" role="tab" data-toggle="tab" class="tab">Carrier</a></li>

                        <li role="presentation"><a href="#coverage" aria-controls="coverage" role="tab" data-toggle="tab" class="tab">Coverage Type</a></li>

                        <li role="presentation"><a href="#fronters" aria-controls="fronters" role="tab" data-toggle="tab" class="tab">Fronters</a></li>

                        <li role="presentation"><a href="#closers" aria-controls="closers" role="tab" data-toggle="tab" class="tab">Closers</a></li>
                        <?php if((strtoupper($_SESSION['api']['user']['permissionLevel']) == "ADMINISTRATOR")) : ?>
                            <a class="btn btn-success pull-right" id="btn-export-data" style="margin: 10px 20px 0px 0px;">Export</a>
                        <?php endif; ?>
                    </ul>

                </div>

            </div>

            <div class="tab-content">

                <!-- Phone Tabs -->

                <div role="tabpanel" class="tab-pane active" id="state">

                    <div class="ibox-title">

                        <h5>Sales by State</h5>

                    </div>

                    <div class="ibox-content">

                        <div class="table-responsive">

                            <table id="salesByState" class="table table-striped table-bordered">

                                <thead>

                                    <tr>

                                        <th width="50%" data-sort="int">State </th>

                                        <th class="text-center" data-sort="int">Policies </th>

                                        <th class="text-center">Average</th>

                                        <th class="text-center">Premium</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php 

if(!empty($result['salesByState']['states'])){

    foreach($result['salesByState']['states'] as $abbrev=>$statInfo){

                                    ?>

                                    <tr>

                                        <td>
                                            <a target="_blank" href="#policies/reportsSubmissonStatus=<?php echo $result['reportsSubmissonStatus']?>&reportsUnderwritingStatus=<?php echo $result['reportsUnderwritingStatus']?>&reportsCommisionStatus=<?php echo $result['reportsCommisionStatus']?>&reportsStatus=<?php echo $result['reportsStatus']?>&reportsStartDate=<?php echo $result['reportsStartDateNoFormat']?>&reportsEndDate=<?php echo $result['reportsEndDateNoFormat']?>&reportsCarrier=<?php echo $result['reportsCarrier'];?>&reportsCarrierPlan=<?php echo $result['reportsCarrierPlan'];?>&reportsLeadSource=<?php echo $result['reportsLeadSource'];?>&reportsFronter=<?php echo $result['reportsFronter'];?>&reportsCloser=<?php echo $result['reportsCloser'];?>&reportsState=<?php echo $statInfo['value'];?>"><?php echo ucwords(strtolower($abbrev));?></a>
                                            <!-- <a onClick="changeFilters('State','<?php echo $abbrev; ?>');"><?php echo ucwords(strtolower($abbrev));?></a> -->

                                        </td>

                                        <td class="text-center">

                                            <?php echo $statInfo['policies'];?>

                                        </td>

                                        <td class="text-right">$

                                            <?php echo number_format($statInfo['average'],2,'.',',');?>

                                        </td>

                                        <td class="text-right">$

                                            <?php echo number_format($statInfo['premium'],2,'.',',');?>

                                </i>

                                </a>

                            </td>

                        </tr>

                    <?php

    }

}

                    ?>

                    <thead>

                        <tr>

                            <th>Totals: </th>

                            <th class="text-center">

                                <?php echo number_format($result['salesByState']['totalPolicies'],0,'.',',');?>

                            </th>

                            <th class="text-right">$

                                <?php echo number_format($result['salesByState']['totalAverage'],2,'.',',');?>

                            </th>

                            <th class="text-right">$

                                <?php echo number_format($result['salesByState']['totalPremium'],2,'.',',');?>

                            </th>

                        </tr>

                    </thead>

                    </tbody>

                </table>

        </div>

    </div>

</div>

<div role="tabpanel" class="tab-pane" id="leadsource">

    <div class="ibox-title">

        <h5>Sales By Lead Source</h5>

    </div>

    <div class="ibox-content">

        <div class="table-responsive">

            <table  id="salesByLeadSource" class="table table-striped table-bordered">

                <thead>

                    <tr>

                        <th width="50%" data-sort="string">Lead Source </th>

                        <th class="text-center" data-sort="int">Policies </th>

                        <th class="text-center">Average</th>

                        <th class="text-center">Premium</th>

                    </tr>

                </thead>

                <tbody>

                    <?php 

if(!empty($result['salesByLead']['leadsources'])){

    foreach($result['salesByLead']['leadsources'] as $abbrev=>$leadInfo){

                    ?>

                    <tr>

                        <td nowrap>
                            <?php if(empty($leadInfo['id']))
                                $leadInfo['id'] = 'UNKNOWN';
                            ?>
                            <a target="_blank" href="#policies/reportsSubmissonStatus=<?php echo $result['reportsSubmissonStatus']?>&reportsUnderwritingStatus=<?php echo $result['reportsUnderwritingStatus']?>&reportsCommisionStatus=<?php echo $result['reportsCommisionStatus']?>&reportsStatus=<?php echo $result['reportsStatus']?>&reportsStartDate=<?php echo $result['reportsStartDateNoFormat']?>&reportsEndDate=<?php echo $result['reportsEndDateNoFormat']?>&reportsCarrier=<?php echo $result['reportsCarrier'];?>&reportsCarrierPlan=<?php echo $result['reportsCarrierPlan'];?>&reportsLeadSource=<?php echo $leadInfo['id'];?>&reportsFronter=<?php echo $result['reportsFronter'];?>&reportsCloser=<?php echo $result['reportsCloser'];?>&reportsState=<?php echo $result['reportsState'];?>"> <?php echo ucwords(strtolower($abbrev));?></a>
                            
                            <!-- <a onClick="changeFilters('LeadSource','<?php echo $leadInfo['id']; ?>');"><?php echo ucwords(strtolower($abbrev));?></a> -->

                        </td>

                        <td class="text-center">

                            <?php echo $leadInfo['policies'];?>

                        </td>

                        <td class="text-right">$

                            <?php echo number_format($leadInfo['average'],2,'.',',');?>

                        </td>

                        <td class="text-right">$

                            <?php echo number_format($leadInfo['premium'],2,'.',',');?>

                </i>

                </a>

            </td>

        </tr>

    <?php

    }

}

    ?>

    <thead>

        <tr>

            <th>Totals: </th>

            <th class="text-center">

                <?php echo number_format($result['salesByLead']['totalPolicies'],0,'.',',');?>

            </th>

            <th class="text-right">$

                <?php echo number_format($result['salesByLead']['totalAverage'],2,'.',',');?>

            </th>

            <th class="text-right">$

                <?php echo number_format($result['salesByLead']['totalPremium'],2,'.',',');?>

            </th>

        </tr>

    </thead>

    </tbody>

</table>

</div>

</div>

</div>

<!-- Carrier Tab -->

<div role="tabpanel" class="tab-pane" id="carrier">

    <div class="ibox-title">

        <h5>Sales by Carrier</h5>

    </div>

    <div class="ibox-content">

        <div id="reportResults">

            <div class="table-responsive">

                <table id="salesByCarrier" class="table table-striped table-bordered">

                    <thead>

                        <tr>

                            <th width="50%" data-sort="string">Carrier </th>

                            <th class="text-center" data-sort="int">Policies </th>

                            <th class="text-center">Average</th>

                            <th class="text-center">Premium</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php 

if(!empty( $result['salesByCarrier']['carrier'])){

    foreach( $result['salesByCarrier']['carrier'] as $carrId=>$carrInfo){

                        ?>

                        <tr>

                            <td nowrap>

                                <!-- <a onClick="changeFilters('Carrier','<?php echo $carrInfo['id'];?>');"><?php echo ucwords(strtolower($carrId));?></a> -->
                                <a target="_blank" href="#policies/reportsSubmissonStatus=<?php echo $result['reportsSubmissonStatus']?>&reportsUnderwritingStatus=<?php echo $result['reportsUnderwritingStatus']?>&reportsCommisionStatus=<?php echo $result['reportsCommisionStatus']?>&reportsStatus=<?php echo $result['reportsStatus']?>&reportsStartDate=<?php echo $result['reportsStartDateNoFormat']?>&reportsEndDate=<?php echo $result['reportsEndDateNoFormat']?>&reportsCarrier=<?php echo $carrInfo['id'];?>&reportsCarrierPlan=<?php echo $result['reportsCarrierPlan'];?>&reportsLeadSource=<?php echo $result['reportsLeadSource'];?>&reportsFronter=<?php echo $result['reportsFronter'];?>&reportsCloser=<?php echo $result['reportsCloser'];?>&reportsState=<?php echo $result['reportsState'];?>"> <?php echo ucwords(strtolower($carrId));?></a>
                            </td>

                            <td class="text-center">

                                <?php echo $carrInfo['policies'];?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($carrInfo['average'],2,'.',',');?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($carrInfo['premium'],2,'.',',');?>

                    </i>

                    </a>

                </td>

            </tr>

        <?php

    }

}

        ?>

        <thead>

            <tr>

                <th>Totals: </th>

                <th class="text-center">

                    <?php echo number_format($result['salesByCarrier']['totalCarrierPolicies'],0,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByCarrier']['totalCarrierAverage'],2,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByCarrier']['totalCarrierPremium'],2,'.',',');?>

                </th>

            </tr>

        </thead>

        </tbody>

    </table>

</div>

</div>

</div>

</div>

<!-- Coverage Tab -->

<div role="tabpanel" class="tab-pane" id="coverage">

    <div class="ibox-title">

        <h5>Sales by Coverage</h5>

    </div>

    <div class="ibox-content">

        <div id="reportResults">

            <div class="table-responsive">

                <table id="salesByCoverage" class="table table-striped table-bordered">

                    <thead>

                        <tr>

                            <th width="50%" data-sort="string">Coverage </th>

                            <th class="text-center" data-sort="int">Policies </th>

                            <th class="text-center">Average</th>

                            <th class="text-center">Premium</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php 

if(!empty( $result['salesByCarrier']['carrierPlan'])){

    foreach( $result['salesByCarrier']['carrierPlan'] as $carrId=>$carrInfo){

                        ?>

                        <tr>

                            <td nowrap>
                                <a target="_blank" href="#policies/reportsSubmissonStatus=<?php echo $result['reportsSubmissonStatus']?>&reportsUnderwritingStatus=<?php echo $result['reportsUnderwritingStatus']?>&reportsCommisionStatus=<?php echo $result['reportsCommisionStatus']?>&reportsStatus=<?php echo $result['reportsStatus']?>&reportsStartDate=<?php echo $result['reportsStartDateNoFormat']?>&reportsEndDate=<?php echo $result['reportsEndDateNoFormat']?>&reportsCarrier=<?php echo $result['reportsCarrier'];?>&reportsCarrierPlan=<?php echo $carrInfo['id'];?>&reportsLeadSource=<?php echo $result['reportsLeadSource'];?>&reportsFronter=<?php echo $result['reportsFronter'];?>&reportsCloser=<?php echo $result['reportsCloser'];?>&reportsState=<?php echo $result['reportsState'];?>"> <?php echo ucwords(strtolower($carrId));?></a>
                                
                                <!-- <a onClick="changeFilters('CarrierPlan','<?php echo $carrInfo['id'];?>');"><?php echo ucwords(strtolower($carrId));?></a> -->

                            </td>

                            <td class="text-center">

                                <?php echo $carrInfo['policies'];?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($carrInfo['average'],2,'.',',');?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($carrInfo['premium'],2,'.',',');?>

                    </i>

                    </a>

                </td>

            </tr>

        <?php

    }

}

        ?>

        <thead>

            <tr>

                <th>Totals: </th>

                <th class="text-center">

                    <?php echo number_format($result['salesByCarrier']['totalCarrierPlanPolicies'],0,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByCarrier']['totalCarrierPlanAverage'],2,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByCarrier']['totalCarrierPlanPremium'],2,'.',',');?>

                </th>

            </tr>

        </thead>

        </tbody>

    </table>

</div>

</div>

</div>

</div>

<!-- Fronter Tab -->

<div role="tabpanel" class="tab-pane" id="fronters">

    <div class="ibox-title">

        <h5>Sales by Fronter</h5>

    </div>

    <div class="ibox-content">

        <div id="reportResults">

            <div class="table-responsive">

                <table id="salesByFronter" class="table table-striped table-bordered">

                    <thead>

                        <tr>

                            <th width="50%" data-sort="string">Fronter </th>

                            <th class="text-center" data-sort="int">Policies </th>

                            <th class="text-center">Average</th>

                            <th class="text-center">Premium</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php 

if(!empty(  $result['salesByUsers']['fronters'])){

    foreach(  $result['salesByUsers']['fronters'] as $userId=>$userInfo){

                        ?>

                        <tr>

                            <td nowrap>
                                <a target="_blank" href="#policies/reportsSubmissonStatus=<?php echo $result['reportsSubmissonStatus']?>&reportsUnderwritingStatus=<?php echo $result['reportsUnderwritingStatus']?>&reportsCommisionStatus=<?php echo $result['reportsCommisionStatus']?>&reportsStatus=<?php echo $result['reportsStatus']?>&reportsStartDate=<?php echo $result['reportsStartDateNoFormat']?>&reportsEndDate=<?php echo $result['reportsEndDateNoFormat']?>&reportsCarrier=<?php echo $result['reportsCarrier'];?>&reportsCarrierPlan=<?php echo $result['reportsCarrierPlan'];?>&reportsLeadSource=<?php echo $result['reportsLeadSource'];?>&reportsFronter=<?php echo $userInfo['id'];?>&reportsCloser=<?php echo $result['reportsCloser'];?>&reportsState=<?php echo $result['reportsState'];?>"> <?php echo ucwords(strtolower($userInfo['name']));?></a>
                                
                                <!-- <a onClick="changeFilters('Fronter','<?php echo $userInfo['id'];?>');"><?php echo ucwords(strtolower($userInfo['name']));?></a> -->

                            </td>

                            <td class="text-center">

                                <?php echo $userInfo['policies'];?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($userInfo['average'],2,'.',',');?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($userInfo['premium'],2,'.',',');?>

                    </i>

                    </a>

                </td>

            </tr>

        <?php

    }

}

        ?>

        <thead>

            <tr>

                <th>Totals: </th>

                <th class="text-center">

                    <?php echo number_format($result['salesByUsers']['totalFronterPolicies'],0,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByUsers']['totalFronterAverage'],2,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByUsers']['totalFronterPremium'],2,'.',',');?>

                </th>

            </tr>

        </thead>

        </tbody>

    </table>

</div>

</div>

</div>

</div>

<!-- Closer Tab -->

<div role="tabpanel" class="tab-pane" id="closers">

    <div class="ibox-title">

        <h5>Sales by Closer</h5>

    </div>

    <div class="ibox-content">

        <div id="reportResults">

            <div class="table-responsive">

                <table  id="salesByCloser" class="table table-striped table-bordered">

                    <thead>

                        <tr>

                            <th width="50%" data-sort="string">Closer </th>

                            <th class="text-center" data-sort="int">Policies </th>

                            <th class="text-center" >Average</th>

                            <th class="text-center" >Premium</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php 
if(!empty(  $result['salesByUsers']['closers'])){

    foreach(  $result['salesByUsers']['closers'] as $userId=>$userInfo){

                        ?>
                        <tr>

                            <td nowrap>
                                <a target="_blank" href="#policies/reportsSubmissonStatus=<?php echo $result['reportsSubmissonStatus']?>&reportsUnderwritingStatus=<?php echo $result['reportsUnderwritingStatus']?>&reportsCommisionStatus=<?php echo $result['reportsCommisionStatus']?>&reportsStatus=<?php echo $result['reportsStatus']?>&reportsStartDate=<?php echo $result['reportsStartDateNoFormat']?>&reportsEndDate=<?php echo $result['reportsEndDateNoFormat']?>&reportsCarrier=<?php echo $result['reportsCarrier'];?>&reportsCarrierPlan=<?php echo $result['reportsCarrierPlan'];?>&reportsLeadSource=<?php echo $result['reportsLeadSource'];?>&reportsFronter=<?php echo $result['reportsFronter'];?>&reportsCloser=<?php echo $userInfo['id'];?>&reportsState=<?php echo $result['reportsState'];?>"><?php echo ucwords(strtolower($userInfo['name']));?></a>
                                <!-- <a onClick="changeFilters('Closer','<?php echo $userInfo['id'];?>');"><?php echo ucwords(strtolower($userInfo['name']));?></a> -->
                            </td>

                            <td class="text-center">

                                <?php echo $userInfo['policies'];?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($userInfo['average'],2,'.',',');?>

                            </td>

                            <td class="text-right">$

                                <?php echo number_format($userInfo['premium'],2,'.',',');?>

                    </i>

                    </a>

                </td>

            </tr>

        <?php

    }

}

        ?>

        <thead>

            <tr>

                <th>Totals: </th>

                <th class="text-center">

                    <?php echo number_format($result['salesByUsers']['totalCloserPolicies'],0,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByUsers']['totalCloserAverage'],2,'.',',');?>

                </th>

                <th class="text-right">$

                    <?php echo number_format($result['salesByUsers']['totalCloserPremium'],2,'.',',');?>

                </th>

            </tr>

        </thead>

        </tbody>

    </table>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

</div>





<script>
    $("#btn-export-data").click(function() {
        // alert("SDFDSf");
        toastr.warning('Export Running...', 'Server Response',{'timeOut':100000});
        $.ajax({

            url: "<?php echo $settings['base_uri'];?>api/reports/export-csv",

            type: 'GET',

            // data: {reportsStatus:  'ANY'},
            // data: $(this).serialize(),
            data: { reportsStatus: $( "#reportsStatus" ).val(), 
                    reportsStartDate: $('input[name=reportsStartDate]').val(), 
                    reportsEndDate: $('input[name=reportsEndDate]').val(), 
                    reportsCarrier: $( "#reportsCarrier" ).val(), 
                    reportsCarrierPlan: $( "#reportsCarrierPlan" ).val(), 
                    reportsLeadSource: $( "#reportsLeadSource" ).val(), 
                    reportsFronter: $( "#reportsFronter" ).val(), 
                    reportsCloser: $( "#reportsCloser" ).val(), 
                    reportsState: $( "#reportsState" ).val(),
                    reportsSubmissonStatus: $( "#reportsSubmissonStatus" ).val(), 
                    reportsUnderwritingStatus: $( "#reportsUnderwritingStatus" ).val(), 
                    reportsCommisionStatus: $( "#reportsCommisionStatus" ).val()
                 },
            success: function(result) {
                toastr.remove();
                var obj = jQuery.parseJSON(result);
                window.open("api/reports/files/" + obj.file_name);
            }

        });
    });
    $(function() {

        $(".datepicker").pickadate({

            format: 'mm/dd/yyyy',

            selectYears: 100,

            selectMonths: true,

        });

    });



    function stateSet(state) {

        $("#reportsState option[value=" + state + "]").attr("selected", "selected");

    }

    $(document).ready(function() {

        // Collapse ibox function

        $('.filterToggle').click(function() {

            var button = $(this).find('i');

            $('#filters').slideToggle(200);

            button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');

            $('#filterBox').toggleClass('').toggleClass('border-bottom');

            setTimeout(function() {

                $('#filterBox').resize();

                $('#filterBox').find('[id^=map-]').resize();

            }, 50);

        });

        $("#reportsForm").submit(function(event) {
            toastr.warning('Reports Running...', 'Server Response',{'timeOut':100000});

            // Stop form from submitting normally

            event.preventDefault();

            $.ajax({

                url: $(this).attr("action"),

                type: 'GET',

                data: $(this).serialize(),

                success: function(result) {
                    toastr.remove();
                    $("#results").empty().append(result);

                    toastr.success('Reports Completed', 'Server Response');

                    

                   

                }

            });

        });

        

     

        

    });

    

     function changeFilters(filter,value) {
        $.ajax({
            url: 'http://localhost/insurtainty/api/leads/policies?policies_search=sdsdxxx',
            type: 'GET',
            // data: $(this).serialize(),
            success: function(result) {
                $("#results").empty().append(result);
                console.log("done");
            }
        });
         // $("#reports"+filter+" option:selected").removeAttr("selected");

         // $("#reports"+filter+" option[value='"+value +"']").attr('selected', 'selected');  

         // console.log(filter + " " + value);

         // $("#reportsForm").submit();

    }

    

    $(document).ready(function() 

    { 

        $(".table").tablesorter(); 

    } 

); 

    

</script>











