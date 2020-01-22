
<?php

session_start();
define('unisitecms', true);
header('Content-Type: text/html; charset=utf-8', true);

include_once("./systems/config.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/incognito1408.php");

$d = parse_url(getenv("HTTP_HOST"));
$exp = explode(".", $d["path"]);
if($exp[0] == "www"){ $domen = mb_substr($d["path"], 4, mb_strlen($d["path"], "UTF-8" ), "UTF-8" ); }else{ $domen = $d["path"]; }


$REQUEST_URI = explode("?",$_SERVER['REQUEST_URI']);


$REQUEST_URI = $REQUEST_URI[0];
/*
$URI = $domen.$_SERVER['REQUEST_URI'];*/

if(trim($REQUEST_URI, "/") != "ajax/admin" && trim($REQUEST_URI, "/") != "ajax/metrics" && trim($REQUEST_URI, "/") != "ajax/profile" && trim($REQUEST_URI, "/") != "ajax/user" && trim($REQUEST_URI, "/") != "ajax/ads" && trim($REQUEST_URI, "/") != "ajax/geo" && trim($REQUEST_URI, "/") != "ajax/temp_image" && trim($REQUEST_URI, "/") != "ajax/search-city" && trim($REQUEST_URI, "/") != "ajax/shop"){
    //$Access->check();
    //$Banners->click();
    //$Profile->mode();
}





$routing = [
    "ulogin" => "systems/ajax/ulogin.php",
    "board" => "route/board.php",
    "sales" => "route/sales.php",
    "ajax/admin" => "systems/ajax/admin.php",
    "ajax/profile" => "systems/ajax/profile.php",
    "ajax/user" => "systems/ajax/profile.php",
    "ajax/database" => "systems/ajax/database.php",
    "ajax/metrics" => "systems/ajax/metrics.php",
    "index" => "route/index.php",
];
if(!empty($REQUEST_URI) && $REQUEST_URI != "/"){

    foreach($routing as $pattern=>$router){

        if(preg_match("#^".$pattern."$#iu",trim($REQUEST_URI, "/"))){
            $URI = explode("/", trim($REQUEST_URI, "/"));
            $page = $router;

        }

    }

    if($page){
        if(file_exists($page)){  include($page);
        }else{
            include('files/response/404/404.php');
        }

    }else{
//Тут нужно запилить скрипт на объявления
        include('files/response/404/404.php');
    }

}else{
    include("route/index.php");
}


?>

<?php
/*
#!/usr/bin/php
require_once $_SERVER['DOCUMENT_ROOT'].'phpQuery/phpQuery.php';

class Parser
{
    private static $arErrorCodes = [
        "CURLE_UNSUPPORTED_PROTOCOL",
        "CURLE_FAILED_INIT",
        "CURLE_URL_MALFORMAT",
        "CURLE_URL_MALFORMAT_USER",
        "CURLE_COULDNT_RESOLVE_PROXY",
        "CURLE_COULDNT_RESOLVE_HOST",
        "CURLE_COULDNT_CONNECT",
        "CURLE_FTP_WEIRD_SERVER_REPLY",
        "CURLE_REMOTE_ACCESS_DENIED",
        "CURLE_FTP_WEIRD_PASS_REPLY",
        "CURLE_FTP_WEIRD_PASV_REPLY",
        "CURLE_FTP_WEIRD_227_FORMAT",
        "CURLE_FTP_CANT_GET_HOST",
        "CURLE_FTP_COULDNT_SET_TYPE",
        "CURLE_PARTIAL_FILE",
        "CURLE_FTP_COULDNT_RETR_FILE",
        "CURLE_QUOTE_ERROR",
        "CURLE_HTTP_RETURNED_ERROR",
        "CURLE_WRITE_ERROR",
        "CURLE_UPLOAD_FAILED",
        "CURLE_READ_ERROR",
        "CURLE_OUT_OF_MEMORY",
        "CURLE_OPERATION_TIMEDOUT",
        "CURLE_FTP_PORT_FAILED",
        "CURLE_FTP_COULDNT_USE_REST",
        "CURLE_RANGE_ERROR",
        "CURLE_HTTP_POST_ERROR",
        "CURLE_SSL_CONNECT_ERROR",
        "CURLE_BAD_DOWNLOAD_RESUME",
        "CURLE_FILE_COULDNT_READ_FILE",
        "CURLE_LDAP_CANNOT_BIND",
        "CURLE_LDAP_SEARCH_FAILED",
        "CURLE_FUNCTION_NOT_FOUND",
        "CURLE_ABORTED_BY_CALLBACK",
        "CURLE_BAD_FUNCTION_ARGUMENT",
        "CURLE_INTERFACE_FAILED",
        "CURLE_TOO_MANY_REDIRECTS",
        "CURLE_UNKNOWN_TELNET_OPTION",
        "CURLE_TELNET_OPTION_SYNTAX",
        "CURLE_PEER_FAILED_VERIFICATION",
        "CURLE_GOT_NOTHING",
        "CURLE_SSL_ENGINE_NOTFOUND",
        "CURLE_SSL_ENGINE_SETFAILED",
        "CURLE_SEND_ERROR",
        "CURLE_RECV_ERROR",
        "CURLE_SSL_CERTPROBLEM",
        "CURLE_SSL_CIPHER",
        "CURLE_SSL_CACERT",
        "CURLE_BAD_CONTENT_ENCODING",
        "CURLE_LDAP_INVALID_URL",
        "CURLE_FILESIZE_EXCEEDED",
        "CURLE_USE_SSL_FAILED",
        "CURLE_SEND_FAIL_REWIND",
        "CURLE_SSL_ENGINE_INITFAILED",
        "CURLE_LOGIN_DENIED",
        "CURLE_TFTP_NOTFOUND",
        "CURLE_TFTP_PERM",
        "CURLE_REMOTE_DISK_FULL",
        "CURLE_TFTP_ILLEGAL",
        "CURLE_TFTP_UNKNOWNID",
        "CURLE_REMOTE_FILE_EXISTS",
        "CURLE_TFTP_NOSUCHUSER",
        "CURLE_CONV_FAILED",
        "CURLE_CONV_REQD",
        "CURLE_SSL_CACERT_BADFILE",
        "CURLE_REMOTE_FILE_NOT_FOUND",
        "CURLE_SSH",
        "CURLE_SSL_SHUTDOWN_FAILED",
        "CURLE_AGAIN",
        "CURLE_SSL_CRL_BADFILE",
        "CURLE_SSL_ISSUER_ERROR",
        "CURLE_FTP_PRET_FAILED",
        "CURLE_FTP_PRET_FAILED",
        "CURLE_RTSP_CSEQ_ERROR",
        "CURLE_RTSP_SESSION_ERROR",
        "CURLE_FTP_BAD_FILE_LIST",
        "CURLE_CHUNK_FAILED"
    ];
    public static function getPage($arParams = [])
    {
        if ($arParams) {
            if (!empty($arParams["url"])) {
                $sUrl = $arParams["url"];
                $sUserAgent = !empty($arParams["useragent"]) ? $arParams["useragent"] : "Mozilla/5.0 (Windows NT 6.3; W…) Gecko/20100101 Firefox/57.0";
                $iTimeout = !empty($arParams["timeout"]) ? $arParams["timeout"] : 5;
                $iConnectTimeout = !empty($arParams["connecttimeout"]) ? $arParams["connecttimeout"] : 5;
                $bHead = !empty($arParams["head"]) ? $arParams["head"] : false;
                $sCookieFile = !empty($arParams["cookie"]["file"]) ? $arParams["cookie"]["file"] : false;
                $bCookieSession = !empty($arParams["cookie"]["session"]) ? $arParams["cookie"]["session"] : false;
                $sProxyIp = !empty($arParams["proxy"]["ip"]) ? $arParams["proxy"]["ip"] : false;
                $iProxyPort = !empty($arParams["proxy"]["port"]) ? $arParams["proxy"]["port"] : false;
                $sProxyType = !empty($arParams["proxy"]["type"]) ? $arParams["proxy"]["type"] : false;
                $arHeaders = !empty($arParams["headers"]) ? $arParams["headers"] : false;
                $sPost = !empty($arParams["post"]) ? $arParams["post"] : false;
                if ($sCookieFile) {
                    file_put_contents(__DIR__ . "/" . $sCookieFile, "");
                }
                $rCh = curl_init();
                curl_setopt($rCh, CURLOPT_URL, $sUrl);
                curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($rCh, CURLOPT_USERAGENT, $sUserAgent);
                curl_setopt($rCh, CURLOPT_TIMEOUT, $iTimeout);
                curl_setopt($rCh, CURLOPT_CONNECTTIMEOUT, $iConnectTimeout);
                if ($bHead) {
                    curl_setopt($rCh, CURLOPT_HEADER, true);
                    curl_setopt($rCh, CURLOPT_NOBODY, true);
                }
                if (strpos($sUrl, "https") !== false) {
                    curl_setopt($rCh, CURLOPT_SSL_VERIFYHOST, true);
                    curl_setopt($rCh, CURLOPT_SSL_VERIFYPEER, true);
                }
                if ($sCookieFile) {
                    curl_setopt($rCh, CURLOPT_COOKIEJAR, __DIR__ . "/" . $sCookieFile);
                    curl_setopt($rCh, CURLOPT_COOKIEFILE, __DIR__ . "/" . $sCookieFile);
                    if ($bCookieSession) {
                        curl_setopt($rCh, CURLOPT_COOKIESESSION, true);
                    }
                }
                if ($sProxyIp && $iProxyPort && $sProxyType) {
                    curl_setopt($rCh, CURLOPT_PROXY, $sProxyIp . ":" . $iProxyPort);
                    curl_setopt($rCh, CURLOPT_PROXYTYPE, $sProxyType);
                }
                if ($arHeaders) {
                    curl_setopt($rCh, CURLOPT_HTTPHEADER, $arHeaders);
                }
                if ($sPost) {
                    curl_setopt($rCh, CURLOPT_POSTFIELDS, $sPost);
                }

                curl_setopt($rCh, CURLINFO_HEADER_OUT, true);
                $sContent = curl_exec($rCh);
                $arInfo = curl_getinfo($rCh);
                $arError = false;
                if ($sContent === false) {
                    $arData = false;
                    $arError["message"] = curl_error($rCh);
                    $arError["code"] = self::$arErrorCodes[curl_errno($rCh)];
                } else {

                    $arData["content"] = $sContent;
                    $arData["info"] = $arInfo;
                    //$html["data"]["content"]

                }
                curl_close($rCh);
                return [
                    "data" => $arData,
                    "error" => $arError
                ];
            }
        }
        return false;
    }
}
$html = Parser::getPage([
    "url" => "https://nic.kz/" // string Ссылка на страницу
]);

if(!empty($html["data"])){

    $content = $html["data"]["content"];

    phpQuery::newDocument($content);

    $categories = pq("#last-ten-table")->find(".white-text");

    $tmp = [];
    foreach ($categories as $leagueTable) {
        $leagueTable = pq($leagueTable);
        $leagueName = $leagueTable->find('thead tr th')->text();

    }
   foreach ($leagueTable->find('table') as $table) {
       $table = pq($table);
       foreach ($table->find('tr') as $key => $tr) {
           $tr = pq($tr);
           $tmp[$key] = [];
           $trr1 = pq($tr);
           foreach ($tr->find('td') as $key1 => $td) {
               $td = pq($td);
               if($td->attr("align") == "center"){
                   //$tmp[$key]["date"] = $td->html();
               }
               if($td->attr("align") == "right"){
                   //$tmp[$key]["domain_name"] = $td->html();
                   $ht = pq($td->html());
                   //$tmp[$key]["href"] = $ht->attr("href");


                   $html_info = Parser::getPage([
                       "url" => "https://nic.kz/".$ht->attr("href") // string Ссылка на страницу
                   ]);

                   if(!empty($html_info["data"])) {

                       $content_info = $html_info["data"]["content"];

                       phpQuery::newDocument($content_info);

                       $table = pq("#contentBarCommon")->find("#articleTable");

                       foreach ($table as $table_it) {
                           $leagueTable = pq($table_it);
                           $array_pre = $leagueTable->find('pre')->text();

                       }
                       $array_pre = explode("\n",$array_pre);
                       foreach ($array_pre as $str) {
                           $new_name = name_db_name($str);
                           if ($new_name) {
                               $tmp[$key] = array_merge($tmp[$key], $new_name);
                           }
                           $tmp[$key][':datetime_add'] = date("Y-m-d H:i:s", time());
                           $tmp[$key][':full_info'] = $leagueTable->find('pre')->text();
                       }
                       $db = new PDO('mysql:host=localhost;dbname=parser_domain;charset=UTF8','parser_domain','03011998n');

                       $sql = "SELECT * FROM all_list_domain WHERE domain_name = '{$tmp[$key][':domain_name']}' LIMIT 1";
                       $result = $db->query($sql);
                       $data = $result->fetch(PDO::FETCH_ASSOC);
                       if(!$data) {
                           $query = "INSERT INTO `all_list_domain` (`full_info`,`organization_name`,`domain_name`,`datetime_created`,`user_name`,`phone_number`, `email`, `city`, `street_address`, `registar_created`, `datetime_add`) VALUES (:full_info, :organization_name, :domain_name, :datetime_created, :user_name, :phone_number, :email, :city, :street_address, :registar_created, :datetime_add)";
                           $stmt = $db->prepare($query);
                           $stmt->execute($tmp[$key]);
                       }
                   }

               }
           }
        }
    }

    phpQuery::unloadDocuments();
}

function name_db_name($search){
    $arr = [
        'Domain Name............:' => ":domain_name",
        'Domain created:' => ":datetime_created",
        'Name...................:' => ":user_name",
        'Phone Number...........:' => ":phone_number",
        'Email Address..........:' => ":email",
        'City...................:' => ":city",
        'Street Address.........:' => ":street_address",
        'Registar created:' => ":registar_created",
        'Organization Name......:' => ":organization_name",
    ];
    foreach ($arr as $key => $item) {
        if(strpos($search, $key) !== false){
            $new_str = str_replace($key,'',$search);
            return [
                $item => trim($new_str)
            ];
        }
    }
    return false;
}*/
?>