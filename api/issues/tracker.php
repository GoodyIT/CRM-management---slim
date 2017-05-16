<div class="row">
	<div class="col-lg-12">
		<div style="padding:10px;background-color:white">
			<a class="createIssue btn btn-primary">Report A Problem</a>
			<div class="ibox-tools" >
				<a id="filterOpen" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Filter Open"><i style="color:white;" class="fa fa-file-o"></i></a>
				<a id="filterFixed" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Filter Fixed"><i style="color:white;" class="fa fa-check"></i></a>
				<a id="filterClosed" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Filter Closed"><i style="color:white;" class="fa fa-lock"></i></a>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table table-hover issue-tracker" style="background-color:white">
				<tbody>
					<?php
if(!empty($result)){
	foreach($result as $issue){
					?>
					<tr>
						<td style="font-size:10px">
							<span class="label label-<?php echo $issue['label']; ?>"><?php echo $issue['status'];?></span>
						</td>
						<td style="font-size:10px">
							<strong><a issueId="<?php echo $issue['_id']; ?>" class="viewIssue">
								<?php echo (!empty($issue['subject']))?mb_strimwidth(ucwords($issue['subject']), 0, 20, '...'):'NO TICKET SUBJECT';?>
								</a></strong>
						</td>
						<td style="font-size:10px">
							<?php echo ucwords(strtolower($issue['_parentId']));?>
						</td>
						<td style="font-size:10px">
							<?php echo date('m/d/Y', strtotime($issue['_timestampCreated']));?>
						</td>
						<td style="font-size:10px">
							<?php echo $issue['department']; ?>
						</td>
					</tr>
					<?php
	}
}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	var createissue = function(){
		$('#bugContent').load(base_uri + 'api/issues/create');
	};
	var viewissue = function(obj){
		$('#bugContent').load(base_uri + 'api/issues/view/'+$(obj).attr('issueId'));
	};
	$(function(){
		$('#filterFixed').on('click', function(){
			viewissuelist('FIXED');
		});
		$('#filterOpen').on('click', function(){
			viewissuelist('OPEN');
		});
		$('#filterClosed').on('click', function(){
			viewissuelist('CLOSED');
		});
		$('.createIssue').on('click', function(){
			createissue();
		});
		$('.viewIssue').on('click', function(){
			var obj= $(this);
			viewissue(obj);
		});
	});
</script>