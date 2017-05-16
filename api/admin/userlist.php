<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading">
                 <div class="col-sm-4">
                    <h2>Users</h2>
                    <ol class="breadcrumb">
                        <li><a href="#">Home</a></li>
				<li><a href="#admin/settings">Settings</a></li>
                        <li class="active">
                            <strong>Users</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-8">
                    <div class="title-action">
                       <a  href="#admin/user/create" class="btn btn-primary btn-sm">Create A User</a>
                    </div>
                </div>
</div>
</div>
<div class="row  animated fadeInRight">
    <div class="ibox float-e-margins">
           <div class="ibox-title">
               <div class="col-sm-4">
                <h5>User List</h5>
                </div>
            </div>
        <div class="ibox-content">
                <div class="title-action">
                		<a href="#admin/user/list/INACTIVE" class="btn btn-info btn-sm">Inactive Users</a>
                		<a href="#admin/user/list" class="btn btn-primary btn-sm">Active Users</a>
                </div>
            <?php
echo "<table class='table table-bordered table-striped'>";
echo "<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th></thead>";
if(!empty($result['users'])){
    foreach   ($result['users'] as $key=>$value){        
         echo "<tr><td><a href='#admin/user/edit/".$value['_id']."'>".$value['firstname']. " ".$value['lastname']."</td><td>".$value['email']."</td><td>".$value['phone']."</td><td>".strtoupper($value['status'])."</td></tr>";
    }
}
echo "</table>";
?>
        </div>
    </div>
</div>
