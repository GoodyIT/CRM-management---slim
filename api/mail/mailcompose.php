<div  >
	<link href="css/plugins/dropzone/dropzone.css" rel="stylesheet">
	<div class="row">
		<div class="col-lg-3">
			<?php include "mailmenu.php"; ?>
		</div>
		<div class="col-lg-9 animated fadeInRight">
			<div class="mail-box-header">
				<div class="pull-right tooltip-demo">
					<button id="mailSend" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Send"><i class="fa fa-reply"></i> Send</button>
					<button id="mailDraft" class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to draft folder"><i class="fa fa-pencil"></i> Draft</button>
					<button id="mailDiscard" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Discard email"><i class="fa fa-times"></i> Discard</button>
				</div>
				<h2>
					Compse mail
				</h2>
			</div>
			<div class="mail-box">
				<form id="mailComposeForm" class="form-horizontal">
					<div class="mail-body">
						<div class="form-group"><label id="tags" class="col-sm-2 control-label">To:</label>
							<div class="col-sm-10">
								<input type="text" id="personDisplay" class="form-control autocomplete" name="mail_0_to" value="<?php echo (!empty($email['sender']))?$email['sender']:((!empty($email['from']))?$email['from']:'');?>" required>
								<p id="desc"></p>
							</div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Subject:</label>
							<div class="col-sm-10"><input type="text" class="form-control" name="mail_0_subject" value="<?php echo (!empty($email['subject']))?'FW:'.$email['subject']:'';?>"></div>
						</div>
					</div>
					<div class="mail-text">
						<textarea id="summernote" name="mail_0_bodyHtml">
							<?php echo (!empty($email['bodyHtml']))?$email['bodyHtml']:((!empty($email['strippedHtml']))?$email['strippedHtml'].$email['strippedSignature']:'');?>
						</textarea>
					</div>
					<div id="pelican" class="dropzone">

					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="js/plugins/dropzone/dropzone.js"></script>
<script>
	Dropzone.autoDiscover = false;
	$(document).ready(function(){
		var serialize = function(obj) {
			var str = [];
			for(var p in obj)
				if (obj.hasOwnProperty(p)) {
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				}
			return str.join("&");
		};
		function submitMyFormWithData(data)
		{
			var formData = data;
			$('#mailSend').prop('disabled',true);
			formData['mail_0_createThing']='Y';
			formData['mail_0_folder']='SENT';
			formData['mail_0_from']="<?php echo $_SESSION['api']['user']['email']; ?>";
			$("#mailComposeForm select").each(function() {
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
			$("#mailComposeForm input").each(function() {
				formData[this.name] = this.value;
			});
			$("#summernote").each(function() {
				formData[this.name] = this.value;
			});
			<?php
			if(!empty($email['mailAttachments'])){
				$i=0;
				foreach($email['mailAttachments'] as $attachment){
			?>
			formData['mail_0_mailAttachments_<?php echo $i;?>_tmpName'] = "<?php echo $attachment['tmpName'];?>"
			formData['mail_0_mailAttachments_<?php echo $i;?>_size']    = "<?php echo $attachment['size'];?>"
			formData['mail_0_mailAttachments_<?php echo $i;?>_type']    = "<?php echo $attachment['type'];?>"
			formData['mail_0_mailAttachments_<?php echo $i;?>_error']   = "<?php echo $attachment['error'];?>"
			formData['mail_0_mailAttachments_<?php echo $i;?>_name']    = "<?php echo $attachment['name'];?>"
				<?php
					$i++;
				}
			}
				?>
			console.log(serialize(formData));
			console.log(formData);
			$.post('api/mail/send',serialize(formData)).done(function(response){
				console.log(response);
				toastr.success('Mail Sent','',{'timeOut':1000,'pregressBar':true,});
				window.location.href= '#mail';
			})
				.fail(function(response){
				window.location.href= '#mail';
				console.error(response);
			});
		};
		$('#pelican').dropzone({
			url: "api/mail/attSend",
			autoDiscover: false,
			autoProcessQueue: false,
			uploadMultiple: true,
			parallelUploads: 10,
			maxThumbnailFilesize:.2,
			maxFiles: 10,
			init: function() {
				$(this.element).addClass("dropzone");
				var myDropzone = this;
				var size = 0;
				var removeButton = Dropzone.createElement("<button>Remove file</button>");
				this.on("addedfile", function(file) {
					var removeButton = Dropzone.createElement("<button style='cursor:pointer;' class='btn btn-block btn-danger'><i style='cursor:pointer;' class='fa fa-times'></i></button>");
					var _this = this;
					removeButton.addEventListener("click", function(e) {
						e.preventDefault();
						e.stopPropagation();
						_this.removeFile(file);
					});
					file.previewElement.appendChild(removeButton);
				});
				<?php
				if(!empty($email['mailAttachments'])){
					$i = 0;
					foreach($email['mailAttachments'] as $attachment){
						$i++;
						$info = pathinfo($attachment['name']);
						$ext = $info['extension'];
				?>
				var mockFile<?php echo $i; ?> = { name: "<?php echo $attachment['name'];?>", size: <?php echo $attachment['size'];?> };
												 myDropzone.emit("addedfile", mockFile<?php echo $i; ?>);
												 myDropzone.emit("thumbnail", mockFile<?php echo $i; ?>, "<?php echo $settings['base_uri'].'api/mail/files/'.str_replace('/tmp/','',$attachment['tmpName'].'.'.$ext);?>");
												 myDropzone.emit("complete", mockFile<?php echo $i; ?>);
												 <?php
					}
				}
												 ?>
												 $("#mailSend").on("click", function(event) {
												 var temp = [];
												 event.preventDefault();
												 event.stopPropagation();
												 if (myDropzone.getQueuedFiles().length > 0)
												 {
												 myDropzone.processQueue();
												} else {
													submitMyFormWithData(temp);
												}
			});
			this.on("sendingmultiple", function(file, xhr, formData) {
			formData.append('attachmentCount','<?php echo count($email['mailAttachments']);?>');
		});
		this.on("addedfile", function(file, response) {
			size = size + file.size;
			if(size>=25000000){
				swal('Message size of 25mb exceeded!');
				this.removeFile(file);
			}
			console.log(size);
		});
		this.on("removedfile", function(file, response) {
			size = size - file.size;
			console.log(size);
		});
		this.on("successmultiple", function(files, response) {
			console.log(response);
			$('#mailSend').prop('disabled',true);
			submitMyFormWithData(response);
		});
		this.on("errormultiple", function(files, response) {
			console.error(response);
		});
		this.on("maxfilesexceeded", function(file) { 
			swal('File Limit of 10 Exceeded!');
			this.removeFile(file);
		});
	}
					  });
	$('#summernote').summernote({dialogsInBody: true});
	$('#mailDraft').on('click', function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var formData =[];
		$('#mailDraft').prop('disabled',true);
		formData['mail_0_createThing']='Y';
		formData['mail_0_folder']='DRAFT';
		formData['mail_0_trash']='N';
		formData['mail_0_from']="<?php echo $_SESSION['api']['user']['email']; ?>";
		$("#mailComposeForm select").each(function() {
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
		$("#mailComposeForm input").each(function() {
			formData[this.name] = this.value;
		});
		$("#summernote").each(function() {
			formData[this.name] = this.value;
		});
		console.log(serialize(formData));
		$.post('api/thing/create',serialize(formData),function(response){
			console.log(response);
			window.location.href= '#mail';
			toastr.success('Draft Saved','',{'timeOut':1000,'pregressBar':true,});
		});
	});
	$('#mailDiscard').on('click', function(event){
		$("#mailComposeForm").closest('form').find("input[type=text], textarea").val("");
		window.location.href= '#mail';
		toastr.error('Discarded','',{'timeOut':1000,'pregressBar':true,});
	});
	$('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});
	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}
	$( ".autocomplete" )
	// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB &&
			$( this ).autocomplete( "instance" ).menu.active ) {
			event.preventDefault();
		}
	})
		.autocomplete({
		source: function( request, response ) {
			$.getJSON('api/mail/contact/search', {
				term: extractLast( request.term )
			}, response );
		},
		minLength: 3,
		search: function() {
			// custom minLength
			var term = extractLast( this.value );
			if ( term.length < 2 ) {
				return false;
			}
		},
		focus: function( event, ui ) {
			return false;
		},
		select: function( event, ui ) {
			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push( "" );
			this.value = terms.join( ", " );
			//$( "#personDisplay" ).val( ui.item.label );
			//$( "#emailId" ).val( ui.item.value );
			//$( "#desc" ).html( ui.item.desc );
			console.log(terms);
			return false;
		}
	})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
			.append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
			.appendTo( ul );
	};
	});
</script>
