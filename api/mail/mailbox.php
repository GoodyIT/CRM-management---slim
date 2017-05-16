<div  >
	<div class="row">
		<div class="col-md-3">
			<?php include "mailmenu.php"; ?>
		</div>
		<div class="col-md-9">
			<div class="mail-box-header">
				<form id="mailSearch" class="pull-right mail-search">
					<div class="input-group">
						<input type="text" class="form-control input-sm" name="search" placeholder="Search email">
						<div class="input-group-btn">
							<button type="submit" class="btn btn-sm btn-primary">
								Search
							</button>
						</div>
					</div>
				</form>
				<h2>
					<?php echo ucwords(strtolower($_GET['folder'])).' ';echo (($_GET['folder']=='INBOX')&&(count($unread)>0))?count($unread):''; ?>
				</h2>
				<div class="mail-tools tooltip-demo m-t-md">
					<div class="btn-group pull-right">
						<?php
						if($settings['mail']['total'] > $settings['mail']['mail_per_page']){
							if($settings['mail']['page'] != 0){
						?>
						<a href="#mail/folder/<?php echo $_GET['folder'];?>/page/<?php echo $settings['mail']['page'] - 1;?><?php echo (!empty($_GET['term']))?'/search/'.$_GET['term']:'';?>" class="btn btn-white btn-sm"><i class="fa fa-arrow-left"></i></a>
						<?php
							}
						?>
						<a href="#mail/folder/<?php echo $_GET['folder'];?>/page/<?php echo $settings['mail']['page'] + 1;?><?php echo (!empty($_GET['term']))?'/search/'.$_GET['term']:'';?>" class="btn btn-white btn-sm"><i class="fa fa-arrow-right"></i></a>
						<?php
						}
						?>
					</div>
					<button id="selectAll" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Select All"><i class="fa fa-check"></i> </button>
					<button id="refreshInbox" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Refresh Inbox"><i class="fa fa-refresh"></i> </button>
					<button id="markRead" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Mark as read"><i class="fa fa-eye"></i> </button>
					<button id="markUnread" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Mark as Unread"><i class="fa fa-eye-slash"></i> </button>
					<?php
					if(($_GET['folder']!='SENT')&&($_GET['folder']!='TRASH')){
					?>
					<button id="markInbox" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to Inbox"><i class="fa fa-envelope"></i> </button>
					<?php
					}
					?>
					<?php
					if($_GET['folder']!='TRASH'){
					?>
					<button id="markImportant" class="btn btn-white btn-sm " data-toggle="tooltip" data-placement="top" title="Move to important"><i class="fa fa-exclamation"></i> </button>
					<button id="markDelete" class="btn btn-white btn-sm " data-toggle="tooltip" data-placement="top" title="Move to Trash"><i class="fa fa-trash-o"></i> </button>
					<?php
					}else{
					?>
					<button id="markRestore" class="btn btn-white btn-sm " data-toggle="tooltip" data-placement="top" title="Restore"><i class="fa fa-undo"></i> </button>
					<?php
					}
					?>
				</div>
			</div>
			<div class="mail-box ">
				<table id="mailboxTable" class="table table-hover table-mail">
					<tbody class=" animated fadeInRight hidden-sm hidden-xs">
						<?php 
						if(!empty($result)){
							foreach($result as $email){
						?>
						<tr class="<?php echo ($email['state']=='READ')?'read':'unread';?>">
							<td class="check-mail">
								<input type="checkbox" emailId="<?php echo $email['_id'];?>" class="i-checks">
							</td>
							<td class="mail-ontact"><a href="#mail/view/<?php echo $email['_id'];?>"><?php echo ($email['folder']=='INBOX')?$email['from']:((!empty($email['to']))?$email['to']:'No Email Inputed');?></a> <span class="label label-<?php echo (!empty($email['categoryColor'])?$email['categoryColor']:'warning');?> pull-right"><?php echo (!empty($email['category'])?$email['category']:'');?></span> </td>
							<td class="mail-subject"><a href="#mail/view/<?php echo $email['_id'];?>"><?php echo (!empty($email['subject']))?$email['subject']:'No Subject';?></a></td>
							<td class="">
								<?php if(!empty($email['mailAttachments'])){ ?>
								<i class="fa fa-paperclip"></i>
								<?php } ?>
							</td>
							<td class="text-right mail-date"><?php echo (!empty($email['_timestampCreated']))?date('m/d/Y', strtotime($email['_timestampCreated'])).' at '.date('H:i A', strtotime($email['_timestampCreated'])):'';?></td>
						</tr>
						<?php
							}
						}else{
							echo "<div class='col-lg-12 animated fadeInRight'><strong>There isn't any mail here...</strong></div>";
						}
						?>
					</tbody>
				</table>
				<div id="mobileBox" class="sidebar-container hidden-md hidden-lg">
					<ul class="sidebar-list">
						<?php
						if(!empty($result)){
							foreach($result as $mail){
						?>
						<li style="height:100px;padding:0;background-color:<?php echo ($mail['state']=='UNREAD')?'#F1F1F1':'white';?>;">
							<div class="col-xs-1" style="line-height:8">
								<input type="checkbox" emailId="<?php echo $mail['_id'];?>" class="i-checks">
							</div>
							<div style="height:100%;padding-top:10px" class="col-xs-10">
								<a onclick="viewmaillist()" href="#mail/view/<?php echo $mail['_id'];?>" style="padding:0;font-size:12px;"><label class="label" style="padding:2px;font-size:8px;margin-right:3px;">From: </label>
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
							</div>
						</li>
						<?php
							}
						}else{
						?>
						<?php
						}
						?>
					</ul>
				</div>
				<div class="col-sm-6">
					<div class="pagination">
						<?php echo $settings['mail']['total'];?> Emails
					</div>
				</div>
				<script type="text/javascript">
					var serialize = function(obj) {
						var str = [];
						for(var p in obj)
							if (obj.hasOwnProperty(p)) {
								str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
							}
						return str.join("&");
					};
					var selectAll = function(mailGroup,obj){
						$(mailGroup+ " input:checkbox").each(function(e){
							$(this).prop("checked", !$(this).prop("checked"));
							$(this).parent().toggleClass('checked');
						});
					};
					var markRead = function(mailGroup,selected){
						$(mailGroup+ ' input:checked').each(function(){
							selected.push($(this).attr('emailId'));
						});
						$.post('api/mail/function/state/READ', serialize(selected),function(response){
							console.log(response);
							if(window.location.hash=='#mail'){
								$('#results').load(base_uri + 'api/mail');
							}else{
								$('#results').load(base_uri + 'api/mail?folder=<?php echo (!empty($_GET['folder']))?$_GET['folder']:'';?>&mail_page=<?php echo $settings['mail']['page'];?>');
							}
						});
					};
					var windowSize = function(){
						if($(window).width() > 1073){
							var mailGroup = '#mailboxTable';
						}else{
							var mailGroup = '#mobileBox';
						}
						return mailGroup;
					}
					$(document).ready(function(){
						var mailGroup = windowSize();
						$(window).resize( function(){
							var mailGroup = windowSize();
						}).resize();
						$('#mailSearch').on('submit', function(event){
							event.preventDefault();
							event.stopImmediatePropagation();
							var term = $('#mailSearch input[name="search"]').val();
							$('#results').load(base_uri + 'api/mail?folder=<?php echo $_GET['folder'];?>&mail_page=<?php echo $settings['mail']['page'];?>&term='+term);
						});
						$(document).tooltip();
						$('.i-checks').iCheck({
							checkboxClass: 'icheckbox_square-green',
							radioClass: 'iradio_square-green',
						});
						$('#refreshInbox').on('click',function(){
							if(window.location.hash=='#mail'){
								$('#results').load(base_uri + 'api/mail');
							}else{
								$('#results').load(base_uri + 'api/mail?folder=<?php echo (!empty($_GET['folder']))?$_GET['folder']:'';?>');
							}
						});
						var selected = [];
						$('#selectAll').on('click', function(){
							var mailGroup = windowSize();
							var obj =$(this);
							selectAll(mailGroup,obj);
						});
						$('#markRead').on('click', function(){
							var mailGroup = windowSize();
							markRead(mailGroup,selected);
						});
						$('#markUnread').on('click', function(){
							var mailGroup = windowSize();
							$(mailGroup+ ' input:checked').each(function(){
								selected.push($(this).attr('emailId'));
							});
							$.post('api/mail/function/state/UNREAD', serialize(selected),function(response){
								console.log(response);
								if(window.location.hash=='#mail'){
									$('#results').load(base_uri + 'api/mail');
								}else{
									$('#results').load(base_uri + 'api/mail?folder=<?php echo (!empty($_GET['folder']))?$_GET['folder']:'';?>&mail_page=<?php echo $settings['mail']['page'];?>');
								}
							});
						});
						$('#markImportant').on('click', function(){
							var mailGroup = windowSize();
							$(mailGroup+ ' input:checked').each(function(){
								selected.push($(this).attr('emailId'));
							});
							$.post('api/mail/function/folder/IMPORTANT', serialize(selected),function(response){
								console.log(response);
								if(window.location.hash=='#mail'){
									$('#results').load(base_uri + 'api/mail');
								}else{
									$('#results').load(base_uri + 'api/mail?folder=<?php echo (!empty($_GET['folder']))?$_GET['folder']:'';?>&mail_page=<?php echo $settings['mail']['page'];?>');
								}
							});
						});
						$('#markInbox').on('click', function(){
							var mailGroup = windowSize();
							$(mailGroup+ ' input:checked').each(function(){
								selected.push($(this).attr('emailId'));
							});
							$.post('api/mail/function/folder/INBOX', serialize(selected),function(response){
								console.log(response);
								if(window.location.hash=='#mail'){
									$('#results').load(base_uri + 'api/mail');
								}else{
									$('#results').load(base_uri + 'api/mail?folder=<?php echo (!empty($_GET['folder']))?$_GET['folder']:'';?>&mail_page=<?php echo $settings['mail']['page'];?>');
								}
							});
						});
						$('#markDelete').on('click', function(){
							var mailGroup = windowSize();
							$(mailGroup+ ' input:checked').each(function(){
								selected.push($(this).attr('emailId'));
							});
							$.post('api/mail/function/trash/Y', serialize(selected),function(response){
								console.log(response);
								if(window.location.hash=='#mail'){
									$('#results').load(base_uri + 'api/mail');
								}else{
									$('#results').load(base_uri + 'api/mail?folder=<?php echo (!empty($_GET['folder']))?$_GET['folder']:'';?>&mail_page=<?php echo $settings['mail']['page'];?>');
								}
							});
						});
						$('#markRestore').on('click', function(){
							var mailGroup = windowSize();
							$(mailGroup+ ' input:checked').each(function(){
								selected.push($(this).attr('emailId'));
							});
							$.post('api/mail/function/trash/N', serialize(selected),function(response){
								console.log(response);
								if(window.location.hash=='#mail'){
									$('#results').load(base_uri + 'api/mail');
								}else{
									$('#results').load(base_uri + 'api/mail?folder=<?php echo (!empty($_GET['folder']))?$_GET['folder']:'';?>&mail_page=<?php echo $settings['mail']['page'];?>');
								}
							});
						});
					});
				</script>
			</div>
		</div>
	</div>
</div>