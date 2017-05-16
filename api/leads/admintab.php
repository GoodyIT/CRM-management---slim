<form>
    <div class="row">
        <div class="col-xs-6">
            <?php
if(!empty($result['policy'][0])){
     if(!empty($result['person'][0])){
        echo    "<br><label>Name:</label> ".$result['person'][0]['firstName'] . " ".$result['person'][0]['lastName'];
    }
    echo    "<br><label>Policy Number:</label> ".$result['policy'][0]['policyNumber'];
    echo    "<br><label>Carrier:</label> ".$result['policy'][0]['carrier'];
    echo    "<br><label>Coverage Type:</label> ".$result['policy'][0]['coverageType'];
    echo    "<br><label>Premium:</label> ".$result['policy'][0]['premiumMoney'];
    echo    "<br><label>Setup Fee:</label> ".$result['policy'][0]['setupFeeMoney'];
    echo    "<br><label>Submission Date:</label> ".$result['policy'][0]['submissionDate'];
    echo    "<br><label>Effective Date:</label> ".$result['policy'][0]['effectiveDate'];
    echo    "<br><label>Sold By:</label> ".$result['policy'][0]['soldBy'];
    echo    "<br><label>Closed By:</label> ".$result['policy'][0]['closedBy'];
   
    if(!empty($result['addresses'][0])){
        echo    "<br><label>State / ZipCode:</label> ".$result['addresses'][0]['state'] . ", ".$result['addresses'][0]['zipCode'];
    }
    echo    "<br><label>Notes:</label> ".$result['policy'][0]['notes'];

}
            ?>
        </div>
        <div class="col-xs-6">
  
            <?php $apiObj->displayThingForm("adminTab", $result['policy'], 0, "person_0_", "Y");  ?>
        </div>
    </div>
</form>