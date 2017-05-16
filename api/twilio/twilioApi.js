var newSMSModal = function(obj) {
	$('#toNumber').val($(obj).attr('selectedNumber'));
};
var smsSubmit =function(){
	
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
	$('body').on('mousemove', '#smsTemplateForm', function(){
		var formData=[];
		$('#smsTemplateForm').on('submit', function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			$('#smsTemplateSubmit').prop('disabled',true);
			$("#smsTemplateForm select").each(function() {
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
			$("#smsTemplateForm input").each(function() {
				formData[this.name] = this.value;
			});
			$("#smsTemplateForm textarea").each(function() {
				formData[this.name] = this.value;
			});
			$('#modal').modal('hide');
			$.post('api/thing/create', serialize(formData), function(response){
				console.log(response);
				toastr.success('Template Created','',{'progressBar':true,'timeOut':1000});
				$('#results').load('api/twilio/messageManager');
			});
		});
	});
	$('body').on('mousemove', '#smsform', function(){
		var formData=[];
		$('#smsform').on('submit', function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			$('#smsSubmit').prop('disabled',true);
			$("#smsform select").each(function() {
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
			$("#smsform input").each(function() {
				formData[this.name] = this.value;
			});
			$("#smsform textarea").each(function() {
				formData[this.name] = this.value;
			});
			$('#modalSMS').modal('hide');
			$.post('api/thing/create', serialize(formData), function(response){
				console.log(response);
			});
			$.post('api/twilio/sms', serialize(formData), function(response){
				console.log(response);
				toastr.success('Message Sent','',{'progressBar':true,'timeOut':1000});
			});
		});
	});
});