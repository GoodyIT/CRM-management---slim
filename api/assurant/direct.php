
<table class="table table-hover">
    <tbody>
        <tr>
            <td class="project-title">
                Misc Insurance
                <br>
                <small>Get Misc Insurance Quotes</small>
            </td>
            <td class="project-actions">
                <a data-toggle="modal" data-target="#quoteMiscInsurance" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
            </td>
        </tr>
        <?php 
        try {
            include ("./insurehc.php");
        } catch (Exception $e) {
        }
        ?>
        <tr style="display:none">
            <td class="project-title" >
                <?php
                shuffle($settings['assurant_writing_numbers']);
                mt_srand(date("YmdHis"));
                $writingnumber = $settings['assurant_writing_numbers'][mt_rand(0, count($settings['assurant_writing_numbers']) - 1)];
                ?>
                <a href="https://www.groupihq.com/NGIC/WebFormCollectDemographics.aspx?destination=c&writingagentnumber=<?php echo $writingnumber; ?>" target="_blank">Assurant Direct Link</a>
                <br>
                <small>Go straight to Assurant for quote</small>
            </td>
            <td class="project-actions">
                <!-- <a href="https://www.healthsherpa.com/insurance_plans?zip_code=33604#c12057/ppl35,35,15/cspremium/hhs3" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>-->
                <a href="https://www.groupihq.com/NGIC/WebFormCollectDemographics.aspx?destination=c&writingagentnumber=<?php echo $writingnumber; ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
            </td>
        </tr>
        <tr  style="display:none">
            <td class="project-title">
                <a href="https://www.brokeroffice.com/" target="_blank">Broker Office - Direct Link (Do Not Use)</a>
                <br>
                <small>Go To Broker Office for MAJOR MEDICAL / CORE Quotes and Submissions</small>
            </td>
            <td class="project-actions">
                <a href="https://www.brokeroffice.com/" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Get Quote </a>
            </td>
        </tr>
    </tbody>
</table>