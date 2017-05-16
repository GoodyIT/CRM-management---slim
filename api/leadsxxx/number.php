<div class="ibox-title">
	<div class="row">
		<div class="col-xs-11 text-left">
			<h4>Recordings</h4>
		</div>
		<div class="col-xs-1  text-center">
		</div>
	</div>
</div>
<div class="ibox-content">
	<div class="row">
		<div class="col-md-4">
			<input class="form-control" id="numberInput">
		</div>
		<div class="col-md-4">
			<a id="numberSearch" class="btn btn-primary">Search Recordings</a>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$('#numberSearch').on('click', function(){
			toastr.warning('processing....');
			$('#results').load(base_uri + 'api/leads/recordingsnumber/'+$('#numberInput').val().replace(/\W/g, ''), function(){
				toastr.remove();
				setTimeout(function(){
					toastr.success('done');
				},100);
			});
		});

	});
</script>