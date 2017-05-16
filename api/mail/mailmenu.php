<div class="ibox float-e-margins">
                    <div class="ibox-content mailbox-content">
                        <div class="file-manager">
                            <a class="btn btn-block btn-primary compose-mail" href="#mail/compose">Compose Mail</a>
                            <div class="space-25"></div>
                            <h5>Folders</h5>
                            <ul class="folder-list m-b-md" style="padding: 0">
                                <li><a href="#mail"> <i class="fa fa-inbox "></i> Inbox <span class="label label-warning pull-right"><?php echo (!empty($unread))?count($unread):''; ?></span> </a></li>
                                <li><a href="#mail/folder/SENT"> <i class="fa fa-envelope-o"></i> Sent Mail</a></li>
                                <li><a href="#mail/folder/IMPORTANT"> <i class="fa fa-certificate"></i> Important <span class="label label-info pull-right"><?php echo (!empty($important))?count($important):''; ?></span></a> </li>
                                <li><a href="#mail/folder/DRAFT"> <i class="fa fa-file-text-o"></i> Drafts <span class="label label-danger pull-right"><?php echo (!empty($draft))?count($draft):''; ?></span></a></li>
                                <li><a href="#mail/folder/TRASH"> <i class="fa fa-trash-o"></i> Trash</a></li>
                            </ul>
                                <?php
								/*
                            <h5>Categories</h5>
                            <ul class="category-list" style="padding: 0">
                                <li><a href="#mail/folder"> <i class="fa fa-circle text-navy"></i> Work </a></li>
                                <li><a href="#mail/folder"> <i class="fa fa-circle text-danger"></i> Documents</a></li>
                                <li><a href="#mail/folder"> <i class="fa fa-circle text-primary"></i> Social</a></li>
                                <li><a href="#mail/folder"> <i class="fa fa-circle text-info"></i> Advertising</a></li>
                                <li><a href="#mail/folder"> <i class="fa fa-circle text-warning"></i> Clients</a></li>
                            </ul>
                                */
                                ?>
                        </div>
                    </div>
                </div>