<div class=" animated fadeInRight">
	<div class="row wrapper border-bottom white-bg page-heading ng-scope">
		<div class="col-lg-10">
			<h2>Create a User Group</h2>
			<ol class="breadcrumb">
				<li><a href="#">Home</a></li>
				<li><a href="#admin/settings">Settings</a></li>
				<li><a href="#admin/usergroups">User Groups</a></li>
				<li class="active">
					<strong>Create</strong>
				</li>
			</ol>
		</div>
		<?php
		if(!empty($result['_id'])){
		?>
		<div class="col-sm-2">
			<div class="title-action">
				<a id="deleteUserGroup" class="btn btn-danger btn-sm">Delete User Group</a>
			</div>
		</div>
		<?php
		}
		?>
	</div>
</div>
<div class="row animated fadeInRight">
	<form id="userform" name="saveTemplateData" method="post" action="<?php echo $settings['base_uri'];?>api/admin/usergroups/createUserGroup" class="form-horizontal ">
		<div>
			<div class="row">
				<div class="col-lg-12 col-xs-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<div>
								<h4>User Groups</h4>
							</div>
						</div>
						<div class="ibox-content">
							<div class="row">
								<div class="col-md-4">
									<label>
										User Group Name
									</label>
									<?php
									if(!empty($result['_id'])){
									?>
									<input style="display:none" type="text" name="userGroups_0_id" value="<?php echo (!empty($result['_id']))?$result['_id']:'';?>">
									<?php
									}
									?>
									<input class="form-control" name="userGroups_0_label" type="text" value="<?php echo (!empty($result['label']))?$result['label']:'';?>">
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label>Attach Users</label> 
									<select multiple class="chosen-select" name="userGroups_0_attachusers[]">
										<?php
										$userCursor->sort(array('firstname'=>1));
										foreach($userCursor as $user){
										?>
										<option value="<?php echo $user['_id'];?>"><?php echo $user['firstname'].' '.$user['lastname'];?></option>
										<?php
										}
										?>
									</select>
								</div>
								<div class="col-md-4">
									<label>Attach Managers</label> 
									<select multiple class="chosen-select" name="userGroups_0_managers[]">
										<?php
										foreach($userCursor as $user){
										?>
										<option value="<?php echo $user['_id'];?>"><?php echo $user['firstname'].' '.$user['lastname'];?></option>
										<?php
										}
										?>
									</select>
								</div>
								<div class="col-md-4">
									<label>Attach Administrators</label> 
									<select multiple class="chosen-select" name="userGroups_0_admins[]">
										<?php
										foreach($userCursor as $user){
										?>
										<option value="<?php echo $user['_id'];?>"><?php echo $user['firstname'].' '.$user['lastname'];?></option>
										<?php
										}
										?>
									</select>
								</div>
							</div>
						</div>
						<div style="padding-top:30px; padding-bottom: 30px;">
							<div class="row">
								<div class="col-xs-12">
									<a class="btn btn-white" onClick="cancelUserInfo()">Cancel</a>
									<button id="saveButton" class="btn btn-primary" type="submit">Save User Group</button>
								</div>
							</div>
						</div>
						<?php
						if(!empty($result)){
						?>
						<h2>Attached Users</h2>
						<div class="row">
							<div class="col-md-12">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th>Name</th>
											<th>Level</th>
											<th>Delete</th>
										</tr>
									</thead>
									<?php
							if(!empty($result['users'])){
								foreach($result['users'] as $usr){
									if($usr['level']!='NONE'){
										$apiObj->mongoSetDB($settings['database']);
										$apiObj->mongoSetCollection("user");
										$person=$apiObj->mongoFindOne(array('_id'=>$usr['userId']));
										if(!empty($person)){
									?>
									<tr>
										<td><?php echo $person['firstname'].' '.$person['lastname'];?></td>
										<td><?php echo $usr['level'];?></td>
										<td><a userId="<?php echo $usr['_id'];?>" class="userRemove btn btn-danger"> Delete</a></td>
									</tr>
									<?php
										}
									}
								}
							}
									?>
								</table>
							</div>
						</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
	function cancelUserInfo(){
		window.location.hash = '#admin/usergroups';   
	}
	$(document).ready(function() {
		var serialize = function(obj) {
			var str = [];
			for(var p in obj)
				if (obj.hasOwnProperty(p)) {
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				}
			return str.join("&");
		};
		$('#deleteUserGroup').on('click', function(){
			swal({
				title: "Are you sure?",
				text: "This will remove visibility of clients and policies under this user group until users are transfered to new user group! This can't be Undone!",
				type: "error",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				showLoaderOnConfirm: true,
				closeOnConfirm: false,
			},
				 function(){
				$.get('api/thing/remove/userGroups/<?php echo (!empty($result['_id']))?$result['_id']:'';?>', function(response){
					console.log(response);
				});
				setTimeout(function(){
					swal("Deleted!", "Your User Group has been deleted.", "success");
					$('#results').load(base_uri + 'api/admin/usergroups');
				}, 2000);
			});
		});
		$('.userRemove').on('click', function(){
			var usrInfo = [];
			usrInfo['userGroups_0_users_0_id']=$(this).attr('userId');
			usrInfo['userGroups_0_createThing']='Y';
			usrInfo['userGroups_0_users_0_createThing']='N';
			usrInfo['userGroups_0_users_0_level']='NONE';
			usrInfo['userGroups_0_id']="<?php echo (!empty($result['_id']))?$result['_id']:'';?>";
			$.ajax({
				url: 'api/thing/create',
				type: 'POST',
				data: serialize(usrInfo),
				success: function(result) {
					console.log(result);
					$('#results').load(base_uri + 'api/admin/usergroups');
				}
			});
		});
		$(".table").tablesorter({sortList:[[0,0]]});
		$('input').focus(function() {
			$(this).parent().removeClass("has-error");
		});
		$(".chosen-select").chosen({width:'100%', placeholder_text_multiple:'Select People'});
		// Attach a submit handler to the form
		$("#userform").submit(function(event) {
			// Stop form from submitting normally
			event.preventDefault();
			console.log( $(this).serialize());
			$.post($(this).attr("action"), $(this).serialize(), function(response){
				console.log(response);
				$('#results').load(base_uri + 'api/admin/usergroups');
			});
		});
	});
</script>