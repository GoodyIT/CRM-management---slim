<div  >
	<div class="row">
		<div class="col-lg-12 animated fadeInRight">
			<div class="mail-box-header">
				<h2>
					<?php echo "Emails" ?>
				</h2>
			</div>
			<div class="mail-box">
				<div class="ibox-content" >
					<div class="row">
						<div class="panel-group" id="accordion">
							<?php 
	if(!empty($result)){
		foreach($result as $key=>$email){
							?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h5 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" class=""><?php echo (!empty($email['from']))?$email['from']:'';?><span class="pull-right"><?php echo (!empty($email['subject']))?$email['subject']:'';?></span></a>
									</h5>
								</div>
								<div id="collapseOne" class="panel-collapse collapse in" aria-expanded="true">
									<div class="panel-body">
										<?php echo (!empty($email['bodyHtml']))?$email['bodyHtml']:((!empty($email['bodyPlain']))?$email['bodyPlain']:'');?>
									</div>
								</div>
							</div>
							<?php
		}
	}else{
		echo "<div class='col-lg-12'><strong>There isn't any mail here...</strong></div>";
	}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>