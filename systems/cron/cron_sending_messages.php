<?php
session_start();
define('unisitecms', true);
header('Content-Type: text/html; charset=utf-8', true);
$_SERVER['DOCUMENT_ROOT'] = "/var/www/klaster-web/data/www/parser.trk.kz";
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/config.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/incognito1408.php");
if($settings['settings']['mailing']) {
    $balance = json_decode(file_get_contents('https://api.mobizon.kz/service/user/getownbalance?output=json&api=v1&apiKey='.$secret_key_mobile_message), true);
    if($balance['data']['balance'] > 16) {
        $res_db = db_query_while("SELECT domain_name, phone_number, id FROM all_list_domain WHERE send_message=1 AND message_sent=0");
        $array_send_message = [];
        foreach ($res_db AS $res) {
            $data = db_query("SELECT COUNT(*) AS count FROM `black_list_domain` WHERE domain ='" . $res['domain_name'] . "' OR number='" . $res['phone_number'] . "'");


            if ($data['count'] == 0)
                $array_send_message[] = $res;

        }

        if (count($array_send_message) > 0) {

            foreach ($array_send_message AS $key => $item) {
                $num_start = $num = $item['phone_number'];
                if ($num[0] === "8")
                    $num[0] = 7;
                $array_send_message[$key]['phone_number'] = $item['phone_number'] = $num;
                $count_domains_month = db_query_while("SELECT * FROM all_list_domain WHERE phone_number='" . $item['phone_number'] . "' OR  phone_number='" . $num_start . "' ORDER BY datetime_send DESC LIMIT 4");
                $flag_black_list = 0;
                if (count($count_domains_month) == 4)
                    $flag_black_list = (time() - (strtotime($count_domains_month[4]['datetime_created']) + (int)$settings['settings']['time_zone'] * 3600)) < 2592000 ? 1 : 0;
                //Если последний четвертый домен за последние 30дней, то добавляю в блэк лист
                if ($flag_black_list) {
                    $data1 = db_query("SELECT * FROM black_list_domain WHERE number = '{$item['phone_number']}' LIMIT 1");
                    if (count($data1) == 0)
                        db_insert_update("INSERT INTO black_list_domain (number) VALUES ('".$item['phone_number']."')");
                }else {
                    var_dump($count_domains_month);
                    $settings_arr = str_replace("{----name_domain----}", $item['domain_name'], $settings['settings']['text_message']);
                    $settings_arr = str_replace("{--phone--}", "+77073649228", $settings_arr);
                    $settings_arr = $settings_arr . " ";
                    $datetime_last_record = db_query("SELECT * FROM message_reply WHERE phone='" . $item['phone_number'] . "' OR  phone='" . $num_start . "' ORDER BY datetime_send DESC");
                    //echo time() - (strtotime($datetime_last_record['datetime_send']) + (int)$settings['settings']['time_zone']*3600);
                    if ((time() - (strtotime($datetime_last_record['datetime_send']) + (int)$settings['settings']['time_zone'] * 3600)) > 86400) {//если прошло больше суток с отправки последнего смс, то отправляем еще, если нет, то помечаем новый домен

                        $arr_http = array(
                            'apiKey' => $secret_key_mobile_message,
                            'recipient' => $item['phone_number'],
                            'output' => 'json',
                            'text' => $settings_arr
                        );
                        $arr_http = http_build_query($arr_http) . "\n";
                        $arr_http = substr($arr_http, 0, -1);
                        $url = 'https://api.mobizon.kz/service/message/sendsmsmessage?' . $arr_http;

                        $document = file_get_contents($url);
                        $document = json_decode($document, true);
                        //echo $url;
                        //var_dump($document);
                        /*echo "
                         ".$document."
                        ";*/
                        $recipient1 = $recipient2 = "";
                        $document['data']['recipient'] = isset($document['data']['recipient']) ? $document['data']['recipient'] : "";
                        $document['data']['campaignId'] = $document['data']['campaignId'] ? $document['data']['campaignId'] : "";
                        $document['data']['messageId'] = $document['data']['messageId'] ? $document['data']['messageId'] : "";
                        $document['data']['status'] = $document['data']['status'] ? $document['data']['status'] : "";
                        db_insert_update("INSERT INTO message_reply(recipient, code, campaignId, messageId, status, message, phone, domain, datetime_send)VALUES('" . $document['data']['recipient'] . "', '" . $document['code'] . "', '" . $document['data']['campaignId'] . "', '" . $document['data']['messageId'] . "', '" . $document['data']['status'] . "', '" . $document['message'] . "', " . $item['phone_number'] . ", '" . $item['domain_name'] . "', now())");
                        $message_sent = 1;
                        $comment_to_send = "_";
                        if ($document['code'] != 0) {
                            $message_sent = 2;
                            $comment_to_send = $document['data']['recipient'];
                        }
                        db_insert_update("UPDATE all_list_domain SET message_sent=$message_sent, comment_to_send='" . $comment_to_send . "' WHERE id={$item['id']}");
                    } else {
                        db_insert_update("UPDATE all_list_domain SET message_sent=2, comment_to_send='На один из доменов в этот день уже было отправлено смс.' WHERE id={$item['id']}");
                    }
                    //var_dump($test);/**/
                }
            }
        }
        echo "<pre>";
        var_dump($array_send_message);
        echo "</pre><br>";
    }else{
        echo "lol";
    }
}