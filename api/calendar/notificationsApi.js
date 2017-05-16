$(document).ready(function() {
	var convArrToObj = function(array){
		var thisEleObj = new Object();
		if(typeof array == "object"){
			for(var i in array){
				var thisEle = convArrToObj(array[i]);
				thisEleObj[i] = thisEle;
			}
		}else {
			thisEleObj = array;
		}
		return thisEleObj;
	};
	var serialize = function(obj) {
		var str = [];
		for(var p in obj)
			if (obj.hasOwnProperty(p)) {
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
			}
		return str.join("&");
	};
	//setInterval(function(){
	//	$('#toastr').load('api/calendar/toastr.php');
	//}, 1000 * 10 );
	$( ".calendarNotifications" ).load( "api/calendar/notificationController");
	//setInterval( function() {
	//	$( ".calendarNotifications" ).load( "api/calendar/notificationController");
	//}, 30000);
	$('body').on('mouseenter', '#notifications', function(){
		$('.appointmentDetach').on('click', function(event){
			event.stopImmediatePropagation();
			var appointmentId=$(this).attr('appointmentId');
			var inviteId = $(this).attr('inviteId');
			swal({
				title: "Are you sure?",
				text: "If no other users are attached, this appointment will be lost!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#f5ad5b",
				confirmButtonText: "Yes, Detach Me!",
				showLoaderOnConfirm: true,
				closeOnConfirm: false,
			},
				 function(){
				$.get('api/calendar/remove/appointment/invitesAccepted/'+appointmentId+'/'+inviteId, function(response){
					console.log(response);
				});
				$.get('api/calendar/remove/appointment/invitesPending/'+appointmentId+'/'+inviteId, function(response){
					console.log(response);
				});
				setTimeout(function(){
					swal("Detached!", "You have been detached from the appointment!", "success");
					if(window.location.hash=='#calendar'){
						$('#results').load(base_uri + 'api/calendar/render');
					}
					$( ".calendarNotifications" ).load( "api/calendar/notificationController");
				}, 2000);
			});
		});
		$('.appointmentAccept').on('click', function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			var data = {
				person_0_appointment_0_invitesAccepted_0_agentId:$(this).attr('agentId'), 
				person_0_appointment_0_id:$(this).attr('appointmentId'),
				person_0_appointment_0_createThing:'Y',
			};
			$.get('api/calendar/remove/appointment/invitesPending/'+$(this).attr('appointmentId')+'/'+$(this).attr('inviteId'), function(response){
				console.log(response);
			});
			$.post('api/calendar/push/appointment/invitesAccepted/'+$(this).attr('appointmentId'), data,function(response){
				console.log(response);
			});
			setTimeout(function(){
				$( ".calendarNotifications" ).load( "api/calendar/notificationController");
			}, 500);
			if(window.location.hash=='#calendar'){
				$('#results').load('api/calendar/render');
			}
			toastr.remove();
		});
		$('.appointmentDecline').on('click', function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			var data = {
				person_0_appointment_0_invitesDeclined_0_agentId:$(this).attr('agentId'), 
				person_0_appointment_0_id:$(this).attr('appointmentId'),
				person_0_appointment_0_createThing:'Y',
			};
			$.get('api/calendar/remove/appointment/invitesPending/'+$(this).attr('appointmentId')+'/'+$(this).attr('inviteId'), function(response){
				console.log(response);
			});
			$.post('api/calendar/decline/appointment/invitesDeclined/'+$(this).attr('appointmentId'), data,function(response){
				console.log(response);
			});
			setTimeout(function(){
				$( ".calendarNotifications" ).load( "api/calendar/notificationController");
			}, 500);
			if(window.location.hash=='#calendar'){
				$('#results').load('api/calendar/render');
			}
			toastr.remove();
		});
	});
});