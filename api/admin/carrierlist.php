<div class=" animated fadeInRight">
	<div class="row wrapper border-bottom white-bg page-heading ng-scope">
		<div class="col-lg-10">
			<h2>Create Carrier</h2>
			<ol class="breadcrumb">
				<li><a href="#">Home</a></li>
				<li><a href="#admin/settings">Settings</a></li>
				<li class="active">
					<strong>Create</strong>
				</li>
			</ol>
		</div>
	</div>
</div>
<div class="row animated fadeInRight">
	<form id="carrierform" name="saveTemplateData" method="post" action="<?php echo $settings['base_uri'];?>api/admin/createCarrier" class="form-horizontal ">
		<div>
			<div class="row">
				<div class="col-lg-12 col-xs-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<div>
								<h4>Carrier List</h4>
							</div>
						</div>
						<div class="ibox-content">
							<div class="row">
								<div class="col-md-4">
									<label>
										Carrier
									</label>
									<input style="display:none" name="carrier_0_createThing" value="Y">
									<input class="form-control" name="carrier_0_name" type="text" >
								</div>
								<div class="col-md-4">
									<label>Sort</label>
									<select class="form-control" name="carrier_0_sort" >
										<?php for($i=1; $i<=count($result); $i++){?>
										<option value="<?php echo $i;?>"><?php echo $i;?></option>
										<?php }?>
									</select>
								</div>
							</div>
						</div>
						<div style="padding-top:30px; padding-bottom: 30px;">
							<div class="row">
								<div class="col-xs-12">
									<a class="btn btn-white" onClick="cancelUserInfo()">Cancel</a>
									<button id="saveButton" class="btn btn-primary" type="submit">Save Carrier</button>
								</div>
							</div>
						</div>
						<?php
						if(!empty($result)){
						?>
						<h2>Current Carriers</h2>
						<div class="row">
							<div class="col-md-12">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th>Name</th>
											<th>Sort</th>
											<th>Delete</th>
										</tr>
									</thead>
									<?php
							foreach($result as $carrier){
									?>
									<tr>
										<td><?php echo $carrier['name'];?></td>
										<td><?php echo $carrier['sort'];?></td>
										<td><a userId="<?php echo $carrier['_id'];?>" class="carrierRemove btn btn-danger"> Delete</a></td>
									</tr>
									<?php
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
		window.location.hash = '#admin/settings';   
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
		$('.carrierRemove').on('click', function(){
			var usrInfo = [];
			usrInfo['carrier_0_createThing']='Y';
			usrInfo['carrier_0_status']='INACTIVE';
			usrInfo['carrier_0_id']=$(this).attr('userId');
			$.ajax({
				url: 'api/thing/create',
				type: 'POST',
				data: serialize(usrInfo),
				success: function(result) {
					console.log(result);
					$('#results').load(base_uri + 'api/admin/carriers');
				}
			});
		});
		$(".table").tablesorter({sortList:[[0,0]]});
		$('input').focus(function() {
			$(this).parent().removeClass("has-error");
		});
		$(".chosen-select").chosen({width:'100%', placeholder_text_multiple:'Select People'});
		// Attach a submit handler to the form
		$("#carrierform").submit(function(event) {
			// Stop form from submitting normally
			event.preventDefault();
			console.log( $(this).serialize());
			$.post($(this).attr("action"), $(this).serialize(), function(response){
				console.log(response);
					$('#results').load(base_uri + 'api/admin/carriers');
			});
		});
	});
</script>