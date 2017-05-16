<div class=" animated fadeInRight">
	<div  class="row wrapper border-bottom white-bg page-heading ">
		<div class="col-lg-4">
			<h2>Create SMS Templates</h2>
			<ol class="breadcrumb">
				<li>
					<a href="#">Home</a>
				</li>
				<li class="active">
					<a href="#admin/settings">Settings</a>
				</li>
				<li class="active">
					<strong>SMS Templates</strong>
				</li>
			</ol>
		</div>
		<div class="col-lg-8">
			<div class="title-action">
				<a data-toggle="modal" data-target="#modal" class="newSMSTemplate btn btn-primary">New SMS Template</a>
			</div>
		</div>
	</div>
	<div class="wrapper  row ibox">
		<div class="ibox-content">
			<div class="row wrapper">
				<div class="col-lg-12">
					<h2 class="pull-left">SMS Templates</h2>
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
	$(function(){
		$('.newSMSTemplate').on('click', function(){
			$( ".modal-content" ).load( "api/twilio/templateModal", { templateId: $(this).attr('templateId')}, function(){
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
				$.get( "api/thing/remove/smsTemplate/"+smsId);
				setTimeout(function(){
					swal("Deleted!", "Tempalte has been deleted!", "success");
					$('#results').load('api/twilio/messageManager');
				}, 1000);
			});
		});
	});
</script>
