
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
?>
<div class="toolkit">
    <p>
        Лог сообщений
    </p>
    <script>$("title").text("Лог сообщений"); </script>
</div>
    <?php

$currentPage = $_GET['page'];
$document = file_get_contents("https://api.mobizon.kz/service/message/list?output=json&pagination[currentPage]=".($_GET['page']-1)."&pagination[pageSize]=$LIMIT_PAGE&api=v1&apiKey=$secret_key_mobile_message");
$document = json_decode($document, true);
?>
</div>
<div class="col-lg-12">
    <div class="widget has-shadow">

        <div class="widget-body">
            <div class="table-responsive">

                <table class="table mb-0">
                    <thead>
                    <tr>
                        <!--  <td>id</td>-->
                        <td>campaignId</td>
                        <td>segUserBuy</td>
                        <td>status</td>
                        <td>Номер получателя</td>
                        <td>text</td>
                        <td>Дата</td>
                        <!-- <td></td>
                         <td></td>-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($document['data']['items'] as $item) {
                        ?>
                        <td>
                            <?php echo $item['campaignId'];?>
                        </td>
                        <td>
                            <?php echo $item['segUserBuy']*$item['segNum']."KZT";?>
                        </td>
                        <td>
                            <?php echo $item['status'];?>
                        </td>
                        <td>
                            <?php echo "+".substr($item['to'], 0, 1)."(".substr($item['to'], 1,  3).")".substr($item['to'], 3,  2)."-".substr($item['to'], 5,  2)."-".substr($item['to'], 7, 2)."-".substr($item['to'], 9, 2);?>
                        </td>
                        <td>
                            <?php echo $item['text'];?>
                        </td>
                        <td>
                            <?php echo datetime_format($item['statusUpdateTs']);?>
                        </td>
                         </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div></div></div></div>
<ul class="pagination">
    <?php echo out_navigation($document['data']['totalItemCount'], $LINK, $LIMIT_PAGE);?>
</ul>