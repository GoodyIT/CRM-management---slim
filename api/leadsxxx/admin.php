<?php
//debug($result['policy']);   
?>

 <?php
      /*
        
                $formitem['systemForm_0_createThing'] = "Y";
                $formitem['systemForm_0_thing'] = 'adminTab';
                $formitem['systemForm_0_name'] = 'underwritingDisposition';
                $formitem['systemForm_0_label'] = 'Underwriting Disposition';
                $formitem['systemForm_0_type'] = 'SELECT';
                $formitem['systemForm_0_row'] = '1';
                $formitem['systemForm_0_sort'] = '3';
                $formitem['systemForm_0_columns'] = '4';
                $formitem['systemForm_0_required'] = '0';
                $formitem['systemForm_0_options_0_createThing'] = "N";
                $formitem['systemForm_0_options_0_value'] = '';
                $formitem['systemForm_0_options_0_label'] = 'Not Selected';
                $formitem['systemForm_0_options_0_default'] = 'ACTIVE';
                $formitem['systemForm_0_options_1_createThing'] = "Active";
                $formitem['systemForm_0_options_1_value'] = 'HELD';
                $formitem['systemForm_0_options_1_label'] = 'Held In Underwriting';
                $formitem['systemForm_0_options_1_default'] = 'N';
                $formitem['systemForm_0_options_2_createThing'] = "N";
                $formitem['systemForm_0_options_2_value'] = 'PAYMENTISSUE';
                $formitem['systemForm_0_options_2_label'] = 'Payment Issue';
                $formitem['systemForm_0_options_2_default'] = 'N';
                $formitem['systemForm_0_options_3_createThing'] = "N";
                $formitem['systemForm_0_options_3_value'] = 'CMSPROOF';
                $formitem['systemForm_0_options_3_label'] = 'CMS Proof Needed';
                $formitem['systemForm_0_options_3_default'] = 'N';
                $formitem['systemForm_0_options_4_createThing'] = "N";
                $formitem['systemForm_0_options_4_value'] = 'CANCELLED';
                $formitem['systemForm_0_options_4_label'] = 'Cancelled';
                $formitem['systemForm_0_options_4_default'] = 'N';
                $formitem['systemForm_0_options_5_createThing'] = "N";
                $formitem['systemForm_0_options_5_value'] = 'NOTTAKEN';
                $formitem['systemForm_0_options_5_label'] = 'Not Taken';
                $formitem['systemForm_0_options_5_default'] = 'N';
                $formitem['systemForm_0_options_6_createThing'] = "N";
                $formitem['systemForm_0_options_6_value'] = 'TERMINATED';
                $formitem['systemForm_0_options_6_label'] = 'Terminated';
                $formitem['systemForm_0_options_6_default'] = 'N';

                $apiObj->save_things($formitem);
                
                  */

                
                          ?>


<div class="ibox float-e-margins">
    

    <?php

//debug($result);

$i = 0;
if(!empty($result['policy'])){
    foreach($result['policy'] as $key=>$value){


    ?>

    <div class="ibox-content">
        <div class="row">

            <div class="col-xs-4">
               
               <div class="row">
                    <div class=" col-sm-12 col-md-6">
                        <div class="form-group ">
                            <label>
                                Policy Number
                            </label>
                            <div class="input-group col-xs-12">
                                <?php echo $value['policyNumber'];?>
                            </div>
                        </div>
                    </div>

                    <div class=" col-sm-12 col-md-6">
                        <div class="form-group ">
                            <label>
                                Status
                            </label>
                            <div class="input-group col-xs-12">
                                <?php echo $value['status'];?>
                            </div>
                        </div>
                    </div>
                </div>
                
                 <div class="row">
                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Carrier
                        </label>
                        <div class="input-group col-xs-12">
                           <?php
                                try {
                                    foreach($result['carriers'] as $kc=>$kv){
                                        if($kv['_id'] ==  $value['carrier']){
                                            echo   $kv['name'];        
                                        }
                                    }
                                } catch (Exception $e) {
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Coverage
                        </label>
                        <div class="input-group col-xs-12">
                           <?php
                                try {
                                    foreach($result['carrierPlans'] as $kc=>$kv){
                                        if($kv['_id'] ==  $value['coverageType']){
                                            echo   $kv['name'];        
                                        }
                                    }
                                } catch (Exception $e) {
                                }
                            ?>
                           
        
                        </div>
                    </div>
                </div>
                </div>
                 <div class="row">
                 <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Submission Date
                        </label>
                        <div class="input-group col-xs-12">
                            <?php echo $value['submissionDate'];?>
                        </div>
                    </div>
                </div>

           

                 <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Effective Date
                        </label>
                        <div class="input-group col-xs-12">
                            <?php echo $value['effectiveDate'];?>
                        </div>
                    </div>
                </div>
                </div>
                 <div class="row">
                 <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Payment Date
                        </label>
                        <div class="input-group col-xs-12">
                            <?php echo $value['dateToPay'];?>
                        </div>
                    </div>
                </div>

                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Premium
                        </label>
                        <div class="input-group col-xs-12">
                            <?php echo $value['premiumMoney'];?>
                        </div>
                    </div>
                </div>
                </div>
                 <div class="row">

                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Subsidy
                        </label>
                        <div class="input-group col-xs-12">
                            <?php echo $value['subsidyMoney'];?>
                        </div>
                    </div>
                </div>

                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Setup Fee
                        </label>
                        <div class="input-group col-xs-12">
                            <?php echo $value['setupFeeMoney'];?>
                        </div>
                    </div>
                </div>

                </div>
                 <div class="row">
           


              

                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Renewal Date
                        </label>
                        <div class="input-group col-xs-12">
                            <?php echo $value['renewalDate'];?>
                        </div>
                    </div>
                </div>
                
                </div>
                <div class="row">

                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Sold By
                        </label>
                        <div class="input-group col-xs-12">
                            <?php
                                try {
                                    foreach($result['users'] as $kc=>$kv){
                                        if($kv['_id'] ==  $value['soldBy']){
                                            echo   $kv['firstname'] . " ".  $kv['lastname'];        
                                        }
                                    }
                                } catch (Exception $e) {
                                }
                            ?>
                           
                        </div>
                    </div>
                </div>
                
                <div class=" col-sm-12 col-md-6">
                    <div class="form-group ">
                        <label>
                            Closed By
                        </label>
                        <div class="input-group col-xs-12">
                            <?php
                                try {
                                    foreach($result['users'] as $kc=>$kv){
                                        if($kv['_id'] ==  $value['closedBy']){
                                            echo   $kv['firstname'] . " ".  $kv['lastname'];        
                                        }
                                    }
                                } catch (Exception $e) {
                                }
                            ?>
                           
                        </div>
                    </div>
                </div>
            </div>
            
            </div>

            <div class="col-xs-8" style="background: #efefef; padding: 10px;">
                   
                 <?php $apiObj->displayThingForm("adminTab", $value['adminTab'], 0, "person_0_policy_".$key."_", "Y");  ?>

               <?php
        /*
                <div class=" col-sm-12 col-md-4">
                    <div class="form-group ">
                        <label>
                            Submission Verified
                        </label>
                        <div class="input-group col-xs-12">
                            <select name="person_0_policy_<?php echo $i ?>_submissionVerified" id="person_0_policy_<?php echo $i ?>_submissionVerified" class=" form-control ">

                                <option value=""></option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                  <div class=" col-sm-12 col-md-4">
                    <div class="form-group ">
                        <label>
                             In Carrier Back Office
                        </label>
                        <div class="input-group col-xs-12">
                            <select name="person_0_policy_<?php echo $i ?>_inCarrierBackOffice" id="person_0_policy_<?php echo $i ?>_inCarrierBackOffice" class=" form-control ">

                                <option value=""></option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>
                </div>
              
                <div class=" col-sm-12 col-md-4">
                    <div class="form-group ">
                        <label>
                            Disposition
                        </label>
                        <div class="input-group col-xs-12">
                            <select name="person_0_policy_<?php echo $i ?>_adminDisposition" id="person_0_policy_<?php echo $i ?>_adminDisposition" class=" form-control ">

                                    <option value=""></option>
                                    <option value="ACTIVE">Active</option>
                                    <option value="HELD">Held In Underwriting</option>
                                    <option value="PAYMENTISSUE">Payment Issue</option>
                                    <option value="CMSPROOF">CMS Proof Needed</option>
                                    <option value="CANCELLED">Cancelled</option>
                                    <option value="NOTTAKEN">Not Taken</option>
                                    <option value="TERMINATED">Terminated</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                
                <div class=" col-sm-12 col-md-4">
                    <div class="form-group ">
                        <label>
                            Factored Amount
                        </label>
                        <div class="input-group col-xs-12">
                            <input type="text" name="person_0_policy_<?php echo $i ?>_factoredAmountMoney" id="person_0_policy_<?php echo $i ?>_factoredAmountMoney" class=" form-control ">
                        </div>
                    </div>
                </div>
                
                
                    <div class=" col-sm-12 col-md-4">
                    <div class="form-group ">
                        <label>
                             Commission Received
                        </label>
                        <div class="input-group col-xs-12">
                            <select name="person_0_policy_<?php echo $i ?>_commissionRecieved" id="person_0_policy_<?php echo $i ?>_commissionRecieved" class=" form-control ">

                                <option value=""></option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                    <div class=" col-sm-12 col-md-4">
                    <div class="form-group ">
                        <label>
                           Commission Amount
                        </label>
                        <div class="input-group col-xs-12">
                            <input type="text" name="person_0_policy_<?php echo $i ?>_commissionAmountMoney" id="person_0_policy_<?php echo $i ?>_commissionAmountMoney" class=" form-control ">
                        </div>
                    </div>
                </div>
                */
        ?>
            </div>
        </div>
    </div>
    <?php
        $i++;
    }
}
    ?>
    </div>