<div class="small-chat-box">
	<div class="heading">
		<?php
		if($manager==0){
		?>
		<select disabled style="color:black; font-size:12px; padding:0; height:auto;" id="roomId" class="form-control">
			<?php
			foreach($usergroups as $group){
			?>
			<option <?php echo ($group['_id']==$roomId)?'selected':'';?> value="<?php echo $group['_id'];?>"><?php echo $group['label'];?></option>
			<?php
			}
			?>
		</select>
		<?php
		}else{
		?>
		<select style="color:black; font-size:12px; padding:0; height:auto;" id="roomId" class="form-control">
			<?php
			foreach($usergroups as $group){
			?>
			<option <?php echo ($group['_id']==$roomId)?'selected':'';?> value="<?php echo $group['_id'];?>"><?php echo $group['label'];?></option>
			<?php
			}
			?>
		</select>
		<?php
		}
		?>
	</div>
	<div id="chat-output" class="content"></div>
	<div class="form-chat">
		<div class="input-group input-group-sm">
			<input type="text" id="chat-input" placeholder="NOT A SOCIAL CHAT!" class="form-control">
			<span class="input-group-btn">
				<?php
				if($podmanager==0){
				?>
				<a id="setup-datachannel" class="btn btn-primary"><i class="fa fa-comment"></i></a>
				<?php
				}else{
				?>
				<a id="toastBtn" class="btn btn-warning btn-xs"><i class="fa fa-bell"></i></a>
				<a id="swalBtn" class="btn btn-info btn-xs"><i class="fa fa-bullhorn"></i></a>
				<a id="linkBtn" class="btn btn-primary btn-xs"><i class="fa fa-link"></i></a>
				<?php
				}
				?>
			</span>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		var height=$('#right-sidebar').height()-150;
		$('#chat-output').slimScroll({height: height});
		var connection = new Firebase('<?php echo $settings["firebaseUrl"];?>/chat/'+$('#roomId').val());
		connection.authWithCustomToken('<?php echo $token;?>', function(error, authData) { 
			if (error) {
				console.log("Firebase Login Failed!", error);
			} else {
				console.log("Firebase Login Succeeded!");
			}}, {
			remember: "none"
		});
		var userid = '<?php echo $name;?>';
		var messageList = $('#chat-output');
		var now = $('#chatCounter').text();
		$('#roomId').on('change', function(){
			connection.off('child_added');
			$('#chatContent').load(base_uri + 'api/chat?roomId='+$('#roomId').val());
		});
		connection.limitToLast(10).on('child_added', function (snapshot) {
			messageList[0].scrollTop = messageList[0].scrollHeight;
			var data = snapshot.val();
			var username = data.name || "anonymous";
			var time = data.time;
			var stamp = data.stamp;
			<?php 
			if($podmanager){
			?>
			if(/<[^>]*>/ig.test(data.message)){
				var scriptRemove = (data.message).replace(/<[^>]*>/ig,'');
				if(/'[^']*/.test(scriptRemove)){
					var quoteRemove = scriptRemove.match(/'[^']*/);
				}else{
					var quoteRemove = [];
					quoteRemove[0] = "'"+scriptRemove;
				}
				if((quoteRemove!=null)||(quoteRemove!='')||(quoteRemove!=undefined)||(typeof(quoteRemove)!='undefined')||(typeof(quoteRemove)!='null')){
					var message = quoteRemove[0].substring(1);
				}else{
					var message = 'blank';
				}
			}else{
				var message = data.message;
			}
			<?php
			}else{
			?>
			if(moment().subtract(30, 'seconds').unix() <= stamp){
				var message = data.message;
			}else{
				if(/<[^>]*>/ig.test(data.message)){
					var scriptRemove = (data.message).replace(/<[^>]*>/ig,'');
					if(/'[^']*/.test(scriptRemove)){
						var quoteRemove = scriptRemove.match(/'[^']*/);
					}else{
						var quoteRemove = [];
						quoteRemove[0] = "'"+scriptRemove;
					}
					if((quoteRemove!=null)||(quoteRemove!='')||(quoteRemove!=undefined)||(typeof(quoteRemove)!='undefined')||(typeof(quoteRemove)!='null')){
						var message = quoteRemove[0].substring(1);
					}else{
						var message = 'blank';
					}
				}else{
					var message = data.message;
				}
			}
			<?php
			}
			?>
			var manager = data.manager;
			if((!$('#tab-1Icon').hasClass('active'))&&(stamp>=moment().unix())){
				now++;
				$('#chatCounter').html(now);
			}
			if(username=='<?php echo $name;?>'){
				$('#chat-output').append('<div class="right"><div class="author-name">'+username+'<small class="chat-date"> '+time+'</small></div><div class="chat-message active" style="background:#2AABDE">'+message+'</div></div>').animate({scrollTop: $('#chat-output').prop("scrollHeight")}, 300);
			}else{
				if(manager===1){
					$('#chat-output').append('<div class="left"><div class="author-name">'+username+'<small class="chat-date"> '+time+'</small></div><div class="chat-message active" style="background:#FB4E4E">'+message+'</div></div>').animate({scrollTop: $('#chat-output').prop("scrollHeight")}, 300);
				}else{$('#chat-output').append('<div class="left"><div class="author-name">'+username+'<small class="chat-date"> '+time+'</small></div><div class="chat-message active" style="background:#E4E6E6; color:black">'+message+'</div></div>').animate({scrollTop: $('#chat-output').prop("scrollHeight")}, 300);
					 }
			}
		});
		$('#toastBtn').on('click', function(){
			if($('#chat-input').val()!='') {
				var scrpt = "<script>toastr.warning('"+$('#chat-input').val()+"','',{'timeOut':5000,'progressBar':true,})<\/script>";
				connection.push({name:userid, message:scrpt, time: moment().format("M/D/YYYY h:mm a"), stamp: moment().unix(), manager:<?php echo $podmanager;?>,});
				$('#chat-input').val('');
			}
		});
		$('#swalBtn').on('click', function(){
			if($('#chat-input').val()!='') {
				var scrpt = "<script>swal('"+$('#chat-input').val()+"')<\/script>";
				connection.push({name:userid, message:scrpt, time: moment().format("M/D/YYYY h:mm a"), stamp: moment().unix(), manager:<?php echo $podmanager;?>,});
				$('#chat-input').val('');
			}
		});
		$('#linkBtn').on('click', function(){
			if($('#chat-input').val()!='') {
				var scrpt = "<a target='_blank' href='http://"+$('#chat-input').val()+"'>"+$('#chat-input').val()+"<\/a>";
				connection.push({name:userid, message:scrpt, time: moment().format("M/D/YYYY h:mm a"), stamp: moment().unix(), manager:<?php echo $podmanager;?>,});
				$('#chat-input').val('');
			}
		});
		$('#chat-input').on('keypress', function (e) {
			if (e.keyCode != 13) return;
			if($(this).val()!='') {
				if(<?php echo $manager;?>===1){
					connection.push({name:userid, message:$(this).val(), time: moment().format("M/D/YYYY h:mm a"), stamp: moment().unix(), manager:<?php echo $podmanager;?>,});
				}else{
					connection.push({name:userid, message:($(this).val()).replace(/<[^>]*>/,''), time: moment().format("M/D/YYYY h:mm a"), stamp: moment().unix(), manager:<?php echo $podmanager;?>,});
				}
				$(this).val('');
			}
		});
		$('#setup-datachannel').on('click', function (e) {
			if($('#chat-input').val()!='') {
				if(<?php echo $manager;?>===1){
					connection.push({name:userid, message:$('#chat-input').val(), time: moment().format("M/D/YYYY h:mm a"), stamp: moment().unix(), manager:<?php echo $podmanager;?>,});
				}else{
					connection.push({name:userid, message:($('#chat-input').val()).replace(/<[^>]*>/,''), time: moment().format("M/D/YYYY h:mm a"), stamp: moment().unix(), manager:<?php echo $podmanager;?>,});
				}
				$('#chat-input').val('');
			}
		});
		$('.tab-1Icon').click(function () {
			$('.small-chat-box').toggleClass('active');
			now = 0;
			$('#chatCounter').empty();
		});
	});
</script>
