<?php
//echo "<span style='color:white'>".date("m-d-Y H:i:s")."</span>";
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $settings['site_name'];?></title>
		<script>
			var base_uri = "<?php echo $settings['base_uri'];?>";
		</script>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="css/animate.css" rel="stylesheet">
		<link href="css/plugins/fullcalendar/fullcalendar.css" rel="stylesheet">
		<link href="css/plugins/fullcalendar/classic.css" rel="stylesheet">
		<link href="css/plugins/fullcalendar/classic.date.css" rel="stylesheet">
		<link href="css/plugins/fullcalendar/classic.time.css" rel="stylesheet">
		<link href="css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
		<link href="css/plugins/iCheck/custom.css" rel="stylesheet">
		<link href="css/plugins/summernote/bootstrap-tagsinput.css" rel="stylesheet">
		<link href="css/plugins/chosen/chosen.css" rel="stylesheet">
		<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">
		<link href="js/jqueryui/jquery-ui.min.css" rel="stylesheet">
		<!-- Morris -->
		<link href="css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">
		<!-- Gritter -->
		<link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
		<!-- Summernote -->
		<link href="css/plugins/summernote/summernote.css" rel="stylesheet">
		<link href="css/plugins/summernote/summernote-bs3.css" rel="stylesheet">

        <script src="js/plugins/html5media/html5media.min.js"></script>

		<link href="css/style.css" rel="stylesheet">
		<script src="js/handlebars.js"></script>
	</head>
	<body class="top-navigation" >
		<div id="wrapper">
			<div id="page-wrapper" class="gray-bg">
				<div class="modal inmodal" id="modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
					<div class="modal-dialog">
						<div id="uniMod" class="modal-content animated bounceIn">
						</div>
					</div>
				</div>
				<div class="row border-bottom white-bg" >
					<nav class="navbar navbar-fixed-top" role="navigation">
						<div class="navbar-header">
							<button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
								<i class="fa fa-reorder"></i>
							</button>
							<a class="navbar-toggle tab-4Icon">
								<i class="fa fa-question"></i>
							</a>
							<a class="navbar-toggle tab-3Icon">
								<i class="fa fa-calendar-o"></i>
							</a>
							<a class="navbar-toggle tab-2Icon">
								<i class="fa fa-envelope"></i>
							</a>
							<a class="navbar-toggle tab-1Icon">
								<i class="fa fa-comments"></i>
							</a>
							<?php if($_SESSION['api']['user']['firstname']){
	if(!empty($_SESSION['api']['user']['lastname'])){
		$linitial = substr($_SESSION['api']['user']['lastname'], 0,1) . ".";
	}
	echo '<a href="#" class="navbar-brand">'.ucwords(strtolower($_SESSION['api']['user']['firstname'])).' ' .$linitial.'</a>';
} else {
	echo '<a href="#" class="navbar-brand">EBC</a>';
}?>

						</div>
						<div class="navbar-collapse collapse" id="navbar">
							<ul class="nav navbar-nav">
								<li>
									<a href="#" class="confirmation">Dashboard</a>
								</li>
								<li>
									<a href="#lead" class="confirmation">Leads</a>
								</li>
								<li>
									<a href="#clients" class="confirmation">Clients</a>
								</li>
								<li>
									<a href="#policies" class="confirmation">Policies</a>
								</li>

								<li>
									<a href="#news" class="confirmation">News</a>
								</li>
								<li>
									<a href="#reports" class="confirmation">Reports</a>
								</li>

							    <?php
                                  if((!empty($_SESSION['api']['user']['permissionLevel'])) && ((strtoupper($_SESSION['api']['user']['permissionLevel']) == "ADMINISTRATOR")|| ((strtoupper($_SESSION['api']['user']['permissionLevel']) == "MANAGER"))) ){
                                ?>
                                    <li>
                                        <a href="#admin/settings" class="confirmation">Settings</a>
                                    </li>
                                <?php } ?>
								<li>
									<a href="docs" target='_blank' class="confirmation">Docs</a>
								</li>

                                <li>


                                </li>
								<?php
								/*
								<li class="dropdown">
									<a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Leads <span class="caret"></span></a>
									<ul role="menu" class="dropdown-menu">
										<li><a href="#lead">Leads</a></li>
										<li><a href="#lead/create">Create</a></li>
									</ul>
								</li>
								<li class="dropdown">
									<a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Policies <span class="caret"></span></a>
									<ul role="menu" class="dropdown-menu">
										<li><a href="#customers">Customers</a></li>
										<li><a href="#policies">Policies</a></li>
									</ul>
								</li>
								<li class="dropdown">
									<a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> News <span class="caret"></span></a>
									<ul role="menu" class="dropdown-menu">
										<li><a href="#calendar">Calendar</a></li>
										<li><a href="#news">News</a></li>
									</ul>
								</li>
								<li class="dropdown">
									<a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Reports <span class="caret"></span></a>
									<ul role="menu" class="dropdown-menu">
										<li><a href="#sales">Sales</a></li>
										<li><a href="#agents">Agent Logs</a></li>
									</ul>
								</li>
								<li class="dropdown">
									<a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Settings <span class="caret"></span></a>
									<ul role="menu" class="dropdown-menu">
										<li><a href="#system">System Settings</a></li>
										<li><a href="#agency">Agency Settings</a></li>
										<li><a href="#admin/user/list">Users</a></li>
										<li><a href="#user">User Settings</a></li>
									</ul>
								</li>
                                */
								?>
								<li class=" hidden-md hidden-lg">
									<a href="<?php echo $settings['base_uri'];?>api/auth/logout">
										<i class="fa fa-sign-out"></i> Log out
									</a>
								</li>
							</ul>
							<ul class="nav navbar-top-links navbar-right hidden-sm hidden-xs">
                                 <li >
									<a id="tab-Phone" class="tab-Phone count-info">
		    							<i id="phoneStatusIcon" class="fa fa-phone" style="margin-right:0;"></i>
	    							</a>
								</li>
								<li >
									<a id="tab-1Icon" class="tab-1Icon count-info" data-toggle="tooltip" data-placement="top" title="Chat">
										<i class="fa fa-comment" style="margin-right:0;"></i>
									<span id="chatCounter" class="label label-warning bounce animated"></span>
									</a>
								</li>
								<li >
									<a id="tab-2Icon" class="tab-2Icon count-info" data-toggle="tooltip" data-placement="top" title="Mail">
										<i class="fa fa-envelope" style="margin-right:0;"></i>
									<span id="mailCounter" class="label label-warning bounce animated"></span>
									</a>
								</li>
								<li >
									<a id="tab-3Icon" class="tab-3Icon count-info" data-toggle="tooltip" data-placement="top" title="Appointments">
										<i class="fa fa-calendar-o" style="margin-right:0;"></i>
									<span id="appointmentCounter" class="label label-warning bounce animated"></span>
									</a>
								</li>
								<li >
									<a id="tab-4Icon" class="tab-4Icon count-info" data-toggle="tooltip" data-placement="top" title="Issue Tracker">
										<i class="fa fa-question" style="margin-right:0;"></i>
									</a>
								</li>
								<li>
									<a href="<?php echo $settings['base_uri'];?>api/auth/logout">
										<i class="fa fa-sign-out"></i> Log out
									</a>
								</li>
							</ul>
						</div>
					</nav>
					<div id="toastr"></div>
				</div>
				<div class="wrapper wrapper-content" style="margin-top:40px">
					<div class="container">
						<!-- Load API INFO HERE-->
						<div id="results"></div>
					</div>
				</div>
			</div>
			<div class="footer">
				<div class="pull-right">
					<?php echo date("l F j, Y");?>
				</div>
				<div>
					<strong>Copyright</strong> EBrokerCenter &copy; <?php echo date("Y");?>
				</div>
			</div>
		</div>
		<!-- Mainly scripts -->
		<script src="js/jquery-2.1.1.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jqueryui/jquery-ui.min.js"></script>
		<script src="js/jqueryui/jquery.ui.datepicker.js"></script>
		<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
		<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		<!-- Routes -->
		<script src="js/routie.js"></script>
		<script src="js/routes.js"></script>
		<!-- Custom and plugin javascript -->
		<script src="js/inspinia.js"></script>
		<script src="js/plugins/pace/pace.min.js"></script>
		<!-- Flot -->
		<script src="js/plugins/flot/jquery.flot.js"></script>
		<script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
		<script src="js/plugins/flot/jquery.flot.spline.js"></script>
		<script src="js/plugins/flot/jquery.flot.resize.js"></script>
		<script src="js/plugins/flot/jquery.flot.pie.js"></script>
		<script src="js/plugins/flot/jquery.flot.symbol.js"></script>
		<script src="js/plugins/flot/jquery.flot.time.js"></script>
		<!-- Jvectormap -->
		<script src="js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
		<script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
		<!-- EayPIE -->
		<script src="js/plugins/easypiechart/jquery.easypiechart.js"></script>
		<!-- Summernote -->
		<script src="js/plugins/summernote/summernote.min.js"></script>
		<script src="js/plugins/summernote/bootstrap-tagsinput.min.js"></script>
		<!-- Calendar -->
		<script src="js/plugins/fullcalendar/moment.min.js"></script>
		<script src="js/plugins/fullcalendar/picker.js"></script>
		<script src="js/plugins/fullcalendar/picker.date.js"></script>
		<script src="js/plugins/fullcalendar/picker.time.js"></script>
		<script src="js/plugins/chosen/chosen.jquery.js"></script>
		<script src="js/plugins/fullcalendar/fullcalendar.min.js"></script>
		<script src="js/plugins/sweetalert/sweetalert.min.js"></script>
		<script src="js/plugins/idle-timer/idle-timer.min.js"></script>
		<script src="js/plugins/toastr/toastr.min.js"></script>
		<script src="api/calendar/notificationsApi.js"></script>

		<!-- Table Sorter -->
		<script type="text/javascript" src="js/plugins/tablesorter/tablesorter.js"></script>
		<!-- Chat -->
		<script src="js/plugins/chat/firebase.js"></script>
		<!-- Calendar -->
		<script src="api/calendar/calendarApi.js"></script>
		<!-- Twilio -->
		<script src="api/twilio/twilioApi.js"></script>
		<!-- iCheck -->
		<script src="js/plugins/iCheck/icheck.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
                $.ajax({
        			url: "<?php echo $settings['base_uri'];?>api/vicidialer/getagentstatus/<?php echo trim($_SESSION['api']['user']['extension']);?>",
        			success: function(data) {
console.log(data[0]);
                    console.log(data);
        				if(data['response'] == "PAUSED"){
                            console.log("PAUSED!");
                        }
        			}
        		});
				$(document).tooltip();
				//$.get(base_uri + 'api/chat',function(data){$(data).appendTo('#wrapper');});
				$.get(base_uri + 'api/sidebar',function(data){$(data).appendTo('#wrapper');});
				$(document).idleTimer({
					timeout: 2400000 * 55,
				});
				$(document).on("idle.idleTimer", function(event, elem, obj) {
					var count = 10;
					event.stopImmediatePropagation();
					var counter = setInterval(function() {
						count = count - 1;
						if (count <= 0) {
							clearInterval(counter);
							toastr.clear();
							window.location = 'api/auth/logout';
							toastr.info('Logged Out');
							toastr.options = {
								'timeOut': 10000,
								"showDuration": 1000,
								"hideDuration": 1000,
								"progressBar": true,
								'debug': false,
								"preventDuplicates": true,
							};
							return;
						}
						document.getElementById("timer").innerHTML = count;
						$(document).on("active.idleTimer", function(event, elem, obj, triggerevent) {
							clearInterval(counter);
							toastr.clear();
						});
					}, 1000);
					toastr.error('Logout in <span id="timer">10</span> seconds!!', 'Idle Detected', {
						'timeOut': 10000,
						"showDuration": 1000,
						"hideDuration": 1000,
						"progressBar": true,
						'debug': false,
						"preventDuplicates": true,
					});
				});
				$(".chosen-select").chosen({
					width: '100%'
				});
				$(".datepicker").pickadate({
					format: 'mm-dd-yyyy',
					min: '01-01-1920',
					max: '01-01-2020',
					selectYears: 100,
					selectMonths: true,
				});
				$(".timepicker").pickatime({
					format: 'h:i A'
				});

			});


		</script>
	</body>
</html>