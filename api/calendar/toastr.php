<?php
require '../app.php';
$apiObj = new apiclass($settings);
$result = 'User not Logged in';
if($apiObj->userLoggedIn()){
?>
<script type="text/javascript">
	$(document).ready(function() {
		<?php
	$notifications = array();
	$apiObj->mongoSetDB($settings['database']);
	$apiObj->mongoSetCollection("appointment");
	$inviteQuery = array('$and'=>
						 array(
							 array('invitesPending.agentId'=>$_SESSION['api']['user']['_id']),
							 array('day'=>array('$gte'=>date('m/d/Y')))
						 )
						);
	$inviteCursor = $apiObj->mongoFind($inviteQuery);
	if(!empty($inviteCursor)){
		foreach($inviteCursor as $invite){
			$personQuery = array(
				'_id'=>$invite['_parentId']
			);
			$adressQuery = array(
				'_parentId'=>$invite['_parentId']
			);
			$apiObj->mongoSetCollection("person");
			$personCursor= $apiObj->mongoFindOne($personQuery);
			$apiObj->mongoSetCollection("addresses");
			$addressCursor= $apiObj->mongoFindOne($adressQuery);
			$userQuery= array('_id'=>$invite['_createdBy']);
			$apiObj->mongoSetCollection("user");
			$userCusor= $apiObj->mongoFindOne($userQuery);
			/*
			if(!empty($invite['invitesPending'])){
				foreach($invite['invitesPending'] as $inviteNotification){
					if(!strpos(serialize($invite['invitesAccepted']), $_SESSION['api']['user']['_id'])){
						if($inviteNotification['agentId'] == $_SESSION['api']['user']['_id']){
							if(empty($inviteNotification['notified'])){
								$mongo->$settings['database']->appointment->update(
									array('invitesPending.agentId'=>$_SESSION['api']['user']['_id']),
									array('$set'=>array('invitesPending.$.notified'=>date('YmdHis')))
								);
							}else{
								if($inviteNotification['notified'] <= (date('YmdHis')-3600)&&($inviteNotification['notified']=='this is broken')){
									$a['person'] = ucwords(strtolower($personCursor['firstName'])).' '.ucwords(strtolower($personCursor['lastName'])). ' of '.$addressCursor['state'];
									$date = $invite['day'].' '.$invite['time'];
									$a['start'] = date(DATE_ISO8601, strtotime($date));
									$a['end'] = date(DATE_ISO8601, strtotime($date)+3600);
									$mongo = new MongoClient();
									$mongo->$settings['database']->appointment->update(
										array('invitesPending.agentId'=>$_SESSION['api']['user']['_id']),
										array('$set'=>array('invitesPending.$.notified'=>date('YmdHis')))
									);
		?>
		toastr.info("<?php echo $invite['title']?> with <?php echo $a['person'];?> at <?php echo $date;?><br><small class='text-muted'>Created by <?php echo ucwords(strtolower($userCusor['firstName'])).' '.ucwords(strtolower($userCusor['lastName']));?></small><br><button id='notifications' inviteId='<?php echo $inviteNotification['_id']?>' agentId='<?php echo $_SESSION['api']['user']['_id'];?>' appointmentId='<?php echo $invite['_id'];?>' class='appointmentAccept btn btn-xs btn-primary'><i class='fa fa-check'></i></button> <button id='notifications' inviteId='<?php echo $inviteNotification['_id']?>' agentId='<?php echo $_SESSION['api']['user']['_id'];?>' appointmentId='<?php echo $invite['_id'];?>' class='appointmentDecline btn btn-xs btn-danger'><i class='fa fa-times'></i></button>","New Appointment Invite Pending",{
			"debug": true,
			'progressBar': true,
			'preventDuplicates': true,
		});
		<?php
								}
							}
						}
					}
				}
			}
			*/
		}
	}
		?>
	});
</script>
<?php
}
?>