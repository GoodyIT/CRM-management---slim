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

		<div class="table-responsive">

			<table class="table table-striped">

				<thead>

					<tr>

						<th>#</th>

						<th>Date </th>

						<th>Length </th>

						<th>File </th>

					</tr>

				</thead>

				<tbody>

					<?php

					$settings['vici']['active']=TRUE;

 if($settings['vici']['active'] === TRUE){

					if(!empty($result)){

						foreach($result as $phone){

							$number = preg_replace('/\D+/', '', $phone['phoneNumber']);

							$url=$settings['vici']['serverapi'].'recordings_curl.php?phone='.$number.'&key='.$settings['vici']['apiKey'];
echo $url;die();
							$recordings = file_get_contents($url);

							$i=0;

							$record = unserialize($recordings);

							if(!empty($record)){

								foreach($record as $recording){

									++$i;

					?>

					<tr>

						<td><?php echo $i; ?></td>

						<td><?php echo date('m/d/Y', $recording['start_epoch']);?></td>

						<td><?php echo $recording['length_in_min'];?></td>

						<?php

									if(strpos(serialize($recording['location']),'ftp')){

                                    $recording['location'] = str_replace("QQ07Fa64XH1S3Q", "VyO_Tr_3IkGTnw", $recording['location']);


						?>

						<td><a target="_blank" href="<?php echo $recording['location'];?>">download <i class="fa fa-headphones"></i></a></td>

						<?php

									}else{

										echo '<td>Recording not processed yet.</td>';

									}

						?>

					</tr>

					<?php

								}

							}else{

								echo '<div class="col-lg-12">No recordings for this phone '.$phone['phoneNumber'].'</div>';

							}

						}

					}else{

						echo 'Phone was not right   ';print_r($result);

					}

 }else{

	 echo 'false';

 }

					?>

				</tbody>

			</table>

		</div>

	</div>

</div>