<div class="col-lg-12">

    <div class="profile-page">

        <div style="background: #dedede;text-align: center;padding: 9px;">
            <h4>Общая статистика</h4>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="widget-body">
                    <div class="manager-graphic line-chart" id="line-chart" >
                        <div class="area-chart" >
                            <i class="la la-area-chart"></i>
                            <p>Текст</p>
                        </div>
                    </div>
                </div>
                <div class="widget has-shadow fixPanel">


                </div>
            </div>
        </div>

        <link href="https://gorod-masterov.kz/templates/js/morris/morris.css" rel="stylesheet">
        <style>
            .morris-hover.morris-default-style {
                border-radius: 10px;
                padding: 6px;
                color: #f9f9f9;
                background: rgba(0, 0, 0, 0.8);
                border: solid 2px rgba(0, 0, 0, 0.9);
                font-weight: 600;
                font-size: 14px;
                text-align: center;
            }
        </style>
        <script type="text/javascript" src="https://gorod-masterov.kz/templates/js/raphael.js"></script>
        <script type="text/javascript" src="https://gorod-masterov.kz/templates/js/morris/morris.min.js"></script>
        <?php
        function load_stat_orders()
        {

            $data = array();
            $domain_all = db_query_while("SELECT datetime_created FROM all_list_domain group by DATE(datetime_created) DESC");
            if (count($domain_all) > 0) {
                 foreach ($domain_all AS $data_array) {
                     $domain_count = db_query("SELECT COUNT(*) as total FROM all_list_domain WHERE date(datetime_created)='" . date("Y-m-d", strtotime($data_array["datetime_created"])) . "'");
                     $data[] = array("y" => date('Y-m-d', strtotime($data_array["datetime_created"])), "domain" => $domain_count["total"], "black-list" => "0");
                 }
            }
//            if (count($result_phone) > 0) {
//                foreach ($result_phone AS $data_array) {
//                    $count_order = db_query("SELECT COUNT(*) as total FROM uni_ads_click_phone WHERE date(ads_click_phone_datetime_add)='" . date("Y-m-d", strtotime($data_array["ads_click_phone_datetime_add"])) . "' AND ads_click_phone_id_ads = '" . $id_ads . "'");
//                    $data[] = array("y" => date('Y-m-d', strtotime($data_array["ads_click_phone_datetime_add"])), "phone" => $count_order["total"], "view" => "0");
//                }
//            }
//            if (count($result_view) > 0) {
//                foreach ($result_view AS $data_array) {
//                    $count_order = db_query("SELECT COUNT(*) as total FROM uni_ads_view WHERE date(ads_view_datetime_add)='" . date("Y-m-d", strtotime($data_array["ads_view_datetime_add"])) . "' AND ads_view_id_ads = '" . $id_ads . "'");
//
//                    $date_v = date('Y-m-d', strtotime($data_array["ads_view_datetime_add"]));
//                    $flag = false;
//                    for ($i = 0; $i < count($data); $i++) {
//                        if ($data[$i]['y'] == $date_v)
//                            $flag = $i;
//                    }
//                    if ($flag !== false)
//                        $data[$flag]['view'] = $count_order["total"];
//                    else
//                        $data[] = array("y" => $date_v, "domain" => "0", "black-list" => $count_order["total"]);
//                }
//            }
            return json_encode($data);
        }
        ?>
        <script type="text/javascript">
            json = <?php echo load_stat_orders();?>;
            if(json != ""){
                $(".area-chart").hide();
                var line = new Morris.Line({
                    element: 'line-chart',
                    resize: true,
                    xkey: 'y',
                    ykeys: ['domain','black-list'],
                    labels: ['Кол-во добавленных доменов','Находятся в черном списке'],
                    lineColors: ['#03d8b0','#a74c4c'],
                    lineWidth: 2,
                    hideHover: 'auto',
                    gridTextColor: "#3F7394",
                    gridStrokeWidth: 0.4,
                    pointSize: 4,
                    pointStrokeColors: ["#efefef"],
                    gridLineColor: "#efefef",
                    gridTextFamily: "Open Sans",
                    gridTextSize: 10
                });

                line.setData(json);
            }
        </script>

    </div>

</div>
<!--<pre>
        <?php /*echo var_dump(json_decode(load_stat_orders(), true));*/?></pre>-->