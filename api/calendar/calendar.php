<?php// echo $_SESSION['api']['user']['_id'];exit();?>
<div class="animated fadeInRight">
	<div  class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-4">
			<h2>Calendar</h2>
			<ol class="breadcrumb">
				<li>
					<a href="#">Home</a>
				</li>
				<li class="active">
					<strong>Calendar</strong>
				</li>
			</ol>
		</div>
		<div class="col-lg-8">
			<div class="title-action">
				<button id="newAppointment" data-toggle="modal" data-target="#modal" _parentId="" class="btn btn-primary">Add New Appointment</button>
			</div>
		</div>
	</div>
	<div id="calHead" class="row">
		<div class="ibox float-e-margins">
			<div>
			</div>
			<div class="ibox-content ui-calendar">
				<div class="btn-toolbar">
					<p class="lead">Appointment Calendar</p>
				</div>
				<div id="calendar" class="calendar"></div>
			</div>
		</div>
	</div>
</div>


<script>
	$('#newAppointment').on('click', function(){
		newAppointmentModal(this);
	});
    $(document).ready(function() {
        loadCalendar();
    }); 
        
</script>
