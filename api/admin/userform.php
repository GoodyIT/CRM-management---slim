<div class=" animated fadeInRight">
	<div class="row wrapper border-bottom white-bg page-heading ng-scope">
		<div class="col-lg-10">
			<h2>Create a User</h2>
			<ol class="breadcrumb">
				<li><a href="#">Home</a></li>
				<li><a href="#admin/settings">Settings</a></li>
				<li><a href="#admin/user/list">Users</a></li>
				<li class="active">
					<strong>Create</strong>
				</li>
			</ol>
		</div>
		<div class="col-lg-2">
		</div>
	</div>
</div>
<div class="row animated fadeInRight">
	<form id="userform" name="saveTemplateData" method="post" action="<?php echo $settings['base_uri'];?>api/admin/user/createuser" class="form-horizontal ">
		<input type="hidden" name="previousEmail" value="<?php echo $result['user'][0]['email'];?>">
		<div>
			<div class="row">
				<div class="col-lg-12 col-xs-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<div>
								<h4>User Information</h4>
							</div>
						</div>
						<div class="ibox-content">
							<?php $apiObj->displayThingForm("user", $result['user'], 0, '' , "Y");  ?>
							<div class="row">
								<div class="col-md-3">
									<label>Department</label>
									<select name="user_0_department" class="form-control">
										<option <?php echo (!empty($result['user'][0]['department']))?(($result['user'][0]['department']=='MANAGERS')?'selected':''):'';?> value="MANAGERS">Managers</option>
										<option <?php echo (!empty($result['user'][0]['department']))?(($result['user'][0]['department']=='HR')?'selected':''):'';?> value="HR">Human Resources</option>
										<option <?php echo (!empty($result['user'][0]['department']))?(($result['user'][0]['department']=='ACCOUNTING')?'selected':''):'';?> value="ACCOUNTING">Accounting</option>
										<option <?php echo (!empty($result['user'][0]['department']))?(($result['user'][0]['department']=='INSUREHC')?'selected':''):'';?> value="INSUREHC">Insure HC</option>
										<option <?php echo (!empty($result['user'][0]['department']))?(($result['user'][0]['department']=='CRM')?'selected':''):'';?> value="CRM">WebDev/CRM</option>
										<option <?php echo (!empty($result['user'][0]['department']))?(($result['user'][0]['department']=='IT')?'selected':''):'';?> value="IT">IT</option>
										<option <?php echo (!empty($result['user'][0]['department']))?(($result['user'][0]['department']=='SALES')?'selected':''):'';?> value="SALES">Sales</option>
									</select>
								</div>
							</div>
							<div class="row">
								<?php
								if(!empty($result['usergroups'])){
									foreach($result['usergroups'] as $userGroup){
                                        $selected = "";
										foreach($userGroup['users'] as $user){
											if($user['userId']==$userId){
												$selected=$user['level'];
											}
										}
								?>
								<div class="col-md-3">
									<label><?php echo $userGroup['label'];?> User Group</label>
									<select name="<?php echo 'user_0_'.lcfirst(str_replace(' ', '',$userGroup['label'])); ?>" class="form-control">
										<option <?php echo (($selected=='none')||($selected=='NONE'))?'selected':'';?> value="NONE">None</option>
										<option <?php echo (($selected=='USER')||($selected=='user'))?'selected':'';?> value="USER">User</option>
										<option <?php echo (($selected=='MANAGER')||($selected=='manager'))?'selected':'';?> value="MANAGER">Manager</option>
										<option <?php echo (($selected=='ADMINISTRATOR')||($selected=='administrator'))?'selected':'';?> value="ADMINISTRATOR">Admin</option>
									</select>
								</div>
								<?php
									}
								}
								?>
							</div>
						</div>
						<div style="padding-top:30px; padding-bottom: 30px;">
							<div class="row">
								<div class="col-xs-12">
									<a class="btn btn-white" onClick="cancelUserInfo()">Cancel</a>
									<button id="saveButton" class="btn btn-primary" type="submit">Save User</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
	function cancelUserInfo(){
		window.location.hash = '#admin/user/list';
	}


	$(document).ready(function() {

		$('input').focus(function() {
			$(this).parent().removeClass("has-error");
		});



		// Attach a submit handler to the form
		$("#userform").submit(function(event) {
			// Stop form from submitting normally
			event.preventDefault();
			$.ajax({
				url: $(this).attr("action"),
				type: 'POST',
				data: $(this).serialize(),
				success: function(result) {
					var returnedData = JSON.parse(result);
					console.log(returnedData);
					if(returnedData.result == "ERROR"){
						toastr.error(returnedData.message);
						if (typeof returnedData.field !== 'undefined') {
							$('#'+returnedData.field).parent().addClass("has-error");
							$('#'+returnedData.field).focus();
						}

					} else {
						toastr.success(returnedData.message);
						window.location.hash = '#admin/user/list';
					}



				}
			});
		});
	});
</script>