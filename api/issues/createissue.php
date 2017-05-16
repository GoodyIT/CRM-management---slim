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
								<strong>Create</strong>
							</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="ibox-content">
				<form id="issueForm">
					<input style="display:none;" name="user_0_id" value="<?php echo $_SESSION['api']['user']['_id']; ?>">
					<input style="display:none;" name="user_0_createThing" value="Y">
					<input style="display:none;" name="user_0_issues_0_createThing" value="Y">
					<input style="display:none;" name="user_0_issues_0_status" value="OPEN">
					<input style="display:none;" name="user_0_issues_0_label" value="info">
					<input style="display:none;" name="user_0_issues_0_read" value="UNREAD">
					<div class="row">
						<div class="col-md-12">
							<label>Issue Department</label>
							<select class="form-control" name="user_0_issues_0_department">
								<option value="MANAGERS">Managers</option>
								<option value="HR">Human Resources</option>
								<option value="ACCOUNTING">Accounting</option>
								<option value="INSUREHC">Insure HC</option>
								<option value="IT">IT</option>
								<option value="CRM">WebDev/CRM</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label>Issue Title</label>
							<input class="form-control" name="user_0_issues_0_subject">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<label>Issue Description</label>
							<textarea id="summernote" class="form-control" rows="10" name="user_0_issues_0_bodyHtml" placeholder="Type Description Here"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<br>
							<button type="submit" class="btn btn-primary">Submit Issue</button>
							<a onclick="viewissuelist('OPEN')" class="btn btn-white pull-right">Cancel</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$('#summernote').summernote({dialogsInBody: true,toolbar: [
    // [groupName, [list of button]]
    ['para', ['ul', 'ol', 'paragraph']],
  ]});
		$('#issueForm').submit(function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			$.post('api/thing/create', $(this).serialize(), function(response){
				console.log(response);
				viewissuelist('OPEN');
			});
		});
	});
</script>