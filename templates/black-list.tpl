
<?php
$_GET["page"] = empty($_GET["page"]) ? 1 : $_GET["page"];

$arr_http = [];
foreach ($_GET as $key => $item) {
    if($key != 'page'){
        $arr_http[$key] = $item;
    }
}
$arr_http = http_build_query($arr_http);
$url[] = "$arr_http";
$query = "";

$LINK = "?".implode("&",$url);

$LIMIT_PAGE = $settings['settings']['page_output'];
if($_GET['show'] == 1){
    $show = "black_list_domain.number = all_list_domain.phone_number AND black_list_domain.number != ''";//По номеру
}elseif($_GET['show'] == 2){
    $show = "all_list_domain.domain_name = black_list_domain.domain AND black_list_domain.domain != ''";//По домену
}else{
    $show = "(black_list_domain.number = all_list_domain.phone_number AND black_list_domain.number != '') OR (black_list_domain.domain = all_list_domain.domain_name AND black_list_domain.number = '')";
}
$type_orders_name = $_GET["type_orders_name"];
    $result_count = db_query("SELECT COUNT(*) as result_count FROM all_list_domain, black_list_domain WHERE $show");//$where $show $search $sort
    $result_logon = db_query_while("SELECT * FROM all_list_domain, black_list_domain  WHERE $show " . navigation_offset($result_count["result_count"], $LIMIT_PAGE));
//echo "<pre>";
//var_dump($result_count);
//echo "</pre>";
?>
<div class="toolkit">
    <p>
        Черный список
    </p>
    <script>$("title").text("Черный список"); </script>
</div>
<div class="form-group" style="margin-bottom: 25px;">


    <?php

    $arr_http = [];
    foreach ($_GET as $key => $item) {
        if($key != 'show'){
            $arr_http[$key] = $item;
        }
    }
    $arr_http = http_build_query($arr_http);
    ?>
    <div class="btn-group">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Показать только     </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="?<?php echo $arr_http;?>">Показать все</a>
                <a class="dropdown-item" href="?<?php echo $arr_http;?>&show=1">Заблокированные номера</a>
                <a class="dropdown-item" href="?<?php echo $arr_http;?>&show=2">Заблокированные домены</a>
            </div>
        </div>
    </div>


</div>
<input type="hidden" id="text_message_send" value="<?php echo $settings['settings']['text_message']; ?>">

<div class="col-lg-12">
    <div class="widget has-shadow">

        <div class="widget-body">
            <div class="table-responsive">

                <table class="table mb-0">
                    <thead>
                    <tr>
                        <td></td>
                        <td>Домен</td>
                        <td>Создали</td>
                        <td>Имя польз</td>
                        <td>Номер</td>
                        <td>Email</td>
                        <td>Город</td>
                        <td>Адресс</td>
                        <td>Кто выдал</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($result_logon as $item) {
                        $sp_bun = $bgr = "";
                        $black_list = db_query("SELECT * FROM black_list_domain WHERE number = '".trim($item["phone_number"])."' OR domain = '".trim($item["domain_name"])."'");
                        if($black_list){
                            $bgr = "style='background: #a74c4c;'";
                            $sp_bun = '<span class="badge-pulse-red-small" title="Домен/Номер тел. находится в черном списке."></span>';
                        }
                        $name = $item['user_name'] != "" ? $item['user_name'] : $item['organization_name'];
                        $name_arr = ["Hostmaster"];
                        if (in_array($name, $name_arr)) {
                            $name = $item['organization_name'];
                        }
                        ?>
                        <tr <?php echo $bgr;?>>
                            <td style="position: relative; border-width: 1px 0px 1px 1px !important">
                                <div class="left_block">

                                </div>

                                <span class="icon_table icon_table_message <?php echo $item['message_sent'] == 1 ? "icon_message_active" : "icon_message"; ?>" data-id="<?php echo $item['id']; ?>"></span>
                                <span class="icon_table <?php echo strlen(trim($item['comment_call'])) > 0 ? "icon_phone_active" : "icon_phone"; ?>"></span>
                                <?php
                                if($item['send_message'] == 0)
                                    echo '<span class="badge-pulse-blue" title="Рассылка была отключена."></span>';
                                if($item['message_sent'] == 1)
                                    echo '<span class="badge-pulse-green" title="Сообщение было отправлено."></span>';
                                if($item['message_sent'] == 2)
                                    echo '<span class="badge-pulse-orange" title="'.$item['comment_to_send'].'"></span>';
                                echo $sp_bun;
                                ?>
                            </td>
                            <td style="position: relative; border-width: 1px 1px 1px 0px !important;">
                                <?php echo $item['domain_name'];?>
                            </td>
                            <td>
                                <?php
                                echo datetime_format($item['datetime_created']);
                                ?>
                                <div class="block_icons_bottom">
                                    <a href="https://www.ps.kz/domains/whois/result?q=<?php echo $item['domain_name']?> " target="_blank" class="link_style icons_ps"></a>
                                    <a href="https://hoster.kz/whois/?d=<?php echo $item['domain_name']?> " target="_blank" class="link_style icons_ula"></a>
                                    <a href="https://hoster.kz/whois/?d=<?php echo $item['domain_name']?> " target="_blank" class="link_style icons_refresh"></a>
                                </div>
                            </td>
                            <td>
                                <?php echo $name;?>
                            </td>
                            <td>
                                <?php echo $item['phone_number'];?>
                            </td>
                            <td>
                                <?php echo $item['email'];
//                                var_dump($item);
                                ?>
                            </td>
                            <td>
                                <?php echo $item['city'];?>
                            </td>
                            <td>
                                <?php echo $item['street_address'];?>
                            </td>
                            <td>
                                <?php echo $item['registar_created'];?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div></div></div></div>

<ul class="pagination">
    <?php echo out_navigation($result_count['result_count'],$LINK,$LIMIT_PAGE);?>
</ul>
