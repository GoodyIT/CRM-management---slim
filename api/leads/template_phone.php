 <div class="ibox-content"  id="phoneItem_<?php echo $index;?>">
    <div class="row">
        <div class="col-xs-11">
            <?php $apiObj->displayThingForm("phones", $result['phones'], $index, "person_0_");  ?>
        </div>
        <div class="col-xs-1 text-center">
            <div class="form-group">
                <a onClick="removePhone(<?php echo $index;?>, 0)" class="btn btn-warning btn-sm btn-bitbucket"><i class="fa fa-times"></i></a>
            </div>
        </div>
    </div>
</div>