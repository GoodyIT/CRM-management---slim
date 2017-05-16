<div class="row">
	<div class="col-lg-12">
		<div class="ibox">
			<div class="ibox-title">
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
							<li class="active">
								<a onclick="viewissuelist('OPEN')">Ticket List</a>
							</li>
							<li class="active">
								<strong>View</strong>
							</li>
						</ol>
					</div>
				</div>
			</div>
			<?php
			if(!empty($result)){
			?>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-12">
						<?php
				if((strtoupper($_SESSION['api']['user']['permissionLevel'])=='INSUREHC')||(strtoupper($_SESSION['api']['user']['permissionLevel'])=='ADMINISTRATOR')||(strtoupper($_SESSION['api']['user']['permissionLevel'])=='MANAGER')){
						?>
						<div class="ibox-tools" >
							<a id="markOpen" issueId="<?php echo $result['_id']; ?>" createdBy="<?php echo $result['_createdBy']; ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Mark Open"><i style="color:white;" class="fa fa-file-o"></i></a>
							<a id="markFixed" issueId="<?php echo $result['_id']; ?>" createdBy="<?php echo $result['_createdBy']; ?>" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Mark Fixed"><i style="color:white;" class="fa fa-check"></i></a>
							<a id="markClosed" issueId="<?php echo $result['_id']; ?>" createdBy="<?php echo $result['_createdBy']; ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Mark Closed"><i style="color:white;" class="fa fa-lock"></i></a>
						</div>
						<?php
				}
						?>
						<h2><?php echo $result['subject']; ?></h2>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<small class="pull-left">Created: <?php echo date('m/d/Y', strtotime($result['_timestampCreated'])); ?> by <strong><?php echo $result['_parentId']; ?></strong></small>
						<hr>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<p style="word-wrap:break-word"><?php echo $result['bodyHtml']; ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<label>Comment</label>
						<textarea id="commentArea" class="form-control"></textarea>
						<br>
						<a id="commentSubmit" createdBy="<?php echo $result['_createdBy']; ?>" issueId="<?php echo $result['_id']; ?>" class="btn btn-primary">Submit Comment</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<br>
						<h4>Comments</h4>
						<hr style="margin-top:0">
						<?php 
				if(!empty($comments)){
					foreach($comments as $comment){
						?>
						<p><?php echo $comment['body']; ?></p>
						<small><?php echo $comment['name'];?> on <?php echo date('m/d/Y', strtotime($comment['_timestampCreated'])); ?></small>
						<hr>
						<?php
					}
				}
						?>
					</div>
				</div>
			</div>
			<?php
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		var serialize = function(obj) {
			var str = [];
			for(var p in obj)
				if (obj.hasOwnProperty(p)) {
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				}
			return str.join("&");
		};
		$(document).tooltip();
		$('#commentSubmit').on('click', function(){
			var data=[]
			data['user_0_id']=$(this).attr('createdBy');
			data['user_0_createThing']='Y';
			data['user_0_issues_0_id']=$(this).attr('issueId');
			data['user_0_issues_0_createThing']='Y';
			data['user_0_issues_0_status']='OPEN';
			data['user_0_issues_0_label']='warning';
			data['user_0_issues_0_comments_0_createThing']='N';
			data['user_0_issues_0_comments_0_body']=$('#commentArea').val();
			$.post('api/thing/create', serialize(data), function(response){
				console.log(response);
				viewissuelist('OPEN');
			});
		});
		$('#markFixed').on('click', function(){
			var data=[]
			data['user_0_id']=$(this).attr('createdBy');
			data['user_0_createThing']='Y';
			data['user_0_issues_0_id']=$(this).attr('issueId');
			data['user_0_issues_0_createThing']='Y';
			data['user_0_issues_0_status']='FIXED';
			data['user_0_issues_0_label']='success';
			$.post('api/thing/create', serialize(data), function(response){
				console.log(response);
				viewissuelist('OPEN');
			});
		});
		$('#markOpen').on('click', function(){
			var data=[]
			data['user_0_id']=$(this).attr('createdBy');
			data['user_0_createThing']='Y';
			data['user_0_issues_0_id']=$(this).attr('issueId');
			data['user_0_issues_0_createThing']='Y';
			data['user_0_issues_0_status']='OPEN';
			data['user_0_issues_0_label']='primary';
			$.post('api/thing/create', serialize(data), function(response){
				console.log(response);
				viewissuelist('OPEN');
			});
		});
		$('#markClosed').on('click', function(){
			var data=[]
			data['user_0_id']=$(this).attr('createdBy');
			data['user_0_createThing']='Y';
			data['user_0_issues_0_id']=$(this).attr('issueId');
			data['user_0_issues_0_createThing']='Y';
			data['user_0_issues_0_status']='CLOSED';
			data['user_0_issues_0_label']='danger';
			$.post('api/thing/create', serialize(data), function(response){
				console.log(response);
				viewissuelist('OPEN');
			});
		});
	});
</script>