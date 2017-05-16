<div  >
	<div class="row">
		<div class="col-lg-3">
			<?php include "mailmenu.php"; ?>
		</div>
		<div class="col-lg-9 animated fadeInRight">
			<div class="mail-box-header">
				<div class="pull-right tooltip-demo">
					<?php 
					if($email['trash']!='Y'){
					?>
					<a class="btn btn-sm btn-white" href="#mail/compose/<?php echo $email['_id']; ?>"><i class="fa fa-reply"></i> Reply</a>
					<a class="btn btn-sm btn-white" href="#mail/compose/<?php echo $email['_id']; ?>"><i class="fa fa-arrow-right"></i> Forward</a>
					<?php /*
					<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Print" class="btn btn-sm btn-white"><i class="fa fa-print"></i> Print</button>
						*/?>
					<button id="deleteEmail" emailId="<?php echo $email['_id']; ?>" data-placement="top" data-toggle="tooltip" data-original-title="Trash" class="btn btn-sm btn-white"><i class="fa fa-trash-o"></i> Remove</button>
					<?php
					}else{
					?>
					<button id="restoreEmail" emailId="<?php echo $email['_id']; ?>" data-placement="top" data-toggle="tooltip" data-original-title="Restore" class="btn btn-sm btn-white"><i class="fa fa-undo"></i> Restore</button>
					<?php
					}
					?>
				</div>
				<h2>
					View Message
				</h2>
				<div class="mail-tools tooltip-demo m-t-md">
					<h3>
						<span class="font-noraml">Subject: </span><?php echo (!empty($email['subject']))?$email['subject']:'';?>
					</h3>
					<h5>
						<span class="pull-right font-noraml"><?php echo (!empty($email['timestamp']))?date('m/d/Y', strtotime($email['timestamp'])).' at '.date('H:i A', strtotime($email['timestamp'])):'';?></span>
						<span class="font-noraml">From: </span><?php echo (!empty($email['from']))?$email['from']:'';?><?php echo (!empty($email['sender']))?'('.$email['sender'].')':'';?>
					</h5>
					<h5><span class="font-noraml">Recipients: </span> <?php echo (!empty($email['recipient']))?$email['recipient']:( (!empty($email['to']))?$email['to']:'');?></h5>
				</div>
			</div>
			<div class="mail-box">
				<div class="mail-body">
					<?php echo (!empty($email['bodyPlain']))?preg_replace('/(<\/?base)((.*=".*")?>)/','',nl2br($email['bodyPlain'])):((!empty($email['strippedHtml']))?preg_replace('/(<\/?base)((.*=".*")?>)/','',$email['strippedHtml']):((!empty($email['bodyHtml']))?nl2br($email['bodyHtml']):'')); ?>
					<?php echo (!empty($email['bodyHtml']))?'':'<br>'.((!empty($email['strippedSignature']))?$email['strippedSignature']:'');?>
				</div>
				<?php if(!empty($email['mailAttachments'])){ ?>
				<div class="mail-attachment">
					<p>
						<span><i class="fa fa-paperclip"></i> <?php echo count($email['mailAttachments']);?> attachments - </span>
						<a id="zipFiles" href="<?php echo $settings['base_uri'].'api/mail/zip/'.$email['_id'];?>">Download all</a>
					</p>
					<div class="attachment">
						<?php
															$imgExts = array('jpg','png','jpeg','gif','svg');
															foreach($email['mailAttachments'] as $attachment){
																$info = pathinfo($attachment['name']);
																$ext = $info['extension'];
																if(!in_array($ext, $imgExts)){
						?>
						<div class="file-box">
							<div class="file">
								<a href="<?php echo $settings['base_uri'].'api/mail/files/'.str_replace('/tmp/','',$attachment['tmpName'].'.'.$ext);?>" download="<?php echo $attachment['name'];?>">
									<span class="corner"></span>
									<div class="icon">
										<i class="fa fa-file"></i>
									</div>
									<div class="file-name">
										<?php echo (!empty($attachment['name']))?$attachment['name']:'';?>
									</div>
								</a>
							</div>
						</div>
						<?php
																}else{
						?>
						<div class="file-box">
							<div class="file">
								<a href="<?php echo $settings['base_uri'].'api/mail/files/'.str_replace('/tmp/','',$attachment['tmpName'].'.'.$ext);?>" download="<?php echo $attachment['name'];?>">
									<span class="corner"></span>
									<div class="image">
										<img alt="image" class="img-responsive" src="<?php echo $settings['base_uri'].'api/mail/files/'.str_replace('/tmp/','',$attachment['tmpName'].'.'.$ext);?>">
									</div>
									<div class="file-name">
										<?php echo (!empty($attachment['name']))?$attachment['name']:'';?>
									</div>
								</a>
							</div>
						</div>
						<?php
																}
															}
						?>
						<div class="clearfix"></div>
					</div>
				</div>
				<?php }?>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		var serialize = function(obj) {
			var str = [];
			for(var p in obj)
				if (obj.hasOwnProperty(p)) {
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				}
			return str.join("&");
		};
		$('#deleteEmail').on('click',function(){
			var data = [];
			data['mail_0_createThing']= 'Y';
			data['mail_0_id']= $(this).attr('emailId');
			data['mail_0_trash']= 'Y';
			$.post(base_uri+'api/thing/create', serialize(data), function(response){
				console.log(response);
			});
			$('#results').load(base_uri + 'api/mail');
		});
		$('#restoreEmail').on('click',function(){
			var data = [];
			data['mail_0_createThing']= 'Y';
			data['mail_0_id']= $(this).attr('emailId');
			data['mail_0_trash']= 'N';
			$.post(base_uri+'api/thing/create', serialize(data), function(response){
				console.log(response);
			});
			$('#results').load(base_uri + 'api/mail');
		});
	});
</script>