<div class="ibox float-e-margins">
    <?php 
        if(!empty($ibox['title'])){
    ?>
    <div class="ibox-title">
        <h5><?php echo $ibox['title'];?></h5>
        <div class="ibox-tools">
            <?php echo $ibox['tools'];?>
        </div>
    </div>
    <?php
        }
    ?>
    <div class="ibox-content">
       <?php echo $ibox['content'];?>
    </div>
</div>