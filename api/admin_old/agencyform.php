<div class=" animated fadeInRight">
	<div class="row wrapper border-bottom white-bg page-heading ng-scope">
		<div class="col-lg-10">
			<h2>Create Agency</h2>
			<ol class="breadcrumb">
				<li><a href="#">Home</a></li>
				<li><a href="#admin/settings">Settings</a></li>
				<li><a href="#admin/agencies">Agency</a></li>
				<li class="active">
					<strong>Create</strong>
				</li>
			</ol>
		</div>
	</div>
</div>
<div class="row animated fadeInRight">
	<form id="userform" name="saveTemplateData" method="post" action="<?php echo $settings['base_uri'];?>api/admin/agencies/createAgency" class="form-horizontal ">
		<div>
			<div class="row">
				<div class="col-lg-12 col-xs-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<div>
								<h4>Agency Create</h4>
							</div>
						</div>
						<div class="ibox-content">
							<div class="row">
								<div class="col-md-4">
									<label>
										Agency Name
									</label>
									<?php
									if(!empty($result['_id'])){
									?>
									<input style="display:none" type="text" name="agency_0_id" value="<?php echo (!empty($result['_id']))?$result['_id']:'';?>">
									<?php
									}
									?>
									<input class="form-control" name="agency_0_agencyName" type="text" value="<?php echo (!empty($result['agencyName']))?$result['agencyName']:'';?>">
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label>Agency Owner</label> 
									<select class="chosen-select" name="agency_0_agencyOwner">
										<?php
										$userCursor->sort(array('firstname'=>1));
										foreach($userCursor as $user){
										?>
										<option <?php echo ($user['_id']==$result['agencyOwner'])?'selected':'';?> value="<?php echo $user['_id'];?>"><?php echo $user['firstname'].' '.$user['lastname'];?></option>
										<?php
										}
										?>
									</select>
								</div>
								<div class="col-md-4">
									<label>Agency Contact</label> 
									<select class="chosen-select" name="agency_0_agencyContact">
										<?php
										foreach($userCursor as $user){
										?>
										<option <?php echo ($user['_id']==$result['agencyContact'])?'selected':'';?> value="<?php echo $user['_id'];?>"><?php echo $user['firstname'].' '.$user['lastname'];?></option>
										<?php
										}
										?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label>Agency Phone Number</label>
									<input class="form-control" name="agency_0_phoneNumber" value="<?php echo (!empty($result['phoneNumber']))?$result['phoneNumber']:'';?>">
								</div>
								<div class="col-md-4">
									<label>Agency Street Address</label>
									<input class="form-control" name="agency_0_addressStreet1" value="<?php echo (!empty($result['addressStreet1']))?$result['addressStreet1']:'';?>">
								</div>
								<div class="col-md-4">
									<label>Agency City</label>
									<input class="form-control" name="agency_0_addressCity" value="<?php echo (!empty($result['addressCity']))?$result['addressCity']:'';?>">
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label>Agency Postal Code</label>
									<input class="form-control" name="agency_0_addressPostalCode" value="<?php echo (!empty($result['addressPostalCode']))?$result['addressPostalCode']:'';?>">
								</div>
								<div class="col-md-4">
									<label>Agency State</label>
									<select class="chosen-select" name="agency_0_addressState">
										<option value="" selected=""></option>
										<option <?php echo ($result['addressState']=='AL')?'selected':'';?> value="AL">Alabama</option>
										<option <?php echo ($result['addressState']=='AK')?'selected':'';?> value="AK">Alaska</option>
										<option <?php echo ($result['addressState']=='AZ')?'selected':'';?> value="AZ">Arizona</option>
										<option <?php echo ($result['addressState']=='AR')?'selected':'';?> value="AR">Arkansas</option>
										<option <?php echo ($result['addressState']=='CA')?'selected':'';?> value="CA">California</option>
										<option <?php echo ($result['addressState']=='CO')?'selected':'';?> value="CO">Colorado</option>
										<option <?php echo ($result['addressState']=='CT')?'selected':'';?> value="CT">Connecticut</option>
										<option <?php echo ($result['addressState']=='DE')?'selected':'';?> value="DE">Delaware</option>
										<option <?php echo ($result['addressState']=='DC')?'selected':'';?> value="DC">District of Columbia</option>
										<option <?php echo ($result['addressState']=='FL')?'selected':'';?> value="FL">Florida</option>
										<option <?php echo ($result['addressState']=='GA')?'selected':'';?> value="GA">Georgia</option>
										<option <?php echo ($result['addressState']=='HI')?'selected':'';?> value="HI">Hawaii</option>
										<option <?php echo ($result['addressState']=='ID')?'selected':'';?> value="ID">Idaho</option>
										<option <?php echo ($result['addressState']=='IL')?'selected':'';?> value="IL">Illinois</option>
										<option <?php echo ($result['addressState']=='IN')?'selected':'';?> value="IN">Indiana</option>
										<option <?php echo ($result['addressState']=='IA')?'selected':'';?> value="IA">Iowa</option>
										<option <?php echo ($result['addressState']=='KS')?'selected':'';?> value="KS">Kansas</option>
										<option <?php echo ($result['addressState']=='KY')?'selected':'';?> value="KY">Kentucky</option>
										<option <?php echo ($result['addressState']=='LA')?'selected':'';?> value="LA">Louisiana</option>
										<option <?php echo ($result['addressState']=='ME')?'selected':'';?> value="ME">Maine</option>
										<option <?php echo ($result['addressState']=='MD')?'selected':'';?> value="MD">Maryland</option>
										<option <?php echo ($result['addressState']=='MA')?'selected':'';?> value="MA">Massachusetts</option>
										<option <?php echo ($result['addressState']=='MI')?'selected':'';?> value="MI">Michigan</option>
										<option <?php echo ($result['addressState']=='MN')?'selected':'';?> value="MN">Minnesota</option>
										<option <?php echo ($result['addressState']=='MS')?'selected':'';?> value="MS">Mississippi</option>
										<option <?php echo ($result['addressState']=='MO')?'selected':'';?> value="MO">Missouri</option>
										<option <?php echo ($result['addressState']=='MT')?'selected':'';?> value="MT">Montana</option>
										<option <?php echo ($result['addressState']=='NE')?'selected':'';?> value="NE">Nebraska</option>
										<option <?php echo ($result['addressState']=='NV')?'selected':'';?> value="NV">Nevada</option>
										<option <?php echo ($result['addressState']=='NH')?'selected':'';?> value="NH">New Hampshire</option>
										<option <?php echo ($result['addressState']=='NJ')?'selected':'';?> value="NJ">New Jersey</option>
										<option <?php echo ($result['addressState']=='NM')?'selected':'';?> value="NM">New Mexico</option>
										<option <?php echo ($result['addressState']=='NY')?'selected':'';?> value="NY">New York</option>
										<option <?php echo ($result['addressState']=='NC')?'selected':'';?> value="NC">North Carolina</option>
										<option <?php echo ($result['addressState']=='ND')?'selected':'';?> value="ND">North Dakota</option>
										<option <?php echo ($result['addressState']=='OH')?'selected':'';?> value="OH">Ohio</option>
										<option <?php echo ($result['addressState']=='OK')?'selected':'';?> value="OK">Oklahoma</option>
										<option <?php echo ($result['addressState']=='OR')?'selected':'';?> value="OR">Oregon</option>
										<option <?php echo ($result['addressState']=='PA')?'selected':'';?> value="PA">Pennsylvania</option>
										<option <?php echo ($result['addressState']=='RI')?'selected':'';?> value="RI">Rhode Island</option>
										<option <?php echo ($result['addressState']=='SC')?'selected':'';?> value="SC">South Carolina</option>
										<option <?php echo ($result['addressState']=='SD')?'selected':'';?> value="SD">South Dakota</option>
										<option <?php echo ($result['addressState']=='TN')?'selected':'';?> value="TN">Tennessee</option>
										<option <?php echo ($result['addressState']=='TX')?'selected':'';?> value="TX">Texas</option>
										<option <?php echo ($result['addressState']=='UT')?'selected':'';?> value="UT">Utah</option>
										<option <?php echo ($result['addressState']=='VT')?'selected':'';?> value="VT">Vermont</option>
										<option <?php echo ($result['addressState']=='VA')?'selected':'';?> value="VA">Virginia</option>
										<option <?php echo ($result['addressState']=='WA')?'selected':'';?> value="WA">Washington</option>
										<option <?php echo ($result['addressState']=='WV')?'selected':'';?> value="WV">West Virginia</option>
										<option <?php echo ($result['addressState']=='WI')?'selected':'';?> value="WI">Wisconsin</option>
										<option <?php echo ($result['addressState']=='WY')?'selected':'';?> value="WY">Wyoming</option>
									</select>
								</div>
								<div class="col-md-4">
									<label>Agency Country</label>
									<input class="form-control" name="agency_0_addressCountry" value="<?php echo (!empty($result['addressCountry']))?$result['addressCountry']:'';?>">
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<label>Attach Users</label> 
									<select multiple class="chosen-select" name="agency_0_attachusers[]">
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
					</div>
					<div style="padding-top:30px; padding-bottom: 30px;">
						<div class="row">
							<div class="col-xs-12">
								<a class="btn btn-white" onClick="cancelUserInfo()">Cancel</a>
								<button id="saveButton" class="btn btn-primary" type="submit">Save Agency</button>
							</div>
						</div>
					</div>
					<h2>Attached Users</h2>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Name</th>
										<th>Delete</th>
									</tr>
								</thead>
								<?php
								$apiObj->mongoSetDB($settings['database']);
								$apiObj->mongoSetCollection("user");
								$person=$apiObj->mongoFind();
								foreach($person as $usr){
									if(!empty($result['_id'])){
										if($usr['agencyId']==$result['_id']){
											$hasusers = true;
								?>
								<tr>
									<td><?php echo $usr['firstname'].' '.$usr['lastname'];?></td>
									<td><a userId="<?php echo $usr['_id'];?>" class="userRemove btn btn-danger"> Delete</a></td>
								</tr>
								<?php
										}
									}
								}
								?>
							</table>
						</div>
					</div>
					<?php 
					if(($hasusers==false)&&(!empty($result['_id']))){
					?>
					<a id="deleteAgency" class="btn btn-danger">Delete Agency</a>
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
		window.location.hash = '#admin/agencies';   
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
		$('.userRemove').on('click', function(){
			var usrInfo = [];
			usrInfo['user_0_id']=$(this).attr('userId');
			usrInfo['user_0_createThing']='Y';
			usrInfo['user_0_agencyId']="none";
			$.ajax({
				url: 'api/thing/create',
				type: 'POST',
				data: serialize(usrInfo),
				success: function(result) {
					console.log(result);
					$('#results').load(base_uri + 'api/admin/agencies/edit/<?php echo (!empty($result['_id']))?$result['_id']:'';?>');
				}
			});
		});
		$('#deleteAgency').on('click', function(){
			swal({
				title: "Are you sure?",
				text: "This will Delete the Agency and might cause lease to not be Visible!",
				type: "error",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				showLoaderOnConfirm: true,
				closeOnConfirm: false,
			},
				 function(){
				$.get('api/thing/remove/agency/<?php echo (!empty($result['_id']))?$result['_id']:'';?>', function(response){
					console.log(response);
				});
				setTimeout(function(){
					swal("Deleted!", "Your User Agency has been deleted.", "success");
					window.location.hash = '#admin/agencies';
				}, 2000);
			});
		});
		<?php
		if($hasusers){
		?>
		$(".table").tablesorter({sortList:[[0,0]]});
		<?php
		}
		?>
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
				window.location.hash = '#admin/agencies';
			});
		});
	});
</script>