#!/usr/bin/php
<?php
echo setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');

require_once $_SERVER['DOCUMENT_ROOT'] . 'phpQuery/phpQuery.php';
//$url = 'https://api.mobizon.kz/service/user/getownbalance?output=json&api=v1&apiKey=kz25a7b0078a9e5b01736e001d3999475efb5a21c37518c23779ca77219c28d370f5d7';
/*$url = 'https://api.mobizon.kz/service/message/sendsmsmessage?recipient=номер&output=json&text=Message+text+here%21&apiKey=kz25a7b0078a9e5b01736e001d3999475efb5a21c37518c23779ca77219c28d370f5d7';

$ch = curl_init(); // initialize curl handle
curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out after 15s

$document = curl_exec($ch);

echo htmlcpesialchars($document);*/
echo "привет";
$db = new PDO('mysql:host=localhost;dbname=parser_domain;charset=UTF8','parser_domain','03011998n');
/*all_list_domain.send_message=1 AND*/
$sql = "SELECT *, COUNT(*) AS lol FROM all_list_domain WHERE send_message=1 AND message_sent=0";
/*$result = $db->query($sql);
$stmt = $db->prepare($sql);
$stmt->execute();*/
/*$data =*/
/*$data = $result->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($data);
echo "</pre><br>";*/
/*$stmt->bindColumn("domain_name", $domain_name);
while($stmt->fetch(PDO::FETCH_BOUND)){
echo $domain_name."
";

}*/

$result = $db->prepare("SELECT domain_name, phone_number FROM `all_list_domain` WHERE send_message=1 AND message_sent=0 LIMIT 2");
$result->execute();
$array_send_message = [];
    while($res = $result->fetch(PDO::FETCH_BOTH)){

        $result1 = $db->prepare("SELECT COUNT(*) AS count FROM `black_list_domain` WHERE domain ='".$res['domain_name']."' OR number='".$res['phone_number']."'");
        $result1->execute();
        $data = $result1->fetch(PDO::FETCH_BOTH);
        if($data['count'] == 0)
            $array_send_message[] = $res;

    }
function db_query($query){

    $db_host = "localhost";
    $db_user = "parser_domain";
    $db_pass = "03011998n";
    $db_charset = "utf8";
    $db_database = "parser_domain";
    $db_prefix = "uni_";
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_database);
    $sql = $mysqli->query("$query");
    if ($sql->num_rows)
    {
        return $sql->fetch_assoc();
    }else{
        return array();
    }
}
    if(count($array_send_message) > 0) {
        $result = $db->prepare("SELECT settings_array FROM `settings` WHERE settings_name='settings'");
        $result->execute();
        $settings = $result->fetch(PDO::FETCH_BOTH);
        //$settings = db_query("SELECT settings_array FROM `settings` WHERE settings_name='settings'");
        $settings = json_decode($settings['settings_array'], true);
        foreach ($array_send_message AS $key => $item) {

            $settings_arr = str_replace("{----name_domain----}", $item['domain_name'], $settings['text_message']);
            $settings_arr = str_replace("{--phone--}", "+77073649228", $settings_arr);
            $settings_arr = $settings_arr." ";
            /*echo "
            ".$settings_arr."
            ";*/
           /* var_dump($settings);*/
            $num = $item['phone_number'];
            if($num[0] === "8")
                $num[0] = 7;$array_send_message[$key]['phone_number'] = $item['phone_number'] = $num;
            $arr_http = array(
                'apiKey' => $secret_key_mobile_message,
                'recipient' => $item['phone_number'],
                'output' => 'json',
                'text' => $settings_arr
            );

            $arr_http = http_build_query($arr_http) . "\n";
            $arr_http = substr($arr_http,0,-1);
           // echo "eddfsdsdfs       ".$arr_http;
            $url = 'https://api.mobizon.kz/service/message/sendsmsmessage?'.$arr_http;
            /*
                        echo "eddfsdsdfs       ".$url;
                        $ch = curl_init(); // initialize curl handle
                        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
                        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
                        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out after 15s

                        $document = curl_exec($ch);*/
            /*$ua = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 MRA 5.7 (build 03796) Firefox/3.6.13';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
            $get=curl_exec($ch);
            curl_close($ch);*/
            //echo $url;
           $document = file_get_contents($url);
           var_dump(json_decode($document, true));
            /*echo "
             ".$document."
            ";*/

        }
    }
echo "<pre>";
var_dump($array_send_message);
echo "</pre><br>";
/*while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $data = $name . "\t" . $colour . "\t" . $cals . "\n";
    print $data;
}*/