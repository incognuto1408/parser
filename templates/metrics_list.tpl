
<div class="row" >
    <div class="col-lg-12" >
        <div class="widget has-shadow">
            <div class="widget-header bordered no-actions d-flex align-items-center">
                <h4><?php echo $lang["display_index_title6"];?></h4>
            </div>
            <div class="widget-body">
                <div class="table-responsive">

                    <?php

                    $result_count = db_query("SELECT count(*) as result_count FROM uni_metrics order by datetime_view");

                    $sql = db_query_while("SELECT * FROM uni_metrics order by datetime_view desc");// ".navigation_offset($result_count["result_count"],$_SESSION["ByShow"])
                    if(count($sql) > 0){

                        ?>
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th>Браузер</th>
                                <th>Расположение</th>
                                <th>Переход с</th>
                                <th>Время посещения</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach($sql AS $array_data){

                                $location = array();

                                if(urldecode($array_data["referrer"])){ $referrer = ' <a href="'.urldecode($array_data["referrer"]).'" target="_blank" >'.$strreferrer.'</a>'; }else{ $referrer=""; }

                                if(!empty($array_data["city"])) $location[] = $array_data["city"];
                                if(!empty($array_data["region"])) $location[] = $array_data["region"];
                                if(!empty($array_data["country"])) $location[] = $array_data["country"];

                                ?>

                                <tr>
                                    <td><span title="<?php echo browser(urldecode($array_data["user_agent"]));?>" class="iconbrowser icon<?php echo browser(urldecode($array_data["user_agent"]));?>" ></span></td>
                                    <td>

                                        <?php
                                        if(count($location) > 0){
                                            echo implode(", ",$location);
                                        }else{
                                            echo '-';
                                        }
                                        ?>

                                        <?php if((strtotime($array_data["datetime_view"]) + 180) > time()){ ?>
                                            <br>
                                            <span class="online badge-pulse-green-small"></span> <?php echo $lang["display_index_title16"]; ?> <a href="<?php echo urldecode($array_data["page"]); ?>"><?php echo urldecode($array_data["page"]); ?></a>
                                        <?php }else{ ?>
                                            <br>
                                            <a href="<?php echo urldecode($array_data["page"]); ?>"><?php echo urldecode($array_data["page"]); ?></a>
                                        <?php } ?>


                                    </td>
                                    <td>
                                        <?php if($array_data["referrer"]){ ?>
                                            <a href="<?php echo urldecode($array_data["referrer"]); ?>" target="_blank" ><?php echo urldecode($array_data["referrer"]); ?></a>
                                        <?php }else{ ?>
                                            -
                                        <?php } ?>
                                    </td>
                                    <td><?php echo datetime_format($array_data["datetime_view"]);?></td>
                                </tr>


                                <?php
                            }

                            ?>

                            </tbody>
                        </table>

                        <br>
                        <ul class="pagination">
                            <?php /*echo out_navigation($result_count["result_count"],"",$_SESSION["ByShow"]);*/?>
                        </ul>

                        <?php
                    }else{

                        ?>
                        <div class="plug" >
                            <i class="la la-exclamation-triangle"></i>
                            <p><?php echo $lang["message_system_data_no_data"]; ?></p>
                        </div>
                        <?php

                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>