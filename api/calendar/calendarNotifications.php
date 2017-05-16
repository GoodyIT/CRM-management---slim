<div class="row">
	<div class="col-md-12" style="background-color:white">
		<div class="pull-left" style="padding:10px;background-color:white">
			<a id="newAppointment" data-toggle="modal" data-target="#modal" _parentId="" class="newAppointment btn btn-primary">Add New Appointment</a>
		</div>
		<div class="pull-left" style="padding:10px;background-color:white">
			<a href="#calendar" class="confirmation newAppointment btn btn-primary">View Calendar</a>
		</div>
	</div>
</div>
<ul class="sidebar-list">
	<?php
	if(!empty($result)){
		foreach($result as $notification){
	?>
	<div>
		<li style="background-color:white;">
			<div class="dropdown-messages-box">
				<div>
					<small class="pull-right"><?php echo $notification['time'];?></small>
					<?php 
			if($notification['title']!='PERSONAL'){
					?>
					<a href="#lead/edit/<?php echo $notification['parentId'];?>" style="padding:0;font-size:12px;">
						<strong><?php echo $notification['title'];?></strong> with <?php echo $notification['person'];?>
					</a>
					<?php
			}else{
					?>
					<a href="#calendar" style="padding:0;font-size:12px;">
						<strong>Personal Reminder</strong>
					</a>
					<?php 
			}
					?>
					<p><?php echo $notification['notes'];
			if(count($acceptedUsers)>1){
						?></p><br>
					<button id="notifications" inviteId="<?php echo $notification['inviteId']?>" appointmentId="<?php echo $notification['id'];?>" class="appointmentDetach pull-right btn btn-xs btn-warning" style="margin-right:10px;"><i class="fa fa-chain-broken" data-toggle="tooltip" data-placement="top" title="Detach Self"></i></button><?php }?>
					<button id="notifications" inviteId="<?php echo $notification['inviteId']?>" appointmentId="<?php echo $notification['id'];?>" class="appointDelete pull-right btn btn-xs btn-danger" style="margin-right:10px;" data-toggle="tooltip" data-placement="top" title="Delete Appointment"><i class="fa fa-times"></i></button><br>
					<small class="text-muted"><?php echo $notification['day'];?> at <?php echo $notification['time'];?></small>
				</div>
			</div>
		</li>
	</div>
	<?php
		}
	}else{
	?>
	<?php
	}
	?>
</ul>
<script>
	$(function(){
		var count = <?php echo (count($result)!=0)?count($result):'" "'; ?>;
		if(count!=null){
			if($.isNumeric(count)){
			$('#appointmentCounter').html(count);
			}
		}
		$('.newAppointment').on('click', function(){
			newAppointmentModal(this);
		});
		$('.appointDelete').on('click', function(event){
			var obj = $(this);
			appointDelete(event,obj);
		});
	});
</script>
