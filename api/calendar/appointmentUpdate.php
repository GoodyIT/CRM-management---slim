<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
			<p class="text-muted">Created By <strong><?php echo $createdName;?></strong></p>
		</div>
		<div class="col-sm-12">
			<form id="appointform" role="form" name="appointform">
				<input type="text" value="<?php echo $_POST['_id'];?>" style="display:none;" name="person_0_appointment_0_id">
				<input type="text" value="<?php echo $_SESSION['api']['user']['_id'];?>" style="display:none;" name="person_0_appointment_0_invitesAccepted_0_agentId">
				<div class="row">
					<div class="col-md-8">
						<label>Date</label>
						<input type="text" name="person_0_appointment_0_day" data-mask="99/99/9999" class="datepicker form-control" value="<?php echo $_POST['day'];?>" required>
					</div>
					<div class="col-md-4">
						<label>Time</label>
						<input type="text" name="person_0_appointment_0_time" data-mask="99:99" class="timepicker form-control" value="<?php echo $_POST['time'];?>" required>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-8">
						<label>Person</label>
						<input id="personId" name="person_0_id" style="display:none;" value="<?php echo $_POST['_parentId'];?>">
						<?php 
						$apiObj->mongoSetCollection('addresses');
						$addressQuery = array('_parentId'=>$personCursor['_id']);
						$addressCursor = $apiObj->mongoFindOne($addressQuery);
						if((!empty($_POST['_parentId']))&&($_POST['title']!='PERSONAL')){
							if(($_POST['_parentId'] == $personCursor['_id'])){
						?>
						<a class="form-control close-modal" href="#lead/edit/<?php echo $_POST['_parentId'];?>"><?php echo $personCursor['firstName']. ' ' . $personCursor['lastName']; ?> <?php if(!empty($addressCursor['state'])){echo 'of '.$addressCursor['state'];}?><i class="pull-right fa fa-arrow-right" style="line-height:1.5;"></i></a>
						<?php }else{
						?>
						<a class="form-control close-modal" href="#lead/edit/<?php echo $_POST['_parentId'];?>"><?php echo $_POST['_parentId'];?><i class="pull-right fa fa-arrow-right" style="line-height:1.5;"></i></a>
						<?php
							}
						}?>
					</div>
					<script>
						$('.close-modal').on('click', function(){
							setTimeout(function(){
								$('#modal').modal('toggle');
							}, 300);
						});
					</script>
					<div class="col-md-4">
						<label>Label</label> 
						<select id="appointmentLabel" name="person_0_appointment_0_title" class="chosen-select" required>
							<option <?php if($_POST['title'] == "CALLBACK"){echo 'selected';}else{echo $_POST['title'];}?> value="CALLBACK">Callback</option>
							<option <?php if($_POST['title'] == "FOLLOWUP"){echo 'selected';}else{echo $_POST['title'];}?> value="FOLLOWUP">Follow Up</option>
							<option <?php if($_POST['title'] == "SUBMIT"){echo 'selected';}else{echo $_POST['title'];}?> value="SUBMIT">Submit Policy</option>
							<option <?php if($_POST['title'] == "PERSONAL"){echo 'selected';}else{echo $_POST['title'];}?> value="PERSONAL">Personal Reminder</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label>Attach People to Appointment</label> 
						<select multiple class="chosen-select" name="person_0_appointment_0_invitesPending">
							<?php
							foreach($userCursor as $user){
								if(count( $acceptedUsers)>1){
							?>
							<option <?php if(in_array($user['_id'], $attachedUsers)){echo 'selected';}?> value="<?php echo $user['_id'];?>"><?php echo $user['firstName'].' '.$user['lastName'].$user['firstname'].' '.$user['lastname'];?></option>
							<?php
								}else{
									if(($user['_id'] != $_POST['createdBy'])){
							?>
							<option <?php if(in_array($user['_id'], $attachedUsers)){echo 'selected';}?> value="<?php echo $user['_id'];?>"><?php echo $user['firstName'].' '.$user['lastName'].$user['firstname'].' '.$user['lastname'];?></option>
							<?php
									}
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group"><label>Notes</label> <textarea rows="5" name="person_0_appointment_0_notes" class="form-control" ng-model="appointment.person_0_appointment_0_notes"><?php echo $_POST['notes'];?></textarea></div>
				<div>
					<a id="appointSubmit" class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit">Set</a>
					<div id="appointDelete" appointmentId="<?php echo $_POST['_id'];?>" class="appointDelete btn btn-sm btn-danger pull-left m-t-n-xs"><strong>Delete</strong></div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('.appointDelete').on('click', function(event){
		var obj = $(this);
		appointDelete(event,obj);
	});
</script>