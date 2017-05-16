<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
			<form id="smsTemplateForm" role="form" name="smsTemplateForm">
				<input style="display:none" name="smsTemplate_0_createThing" value="Y">
				<?php
if(!empty($template['_id'])){
				?>
				<input style="display:none" name="smsTemplate_0_id" value="<?php echo $template['_id'];?>">
				<?php 
}
				?>
				<div class="row">
					<h2>New SMS Template</h2>
				</div>
				<div class="row">
					<textarea class="form-control" rows="5" name="smsTemplate_0_template"><?php
if(!empty($template['template'])){
	echo $template['template'];
}?></textarea>
				</div>
				<br>
				<div class="row">
					<button id="smsTemplateSubmit" class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit">Create</button>
				</div>
			</form>
		</div>
	</div>
</div>