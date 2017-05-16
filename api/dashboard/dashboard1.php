<?php
function widget($widget, $label_color = "success"){
    $return_string = '';
    $return_string .= '<div class="ibox float-e-margins">';
    $return_string .= '<div class="ibox-title">';
    //$return_string .= '<span class="label label-'.$label_color.' pull-right">'.$widget['time'].'</span>';
    $return_string .= '<h5>'.$widget['label'].'</h5>';
    $return_string .= '</div>';
    $return_string .= '<div class="ibox-content">';
    $return_string .= '<h1 class="no-margins">'.$widget['amount'].'</h1>';
    $return_string .= '<div class="stat-percent font-bold text-danger">'.$widget['percent'].'</div>';
    $return_string .= '<small>'.$widget['note'].'</small>';
    $return_string .= '</div>';
    $return_string .= '</div>';
    return $return_string;
}
?>




    <div class=" animated fadeInRight">
        <div class="row">
            <div class="col-lg-3">
                <?php echo widget($result['widgets']['leads'], 'info'); ?>
            </div>
            <div class="col-lg-3">
                <?php echo widget($result['widgets']['clients'], 'danger'); ?>
            </div>
            <div class="col-lg-3">
                <?php echo widget($result['widgets']['policies'], 'primary'); ?>
            </div>
            <div class="col-lg-3">
                <?php echo widget($result['widgets']['premiums'], 'success'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Orders</h5>
                        <div class="pull-right">
                            <div class="btn-group">
                                <?php
$active0 = "";
$active1 = "";
$active2 = "";
$active3 = "";
$ticksize = 7;
if($result['timeIncrement'] == "1"){
    $active0 = "active";
    $ticksize = 1;
}

if($result['timeIncrement'] =="7"){
    $active1 = "active";
    $ticksize = 1;
}
if($result['timeIncrement'] == "30"){
    $active2 = "active";
    $ticksize = 7;
}
if($result['timeIncrement'] == "90"){
    $active3 = "active";
    $ticksize = 7;
}

if($result['timeIncrement'] =="week"){
    $activew = "active";
    $ticksize = 1;
}
if($result['timeIncrement'] =="prevweek"){
    $activepw = "active";
    $ticksize = 1;
}

                        ?>
                                    <a href="#dashboard/1" type="button" class="btn btn-xs btn-white <?php echo $active0;?>">Today</a>
                                    <a href="#dashboard/week" type="button" class="btn btn-xs btn-white <?php echo $activew;?>">This Week</a>
                                    <a href="#dashboard/prevweek" type="button" class="btn btn-xs btn-white <?php echo $activepw;?>">Last Week</a>
                                    <a href="#dashboard/30" type="button" class="btn btn-xs btn-white <?php echo $active2;?>">30 Days</a>
                                    <a href="#dashboard/90" type="button" class="btn btn-xs btn-white <?php echo $active3;?>">90 Days</a>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="flot-chart">
                                    <div class="flot-chart-content" id="flot-dashboard-chart"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <ul class="stat-list">
                                    <li>
                                        <h2 class="no-margins"><?php echo $result['chart']['info'][1]['value'];?></h2>
                                        <small><?php echo $result['chart']['info'][1]['description'];?></small>
                                        <div class="stat-percent">
                                            <?php echo $result['chart']['info'][1]['percentage'];?>%</div>
                                        <div class="progress progress-mini">
                                            <div style="width: <?php echo $result['chart']['info'][1]['percentage'];?>%;" class="progress-bar"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <h2 class="no-margins"><?php echo $result['chart']['info'][2]['value'];?></h2>
                                        <small><?php echo $result['chart']['info'][2]['description'];?></small>
                                        <div class="stat-percent">
                                            <?php echo $result['chart']['info'][2]['percentage'];?>%</div>
                                        <div class="progress progress-mini">
                                            <div style="width: <?php echo $result['chart']['info'][2]['percentage'];?>%;" class="progress-bar"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <h2 class="no-margins"><?php echo $result['chart']['info'][3]['value'];?></h2>
                                        <small><?php echo $result['chart']['info'][3]['description'];?></small>
                                        <div class="stat-percent">
                                            <?php echo $result['chart']['info'][3]['percentage'];?>%</div>
                                        <div class="progress progress-mini">
                                            <div style="width: <?php echo $result['chart']['info'][3]['percentage'];?>%;" class="progress-bar"></div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>



         <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Sales Groups</h5>
                        <div class="pull-right">
                            <div class="btn-group">
                                <button class="btn btn-xs btn-white dataSalesGroup" dataTable="salesGroupTable">Update</button>
                            </div>
                        </div>
                    </div>



                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="salesGroupTable" class="table table-striped">
                                    <!-- Filled by Javascript -->
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>    
        
        <?php 
        
            $crement_name = 'Today';
            if($_SESSION['timeIncrement'] == 1){
                $crement_name = 'Today';
            }else if($_SESSION['timeIncrement'] == 'week'){
                $crement_name = 'This Week';
            }else if($_SESSION['timeIncrement'] == 'prevweek'){
                $crement_name = 'Last Week';
            }else if($_SESSION['timeIncrement'] == '30'){
                $crement_name = '30 Days';
            }else if($_SESSION['timeIncrement'] == '90'){
                $crement_name = '90 Days';
            }
        ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        
                        <h5>Top Fronters for <?php echo $crement_name;?></h5>
                        <div class="pull-right">
                            <div class="btn-group">
                                <button class="btn btn-xs btn-white dataButton" dataTable="fronterTable">Update</button>
                            </div>
                        </div>
                    </div>



                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="fronterTable" class="table table-striped">
                                    <!-- Filled by Javascript -->
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Top Closers For <?php echo $crement_name;?></h5>
                        <div class="pull-right">
                            <div class="btn-group">
                                <button class="btn btn-xs btn-white dataButton" dataTable="closerTable">Update</button>
                            </div>
                        </div>
                    </div>


                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="closerTable" class="table table-striped">
                                    <!-- Filled by Javascript -->
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
     <div class="row">
        <div class="col-lg-12" id="viciTable">
                
        </div>
    </div>      

    <script>
        $(document).ready(function() {
            dataTableLoad("fronterTable");
            dataTableLoad("closerTable");
            dataGroupTableLoad("salesGroupTable");
            vicidialerLoad();
        });


       // var myVar = setInterval(myTimer, 60000);
        function myTimer() {
            dataTableLoad("fronterTable");
            dataTableLoad("closerTable");
             toastr.success('Tables Auto Updated', 'Server Response');
        }
        
         $(".confirmation").click(function() {
         //    clearInterval(myVar);
         });

        $(".dataButton").click(function() {
            var tableId = $(this).attr("dataTable");
            dataTableLoad(tableId);
            toastr.success('Table Updated', 'Server Response');
        });

        function dataTableLoad(tableId) {
            $.ajax({
                url: '<?php echo $settings['base_uri'];?>api/dashboard/info/' + tableId,
                type: "post",
                dataType: "json",
                success: function(data, textStatus, jqXHR) {
                    // since we are using jQuery, you don't need to parse response
                    drawTable(data, tableId);
                }
            });
        }
        
          $(".dataSalesGroup").click(function() {
            var tableId = $(this).attr("dataTable");
            dataGroupTableLoad(tableId);
            toastr.success('Table Updated', 'Server Response');
        });

        function dataGroupTableLoad(tableId) {
            $.ajax({
                url: '<?php echo $settings['base_uri'];?>api/dashboard/grouptables',
                type: "post",
                dataType: "json",
                success: function(data, textStatus, jqXHR) {
                    // since we are using jQuery, you don't need to parse response
                    drawTable(data, tableId);
                }
            });
        }


         function vicidialerLoad() {
            $.ajax({
                url: '<?php echo $settings['base_uri'];?>api/vicidialer',
                type: "post",
                success: function(result) {
                        $("#viciTable").html(result);
                    }
            });
        }

        
        function drawTable(data, tableId) {
            $("#" + tableId).empty();
            drawHeader(data.headers, tableId);
            for (var i = 0; i < data.rows.length; i++) {
                drawRow(data.rows[i], tableId);
            }
        }

        function drawHeader(rowData, tableId) {
            var row = $("<thead />")
            $("#" + tableId).append(row); //this will append tr element to table... keep its reference for a while since we will add cels into it
            for (var i = 0; i < rowData.length; i++) {
                if (i == 0){
                    row.append($("<th class='text-left'>" + rowData[i] + "</th>"));
                } else {
                    row.append($("<th class='text-center'>" + rowData[i] + "</th>"));
                }
                
            }
        }



        function drawRow(rowData, tableId) {
            var row = $("<tr />")
            $("#" + tableId).append(row); //this will append tr element to table... keep its reference for a while since we will add cels into it
             var i = 0;
            for (var k in rowData) {
                if (rowData.hasOwnProperty(k)) {
                    if (i == 0){
                        row.append($("<td class='text-left'>" + rowData[k] + "</td>"));
                    } else {
                        row.append($("<td class='text-center'>" + rowData[k] + "</td>"));
                    }
                    i = i +1;
                }
            }
        }

 
        $(document).ready(function() {
            $('.chart').easyPieChart({
                barColor: '#f8ac59',
                //                scaleColor: false,
                scaleLength: 5,
                lineWidth: 4,
                size: 80
            });

            $('.chart2').easyPieChart({
                barColor: '#1c84c6',
                //                scaleColor: false,
                scaleLength: 5,
                lineWidth: 4,
                size: 80
            });

            var data2 = [
                <?php
foreach($result['chart']['data1'] as $key=>$info){
    echo  "[gd(".$info[0]."), ".$info[1]."],";   
}
            ?>
            ];

            var data3 = [
                <?php
foreach($result['chart']['data2'] as $key=>$info){
    echo  "[gd(".$info[0]."), ".$info[1]."],";   
}
            ?>
            ];


            var dataset = [{
                label: "Policies Created",
                data: data3,
                color: "#1ab394",
                bars: {
                    show: true,
                    align: "center",
                    barWidth: 24 * 60 * 60 * 600,
                    lineWidth: 0
                }

            }, {
                label: "Policies Sold",
                data: data2,
                yaxis: 2,
                color: "#464f88",
                lines: {
                    lineWidth: 1,
                    show: true,
                    fill: true,
                    fillColor: {
                        colors: [{
                            opacity: 0.2
                        }, {
                            opacity: 0.2
                        }]
                    }
                },
                splines: {
                    show: false,
                    tension: 0.6,
                    lineWidth: 1,
                    fill: 0.1
                },
            }];


            var options = {
                xaxis: {
                    mode: "time",
                    tickSize: [<?php echo $ticksize; ?>, "day"],
                    tickLength: 0,
                    axisLabel: "Date",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Arial',
                    axisLabelPadding: 10,
                    color: "#d5d5d5"
                },
                yaxes: [{
                    position: "left",
                    max: <?php echo $result['chart']['highestDay'];?>,
                    color: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Arial',
                    axisLabelPadding: 3
                }, {
                    position: "right",
                    clolor: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: ' Arial',
                    axisLabelPadding: 67
                }],
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: false,
                    borderWidth: 0
                }
            };

            function gd(year, month, day) {
                return new Date(year, month - 1, day).getTime();
            }

            var previousPoint = null,
                previousLabel = null;

            $.plot($("#flot-dashboard-chart"), dataset, options);

        });
    </script>