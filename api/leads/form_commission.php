<div class="row" id="commission_<?php echo $index?>">
	<div class="row">
		<div class="col-xs-12 text-right">
				<a onClick="removeItemCommission(<?php echo $index?>)" style="margin-top:22px" class="btn btn-warning btn-sm btn-bitbucket"><i class="fa fa-times"></i></a>
		</div>
	</div>
	<div class="row">
		<input type="hidden" id="person_0_policy_<?php echo ($index+50);?>_createThing" name="person_0_policy_<?php echo ($index+50);?>_createThing" value="N">
		<input type="hidden" id="person_0_policy_<?php echo ($index+50);?>_id" name="person_0_policy_<?php echo ($index+50);?>_id" value="">
		<input type="hidden" id="person_0_policy_<?php echo ($index+50);?>_isReceived" name="person_0_policy_<?php echo ($index+50);?>_isReceived" value="">
		<input type="hidden" name="person_0_commission_<?php echo $index;?>_createThing" value="Y">
		<input type="hidden" name="person_0_commission_<?php echo $index;?>_id" value="<?php echo $apiObj->getRandomId();?>">
		<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label>
	            	Client Name
	            	</label>
		            <div class="input-group col-xs-12">
		               	<input type="text" name="person_0_commission_<?php echo $index;?>_clientName" id="person_0_commission_<?php echo $index;?>_clientName" value="<?php echo ucwords(strtolower($result['person']['firstName'])).' '. ucwords(strtolower($result['person']['lastName']))?>" class="form-control " placeholder="">	
		            </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Policy Number 
		            </label>
	            	<div class="input-group col-xs-12">
	               		<select index="<?php echo $index;?>" name="person_0_commission_<?php echo $index;?>_policyNumber" id="person_0_commission_<?php echo $index;?>_policyNumber" class="form-control dd-policy-number">
						   <option value=""></option>
						   	<?php foreach ($result['policies'] as $key=>$item){ ?>
						  		 <option coverage-type="<?php echo $item['coverageType']?>" carrier="<?php echo $item['carrier']?>" premium-money="<?php echo $item['premiumMoney']?>" submission-date="<?php echo date("m/d/Y",strtotime($item['submissionDate'])); ?>" policy-number="<?php echo $item['policyNumber']?>" value="<?php echo $item['_id']?>"><?php echo $item['policyNumber'];?></option>
							<?php } ?>
						</select>
	            	</div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Carrier 
		            </label>
		            <div class="input-group col-xs-12">
		            	
		               	<select name="person_0_commission_<?php echo $index;?>_carrier" id="person_0_commission_<?php echo $index;?>_carrier" class=" form-control ">
						   <option value=""></option>
						   	<?php foreach ($result['carriers'] as $key=>$item){ ?>
						  		 <option value="<?php echo $item['_id']?>"><?php echo $item['name'];?></option>
							<?php } ?>
						</select>

		            </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label>
	            	Coverage Type 
	            	</label>
		            <div class="input-group col-xs-12">
		               	<select name="person_0_commission_<?php echo $index;?>_coverageType" id="person_0_commission_<?php echo $index;?>_coverageType" class=" form-control ">
						   <option value=""></option>
						   	<?php foreach ($result['carrierPlans'] as $key=>$item){ ?>
						  		 <option value="<?php echo $item['_id']?>"><?php echo $item['name'];?></option>
							<?php } ?>
						</select>
		            </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      <div class=" col-sm-12 col-md-3">
	         <div class="form-group ">
	            <label>
	            	Premium
	            </label>
	            <div class="input-group col-xs-12">
	               	<input type="text" name="person_0_commission_<?php echo $index;?>_premium"  id="person_0_commission_<?php echo $index;?>_premium" class="form-control " placeholder="">	
	            </div>
	         </div>
	      </div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Commission Rate 
		            </label>
		            <div class="input-group col-xs-12">
		               	<input type="text" name="person_0_commission_<?php echo $index;?>_commissonRate" id="person_0_commission_<?php echo $index;?>_commissonRate" class="form-control " placeholder="">	
		            </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            # Of Lives
		            </label>
	            	<div class="input-group col-xs-12">
	               		<input type="text" name="person_0_commission_<?php echo $index;?>_ofLives" id="person_0_commission_<?php echo $index;?>_ofLives" value="" class="form-control " placeholder="">	
	            	</div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label>
	            	Commission
	            	</label>
		            <div class="input-group col-xs-12">
		            	<input type="text" name="person_0_commission_<?php echo $index;?>_commission" id="person_0_commission_<?php echo $index;?>_commission" class="form-control " placeholder="">
		            </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      <div class=" col-sm-12 col-md-3">
	         <div class="form-group ">
	            <label>
	            	Received Date
	            </label>
	            <div class="input-group col-xs-12">
	               	<input type="text" name="person_0_commission_<?php echo $index;?>_receivedDate" id="person_0_commission_<?php echo $index;?>_receivedDate" class="form-control datepicker" placeholder="">	
	            </div>
	         </div>
	      </div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Advanced
		            </label>
		            <div class="input-group col-xs-12">
		               	<input type="text" name="person_0_commission_<?php echo $index;?>_advanced" id="person_0_commission_<?php echo $index;?>_advanced" class="form-control " placeholder="">	
		            </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Debit Balance
		            </label>
	            	<div class="input-group col-xs-12">
	               		<input type="text" name="person_0_commission_<?php echo $index;?>_debitBalance" id="person_0_commission_<?php echo $index;?>_debitBalance" class="form-control " placeholder="">	
	            	</div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label>
	            	Earned after Adv
	            	</label>
		            <div class="input-group col-xs-12">
		            	<input type="text" name="person_0_commission_<?php echo $index;?>_earnedAfterAdv" id="person_0_commission_<?php echo $index;?>_earnedAfterAdv" class="form-control " placeholder="">
		            </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	   		<div class=" col-sm-12 col-md-3">
		         <div class="form-group ">
		            <label>
		            	Total Earned
		            </label>
		            <div class="input-group col-xs-12">
		               	<input type="text" name="person_0_commission_<?php echo $index;?>_totalEarned" id="person_0_commission_<?php echo $index;?>_totalEarned" class="form-control " placeholder="">	
		            </div>
		         </div>
		    </div>
	   	</div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3" id="commission_month_0" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_0_label">
		            Month 1
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_0_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_0_label" id="person_0_commission_<?php echo $index;?>_months_0_label" value="Month 1"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_0_value" id="person_0_commission_<?php echo $index;?>_months_0_value" class="form-control">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_0_value2" id="person_0_commission_<?php echo $index;?>_months_0_value2" class="form-control">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_1" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_1_label">
		            Month 2
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_1_id" value="<?php echo $apiObj->getRandomId();?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_1_label" id="person_0_commission_<?php echo $index;?>_months_1_label" value="Month 2"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_1_value" id="person_0_commission_<?php echo $index;?>_months_1_value" class="form-control">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_1_value2" id="person_0_commission_<?php echo $index;?>_months_1_value2" class="form-control">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_2" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_2_label">
	            	Month 3
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_2_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_2_label" id="person_0_commission_<?php echo $index;?>_months_2_label" value="Month 3"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_2_value" id="person_0_commission_<?php echo $index;?>_months_2_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_2_value2" id="person_0_commission_<?php echo $index;?>_months_2_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_3" style="display: none">
	         	<div class="form-group">
	            	<label class="person_0_commission_<?php echo $index;?>_months_3_label">
	            	Month 4
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_3_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_3_label" id="person_0_commission_<?php echo $index;?>_months_3_label" value="Month 4"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_3_value" id="person_0_commission_<?php echo $index;?>_months_3_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_3_value2" id="person_0_commission_<?php echo $index;?>_months_3_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3" id="commission_month_4" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_4_label">
	            	Month 5
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_4_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_4_label" id="person_0_commission_<?php echo $index;?>_months_4_label" value="Month 5"/>
			        <div class="row">
			            <div class="col-xs-12  col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_4_value" id="person_0_commission_<?php echo $index;?>_months_4_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12  col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_4_value2" id="person_0_commission_<?php echo $index;?>_months_4_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_5" style="display: none">
	         <div class="form-group ">
	            <label class="person_0_commission_<?php echo $index;?>_months_5_label">
	            	Month 6
	            </label>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_5_id" value="<?php echo $apiObj->getRandomId();?>"/>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_5_label" id="person_0_commission_<?php echo $index;?>_months_5_label" value="Month 6"/>
		        <div class="row">
		            <div class="col-xs-12 col-md-5 no-padding-right">
		               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_5_value" id="person_0_commission_<?php echo $index;?>_months_5_value" class="form-control ">	
		            </div>
		            <div class="col-xs-12 col-md-6 no-padding-left">
		               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_5_value2" id="person_0_commission_<?php echo $index;?>_months_5_value2" class="form-control ">	
		            </div>
		        </div>    
	         </div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_6" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_6_label">
		            Month 7
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_6_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_6_label" id="person_0_commission_<?php echo $index;?>_months_6_label" value="Month 7"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_6_value" id="person_0_commission_<?php echo $index;?>_months_6_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_6_value2" id="person_0_commission_<?php echo $index;?>_months_6_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_7" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_7_label">
		           	Month 8
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_7_id" value="<?php echo $apiObj->getRandomId();?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_7_label" id="person_0_commission_<?php echo $index;?>_months_7_label" value="Month 8"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_7_value" id="person_0_commission_<?php echo $index;?>_months_7_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_7_value2" id="person_0_commission_<?php echo $index;?>_months_7_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
		<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3" id="commission_month_8" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_8_label">
	            	Month 9
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_8_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_8_label" id="person_0_commission_<?php echo $index;?>_months_8_label" value="Month 9"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_8_value" id="person_0_commission_<?php echo $index;?>_months_8_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_8_value2" id="person_0_commission_<?php echo $index;?>_months_8_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_9" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_9_label">
	            	Month 10
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_9_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_9_label" id="person_0_commission_<?php echo $index;?>_months_9_label" value="Month 10"/>
			        <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_9_value" id="person_0_commission_<?php echo $index;?>_months_9_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_9_value2" id="person_0_commission_<?php echo $index;?>_months_9_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group " id="commission_month_10" style="display: none">
	            	<label class="person_0_commission_<?php echo $index;?>_months_10_label">
	            	Month 11
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_10_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_10_label" id="person_0_commission_<?php echo $index;?>_months_10_label" value="Month 11"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_10_value" id="person_0_commission_<?php echo $index;?>_months_10_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_10_value2" id="person_0_commission_<?php echo $index;?>_months_10_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_11" style="display: none">
		         <div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_11_label">
		            	Month 12
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_11_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_11_label" id="person_0_commission_<?php echo $index;?>_months_11_label" value="Month 12"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_11_value" id="person_0_commission_<?php echo $index;?>_months_11_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_11_value2" id="person_0_commission_<?php echo $index;?>_months_11_value2" class="form-control ">	
			            </div>
			        </div>
		         </div>
	     	</div>
		</div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3" id="commission_month_12" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_12_label">
		            Month 13
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_12_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_12_label" id="person_0_commission_<?php echo $index;?>_months_12_label" value="Month 13"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_12_value" id="person_0_commission_<?php echo $index;?>_months_12_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_12_value2" id="person_0_commission_<?php echo $index;?>_months_12_value2" class="form-control ">	
			            </div>
			        </div>    
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_13" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_13_label">
		            Month 14
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_13_id" value="<?php echo $apiObj->getRandomId();?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_13_label" id="person_0_commission_<?php echo $index;?>_months_13_label" value="Month 14"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_13_value" id="person_0_commission_<?php echo $index;?>_months_13_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_13_value2" id="person_0_commission_<?php echo $index;?>_months_13_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_14" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_14_label">
	            	Month 15
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_14_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_14_label" id="person_0_commission_<?php echo $index;?>_months_14_label" value="Month 15"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_14_value" id="person_0_commission_<?php echo $index;?>_months_14_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_14_value2" id="person_0_commission_<?php echo $index;?>_months_14_value2" class="form-control ">	
			            </div>
			        </div>    
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_15" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_15_label">
	            	Month 16
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_15_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_15_label" id="person_0_commission_<?php echo $index;?>_months_15_label" value="Month 16"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_15_value" id="person_0_commission_<?php echo $index;?>_months_15_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_15_value2" id="person_0_commission_<?php echo $index;?>_months_15_value2" class="form-control ">	
			            </div>
			        </div>    
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3" id="commission_month_16" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_16_label">
	            	Month 17
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_16_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_16_label" id="person_0_commission_<?php echo $index;?>_months_16_label" value="Month 17"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_16_value" id="person_0_commission_<?php echo $index;?>_months_16_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_16_value2" id="person_0_commission_<?php echo $index;?>_months_16_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      <div class=" col-sm-12 col-md-3" id="commission_month_17" style="display: none">
	         <div class="form-group ">
	            <label class="person_0_commission_<?php echo $index;?>_months_17_label">
	            	Month 18
	            </label>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_17_id" value="<?php echo $apiObj->getRandomId();?>"/>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_17_label" id="person_0_commission_<?php echo $index;?>_months_17_label" value="Month 18"/>
	            <div class="row">
		            <div class="col-xs-12 col-md-5 no-padding-right">
		               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_17_value" id="person_0_commission_<?php echo $index;?>_months_17_value" class="form-control ">	
		            </div>
		            <div class="col-xs-12 col-md-6 no-padding-left">
		               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_17_value2" id="person_0_commission_<?php echo $index;?>_months_17_value2" class="form-control ">	
		            </div>
		        </div>
	         </div>
	      </div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_18" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_18_label">
		            Month 19
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_18_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_18_label" id="person_0_commission_<?php echo $index;?>_months_18_label" value="Month 19"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_18_value" id="person_0_commission_<?php echo $index;?>_months_18_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_18_value2" id="person_0_commission_<?php echo $index;?>_months_18_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_19" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_19_label">
		            Month 20
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_19_id" value="<?php echo $apiObj->getRandomId();?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_19_label" id="person_0_commission_<?php echo $index;?>_months_19_label" value="Month 20"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_19_value" id="person_0_commission_<?php echo $index;?>_months_19_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_19_value2" id="person_0_commission_<?php echo $index;?>_months_19_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3" id="commission_month_20" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_20_label">
	            	Month 21
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_20_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_20_label" id="person_0_commission_<?php echo $index;?>_months_20_label" value="Month 21"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_20_value" id="person_0_commission_<?php echo $index;?>_months_20_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_20_value2" id="person_0_commission_<?php echo $index;?>_months_20_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_21" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_21_label">
	            	Month 22
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_21_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_21_label" id="person_0_commission_<?php echo $index;?>_months_21_label" value="Month 22"/>
		            <div class="row">
			            <div class="col-xs-12  col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_21_value" id="person_0_commission_<?php echo $index;?>_months_21_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_21_value2" id="person_0_commission_<?php echo $index;?>_months_21_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_22" style="display: none">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_22_label">
	            	Month 23
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_22_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_22_label" id="person_0_commission_<?php echo $index;?>_months_22_label" value="Month 23"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_22_value" id="person_0_commission_<?php echo $index;?>_months_22_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_22_value2" id="person_0_commission_<?php echo $index;?>_months_22_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3" id="commission_month_23" style="display: none">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_23_label">
		            	Month 24
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_23_id" value="<?php echo $apiObj->getRandomId();?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_23_label" id="person_0_commission_<?php echo $index;?>_months_23_label" value="Month 24"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" type="text" name="person_0_commission_<?php echo $index;?>_months_23_value" id="person_0_commission_<?php echo $index;?>_months_23_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" type="text" name="person_0_commission_<?php echo $index;?>_months_23_value2" id="person_0_commission_<?php echo $index;?>_months_23_value2" class="form-control ">	
			            </div>
			        </div>    
	        	</div>
	      	</div>
	   </div>
	</div>
	<!-- End row -->

</div>
<hr>
<script type="text/javascript">
$(function() {
    $(".datepicker").pickadate({format: 'mm/dd/yyyy', selectYears: 100, selectMonths: true, });
});
</script>
