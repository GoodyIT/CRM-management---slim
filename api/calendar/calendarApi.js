var newAppointmentModal = function(obj) {
	$( ".modal-content" ).load( base_uri + "api/calendar/createModal?_parentId="+$(obj).attr('_parentId'), function(){
		$(".chosen-select").chosen({width:'100%',placeholder_text_multiple:'Select People'});
		$(".datepicker").pickadate({format: 'mm/dd/yyyy', min: '01/01/2015', max: '01/01/2020',selectYears: 5, selectMonths: true, });
		$(".timepicker").pickatime({format: 'h:i A' });
	});
};
var loadCalendar = function() {
	$('#calendar').fullCalendar({
		height: 'auto',
		defaultView: 'agendaWeek',
		eventClick: function (event) {
			var tempevent = event;
			if(typeof event.start != 'string'){
				event.start = event.start.format();
				event.end = event.end.format();
				event._start = event._start.format();
				event._end = event._end.format();
			}
			setTimeout(function(){
				event.start = moment(tempevent.start);
				event.end = moment(tempevent.end);
				event._start = moment(tempevent._start);
				event._end =moment(tempevent._end);
			}, 1000);
			$( ".modal-content" ).load( "api/calendar/updateModal", event, function(){
				$(".chosen-select").chosen({width:'100%', placeholder_text_multiple:'Select People'});
				$(".datepicker").pickadate({format: 'mm/dd/yyyy'  , min: '01/01/2015', max: '01/01/2020',selectYears: 5});
				$(".timepicker").pickatime({format: 'h:i A' });
			});
			$('#modal').modal('toggle');
		},
		eventLimit: 5, // adjust to 6 only for agendaWeek/agendaDay
		slotEventOverlap: true,
		allDaySlot: false,
		header: {
			left: 'prev,next, today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: false,
		eventSources: base_uri+'api/calendar',
	});
};
var appointDelete = function(event,obj) {
	event.stopImmediatePropagation();
	var appointmentId=$(obj).attr('appointmentId');
	swal({
		title: "Are you sure?",
		text: "This permanently deletes the appointment!",
		type: "error",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Yes, delete it!",
		showLoaderOnConfirm: true,
		closeOnConfirm: false,
	},
		 function(){
		$.get('api/thing/remove/appointment/'+appointmentId, function(response){
			console.log(response);
		});
		setTimeout(function(){
			swal("Deleted!", "Your appointment has been deleted.", "success");
			$('#modal').modal('hide');
			if(window.location.hash=='#calendar'){
				$('#results').load(base_uri + 'api/calendar/render');
			}
			$( ".calendarNotifications" ).load( "api/calendar/notificationController");
		}, 2000);
		viewappointmentlist();
	});
};
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
	var events = [];
	var appointments = [];
	var formData = [];
	$('body').on('mouseenter', '#appointform', function(){
		$('.autocomplete').autocomplete({
			source: 'api/calendar/search',
			minLength: 3,
			focus: function( event, ui ) {
				$( "#personIdDisplay" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( "#personIdDisplay" ).val( ui.item.label );
				$( "#personId" ).val( ui.item.value );
				return false;
			}
		});
		$('#appointform #appointSubmit').on('click', function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			$('#appointSubmit').prop('disabled',true);
			//formData['person_0_createThing']='Y';
			formData['person_0_appointment_0_createThing']='Y';
			$("#appointform select").each(function() {
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
			$("#appointform input").each(function() {
				formData[this.name] = this.value;
			});
			$("#appointform textarea").each(function() {
				formData[this.name] = this.value;
			});
			var date = [];
			$.get('api/calendar/today', function(response){
				date = response;
				var inviteSelect = formData['person_0_appointment_0_invitesPending'];
				formData['person_0_appointment_0_invitesPending'] = undefined;
				for (var key in inviteSelect){
					formData['person_0_appointment_0_invitesPending_'+[key]+'_agentId'] = inviteSelect[key];
					formData['person_0_appointment_0_invitesPending_'+[key]+'_notified'] = date;
				}
				if (($.trim($("#personId").val()) === "")&&($('#appointmentLabel').val()!='PERSONAL')) {
					swal({title:'You must select a person, choose Personal Reminder if you need a reminder!'});
					$('#appointSubmit').prop('disabled',false);
					return false;
				}else{
					$('#modal').modal('hide');
					$.post('api/thing/create', serialize(formData), function(response){
						console.log(response);
					});
					if(window.location.hash=='#calendar'){
						$('#results').load(base_uri + 'api/calendar/render');
					}
					$( ".calendarNotifications" ).load( "api/calendar/notificationController");
				viewappointmentlist();
				}
			});
		});
	});
});