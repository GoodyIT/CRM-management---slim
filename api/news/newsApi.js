$(document).ready(function() {
	var serialize = function(obj) {
		var str = [];
		for(var p in obj)
			if (obj.hasOwnProperty(p)) {
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
			}
		return str.join("&");
	};
	$('#summernote').summernote();
	$('.tags').tagsinput('refresh');

	$('#articleForm').on('submit', function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var formData =[];
		$('#articleSubmit').prop('disabled',true);
		//formData['person_0_createThing']='Y';
		formData['news_0_createThing']='Y';
		formData['news_0_tags_0_createThing']='Y';
		$("#articleForm select").each(function() {
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
		$("#articleForm input").each(function() {
			formData[this.name] = this.value;
		});
		$("#articleForm textarea").each(function() {
			formData[this.name] = this.value;
		});
		var inviteSelect = formData['news_0_tags'];
		formData['news_0_tags'] = undefined;
		for (var key in inviteSelect){
			formData['news_0_tags_'+[key]+'_id'] = inviteSelect[key];
			formData['news_0_tags_'+[key]+'_tagName'] = inviteSelect[key].toUpperCase().replace(/\s+/g, '');
		}
		$.post('api/thing/create',serialize(formData),function(response){
			console.log(formData);
			window.location.href= '#news';
			toastr.success('Article Created','',{'timeOut':1000,'pregressBar':true,});
		});
	});
	$('.articleDelete').on('click', function(event){
		var articleId = $(this).attr('deleteId');
		swal({
			title: "Are you sure?",
			text: "You will not be able to recover this post!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Yes, delete it!",
			showLoaderOnConfirm: true,
			closeOnConfirm: false,
		},
			 function(){
			$.get('api/thing/remove/news/'+articleId,function(response){
			});
			setTimeout(function(){
				swal("Deleted!", "Your imaginary file has been deleted.", "success");
				$('#results').load(base_uri+'api/news');
			}, 2000);
		});
	});
});