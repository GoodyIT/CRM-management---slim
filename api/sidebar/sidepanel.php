<div id="right-sidebar" style="display:none">
	<div style="height:50px"><br></div>
	<div id="chatContent" class="sidebar-container">
	</div>
</div>
<div id="right-sidebar2" style="display:none">
	<div style="height:50px"><br></div>
	<div id="emailContent" class="sidebar-container">
	</div>
</div>
<div id="right-sidebar3" style="display:none">
	<div style="height:50px"><br></div>
	<div id="appointmentsContent" class="sidebar-container">
	</div>
</div>
<div id="right-sidebar4" style="display:none">
	<div style="height:50px"><br></div>
	<div id="bugContent" class="sidebar-container">
	</div>
</div>
<div id="right-sidebar5" style="display:none">
	<div style="height:50px"><br></div>
	<div id="dialerContent" class="sidebar-container">
	</div>
</div>
<script>
	var viewissuelist = function(filter){
		$('#bugContent').load(base_uri + 'api/issues?filter='+filter);
	};
	var viewappointmentlist = function(){
		$('#appointmentsContent').load(base_uri + 'api/calendar/notificationController');
	};
	var viewmaillist = function(){
		setTimeout(function(){
			$('#emailContent').load(base_uri + 'api/mail/notificationController');
		}, 100);
	};
	$(function(){
		$(window).resize( function(){
			var height=$(window).height()-150;
			$('.slimScrollDiv').height(height);
			$('#bugContent').height(height);
			$('#emailContent').height(height);
			$('#appointmentsContent').height(height);
			;
		});
		var height=$(window).height()-150;
		$('#bugContent').slimScroll({height: height});
		$('#emailContent').slimScroll({height: height+100});
		$('#appointmentsContent').slimScroll({height: height});
		$('.tab-2Icon').on('click', function(){
			$('#emailContent').load(base_uri + 'api/mail/notificationController');
		});
		$('.tab-3Icon').on('click', function(){
			$('#appointmentsContent').load(base_uri + 'api/calendar/notificationController');
		});
		$('.tab-4Icon').on('click', function(){
			$('#bugContent').load(base_uri + 'api/issues');
		});
		$('#sidbarContent').load('api/chat');
		$('#dialerContent').load('api/twilio/dialer');
		$('#chatContent').load(base_uri + 'api/chat');
		$('#emailContent').load(base_uri + 'api/mail/notificationController');
		$('#appointmentsContent').load(base_uri + 'api/calendar/notificationController');
		$('#bugContent').load(base_uri + 'api/issues');
	});
</script>