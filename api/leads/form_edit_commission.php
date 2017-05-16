<?php foreach ($result['comissions'] as $index => $com) { ?>

<div class="row" id="commission_<?php echo $index?>" >
	<div class="row">
		<div class="col-xs-12 text-right">
				<a onClick="removeItemCommission(<?php echo $index?>)" style="margin-top:22px" class="btn btn-warning btn-sm btn-bitbucket"><i class="fa fa-times"></i></a>
		</div>
	</div>
	<div class="row">
		<input type="hidden" id="person_0_policy_<?php echo ($index+50);?>_createThing" name="person_0_policy_<?php echo ($index+50);?>_createThing" value="<?php echo empty($com['policyNumber']) ? 'N' : 'Y'?>">
		<input type="hidden" id="person_0_policy_<?php echo ($index+50);?>_id" name="person_0_policy_<?php echo ($index+50);?>_id" value="<?php echo $com['policyNumber']?>">
		<input type="hidden" id="person_0_policy_<?php echo ($index+50);?>_isReceived" name="person_0_policy_<?php echo ($index+50);?>_isReceived" value="Y">
		<input type="hidden" name="person_0_commission_<?php echo $index;?>_createThing" value="Y">
		<input type="hidden" name="person_0_commission_<?php echo $index;?>_id" value="<?php echo $com['_id']?>">
		<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label>
	            	Client Name
	            	</label>
		            <div class="input-group col-xs-12">
		               	<input type="text" name="person_0_commission_<?php echo $index;?>_clientName" id="person_0_commission_<?php echo $index;?>_clientName" value="<?php echo $com['clientName']?>" class="form-control " placeholder="">	
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
						  		 <option <?php echo $com['policyNumber'] == $item['_id'] ? 'selected' : ''?> coverage-type="<?php echo $item['coverageType']?>" carrier="<?php echo $item['carrier']?>" premium-money="<?php echo $item['premiumMoney']?>" submission-date="<?php echo date("m/d/Y",strtotime($item['submissionDate'])); ?>" policy-number="<?php echo $item['policyNumber']?>" value="<?php echo $item['_id']?>"><?php echo $item['policyNumber'];?></option>
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
						  		 <option <?php echo $com['carrier'] == $item['_id'] ? 'selected' : ''?> value="<?php echo $item['_id']?>"><?php echo $item['name'];?></option>
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
						  		 <option <?php echo $com['coverageType'] == $item['_id'] ? 'selected' : ''?> value="<?php echo $item['_id']?>"><?php echo $item['name'];?></option>
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
	               	<input value="<?php echo $com['premium'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_premium"  id="person_0_commission_<?php echo $index;?>_premium" class="form-control " placeholder="">	
	            </div>
	         </div>
	      </div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Commission Rate 
		            </label>
		            <div class="input-group col-xs-12">
		               	<input value="<?php echo $com['commissonRate'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_commissonRate" id="person_0_commission_<?php echo $index;?>_commissonRate" class="form-control " placeholder="">	
		            </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            # Of Lives
		            </label>
	            	<div class="input-group col-xs-12">
	               		<input value="<?php echo $com['ofLives'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_ofLives" id="person_0_commission_<?php echo $index;?>_ofLives" class="form-control " placeholder="">	
	            	</div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label>
	            	Commission
	            	</label>
		            <div class="input-group col-xs-12">
		            	<input value="<?php echo $com['commission'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_commission" id="person_0_commission_<?php echo $index;?>_commission" class="form-control " placeholder="">
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
	               	<input value="<?php echo $com['receivedDate'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_receivedDate" id="person_0_commission_<?php echo $index;?>_receivedDate" class="form-control datepicker" placeholder="">	
	            </div>
	         </div>
	      </div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Advanced
		            </label>
		            <div class="input-group col-xs-12">
		               	<input value="<?php echo $com['advanced'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_advanced" id="person_0_commission_<?php echo $index;?>_advanced" class="form-control " placeholder="">	
		            </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label>
		            Debit Balance
		            </label>
	            	<div class="input-group col-xs-12">
	               		<input value="<?php echo $com['debitBalance'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_debitBalance" id="person_0_commission_<?php echo $index;?>_debitBalance" class="form-control " placeholder="">	
	            	</div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label>
	            	Earned after Adv
	            	</label>
		            <div class="input-group col-xs-12">
		            	<input value="<?php echo $com['earnedAfterAdv'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_earnedAfterAdv" id="person_0_commission_<?php echo $index;?>_earnedAfterAdv" class="form-control " placeholder="">
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
	               	<input value="<?php echo $com['totalEarned'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_totalEarned" id="person_0_commission_<?php echo $index;?>_totalEarned" class="form-control ">	
	            </div>
	         </div>
	      </div>
		</div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_0_label">
		            <?php echo $com['months'][0]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_0_id" value="<?php echo $com['months'][0]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_0_label" id="person_0_commission_<?php echo $index;?>_months_0_label" value="<?php echo $com['months'][0]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][0]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_0_value" id="person_0_commission_<?php echo $index;?>_months_0_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][0]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_0_value2" id="person_0_commission_<?php echo $index;?>_months_0_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_1_label">
		            <?php echo $com['months'][1]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_1_id" value="<?php echo $com['months'][1]['_id'] ?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_1_label" id="person_0_commission_<?php echo $index;?>_months_1_label" value="<?php echo $com['months'][1]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][1]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_1_value" id="person_0_commission_<?php echo $index;?>_months_1_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][1]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_1_value2" id="person_0_commission_<?php echo $index;?>_months_1_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_2_label">
	            	<?php echo $com['months'][2]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_2_id" value="<?php echo $com['months'][2]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_2_label" id="person_0_commission_<?php echo $index;?>_months_2_label" value="<?php echo $com['months'][2]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][2]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_2_value" id="person_0_commission_<?php echo $index;?>_months_2_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][2]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_2_value2" id="person_0_commission_<?php echo $index;?>_months_2_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group">
	            	<label class="person_0_commission_<?php echo $index;?>_months_3_label">
	            	<?php echo $com['months'][3]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_3_id" value="<?php echo $com['months'][3]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_3_label" id="person_0_commission_<?php echo $index;?>_months_3_label" value="<?php echo $com['months'][3]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][3]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_3_value" id="person_0_commission_<?php echo $index;?>_months_3_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][3]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_3_value2" id="person_0_commission_<?php echo $index;?>_months_3_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_4_label">
	            	<?php echo $com['months'][4]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_4_id" value="<?php echo $com['months'][4]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_4_label" id="person_0_commission_<?php echo $index;?>_months_4_label" value="<?php echo $com['months'][4]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][4]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_4_value" id="person_0_commission_<?php echo $index;?>_months_4_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][4]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_4_value2" id="person_0_commission_<?php echo $index;?>_months_4_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      <div class=" col-sm-12 col-md-3">
	         <div class="form-group ">
	            <label class="person_0_commission_<?php echo $index;?>_months_5_label">
	            	<?php echo $com['months'][5]['label'] ?>
	            </label>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_5_id" value="<?php echo $com['months'][5]['_id'] ?>"/>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_5_label" id="person_0_commission_<?php echo $index;?>_months_5_label" value="<?php echo $com['months'][5]['label'] ?>"/>
		        <div class="row">
		            <div class="col-xs-12 col-md-5 no-padding-right">
		               	<input placeholder="Received" value="<?php echo $com['months'][5]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_5_value" id="person_0_commission_<?php echo $index;?>_months_5_value" class="form-control ">	
		            </div>
		            <div class="col-xs-12 col-md-6 no-padding-left">
		               	<input placeholder="Future commissions" value="<?php echo $com['months'][5]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_5_value2" id="person_0_commission_<?php echo $index;?>_months_5_value2" class="form-control ">	
		            </div>
		        </div>    
	         </div>
	      </div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_6_label">
		            <?php echo $com['months'][6]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_6_id" value="<?php echo $com['months'][6]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_6_label" id="person_0_commission_<?php echo $index;?>_months_6_label" value="<?php echo $com['months'][6]['label'] ?>"/>
			        <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][6]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_6_value" id="person_0_commission_<?php echo $index;?>_months_6_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][6]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_6_value2" id="person_0_commission_<?php echo $index;?>_months_6_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_7_label">
		           	<?php echo $com['months'][7]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_7_id" value="<?php echo $com['months'][7]['_id'] ?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_7_label" id="person_0_commission_<?php echo $index;?>_months_7_label" value="<?php echo $com['months'][7]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][7]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_7_value" id="person_0_commission_<?php echo $index;?>_months_7_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][7]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_7_value2" id="person_0_commission_<?php echo $index;?>_months_7_value2" class="form-control ">	
			            </div>
			        </div>    
	         	</div>
	      	</div>
	   </div>
	</div>
	<!-- End row -->
	<div class="row">
		<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_8_label">
	            	<?php echo $com['months'][8]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_8_id" value="<?php echo $com['months'][8]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_8_label" id="person_0_commission_<?php echo $index;?>_months_8_label" value="<?php echo $com['months'][8]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][8]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_8_value" id="person_0_commission_<?php echo $index;?>_months_8_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][8]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_8_value2" id="person_0_commission_<?php echo $index;?>_months_8_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_9_label">
	            	<?php echo $com['months'][9]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_9_id" value="<?php echo $com['months'][9]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_9_label" id="person_0_commission_<?php echo $index;?>_months_9_label" value="<?php echo $com['months'][9]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][9]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_9_value" id="person_0_commission_<?php echo $index;?>_months_9_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][9]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_9_value2" id="person_0_commission_<?php echo $index;?>_months_9_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_10_label">
	            	<?php echo $com['months'][10]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_10_id" value="<?php echo $com['months'][10]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_10_label" id="person_0_commission_<?php echo $index;?>_months_10_label" value="<?php echo $com['months'][10]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][10]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_10_value" id="person_0_commission_<?php echo $index;?>_months_10_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][10]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_10_value2" id="person_0_commission_<?php echo $index;?>_months_10_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
		         <div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_11_label">
		            	<?php echo $com['months'][11]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_11_id" value="<?php echo $com['months'][11]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_11_label" id="person_0_commission_<?php echo $index;?>_months_11_label" value="<?php echo $com['months'][11]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][11]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_11_value" id="person_0_commission_<?php echo $index;?>_months_11_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][11]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_11_value2" id="person_0_commission_<?php echo $index;?>_months_11_value2" class="form-control ">	
			            </div>
			        </div>
		         </div>
		    </div>
		</div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_12_label">
		            <?php echo $com['months'][12]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_12_id" value="<?php echo $com['months'][12]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_12_label" id="person_0_commission_<?php echo $index;?>_months_12_label" value="<?php echo $com['months'][12]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][12]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_12_value" id="person_0_commission_<?php echo $index;?>_months_12_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][12]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_12_value2" id="person_0_commission_<?php echo $index;?>_months_12_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_13_label">
		            <?php echo $com['months'][13]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_13_id" value="<?php echo $com['months'][13]['_id'] ?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_13_label" id="person_0_commission_<?php echo $index;?>_months_13_label" value="<?php echo $com['months'][13]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Future commissions" placeholder="Received" value="<?php echo $com['months'][13]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_13_value" id="person_0_commission_<?php echo $index;?>_months_13_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][13]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_13_value2" id="person_0_commission_<?php echo $index;?>_months_13_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_14_label">
	            	<?php echo $com['months'][14]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_14_id" value="<?php echo $com['months'][14]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_14_label" id="person_0_commission_<?php echo $index;?>_months_14_label" value="<?php echo $com['months'][14]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][14]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_14_value" id="person_0_commission_<?php echo $index;?>_months_14_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][14]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_14_value2" id="person_0_commission_<?php echo $index;?>_months_14_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_15_label">
	            	<?php echo $com['months'][15]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_15_id" value="<?php echo $com['months'][15]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_15_label" id="person_0_commission_<?php echo $index;?>_months_15_label" value="<?php echo $com['months'][15]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][15]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_15_value" id="person_0_commission_<?php echo $index;?>_months_15_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][15]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_15_value2" id="person_0_commission_<?php echo $index;?>_months_15_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
	   	<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_16_label">
	            	<?php echo $com['months'][16]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_16_id" value="<?php echo $com['months'][16]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_16_label" id="person_0_commission_<?php echo $index;?>_months_16_label" value="<?php echo $com['months'][16]['label'] ?>"/>
			        <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][16]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_16_value" id="person_0_commission_<?php echo $index;?>_months_16_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][16]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_16_value2" id="person_0_commission_<?php echo $index;?>_months_16_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      <div class=" col-sm-12 col-md-3">
	         <div class="form-group ">
	            <label class="person_0_commission_<?php echo $index;?>_months_17_label">
	            	<?php echo $com['months'][17]['label'] ?>
	            </label>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_17_id" value="<?php echo $com['months'][17]['_id'] ?>"/>
	            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_17_label" id="person_0_commission_<?php echo $index;?>_months_17_label" value="<?php echo $com['months'][17]['label'] ?>"/>
	            <div class="row">
		            <div class="col-xs-12 col-md-5 no-padding-right">
		               	<input placeholder="Received" value="<?php echo $com['months'][17]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_17_value" id="person_0_commission_<?php echo $index;?>_months_17_value" class="form-control ">	
		            </div>
		            <div class="col-xs-12 col-md-6 no-padding-left">
		               	<input placeholder="Future commissions" value="<?php echo $com['months'][17]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_17_value2" id="person_0_commission_<?php echo $index;?>_months_17_value2" class="form-control ">	
		            </div>
		        </div>
	         </div>
	      </div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_18_label">
		            <?php echo $com['months'][18]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_18_id" value="<?php echo $com['months'][18]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_18_label" id="person_0_commission_<?php echo $index;?>_months_18_label" value="<?php echo $com['months'][18]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][18]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_18_value" id="person_0_commission_<?php echo $index;?>_months_18_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][18]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_18_value2" id="person_0_commission_<?php echo $index;?>_months_18_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_19_label">
		            <?php echo $com['months'][19]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_19_id" value="<?php echo $com['months'][19]['_id'] ?>"/>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_19_label" id="person_0_commission_<?php echo $index;?>_months_19_label" value="<?php echo $com['months'][19]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][19]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_19_value" id="person_0_commission_<?php echo $index;?>_months_19_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][19]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_19_value2" id="person_0_commission_<?php echo $index;?>_months_19_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	   </div>
	</div>
	<div class="row">
		<div class="col-sm-12">
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_20_label">
	            	<?php echo $com['months'][20]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_20_id" value="<?php echo $com['months'][20]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_20_label" id="person_0_commission_<?php echo $index;?>_months_20_label" value="<?php echo $com['months'][20]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-1 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][20]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_20_value" id="person_0_commission_<?php echo $index;?>_months_20_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][20]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_20_value2" id="person_0_commission_<?php echo $index;?>_months_20_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group">
	            	<label class="person_0_commission_<?php echo $index;?>_months_21_label">
	            	<?php echo $com['months'][21]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_21_id" value="<?php echo $com['months'][21]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_21_label" id="person_0_commission_<?php echo $index;?>_months_21_label" value="<?php echo $com['months'][21]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][21]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_21_value" id="person_0_commission_<?php echo $index;?>_months_21_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][21]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_21_value2" id="person_0_commission_<?php echo $index;?>_months_21_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
	            	<label class="person_0_commission_<?php echo $index;?>_months_22_label">
	            	<?php echo $com['months'][22]['label'] ?>
	            	</label>
	            	<input type="hidden" name="person_0_commission_<?php echo $index;?>_months_22_id" value="<?php echo $com['months'][22]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_22_label" id="person_0_commission_<?php echo $index;?>_months_22_label" value="<?php echo $com['months'][22]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][22]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_22_value" id="person_0_commission_<?php echo $index;?>_months_22_value" class="form-control ">	
			            </div>
			            <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][22]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_22_value2" id="person_0_commission_<?php echo $index;?>_months_22_value2" class="form-control ">	
			            </div>
			        </div>
	         	</div>
	      	</div>
	      	<div class=" col-sm-12 col-md-3">
	         	<div class="form-group ">
		            <label class="person_0_commission_<?php echo $index;?>_months_23_label">
		            	<?php echo $com['months'][23]['label'] ?>
		            </label>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_23_id" value="<?php echo $com['months'][23]['_id'] ?>"/>
		            <input type="hidden" name="person_0_commission_<?php echo $index;?>_months_23_label" id="person_0_commission_<?php echo $index;?>_months_23_label" value="<?php echo $com['months'][23]['label'] ?>"/>
		            <div class="row">
			            <div class="col-xs-12 col-md-5 no-padding-right">
			               	<input placeholder="Received" value="<?php echo $com['months'][23]['value'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_23_value" id="person_0_commission_<?php echo $index;?>_months_23_value" class="form-control ">	
			            </div>
			             <div class="col-xs-12 col-md-6 no-padding-left">
			               	<input placeholder="Future commissions" value="<?php echo $com['months'][23]['value2'] ?>" type="text" name="person_0_commission_<?php echo $index;?>_months_23_value2" id="person_0_commission_<?php echo $index;?>_months_23_value2" class="form-control ">	
			            </div>
			        </div>
	        	</div>
	      	</div>
		</div>
	</div>
	<hr>
</div>

<script type="text/javascript">
$(function() {
    $(".datepicker").pickadate({format: 'mm/dd/yyyy', selectYears: 100, selectMonths: true, });
});
</script>

<?php } ?>