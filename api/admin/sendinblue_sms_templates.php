<div class=" animated fadeInRight">
	<div  class="row wrapper border-bottom white-bg page-heading ">
		<div class="col-lg-4">
			<h2>Create Sendinblue SMS Templates</h2>
			<ol class="breadcrumb">
				<li>
					<a href="#">Home</a>
				</li>
				<li class="active">
					<a href="#admin/settings">Settings</a>
				</li>
				<li class="active">
					<strong>Sendinblue SMS Templates</strong>
				</li>
			</ol>
		</div>
		<div class="col-lg-8">
			<div class="title-action">
				<a href="#admin/sendinblue/sms/setting" class="newSMSTemplate btn btn-primary">Set default template</a>
				<a data-toggle="modal" data-target="#modal" class="newSMSTemplate btn btn-primary">New SMS Template</a>
			</div>
		</div>
	</div>
	<div class="wrapper  row ibox">
		<div class="ibox-content">
			<div class="row wrapper">
				<div class="col-lg-12">
					<h2 class="pull-left">Sendinblue SMS Templates</h2>
				</div>
			</div>
			<?php
			if(empty($templates)){
			?>
			<div class="row wrapper">
				<div class="col-lg-12">
					<h3>No SMS Templats, Click New SMS Template to Create One!</h3>
				</div>
			</div>
			<?php
			}
			?>
			<div id="vertical-timeline" class="vertical-container dark-timeline">
				<?php
				foreach($templates as $template){
				?>
				<div class="vertical-timeline-block">
					<div class="vertical-timeline-icon navy-bg">
						<i class="fa fa-comment"></i>
					</div>
					<div class="vertical-timeline-content">
						<p><?php echo $template['template'];?>
						</p>
						<a style="margin-left:10px;" data-toggle="modal" data-target="#modal" templateId="<?php echo $template['_id'];?>" class="newSMSTemplate btn btn-sm btn-info">Edit</a>
						<a class="deleteSMSTemplate btn btn-sm btn-danger" templateId="<?php echo $template['_id'];?>">Delete</a>
					</div>
				</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<script>
	var serialize = function(obj) {
		var str = [];
		for(var p in obj)
			if (obj.hasOwnProperty(p)) {
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
			}
		return str.join("&");
	};
	$('body').on('mousemove', '#sendinblueSmsTemplateForm', function(){
		var formData=[];
		$('#sendinblueSmsTemplateForm').on('submit', function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			$('#sendinblueSmsTemplateSubmit').prop('disabled',true);
			$("#sendinblueSmsTemplateForm select").each(function() {
				var fieldName = $(this).attr("name");
				var fieldVal = $(this).val();
				if(typeof fieldVal === 'undefined'){
					fieldVal = "";
				}
				if(! fieldVal ){
					fieldVal = "";
				}
				if($(this).val() === "? undefined:undefined ?"){
					fieldVal = "";
				}
				formData[fieldName] = fieldVal;
			});
			$("#sendinblueSmsTemplateForm input").each(function() {
				formData[this.name] = this.value;
			});
			$("#sendinblueSmsTemplateForm textarea").each(function() {
				formData[this.name] = this.value;
			});
			$('#modal').modal('hide');
			$.post('api/thing/create', serialize(formData), function(response){
				console.log(response);
				toastr.success('Template Created','',{'progressBar':true,'timeOut':1000});
				$('#results').load('api/admin/sendinblue/sms/templates');
			});
		});
	});
	$(function(){
		$('.newSMSTemplate').on('click', function(){
			$( ".modal-content" ).load( "api/admin/sendinblue/templateModal", { templateId: $(this).attr('templateId')}, function(){
			});
		});

		$('.deleteSMSTemplate').on('click', function(){
			var smsId = $(this).attr('templateId');
			swal({
				title: "Are you sure?",
				text: "This permanently deletes the template!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				showLoaderOnConfirm: true,
				closeOnConfirm: false,
			},
				 function(){
				$.get( "api/thing/remove/sendinblueSmsTemplate/"+smsId);
				setTimeout(function(){
					swal("Deleted!", "Tempalte has been deleted!", "success");
					$('#results').load('api/admin/sendinblue/sms/templates');
				}, 1000);
			});
		});
	});
</script>
