<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
			<form id="appointform" role="form" name="appointform">
				<input type="text" value="<?php echo $_SESSION['api']['user']['_id'];?>" style="display:none;" name="person_0_appointment_0_invitesAccepted_0_agentId">
				<div class="row">
					<div class="col-md-8">
						<label>Date</label>
						<input type="text" name="person_0_appointment_0_day" data-mask="99/99/9999" class="datepicker form-control" required>
					</div>
					<div class="col-md-4">
						<label>Time</label>
						<input type="text" name="person_0_appointment_0_time" data-mask="99:99" class="timepicker form-control" required>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-8">
						<label>Person</label>
						<?php if(!empty($_GET['_parentId'])){?>
						<input id="personId" name="person_0_id" style="display:none;" value="<?php echo $_GET['_parentId'];?>">
						<?php 
															 $apiObj->mongoSetCollection('addresses');
															 $addressQuery = array('_parentId'=>$personCursor['_id']);
															 $addressCursor = $apiObj->mongoFindOne($addressQuery);
															 if(!empty($_GET['_parentId'])){if($_GET['_parentId'] == $personCursor['_id']){
						?>
						<p class="form-control close-modal"><?php echo $personCursor['firstName']. ' ' . $personCursor['lastName']; ?> <?php if(!empty($addressCursor['state'])){echo 'of '.$addressCursor['state'];}?></p>
						<?php }
																						   }
															}else{?>
						<input type="text" id="personIdDisplay" class="form-control autocomplete" >
						<input type="text" id="personId" style="display:none;" name="person_0_id">
						<?php }?>
					</div>
					<div class="col-md-4">
						<label>Label</label> 
						<select id="appointmentLabel" name="person_0_appointment_0_title" class="form-control" required>
							<option value="CALLBACK">Callback</option>
							<option value="FOLLOWUP">Follow Up</option>
							<option value="SUBMIT">Submit Policy</option>
							<option value="PERSONAL">Personal Reminder</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label>Attach People to Appointment</label> 
						<select multiple class="chosen-select" name="person_0_appointment_0_invitesPending">
							<?php 
							$apiObj->mongoSetCollection('user');
							$userCursor = $apiObj->mongoFind();
							foreach($userCursor as $user){
								if($user['_id'] != $_SESSION['api']['user']['_id']){
							?>
							<option value="<?php echo $user['_id'];?>"><?php echo $user['firstName'].' '.$user['lastName'].$user['firstname'].' '.$user['lastname'];?></option>
							<?php
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group"><label>Notes</label> <textarea rows="5" name="person_0_appointment_0_notes" class="form-control" ng-model="appointment.person_0_appointment_0_notes"></textarea></div>
				<div>
					<a id="appointSubmit" class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit">Set</a>
				</div>
			</form>
		</div>
	</div>
</div>