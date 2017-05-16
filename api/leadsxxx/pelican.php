<link href="css/plugins/dropzone/dropzone.css" rel="stylesheet">

<div class="attachment">

	<?php

	$apiObj->mongoSetDB($settings['database']);

	$apiObj->mongoSetCollection("attachments");

	$cursor=$apiObj->mongoFind(array('_parentId'=>$personId));

	if(!empty($cursor)){

		foreach($cursor as $attachment){

			$info = pathinfo($attachment['name']);

			$ext = $info['extension'];

	?>

	<div class="file-box">

		<div class="file">

			<a href="<?php echo $settings['base_uri'].'files/'.str_replace('/tmp/','',$attachment['tmpName'].'.'.$ext);?>" download="<?php echo $attachment['name'];?>">

				<span class="corner"></span>

				<div class="icon">

					<i class="fa fa-file"></i>

				</div>

				<div class="file-name">

					<?php echo (!empty($attachment['name']))?$attachment['name']:'';?>

				</div>

			</a>

		</div>

	</div>

	<?php

		}

	}

	?>

</div>

<div class="ibox-content">

	<div class="row">

		<div id="pelican" class="dropzone">

		</div>

	</div>

</div>

<script src="js/plugins/dropzone/dropzone.js"></script>

<script>

	Dropzone.autoDiscover = false;

	$('#pelican').dropzone({

		url: "api/leads/attSend",

		uploadMultiple: true,

		parallelUploads: 10,

		maxThumbnailFilesize:.2,

		maxFiles: 10,

		init: function() {

			$(this.element).addClass("dropzone");

			var myDropzone = this;

			var size = 0;

			this.on("sendingmultiple", function(file, xhr, formData) {

				formData.append('person_0_id','<?php echo $personId;?>');

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

</script>