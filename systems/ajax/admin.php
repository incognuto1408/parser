<?php

if(isAjax() == true) {

    if ($_POST["action"] == "get-fones") {
        $sql = db_query("SELECT * FROM all_list_domain WHERE id=".$_POST['id']);
        echo $sql['comment_call'];
    }
    if ($_POST["action"] == "add-phones") {
        $arr_res = [
            "status" => 0,
            "error_text" => "Ошибок нет"
        ];
        $upd_res = db_insert_update("UPDATE all_list_domain SET comment_call='" . $_POST['text'] . "' WHERE id={$_POST['id']}");
        if($upd_res){
            $arr_res['status'] = 1;
        }else{
            $arr_res['error_text'] = "Неизвестная ошибка!";
        }
        echo json_encode($arr_res);
    }
    if ($_POST["action"] == "add-black-list") {
        $sql = db_query("SELECT * FROM all_list_domain WHERE id=".$_POST['id']);
        if($sql) {
            $where = $_POST['type'] == 'domain' ? "domain='".$sql['domain_name']."'" : "number='".$sql['phone_number']."'";
            $ins_name = 'domain';
            if($_POST['type'] == 'domain'){
                $ins = $sql['domain_name'];
            }else {
                $ins = $sql['phone_number'];
                $ins_name = "number";
            }
            $sql_black_list = db_query("SELECT * FROM black_list_domain WHERE $where");
            if(!$sql_black_list) {
                $insert = db_insert_update("INSERT INTO black_list_domain($ins_name)VALUES('$ins')");
                if ($insert) {
                    echo true;
                } else {
                    echo "Ошибка добавления!" . $_POST['type'] . $insert . $ins;
                }
            }else{
                echo "Уже есть в базе!";
            }
        }else{
            echo "Данного домена не существует!";
        }
    }
    if ($_POST["action"] == "info") {
        //echo "Hello world ".$_POST['text']." ".$_POST['id'];
        $arr_res = [
            "status" => 0,
            "error_text" => "Ошибок нет"
        ];
        $query = db_query("SELECT * FROM all_list_domain WHERE id='" . $_POST['id'] . "'");
        if ($query) {
            $data = db_query("SELECT COUNT(*) AS count FROM `black_list_domain` WHERE domain ='" . $query['domain_name'] . "' OR number='" . $query['phone_number'] . "'");
            if ($data['count'] == 0) {

                $num = $query['phone_number'];
                if ($num[0] === "8")
                    $num[0] = 7;
                $query['phone_number'] = $num;


                $settings_arr = str_replace("{----name_domain----}", $query['domain_name'], $_POST['text']);
                $settings_arr = str_replace("{--phone--}", "+77073649228", $settings_arr);

                $arr_http = array(
                    'apiKey' => $secret_key_mobile_message,
                    'recipient' => $query['phone_number'],
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
                $document['data']['recipient'] = isset($document['data']['recipient']) ? $document['data']['recipient'] : "";
                $document['data']['campaignId'] = $document['data']['campaignId'] ? $document['data']['campaignId'] : "";
                $document['data']['messageId'] = $document['data']['messageId'] ? $document['data']['messageId'] : "";
                $document['data']['status'] = $document['data']['status'] ? $document['data']['status'] : "";
                if ($document['data']['code'] == 0){
                    $ins_res =  db_insert_update("INSERT INTO message_reply(recipient, code, campaignId, messageId, status, message, phone, domain, datetime_send, request_type, sender_name)VALUES('" . $document['data']['recipient'] . "', '" . $document['code'] . "', '" . $document['data']['campaignId'] . "', '" . $document['data']['messageId'] . "', '" . $document['data']['status'] . "', '" . $document['message'] . "', " . $query['phone_number'] . ", '" . $query['domain_name'] . "', now(), 'USER', '".$_SESSION['profile']['login']."')");
                    $message_sent = 1;
                    $comment_to_send = "_";
                    if ($document['code'] != 0) {
                        $message_sent = 2;
                        $comment_to_send = $document['data']['recipient'];
                    }
                    $upd_res = db_insert_update("UPDATE all_list_domain SET message_sent=$message_sent, comment_to_send='" . $comment_to_send . "' WHERE id={$query['id']}");
                    if($ins_res && $upd_res){
                        $arr_res["code"] = $document['code'];
                        $arr_res["status"] = 1;
                    }else{
                        $text = $document['code'] == 0 ? "Но сообщение отправлено удачно" : $document['data']['recipient'];
                        $arr_res["status"] = 0;
                        $arr_res["error_text"] = "Неудачное добавление в базу. ".$text;
                    }
                }else{

                    $arr_res["status"] = 0;
                    $arr_res["error_text"] = $document['data']['recipient'];
                }



            } else {
                $arr_res["status"] = 0;
                $arr_res["error_text"] = "Находится в черном списке!";
            }
        } else {
            $arr_res["status"] = 0;
            $arr_res["error_text"] = "Запись отсутствует!";
        }
        echo json_encode($arr_res, JSON_UNESCAPED_UNICODE);
    }
    if ($_POST["action"] == "settings-save") {
        $array = [
          "page_output" => $_POST['page_output'],
          "color_table_line" => $_POST['color_table_line'],
          "color_table_line_2" => $_POST['color_table_line_2'],
          "mailing" => $_POST['mailing'],
          "text_message" => $_POST['text_message'],
          "time_zone" => $_POST['time_zone'],
        ];
        echo $Settings->setSettings($array);
    }

    $profile = $Profile->get_user_info($_POST['id']);
    if ($profile['type_person'] < $_SESSION['profile']['type_person']) {
        if ($_POST["action"] == "user-upload-status") {
            if ($_POST['status'] != 1)
                $_POST['status'] = 0;
            if ($profile['id'] != $_SESSION['profile']['id']) {
                    $update = db_insert_update("UPDATE users SET status='" . $_POST['status'] . "' WHERE id={$_POST['id']}");
                    echo $update;
            } else {
                echo "Невозможно изменить статус своего профиля!!!";
            }
        }
        if ($_POST["action"] == "user-upload-type-person") {

            if ($_POST['type'] < $_SESSION['profile']['type_person']) {
                    if ($profile['id'] != $_SESSION['profile']['id']) {
                        $update = db_insert_update("UPDATE users SET type_person='" . $_POST['type'] . "' WHERE id={$_POST['id']}");
                        echo $update;
                    } else {
                        echo "Невозможно изменить ранг своего профиля!!!";
                    }
                } else {
                    echo "Невозможно выдать ранг выше или равному своему!!!";
                }
        }
        if ($_POST["action"] == "user-delete") {
                if ($profile['id'] != $_SESSION['profile']['id']) {
                    $delete = db_insert_update("DELETE FROM users WHERE id={$_POST['id']}");
                    echo $delete;
                } else {
                    echo "Невозможно удалить свой профиль!!!";
                }
        }
    } else {
        echo "У вас недостаточно прав для выполнения этого действия!";
    }
}