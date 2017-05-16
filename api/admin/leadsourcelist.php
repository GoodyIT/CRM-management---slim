 <form id="leadsourceform" name="saveTemplateData" method="post" action="<?php echo $settings['base_uri'];?>api/admin/createLeadSource" class="form-horizontal ">
<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading">
                 <div class="col-sm-4">
                    <h2>Lead Sources</h2>
                    <ol class="breadcrumb">
                        <li><a href="#">Home</a></li>
                <li><a href="#admin/settings">Settings</a></li>
                        <li class="active">
                            <strong>Lead Sources</strong>
                        </li>
                    </ol>
                </div>
                <!-- <div class="col-sm-8">
                    <div class="title-action">
                       <a  href="#admin/user/create" class="btn btn-primary btn-sm">Create A Lead Source</a>
                    </div>
                </div> -->
                <div class="col-sm-8">
                    <div class="title-action">
                       <!-- <a  href="#admin/usergroups/create" class="btn btn-primary btn-sm">Create Lead Source</a> -->
                       <button id="saveButton" class="btn btn-primary" type="submit">Create Lead Source</button>
                    </div>
                </div>
</div>
</div>
<div class="row animated fadeInRight">
   
        <div>
            <div class="row">
                <div class="col-lg-12 col-xs-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <div>
                                <h4>Create A Lead Source</h4>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        Name
                                    </label>
                                    <input type="hidden" name="systemForm_0_id" value="<?php echo $result['systemForm']['leadSource']['_id'] ?>"/>
                                    <input style="display:none" name="systemForm_0_createThing" value="Y">
                                    <input class="form-control" name="systemForm_0_options_0_label" type="text" >
                                </div>
                            </div>
                        </div>
                        <!-- <div style="padding-top:30px; padding-bottom: 30px;">
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn btn-white" onClick="cancelLeadSource()">Cancel</a>
                                    <button id="saveButton" class="btn btn-primary" type="submit">Save Lead Source</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    
</div>
</form>
<div class="row  animated fadeInRight">
    <div class="ibox float-e-margins">
           <div class="ibox-title">
               <div class="col-sm-4">
                <h5>Lead Source List</h5>
                </div>
                
            </div>
        <div class="ibox-content">
            <div class="pull-right">
                <select style="margin: 10px 0;"  name="fillterLeadSource"  id="fillterLeadSource" class=" form-control">

                                <option value="ANY" <?php if ($result[ 'fillterLeadSource']=="ANY" ){ echo "selected";}?>>Any</option>

                                <?php

if(!empty($result['systemForm']['leadSource']['options'])){

    foreach($result['systemForm']['leadSource']['options'] as $ckey=>$cval){

        $selected = "";

        if ($cval['_id']==$result['fillterLeadSource'] ){ $selected =  "selected";}

        echo "<option value='".$cval['_id']."' ".$selected." >".$cval['label']."</option>";

    }

}

                                ?>

                            </select>
            </div>
            <?php
echo "<table class='table table-bordered table-striped' id='table-lead-resource'>";
echo "<thead><tr><th>Name</th><th>Delete</th></thead>";
if(!empty($result['systemForm']['leadSource']['options'])){
    foreach($result['systemForm']['leadSource']['options'] as $ckey=>$cval){   
        echo '<tr id="'.$cval['_id'].'"><td>'.$cval['label'].'</td>';
        echo '<td><a leadSourceId="'.$cval['_id'].'" class="leadSourceRemove btn btn-danger"> Delete</a></td>';
        echo '</tr>';
         // echo "<tr><td><a href='#admin/user/edit/".$value['_id']."'>".$value['firstname']. " ".$value['lastname']."</td><td>".$value['email']."</td><td>".$value['phone']."</td><td>".strtoupper($value['status'])."</td></tr>";
    }
}
echo "</table>";
?>
        </div>
    </div>
</div>
<script>
    $('#fillterLeadSource').change(function() {
        // console.log($(this).val());
        var leadsource_id = $(this).val();
        $("#table-lead-resource").find('tr').each (function() {
            var td_id = $(this).attr('id');
            // do your cool stuff
            if (typeof td_id != "undefined"){
                $(this).css('display', 'none');
                // console.log(td_id);
                if(td_id === leadsource_id || leadsource_id == 'ANY'){
                    $(this).css('display', '');
                    console.log("SDFDSf");
                }
            }
        });   
    });
    function cancelLeadSource(){
        window.location.hash = '#admin/settings';   
    }
    $(document).ready(function() {
        var serialize = function(obj) {
            var str = [];
            for(var p in obj)
                if (obj.hasOwnProperty(p)) {
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                }
            return str.join("&");
        };
        $('.leadSourceRemove').on('click', function(){
            var leadSourceInfo = [];
            leadSourceInfo['systemForm_0_id'] = "<?php echo $result['systemForm']['leadSource']['_id'] ?>"; 
            leadSourceInfo['systemForm_0_options_0_id']=$(this).attr('leadSourceId');
            $.ajax({
                url: 'api/admin/deleteLeadSource',
                type: 'POST',
                data: serialize(leadSourceInfo),
                success: function(result) {
                    console.log(result);
                    $('#results').load(base_uri + 'api/admin/leadsources');
                }
            });
        });
        $(".table").tablesorter({sortList:[[0,0]]});
        $('input').focus(function() {
            $(this).parent().removeClass("has-error");
        });
        $(".chosen-select").chosen({width:'100%', placeholder_text_multiple:'Select People'});
        // Attach a submit handler to the form
        $("#leadsourceform").submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();
            console.log( $(this).serialize());
            $.post($(this).attr("action"), $(this).serialize(), function(response){
                console.log(response);
                    $('#results').load(base_uri + 'api/admin/leadsources');
            });
        });
    });
</script>