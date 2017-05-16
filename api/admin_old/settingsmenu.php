<div class=" animated fadeInRight">
    <div class="row wrapper border-bottom white-bg page-heading ng-scope">
        <div class="col-lg-10">
            <h2>Settings</h2>
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li class="active">
                    <strong>Settings</strong>
                </li>
            </ol>
        </div>
        <div class="col-sm-2">
            <div class="title-action">
               <a href="#recordings/search" class="btn btn-primary ">Search Recordings</a>
            </div>
        </div>
    </div>
</div>

<div class="row animated fadeInRight">
    <form id="userform" name="saveTemplateData" method="post" action="<?php echo $settings['base_uri'];?>api/admin/user/createuser" class="form-horizontal ">
        <div>
            <div class="row">
                <div class="col-lg-12 col-xs-12">
                   <?php 
                   // $ibox['title'] = "Settings";
                   // $ibox['tools'] = "";
                    $ibox['content'] = '
                    
                    <div class="project-list">

                                <table class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td class="project-status">
                                            <span class="label label-primary">Active</span>
                                        </td>
                                        <td class="project-title">
                                            <a href="#admin/agencies">Agency Settings</a>
                                            <br>
                                            <small>Set information about Agency</small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="#admin/agencies" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                        <td class="project-status">
                                            <span class="label label-primary">Active</span>
                                        </td>
                                        <td class="project-title">
                                            <a href="#admin/usergroups">User Groups</a>
                                            <br>
                                            <small>Create and Manage User Groups</small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="#admin/usergroups" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="project-status" width="80px">
                                            <span class="label label-primary">Active</span>
                                        </td>
                                        <td class="project-title">
                                            <a href="#admin/user/list">User List</a>
                                            <br>
                                            <small>Manage Users</small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="#admin/user/list" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="project-status">
                                            <span class="label label-primary">Active</span>
                                        </td>
                                        <td class="project-title">
                                            <a href="#admin/carriers/list">Carrier List</a>
                                            <br>
                                            <small>Create and Modify Carriers</small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="#" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                        <td class="project-status">
                                            <span class="label label-primary">Active</span>
                                        </td>
                                        <td class="project-title">
                                            <a href="#admin/carriers/plans">Carrier Plans</a>
                                            <br>
                                            <small>Create and Modify Carrier Plans</small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="#" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        </td>
                                    </tr>
                                    
                                     <tr>
                                        <td class="project-status">
                                            <span class="label label-primary">Active</span>
                                        </td>
                                        <td class="project-title">
                                            <a href="#admin/leadsources">Lead Sources</a>
                                            <br>
                                            <small>Create and Modify Lead Sources</small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="#admin/leadsources" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        </td>
                                    </tr>
                                    
                                        <tr>
                                        <td class="project-status">
                                            <span class="label label-primary">Active</span>
                                        </td>
                                        <td class="project-title">
                                            <a href="#sms/templates">SMS Manager</a>
                                            <br>
                                            <small>Create and Modify SMS Templates</small>
                                        </td>
                                        <td class="project-actions">
                                            <a href="#" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        </td>
                                    </tr>
                        
                                    
                                    </tbody>
                                </table>
                            </div>
                            ';
                    ?>
                   <?php include("ibox.php");?>
                </div>
            </div>
        </div>
    </form>
</div>

