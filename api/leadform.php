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

							<li role="presentation"><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab" style="display:none">Admin</a></li>

							<li role="presentation"><a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">Attachments</a></li>

							<li role="presentation"><a href="#history" aria-controls="history" role="tab" data-toggle="tab">History</a></li>
							<li role="presentation"><a href="#commission" aria-controls="commission" role="tab" data-toggle="tab">Commission</a></li>
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

								<?php createPartial("incomeSources", "Other Sources Of Income - Non Employment",  "N", "person_0_", $result, $apiObj); ?>

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
												<div class="col-lg-4 col-xs-4">
													<iframe style="border: 0; width: 100%; height: 500px" src="https://quotenatgen.ngic.com/agent/234572"></iframe>
													</br>
													</br>
													</br>
													</br>
													<iframe style="border: 0; width: 100%; height: 500px" src="http://www.1enrollment.com/index.cfm?id=161786"></iframe>
													<!-- <iframe  style="border: 0; width: 100%; height: 500px" src="https://enroll.revolutioninsure.net/UserAccount/Login?ReturnUrl=%2FMultiQuote%2FReviewQuote%2FCreateQuote"></iframe> -->
													

												</div>
												<div class="col-lg-8 col-xs-8">
													<iframe id="iframe-healthsherpa" style="border: 0; width: 100%; height: 800px" src="<?php echo $iframe_url;?>"></iframe>

												</div>
												<div class="row">
													<div class="col-xs-12">
														<div id="assurantSubmitList" style="display:none">Getting Quote</div>
													</div>
												</div>

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

						  	<div role="tabpanel" class="tab-pane" id="emails" >
							  <div class="ibox-content m-b-sm border-bottom">
									<div class="row">
										<div class="col-sm-12" style="display:none">
												<label>Check 24HourMail: </label>
									<?php
                                    $emailsfound = false;
									if(!empty($result['emails'][0])){
                                        $pos = strpos(strtolower($result['emails'][0]['email']), "24hourmail");
                                        if ($pos === false) {
                                        } else {
                                        $emailsfound = true;
                                        echo "<p><a href='http://24hourmail.net/lookup.php?email=".trim($result['emails'][0]['email'])."' target='_blank'>".$result['emails'][0]['email']."</a></P>";
                                        }
									}
								   	if(!empty($result['emails'][1])){
                                        $pos = strpos(strtolower($result['emails'][1]['email']), "24hourmail");
                                        if ($pos === false) {
                                        } else {
                                        $emailsfound = true;
                                        echo "<p><a href='http://24hourmail.net/lookup.php?email=".trim($result['emails'][1]['email'])."' target='_blank'>".$result['emails'][1]['email']."</a></P>";
                                        }
									}
                                    	if(!empty($result['emails'][2])){
                                        $pos = strpos(strtolower($result['emails'][2]['email']), "24hourmail");
                                        if ($pos === false) {
                                        } else {
                                        $emailsfound = true;
                                        echo "<p><a href='http://24hourmail.net/lookup.php?email=".trim($result['emails'][2]['email'])."' target='_blank'>".$result['emails'][2]['email']."</a></P>";
                                        }
									}
                                    if($emailsfound === false){
                                            echo "<P>No Emails at this time</p>";
                                    }
									?>
                                        	</div>
                                           	</div>
                                            								</div>

								<div id="foundEmails">
								</div>

							</div>

							<div role="tabpanel" class="tab-pane" id="history">

								<div class="ibox float-e-margins">

									<div id="recordingDiv" class="ibox float-e-margins">

										<div class="ibox-title">

											<h4>Recordings</h4>

										</div>

										<div class="ibox-content">

											<a target="_blank" href="#recordings/view/<?php echo $result['leads'][0]['id'];?>">Get Recordings</a>

										</div>

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

							<div role="tabpanel" class="tab-pane" id="attachments">

								<div class="ibox float-e-margins">

									<div class="ibox float-e-margins">

										<div class="ibox-title">

											<div class="row">

												<div class="col-xs-11 text-left">

													<h4>Attachments</h4>

												</div>

												<div class="col-xs-1  text-center">

												</div>

											</div>

										</div>

										<div class="ibox-content" id="<?php echo $type;?>Item_<?php echo $index;?>">

											<div class="row" id="attachmentsDiv">

												<?php

                                                    try {

                                                        include ("./pelican.php");

                                                    } catch (Exception $e) {

												    }

												?>

											</div>

										</div>

									</div>

								</div>

							</div>

							<div role="tabpanel" class="tab-pane" id="admin">

								<div class="ibox float-e-margins">

									<div class="ibox float-e-margins">

										<div class="ibox-title">

											<div class="row">

												<div class="col-xs-11 text-left">

													<h4>Admin Tab</h4>

												</div>

												<div class="col-xs-1  text-center">

												</div>

											</div>

										</div>

										<div class="ibox-content" id="<?php echo $type;?>Item_<?php echo $index;?>">

											<div class="row" id="adminDiv">

												<?php

                                                    try {

                                                        //include ("./admin.php");

                                                    } catch (Exception $e) {

												    }

												?>

											</div>

										</div>

									</div>

								</div>

							</div>

							<!-- Commission tab -->
							<div role="tabpanel" class="tab-pane" id="commission">

								<div class="ibox float-e-margins">

									<div class="ibox float-e-margins">

										<div class="ibox-title">

											<div class="row">

												<div class="col-xs-11 text-left">

													<h4>Commission Tab</h4>

												</div>

												<div class="col-xs-1  text-center">

													<a id="newItemButton" class="btn btn-info btn-sm btn-bitbucket" onClick="addItemCommission()"><i class="fa fa-plus"></i></a>

												</div>

											</div>

										</div>

										<div class="ibox-content" id="commission-content">

											<!-- <div class="row" id="commissionDiv">
												<div class="row">
													<div class="col-xs-12 text-right">
															<a onClick="removeItem('<?php echo $type;?>', <?php echo $index;?>, '<?php echo $data['_id'];?>', '<?php echo $createThing;?>')" style="margin-top:22px" class="btn btn-warning btn-sm btn-bitbucket"><i class="fa fa-times"></i></a>
													</div>
												</div>
												<div class="row">
												   	<div class="col-sm-12">
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
												            	<label>
												            	Client Name
												            	</label>
													            <div class="input-group col-xs-12">
													               	<input type="text" name="" id="" value="Dimitri Jackson" class="form-control " required="true" placeholder="">	
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
													            <label>
													            Carrier 
													            </label>
													            <div class="input-group col-xs-12">
													               	<select name="person_0_policy_0_carrier" id="person_0_policy_0_carrier" class=" form-control ">
																	   <option value=""></option>
																	   <option value="ArcmrtyW-dtzzIWnM-iPIQZHTn">AIG</option>
																	   <option value="FGNjkEft-n1r3SpZe-WA5tDLYi">AM Better Health</option>
																	   <option value="g0416lwZ-S6PBlreM-48IS6BRO">Aetna</option>
																	   <option value="20161118082431-oUKLx5cX-dtt2gA2I">Aliera Medical Share</option>
																	   <option value="nTn3nGz5-670fFb0m-4R1GN2mD">Anthem BCBS</option>
																	   <option value="Qiq3PkVh-alqqsuz0-Il4lL80o">Assurant</option>
																	   <option value="ZwhY99PJ-0LwdbcWt-jOBS8yuC" selected="">BCBS</option>
																	   <option value="20161126080955-QoUmnz4t-OPOudX0P">CBL</option>
																	   <option value="URR6sxdK-iMtjVGL9-Lw9Lx240">CUL First Choice</option>
																	   <option value="PxTOd2bE-8C2QXb2Z-8d6VksG2">Carrington Dental</option>
																	   <option value="4IXcu1Pv-JjtmDxIU-cH4CAMBb">Carrington PPO Network</option>
																	   <option value="crol7s9b-q8sJAzhp-e1D3jgDO">Cigna</option>
																	   <option value="20161110080839-XbMVtHhC-LGM8JK55">Community Health</option>
																	   <option value="mr95O4Tb-UiuIqClj-e7q9Fxdy">Coventry One</option>
																	   <option value="k2wAEgVK-3bCgluZG-GACE5DkI">HII</option>
																	   <option value="Hs90K9S8-QSy9DYEL-FcP2exOc">HII USA+ Dental</option>
																	   <option value="upiJfOac-GiuhR8BC-Kvxve2ET">Humana</option>
																	   <option value="DjK9WADE-k1Miaodg-HjMYjVzZ">Humana One</option>
																	   <option value="20161108074009-TygFuoE2-RiaZvwGe">Insurance Concierge</option>
																	   <option value="20161126134048-uZ8Fb2s8-Jah6KLhx">Kaiser Permanente </option>
																	   <option value="20161121062458-qYdIMX2r-qp4QN4bt">Medi-Share</option>
																	   <option value="I1GfsLmd-mHF6CRrf-Wb195wZn">Molina</option>
																	   <option value="GsxxWpJ1-JZ9rZmHi-4mZ4xhlA">Mutual of Omaha</option>
																	   <option value="20161118082509-Kb1NOeIE-6SNHrXf8">NHIC Accident / Critical</option>
																	   <option value="newfhbj-kwenrMNkem-kwerklnr">Other - Non Appointed</option>
																	   <option value="2QxrLkUw-CKYkbOgh-GQgYagt9">SLAICO</option>
																	   <option value="3o89cwjr-or68CSQQ-SRJzFoKz">Team Corp</option>
																	   <option value="uMeefiPy-6krKIbIe-HucpEtXY">United Health</option>
																	   <option value="20161126080941-ZDg9A2Um-VFykhMCr">VBA</option>
																	   <option value="20161209134743-tmNKqpaY-XnVlK2WY">Well MEC</option>
																	</select>
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
													            <label>
													            Policy Number 
													            </label>
												            	<div class="input-group col-xs-12">
												               		<input type="text" name="" id="" value="	YMCE288260" class="form-control " required="true" placeholder="">	
												            	</div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
												            	<label>
												            	Coverage Type 
												            	</label>
													            <div class="input-group col-xs-12">
													               	<select name="person_0_policy_0_coverageType" id="person_0_policy_0_coverageType" class=" form-control ">
																	   <option value=""></option>
																	   <option value="20161110065942-KRoFo8Kn-RHJN9wz7">AD &amp; D</option>
																	   <option value="uvxXbO2Q-9Tz7FV8R-pbWJngfC">Accident Fixed Benefit</option>
																	   <option value="i6dS31jr-eMtMQUuW-26eIYjXy">Accident Medical Expense - AME</option>
																	   <option value="ywwUsZ5n-K7LGsY5E-RbATFPki">Cancer</option>
																	   <option value="wbjamL5n-YUfuQ0EU-RfAlLPqB">Cancer, Heart, Stroke</option>
																	   <option value="qoMv2tB5-9VRWK0eM-ucwshg4h">Critical Illness</option>
																	   <option value="8xtTZM6S-jnt7wUNu-B7MnKB96">Dental</option>
																	   <option value="TWfTfAOT-fPs8WuQL-Gx5P2A1G">Fixed Benefit</option>
																	   <option value="s7isrhI7-IVijUCL7-Z6Kp0jvo">Heart and Stroke</option>
																	   <option value="20161110070009-VIaCjMGj-OAtKO6HG">Insurance Concierge</option>
																	   <option value="XH0kJdkS-hdazPg1c-iFca8xMV">Life</option>
																	   <option value="20161121062531-2HrFtoN7-Trkp1P0G">MEC</option>
																	   <option value="NNFLei-Mkjie83-Opejr93f" selected="">Major Medical</option>
																	   <option value="20161121062523-8Bo6nRuD-b7ncCVR8">Medicare</option>
																	   <option value="dwcHePIe-TY52sQGp-VIZ842HX">Pet Discount Plan</option>
																	   <option value="q6IpwCcd-G72STAKN-0LH8J5J5">Short Term</option>
																	   <option value="BWarSzsB-kmQTzAE2-9X06UrC4">Vision</option>
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
												               	<input type="text" name="" id="" value="Dimitri Jackson" class="form-control " required="true" placeholder="">	
												            </div>
												         </div>
												      </div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
													            <label>
													            Commission Rate 
													            </label>
													            <div class="input-group col-xs-12">
													               	<input type="text" name="" id="" value="Dimitri Jackson" class="form-control " required="true" placeholder="">	
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
													            <label>
													            # Of Lives
													            </label>
												            	<div class="input-group col-xs-12">
												               		<input type="text" name="" id="" value="	YMCE288260" class="form-control " required="true" placeholder="">	
												            	</div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
												            	<label>
												            	Commission
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="	YMCE288260" class="form-control " required="true" placeholder="">
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
												               	<input type="text" name="" id="" value="Dimitri Jackson" class="form-control " required="true" placeholder="">	
												            </div>
												         </div>
												      </div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
													            <label>
													            Advanced
													            </label>
													            <div class="input-group col-xs-12">
													               	<input type="text" name="" id="" value="Dimitri Jackson" class="form-control " required="true" placeholder="">	
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
													            <label>
													            Debit Balance
													            </label>
												            	<div class="input-group col-xs-12">
												               		<input type="text" name="" id="" value="	YMCE288260" class="form-control " required="true" placeholder="">	
												            	</div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-3">
												         	<div class="form-group ">
												            	<label>
												            	Earned after Adv
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="	YMCE288260" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												   </div>
												</div>
												<div class="row">
												   	<div class="col-sm-12">
												      <div class=" col-sm-12 col-md-2">
												         <div class="form-group ">
												            <label>
												            	Total Earned
												            </label>
												            <div class="input-group col-xs-12">
												               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            </div>
												         </div>
												      </div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Aug-16
													            </label>
													            <div class="input-group col-xs-12">
													               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Oct-16
													            </label>
												            	<div class="input-group col-xs-12">
												               		<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            	</div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Nov-16
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Dec-16
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Feb-17
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												   </div>
												</div>
												<div class="row">
												   	<div class="col-sm-12">
												      <div class=" col-sm-12 col-md-2">
												         <div class="form-group ">
												            <label>
												            	Mar-17
												            </label>
												            <div class="input-group col-xs-12">
												               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            </div>
												         </div>
												      </div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Apr-17
													            </label>
													            <div class="input-group col-xs-12">
													               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Jun-17
													            </label>
												            	<div class="input-group col-xs-12">
												               		<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            	</div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Jul-17
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Aug-17
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Sep-17
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												   </div>
												</div>
												<div class="row">
												   	<div class="col-sm-12">
												      <div class=" col-sm-12 col-md-2">
												         <div class="form-group ">
												            <label>
												            	Oct-17
												            </label>
												            <div class="input-group col-xs-12">
												               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            </div>
												         </div>
												      </div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Dec-17
													            </label>
													            <div class="input-group col-xs-12">
													               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Feb-18
													            </label>
												            	<div class="input-group col-xs-12">
												               		<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            	</div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Mar-18
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Apr-18
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	May-18
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												   </div>
												</div>
												<div class="row">
												   	<div class="col-sm-12">
												      <div class=" col-sm-12 col-md-2">
												         <div class="form-group ">
												            <label>
												            	Oct-17
												            </label>
												            <div class="input-group col-xs-12">
												               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            </div>
												         </div>
												      </div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Dec-17
													            </label>
													            <div class="input-group col-xs-12">
													               	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
													            <label>
													            Feb-18
													            </label>
												            	<div class="input-group col-xs-12">
												               		<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">	
												            	</div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Mar-18
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	Apr-18
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												      	<div class=" col-sm-12 col-md-2">
												         	<div class="form-group ">
												            	<label>
												            	May-18
												            	</label>
													            <div class="input-group col-xs-12">
													            	<input type="text" name="" id="" value="" class="form-control " required="true" placeholder="">
													            </div>
												         	</div>
												      	</div>
												   </div>
												</div>
											</div>
											<hr> -->

										</div>

									</div>

								</div>

							</div>
							<!-- End commission tab -->

						</div>

						<div style="padding-top:30px; padding-bottom: 30px;">

							<div class="row">

								<div class="col-xs-10">

									<button id="saveButton" class="btn btn-primary" type="submit">Save Lead</button>

								</div>

								<div class="col-xs-2 text-right" style="display:none">

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



<div class="modal inmodal" id="assurantDirectLinkModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">

	<div class="modal-dialog modal-lg" style="width: 80%">

		<div class="modal-content animated bounceInRight">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>

				<i class="fa fa-medkit modal-icon"></i>

				<h4 class="modal-title">Assurant Direct Link</h4>

			</div>

			<div class="modal-body" id="assurantDirectLinkModalList">

				Selecting Agents Available.

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>



<div class="modal inmodal" id="quoteModalMajor" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">

	<div class="modal-dialog modal-lg" style="width: 80%">

		<div class="modal-content animated bounceInRight">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>

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

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>

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

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>

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

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>

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

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>

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

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>

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

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span class="sr-only">Close</span></button>

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





            $(document).ready(function() {

                $('#person_0_addresses_0_state').change(function() {

                    $.ajax({

                        url: "<?php echo $settings['base_uri'];?>api/assurant/assurrantlink/" + $(this).val(),

                        type: 'GET',

                        success: function(result) {

                            $("#assurantDirectLink").empty().append(result);

                            toastr.success('Assurant Link Ready', 'Server Response');

                        }

                    });

                });



                if ( $('#person_0_addresses_0_state').length ) {

                    if( $('#person_0_addresses_0_state').val() ) {

                            $.ajax({

                                url: "<?php echo $settings['base_uri'];?>api/assurant/assurrantlink/" + $('#person_0_addresses_0_state').val(),

                                type: 'GET',

                                success: function(result) {

                                    $("#assurantDirectLink").empty().append(result);

                                }

                            });

                    }

                }



            });







    function assurantLinkCreator() {

        var assruantState = "NONE";

         if ( $('#person_0_addresses_0_state').length ) {

            if( $('#person_0_addresses_0_state').val() ) {

                assruantState = $('#person_0_addresses_0_state').val();

            }

        }

		$('.modal').hide();

		$("#assurantDirectLinkModalList").html("<P>Getting Quote....</P>");

		$('#assurantDirectLinkModal').modal('show');

		$.ajax({

			url: "<?php echo $settings['base_uri'];?>api/assurant/agentList/"+ assruantState,

			type: 'POST',

			data: $("#leadform").serialize(),

			success: function(result) {

				$("#assurantDirectLinkModalList").html(result);

			}

		});



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

				
				toastr.success('Save Successful', 'Server Response');

			}

		});

	}

	function submitAssurant() {

		$('.modal').hide();

		$("#assurantSubmitList").html("<P>Getting Quote...<P>");

		$("#assurantSubmitList").show();

        console.log($("#leadform").serialize());

		$.ajax({

			url: "<?php echo $settings['base_uri'];?>api/assurant/",

			type: 'POST',

			data: $("#leadform").serialize(),

			success: function(result) {

				$("#assurantSubmitList").html(result);

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
				document.getElementById('iframe-healthsherpa').src = 'http://news.zing.vn';
				//$("#results").empty().append(result);

				//console.log("done");

				toastr.success('Save Successful', 'Server Response');

			}

		});

	}

</script>

<script>
        $('#uniMod').load('api/twilio/smsModal?parentId=<?php echo $personId;?>');           

	$(document).on("keypress", 'form', function(e) {

		if ($("textarea").is(":focus")) {} else {

			var code = e.keyCode || e.which;

			if (code == 13) {

				e.preventDefault();

				return false;

			}

		}

	});
	var pos = parseInt("<?php echo count($result['comissions'])?>");
		
	function addItemCommission(){
		addCommisson();
		pos ++;
	}

	loadCommissions();

	function loadCommissions(){
		$.ajax({

			url: "<?php echo $settings['base_uri'];?>api/leads/load_commision/<?php echo $personId;?>",

			type: 'GET',

			// data: $("#leadform").serialize(),

			success: function(result) {
				$("#commission-content").append(result);
				
			}

		});
	}

	function removeItemCommission(indx){
		$("#commission_"+indx).css('display', 'none');
		// $("#commission_"+indx).append('<input type="hidden" name="person_0_commission_' + indx + '_createThing"')
		$("#commission_"+indx).append("<input type='hidden' name='person_0_commission_" + indx + "_deleteThing' value='Y'>");
		// "<input type='hidden' name='person_0_" + type + "_" + indx + "_deleteThing' value='Y'>"
		// alert(index);
		$("#person_0_policy_"+(parseInt(indx) + 50)+"_isReceived").val('');
	}

	function addCommisson(){
		$.ajax({

			url: "<?php echo $settings['base_uri'];?>api/leads/commision/<?php echo $personId;?>/" + pos,

			type: 'GET',

			// data: $("#leadform").serialize(),

			success: function(result) {
				$("#commission-content").append(result);
				
			}

		});
	}
	$(document).ready(function() {

		Date.prototype.monthNames = [
		    "January", "February", "March",
		    "April", "May", "June",
		    "July", "August", "September",
		    "October", "November", "December"
		];

		Date.prototype.getMonthName = function() {
		    return this.monthNames[this.getMonth()];
		};
		Date.prototype.getShortMonthName = function () {
		    return this.getMonthName().substr(0, 3);
		};

		function randomIntFromInterval()
		{
		    return Math.floor(Math.random()*(999-100+1)+100);
		}

		$(document).on('change', '.dd-policy-number', function(){
			var policyId = $(this).val();
			var index = $(this).attr('index');
			// $("#person_0_policy_"+(parseInt(index) + 50)+"_id").val(policyId);
			// alert(parseInt(index) + 50);
			// var 
			if(policyId === '')
				$("#person_0_policy_"+(parseInt(index) + 50)+"_createThing").val('N');
			else{
				$("#person_0_policy_"+(parseInt(index) + 50)+"_createThing").val('Y');

				var polNumber = randomIntFromInterval();
			}
			$("#commission_"+index).append("<input type='hidden' name='person_0_policy_" + polNumber + "_createThing' value='Y'>");
			$("#commission_"+index).append("<input type='hidden' name='person_0_policy_" + polNumber + "_id' value='"+$("#person_0_policy_"+(parseInt(index) + 50)+"_id").val()+"'>");
			$("#commission_"+index).append("<input type='hidden' name='person_0_policy_" + polNumber + "_isReceived' value='N'>");
			
			$("#person_0_policy_"+(parseInt(index) + 50)+"_id").val(policyId);
			$("#person_0_policy_"+(parseInt(index) + 50)+"_isReceived").val('Y');
			
	       	var premiumMoney = $('option:selected', this).attr('premium-money');
	       	var carrier = $('option:selected', this).attr('carrier');
	       	var coverageType = $('option:selected', this).attr('coverage-type');
	       	var submissionDate = $('option:selected', this).attr('submission-date');
	        $("#person_0_commission_"+index+"_premium").val(premiumMoney);
	        $("#person_0_commission_"+index+"_carrier").val(carrier);
	        $("#person_0_commission_"+index+"_coverageType").val(coverageType);

	        var d = new Date(submissionDate);
	        
	        for (var i = 0; i < 24; i++) {
	        	var label = d.getShortMonthName() + " " + d.getFullYear();
	        	$(".person_0_commission_"+index+"_months_"+i+"_label").html(label);
	        	$("#person_0_commission_"+index+"_months_"+i+"_label").val(label);
	        	d.setMonth(d.getMonth() + 1);

	        	$("#commission_month_"+i).css("display", "block");
	        };
	    });

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
					console.log(result);
					document.getElementById('iframe-healthsherpa').src = result;
				

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