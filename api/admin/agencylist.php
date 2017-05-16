<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading">
       
                 <div class="col-sm-4">
                    <h2>Agency</h2>
                    <ol class="breadcrumb">
                        <li><a href="#">Home</a></li>
				<li><a href="#admin/settings">Settings</a></li>
                        <li class="active">
                            <strong>Agencies</strong>
                        </li>
                    </ol>
                </div>
                
                <div class="col-sm-8">
                    <div class="title-action">
                       <a  href="#admin/agencies/create" class="btn btn-primary btn-sm">Create An Agency</a>
                    </div>
                </div>
                
                
   

</div>
</div>
<div class="row  animated fadeInRight">
    <div class="ibox float-e-margins">
       
           <div class="ibox-title">
               <div class="col-sm-12">
                <h5>Agency List</h5>
                
                
                
                </div>
            </div>
       
        <div class="ibox-content">
          
           
           
            <?php


echo "<table class='table table-bordered table-striped'>";
echo "<thead><tr><th>Name</th><th>Phone</th><th>City, State</th><th>Status</th></thead>";


if(!empty($result['agencies'])){
    foreach   ($result['agencies'] as $key=>$value){        
         if(empty($value['status'])){ $value['status'] = "Active";} 
         echo "<tr><td><a href='#admin/agencies/edit/".$value['_id']."'>".$value['agencyName']. "</td><td>".$value['phoneNumber']."</td><td>".$value['addressCity'].", ".$value['addressState']."</td><td>".strtoupper($value['status'])."</td></tr>";
    }
}



echo "</table>";

?>


        </div>
    </div>
</div>