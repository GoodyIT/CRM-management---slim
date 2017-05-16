<?php
try {
    $showinsurehc = false;
    $showbuttons = false;
    $submissiondate = "";
    foreach($result['policy'] as $key=>$var){
        if($var['carrier'] == "yBOwCg96-cG94JRf6-4ffCVYDZ"){
            if($var['coverageType'] == "72p0NTzV-XFI3Jolx-pTID4wsw"){
                $showinsurehc = true;
            }
        }
        if(date("YmdHis", strtotime($var['submissionDate'])) <= date("Ymdhis")){
            $showbuttons = true;
        }
        $submissiondate = $var['submissionDate'];
    }
    if ($showinsurehc === true){
        // debug($result['policy']);
?>
<tr>
    <td class="project-title">
        <a href="#admin/agencies">Misc Insurance</a>
        <br>
        <small>Get Misc Insurance Quotes</small>
    </td>
    <td class="project-actions">
        <a data-toggle="modal" data-target="#quoteMiscInsurance" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
    </td>
</tr>
<tr>
    <td class="project-title">
        <a href="#admin/agencies">Pay InsureHC</a>
        <br>
        <small>Use this to Pay InsureHC</small>
    </td>
    <td class="project-actions">
        <?php
        if($showbuttons === true){
            $link_phone_number ="";
            $link_email = "";
            if(!empty($result['phones'][0]['phoneNumber'])){
                $link_phone_number = $result['phones'][0]['phoneNumber'];
            }
            if(!empty($result['emails'][0]['email'])){
                $link_email = $result['emails'][0]['email'];
            }
        ?>
        <a href="http://insurehc.com/public/temp/checking.php?fn=<?php echo $result['leads'][0]['firstName'];?>&ln=<?php echo $result['leads'][0]['lastName'];?>&ph=<?php echo $link_phone_number;?>&em=<?php echo $link_email;?>&ref_id=<?php echo $result['leads'][0]['_id'];?>" target="_blank" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Pay By Bank Account </a>
        <a href="http://insurehc.com/public/temp/stripe.php?fn=<?php echo $result['leads'][0]['firstName'];?>&ln=<?php echo $result['leads'][0]['lastName'];?>&ph=<?php echo $link_phone_number;?>&em=<?php echo $link_email;?>&ref_id=<?php echo $result['leads'][0]['_id'];?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Pay By Credit Card</a>
        <?php
        } else {
            echo "Your submission date is in the future. (".$submissiondate.")";
        }
        ?>
    </td>
</tr>
<?php
    }
} catch (Exception $e) {
}
?>