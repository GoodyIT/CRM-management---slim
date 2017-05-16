 <div class="ibox-content"  id="<?php echo $type;?>Item_<?php echo $index;?>">
    <div class="row">
        <div class="col-xs-11">
            <?php if($type == "phones"){ ?>
                <div class="row">
                    <div class="col-sm-11">
                       <?php $apiObj->displayThingForm($type, $result[$type], $index, "person_0_", $crtThng);  ?>
                    </div>
                    <div class="col-sm-1" style="margin-top:22px">
                             <?php if( (!empty($resultData[$index]['_parentId'])) && (!empty($resultData[$index]['phoneNumber']))){ ?>
                          <a href="#" id="newSms" data-toggle="modal" data-target="#modal" selectedNumber="<?php echo  preg_replace("/[^0-9]/", "", $resultData[$index]['phoneNumber']); ?>" _parentId="<?php echo $resultData[$index]['_parentId'];?>" class="newSms btn btn-primary">Sms</a>
                        <?php } ?>

                    </div>
                </div>
                <?php } else { ?>
                    <?php $apiObj->displayThingForm($type, $result[$type], $index, "person_0_", $crtThng);  ?>
                <?php } ?>
        </div>
        <div class="col-xs-1 text-center">
            <div class="form-group">
                <a onClick="removeItem('<?php echo $type;?>', <?php echo $index;?>, 0)" style="margin-top:22px"  class="btn btn-warning btn-sm btn-bitbucket"><i class="fa fa-times"></i></a>
            </div>
        </div>
    </div>
</div>
 <script>
            $(function() {
                $(".datepicker").pickadate({format: 'mm/dd/yyyy', selectYears: 100, selectMonths: true, });
            });
        </script>