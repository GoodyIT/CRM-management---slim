
<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
			<form id="smsform" role="form" name="smsform">
				<input style="display:none" name="person_0_id" value="<?php echo $parentId;?>">
				<input style="display:none" name="person_0_sms_0_createThing" value="Y">
				<div class="row">
					<div class="col-md-12">
						<label>
							Phone Number
						</label>
						<select id="toNumber" name="person_0_sms_0_toNumber" class="chosen-select form-control">
							<?php
							foreach($phones as $number){
							?>
							<option value="<?php echo $number['phoneNumber'];?>"><?php echo $number['phoneType'].': '.$number['phoneNumber'];?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label>
							Message
						</label>
						<select name="person_0_sms_0_message" class="chosen-select form-control">
							<?php
							foreach($templates as $template){
							?>
							<option value="<?php echo $template['template'];?>"><?php echo $template['template'];?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<br>
				<div>
					<button id="smsSubmit" class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit">Send</button>
				</div>
			</form>
		</div>
	</div>
</div>