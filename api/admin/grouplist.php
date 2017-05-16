<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading">
                 <div class="col-sm-4">
                    <h2>User Groups</h2>
                    <ol class="breadcrumb">
                        <li><a href="#">Home</a></li>
				<li><a href="#admin/settings">Settings</a></li>
                        <li class="active">
                            <strong>User Groups</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-8">
                    <div class="title-action">
                       <a  href="#admin/usergroups/create" class="btn btn-primary btn-sm">Create A User Group</a>
                    </div>
                </div>
</div>
</div>
<div class="row  animated fadeInRight">
    <div class="ibox float-e-margins">
           <div class="ibox-title">
               <div class="col-sm-12">
                <h5>User Group List</h5>
                </div>
            </div>
        <div class="ibox-content">
            <?php
echo "<table class='table table-bordered table-striped'>";
echo "<thead><tr><th>Name</th><th># of Users</th></thead>";
if(!empty($result)){
    foreach   ($result as $key=>$value){
		$users=array();
		if(!empty($value['users'])){
		foreach($value['users'] as $user){
			if(strtoupper($user['level'])!='NONE'){
				array_push($users,$user);
			}
		}
		}
         echo "<tr><td><a href='#admin/usergroups/edit/".$value['_id']."'>".$value['label']."</td><td>".count($users)."</td></tr>";
    }
}
echo "</table>";
?>
        </div>
    </div>
</div>