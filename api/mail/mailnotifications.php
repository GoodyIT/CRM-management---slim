<div class="row">
	<div class="col-md-12" style="background-color:white">
		<div class="pull-left" style="padding:10px;background-color:white">
			<a href="#mail/compose" class="btn btn-primary confirmation">Compose New</a>
		</div>
		<div class="pull-left" style="padding:10px;background-color:white">
			<a href="#mail" class="btn btn-primary confirmation">Inbox</a>
		</div>
	</div>
</div>
<ul class="sidebar-list">
	<?php
	if(!empty($read)){
		foreach($read as $mail){
	?>
	<li style="background-color:<?php echo ($mail['state']=='UNREAD')?'#A7E4D8':'white';?>;height:100px;">
		<a onclick="viewmaillist()" class="confirmation" href="#mail/view/<?php echo $mail['_id'];?>" style="padding:0;font-size:12px;"><label class="label" style="padding:2px;font-size:8px;margin-right:3px;">From: </label>
			<strong><?php echo (!empty($mail['from']))?$mail['from']:'No Sender Info';?></strong><br>
			<span style="font-size:14px;"><?php echo (!empty($mail['subject']))?$mail['subject']:'No Subject';?></span>

			<div class="row">
				<div class="col-md-12" style="height:10px;">
					<?php echo (!empty($mail['bodyHtml']))?substr(strip_tags(preg_replace('/\W\s/','',$mail['bodyHtml'])), 0, 50).'. . .':'NO BODY';?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<br>
					<small class="text-muted"><?php echo (!empty($mail['_timestampCreated']))?date('m/d/Y', strtotime($mail['_timestampCreated'])).' at '.date('H:i A', strtotime($mail['_timestampCreated'])):'';?></small>
				</div>
			</div></a>
	</li>
	<?php
		}
	}else{
	?>
	<h4>No Mail</h4>
	<?php
	}
	?>
</ul>
<script type="text/javascript">
	$(function(){
		var count = <?php echo (count($unread)!=0)?count($unread):'" "'; ?>;
		if(count!=null){
			if($.isNumeric(count)){
				$('#mailCounter').html(count);
			}
		}
		$('.confirmation').on('click', function() {
			//console.log(changedData);
			if (typeof(changedData)!='undefined') {
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
			}
		});
	});
</script>
