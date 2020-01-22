<?php

/**
 * UniSite CMS
 *
 * @copyright 	2018 Artur Zhur
 * @link 		https://unisitecms.ru
 * @author 		Artur Zhur
 *
 */


function mb_ucfirst($str, $encoding='UTF-8')
{
    $str = mb_ereg_replace('^[\ ]+', '', $str);
    $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
        mb_substr($str, 1, mb_strlen($str), $encoding);
    return $str;
}
function switch_lang(){
  global $settings,$image_language;

  if($settings["visible_lang_site"]){
    if($_SESSION["langSite"]){
       if(file_exists($_SERVER["DOCUMENT_ROOT"]."/lang/".$_SESSION["langSite"].".php")){
          return json_decode( ob_get($_SERVER["DOCUMENT_ROOT"]."/lang/".$_SESSION["langSite"].".php"), true );
       }else{
          return json_decode( ob_get($_SERVER["DOCUMENT_ROOT"]."/lang/".$settings["lang_site_default"].".php"), true );
       }
    }else{

      $sql = db_query("select * from uni_languages where iso='{$settings["lang_site_default"]}'");

      $_SESSION["langSite"] = $settings["lang_site_default"];
      $_SESSION["langName"] = $sql["name"];
      $_SESSION["langIcon"] = URL.$image_language.$sql["image"];

       if(file_exists($_SERVER["DOCUMENT_ROOT"]."/lang/".$settings["lang_site_default"].".php")){
          return json_decode( ob_get($_SERVER["DOCUMENT_ROOT"]."/lang/".$settings["lang_site_default"].".php"), true );
       }
    }
  }else{
       if(file_exists($_SERVER["DOCUMENT_ROOT"]."/lang/".$settings["lang_site_default"].".php")){
          return json_decode( ob_get($_SERVER["DOCUMENT_ROOT"]."/lang/".$settings["lang_site_default"].".php"), true );
       }
  }

}

function lang(){
    return $_SESSION["langSite"];
}

function ob_get($content)
{
  ob_start();
  include $content;
  return ob_get_clean();
}

function clear($data)
{
        global $mysqli;
        $str = strip_tags($data);
        $str = $mysqli->real_escape_string($str);
        $str = trim($str);
        return $str;
}

function browser($user_agent){
    if (strpos($user_agent, "Firefox/") !== false) $browser = "Firefox";
    elseif (strpos($user_agent, "Opera/") !== false || strpos($user_agent, 'OPR/') !== false ) $browser = "Opera";
    elseif (strpos($user_agent, "YaBrowser/") !== false) $browser = "Yandex";
    elseif (strpos($user_agent, "Chrome/") !== false) $browser = "Chrome";
    elseif (strpos($user_agent, "MSIE/") !== false || strpos($user_agent, 'Trident/') !== false ) $browser = "Internet Explorer";
    elseif (strpos($user_agent, "Safari/") !== false) $browser = "Safari";
    else $browser = "Undefined";
    return $browser;
}
function settings(){
   $sql = db_query_while("select *, (select name from uni_currency where main=1) as currency_name, (select sign from uni_currency where main=1) as currency_sign, (select code from uni_currency where main=1) as currency_code from uni_settings");
     if(count($sql) > 0){
        foreach ($sql as $key => $value) {
            $settings[$value["name"]] = $value["value"];

            $settings["currency_name"] = $value["currency_name"];
            $settings["currency_sign"] = $value["currency_sign"];
            $settings["currency_code"] = $value["currency_code"];

        }
     }
   return $settings;
}

function settings_tpl(){
   $sql = db_query_while("select * from uni_settings_tpl");
     if(count($sql) > 0){
        foreach ($sql as $key => $value) {
             $settings_tpl[$value["page"]][$value["name"]] = $value["value"];
        }
     }
   return $settings_tpl;
}

function resize($filepath, $newfilepath, $width, $height)
{
    $size = getimagesize ($filepath);
    if($size[0] > $width){
      $new_image = new picture($filepath);
      $new_image->imageresizewidth($width);
      $new_image->imagesave($new_image->image_type, $newfilepath, 80);
      $new_image->imageout();
    }else{
      $new_image = new picture($filepath);
      $new_image->imageresizewidth($size[0]);
      $new_image->imagesave($new_image->image_type, $newfilepath, 80);
      $new_image->imageout();
    }
}

function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}

function validateEmail($email){
  if (!preg_match("/^(?:[a-z0-9\.]+(?:[-_.]?[a-z0-9\_\.\-]+)?@[a-z0-9\_\.\-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i",
      trim($email))) {
      return false;
  }else{
      return true;
  }
}

function validatePhoneNumber($phone)
{

  $phoneNumber = preg_replace('/\s|\+|-|\(|\)/','', $phone);

  if(is_numeric($phoneNumber))
  {
    if(strlen($phoneNumber) < 5)
    {
      return FALSE;
    }
    else
    {
      return true;
    }
  }
  else
  {
    return FALSE;
  }

}


function Exists($dir,$name,$return){
   if(!empty($name) && file_exists($_SERVER['DOCUMENT_ROOT'].$dir.$name)){
    if(substr($dir,0,1) == "/"){$dir = substr($dir,1,strlen($dir));}
       return URL.$dir.$name;
   }else{
       return URL.$return;
   }
}


function out_navigation($count=0,$url="",$output=10,$prev="",$next=""){

   if($output && $count){

    if ($count > 0)
    {
        $total = (($count - 1) / $output) + 1;
        $page = intval($_GET["page"]);
        if(empty($page) or $page < 0) $page = 1;
        if($page > intval($total)) $page = intval($total);
    }

    if($url){

      if(substr($url, 0,1) != "?") $var_page = '?'.$url.'&page='; else $var_page = $url.'&page=';

    }else{$var_page = '?page=';}

    if($page - 5 > 0) $page5left = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page - 5).'">'.($page - 5).'</a></li>';
    if($page - 4 > 0) $page4left = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page - 4).'">'.($page - 4).'</a></li>';
    if($page - 3 > 0) $page3left = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page - 3).'">'.($page - 3).'</a></li>';
    if($page - 2 > 0) $page2left = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page - 2).'">'.($page - 2).'</a></li>';
    if($page - 1 > 0) $page1left = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page - 1).'">'.($page - 1).'</a></li>';

    if($page + 5 <= $total) $page5right = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page + 5).'">'.($page + 5).'</a></li>';
    if($page + 4 <= $total) $page4right = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page + 4).'">'.($page + 4).'</a></li>';
    if($page + 3 <= $total) $page3right = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page + 3).'">'.($page + 3).'</a></li>';
    if($page + 2 <= $total) $page2right = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page + 2).'">'.($page + 2).'</a></li>';
    if($page + 1 <= $total) $page1right = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page + 1).'">'.($page + 1).'</a></li>';


    if ($page+5 < $total)
    {
        $link_total = '<li class="page-item" ><a class="page-link" href="'.$var_page.intval($total).'">'.intval($total).'</a></li>';
    }

    if ($page > 6)
    {
        $link_first = '<li class="page-item" ><a class="page-link" href="'.$var_page.'1">1</a></li>';
    }

    if($prev){ if($page - 1) $prev = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page - 1).'">'.$prev.'</a></li>'; else $prev = ""; }
    if($next){ if($page < $total) $next = '<li class="page-item" ><a class="page-link" href="'.$var_page.($page + 1).'">'.$next.'</a></li>'; else $next = ""; }

    if(intval($total) > 1){
       return $link_first.$page5left.$page4left.$page3left.$page2left.$page1left.'<li class="active page-item" ><a class="page-link" href="'.$var_page.$page.'" >'.$page.'</a></li>'.$page1right.$page2right.$page3right.$page4right.$page5right.$link_total;
    }

   }

}

function navigation_offset($count = 0,$output=10){

    If ($count > 0)
    {
        $total = (($count - 1) / $output) + 1;
        $page = intval($_GET["page"]);
        if(empty($page) or $page < 0) $page = 1;
        if($page > intval($total)) $page = intval($total);
        $start = $page * $output - $output;
        return " LIMIT $start, $output";

    }else{ return ""; }

}


function translite($name)
{
    static $trans = array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' =>
        'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' =>
        't', 'у' => 'u', 'ф' => 'f', 'ы' => 'i', 'э' => 'e', 'А' => 'A', 'Б' => 'B', 'В' =>
        'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' =>
        'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Ы' => 'I', 'Э' =>
        'E', 'ё' => "yo", 'х' => "h", 'ц' => "ts", 'ч' => "ch", 'ш' => "sh", 'щ' =>
        "shch", 'ъ' => "", 'ь' => "", 'ю' => "yu", 'я' => "ya", 'Ё' => "YO", 'Х' => "H",
        'Ц' => "TS", 'Ч' => "CH", 'Ш' => "SH", 'Щ' => "SHCH", 'Ъ' => "", 'Ь' => "", 'Ю' =>
        "YU", 'Я' => "YA");

    $strstring = strtr($name, $trans);
    $result = preg_replace('/[^a-zа-яё0-9]+/iu', ' ', strtolower($strstring));
    return trim(preg_replace('/\s+/', '-', $result),"-");
}


function generatePass($number=7){

 $arr = array('a','b','c','d','e','f',

              'g','h','i','j','k','l',

              'm','n','o','p','r','s',

              't','u','v','x','y','z',

              'A','B','C','D','E','F',

              'G','H','I','J','K','L',

              'M','N','O','P','R','S',

              'T','U','V','X','Y','Z',

              '1','2','3','4','5','6',

              '7','8','9','0');

 $pass = "";

 for($i = 0; $i < $number; $i++)
     {
         $index = rand(0, count($arr) - 1);
         $pass .= $arr[$index];
     }

 return $pass;
}


function datetime_format($string) {
    $monn = array(
        '',
        "января",
        "февраля",
        "марта",
        "апреля",
        "мая",
        "июня",
        "июля",
        "августа",
        "сентября",
        "октября",
        "ноября",
        "декабря"
    );

    $a = preg_split("/[^\d]/",$string);
    $today = date('Ymd');
    if(($a[0].$a[1].$a[2])==$today) {

        return("сегодня"." ".$a[3].":".$a[4]);

    } else {
        $b = explode("-",date("Y-m-d"));
        $tom = date("Ymd",mktime(0,0,0,$b[1],$b[2]-1,$b[0]));
        if(($a[0].$a[1].$a[2])==$tom) {

            return("вчера"." ".$a[3].":".$a[4]);

        } else {

            $mm = intval($a[1]);
            return($a[2]." ".$monn[$mm]." ".$a[0].", ".$a[3].":".$a[4]);
        }
    }
}

function json_encode_cyr($str) {
    $arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
    '\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
    '\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
    '\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
    '\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
    '\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
    '\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
    '\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
    $arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
    'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
    'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
    'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');
    $str1 = json_encode($str);
    $str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
    return $str2;
}

function watermark($path,$wate,$name_img,$pos){
  global $main_image_watermark;

  $path_wate = $_SERVER['DOCUMENT_ROOT'].$main_image_watermark.$wate;
  $path_image = $_SERVER['DOCUMENT_ROOT'].$path.$name_img;

  if($wate && file_exists($path_wate) && $name_img && $pos != 6){

      try {

        $image_info = getimagesize($path_image);
        $image_type = $image_info[2];
        if( $image_type == IMAGETYPE_JPEG ) {
           $im = imagecreatefromjpeg($path_image);
        } elseif( $image_type == IMAGETYPE_GIF ) {
           $im = imagecreatefromgif($path_image);
        } elseif( $image_type == IMAGETYPE_PNG ) {
           $im = imagecreatefrompng($path_image);
        }

        $stamp = imagecreatefrompng($path_wate);

        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        if($pos == 1){
        $marge_left = 15;
        $marge_top = 15;
        }elseif($pos == 2){
        $marge_left = imagesx($im)-$sx-15;
        $marge_top = 15;
        }elseif($pos == 3){
        $marge_left = 15;
        $marge_top = imagesy($im)-$sy-15;
        }elseif($pos == 4){
        $marge_left = imagesx($im)-$sx-15;
        $marge_top = imagesy($im)-$sy-15;
        }elseif($pos == 5){
        $marge_left = imagesx($im)/2-$sx/2;
        $marge_top = imagesy($im)/2-$sy/2;
        }else{
        $marge_left = imagesx($im)/2-$sx;
        $marge_top = imagesy($im)/2-$sy;
        }
        imageSaveAlpha($im, true);
        imagecopy($im, $stamp, $marge_left, $marge_top, 0, 0, imagesx($stamp),
        imagesy($stamp));

        @imagejpeg($im, $_SERVER['DOCUMENT_ROOT'].$path.$name_img, 100);

        return true;

      } catch (Exception $e) {

           return false;

      }


  }else{

      return true;

  }

}

function file_get_contents_curl($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 2);

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}


function cUrlJSONApi($data,$url,$token){
    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/vnd.api+json',
        'Authorization: Bearer '.$token,
        'Content-Length: ' . strlen($data_string))
    );
  echo  curl_exec($ch);
}

function currency_array(){
 global $db_prefix;
 $res = array();
    $sql = db_query_while("SELECT * FROM {$db_prefix}currency WHERE visible = '1' ORDER By id_position ASC");
        if (count($sql) > 0) {

               foreach($sql as $result){

                   $res[$result["code"]]["name"] = $result["name"];
                   $res[$result["code"]]["sign"] = $result["sign"];
                   $res[$result["code"]]["code"] = $result["code"];
                   $res[$result["code"]]["price"] = $result["price"];

                   If(!empty($result["main"])){
                      $res["main"]["main_currency"] = $result["code"];
                   }

               }
        }
    return $res;
}

function currency_converter($price=0,$format=true,$currency_code, $converter){
   global $currency_array,$decimals,$dec_point,$thousands_sep;

    if($converter){
       if(empty($_SESSION["currency"])){
          $currency = $currency_code;
       }else{
          $currency = $_SESSION["currency"];

          if(isset($currency_array[$currency_code]["price"]) && isset($currency_array[$currency]["price"])){
             $course = round($currency_array[$currency_code]["price"],4) / round($currency_array[$currency]["price"],4);
          }else{
             $course = 1;
          }

           $price = $price * $course;

       }
    }else{
      $currency = $currency_code;
    }

    if($format==true){
      return number_format($price,$decimals,$dec_point,$thousands_sep).$currency_array[$currency]["sign"];
    }else{
      return $price;
    }

}

function currency_deconverter($price=0,$format=true,$currency_code){
   global $currency_array,$decimals,$dec_point,$thousands_sep;

   if(empty($_SESSION["currency"])){
      $currency = $currency_code;
   }else{
      $currency = $_SESSION["currency"];

      if(isset($currency_array[$currency_code]["price"]) && isset($currency_array[$currency]["price"])){
         $course = round($currency_array[$currency]["price"],4) / round($currency_array[$currency_code]["price"],4);
      }else{
         $course = 1;
      }

       $price = $price * $course;

   }

    if($format==true){
      return number_format($price,$decimals,$dec_point,$thousands_sep).$currency_array[$currency]["sign"];
    }else{
      return $price;
    }

}

function custom_substr($string, $len){
   if(mb_strlen($string,"UTF-8") > $len){
       return mb_substr($string, 0,$len,"UTF-8")."...";
   }else{ return $string; }
}

function my_ucfirst($str){
  return mb_strtoupper(mb_substr($str, 0, 1, "UTF-8"), "UTF-8").mb_strtolower(mb_substr($str, 1, mb_strlen($str, "UTF-8"), "UTF-8"), "UTF-8");
}

function textReplace($text){
  global $settings;
  return str_replace(array("{url}", "{site_name}", "{email}", "{phone}", "{shop_name}"),array(URL, $settings["site_name"], $settings["contact_email"], $settings["contact_phone"], $_SESSION["shopName"]),$text);
}

function rotateImage($path,$rotate = 0){

  if($rotate != 0){
      $image_info = getimagesize($path);

      if($image_info[2] == 1){
         $source = imagecreatefromgif($path);
         $imagerotate = imagerotate($source, -$rotate, 0);
         imagegif($imagerotate, $path);
      }elseif($image_info[2] == 2){
         $source = imagecreatefromjpeg($path);
         $imagerotate = imagerotate($source, -$rotate, 0);
         imagejpeg($imagerotate, $path, 95);
      }elseif($image_info[2] == 3){
         $source = imagecreatefrompng($path);
         imagealphablending($source, true);
         imagesavealpha($source, true);
         $imagerotate = imagerotate($source, -$rotate, 0);
         imagepng($imagerotate, $path);
      }

      imagedestroy($source);
      imagedestroy($imagerotate);
  }

}

function makrosDefault(){
  global $settings,$main_image_logo;
  return array("{DOMEN}"=>$_SERVER["SERVER_NAME"], "{URL}"=>URL, "{LOGO}"=>URL.$main_image_logo.$settings["logo-image"], "{CONTACT_EMAIL}"=>$settings["contact_email"], "{CONTACT_PHONE}"=>$settings["contact_phone"], "{CONTACT_ADDRESS}"=>$settings["contact_address"], "{SITE_NAME}" => $settings["site_name"], "{ADMIN_LINK}" => URL."admin", "{PROFILE_LINK}" => URL."profile", "{TITLE}"=>$settings["title"]);
}

function email_notification($array,$code, $subject = "", $systems = false){
  global $settings,$main_image_logo;

  $default = makrosDefault();

  foreach ($default as $key => $value) {
     $data[$key] = $value;
  }

  foreach ($array as $key => $value) {
     $data[$key] = $value;
  }

  if($systems == false){
      if($code){
        $result = db_query("SELECT * FROM uni_email_message WHERE code = '$code'");
        if (count($result) > 0)
         {
            $text = urldecode($result["text"]);
            if(!$subject) $subject = $result["subject"];
            if(count($data) != 0){
                foreach($data AS $name => $val){
                    $text = str_replace($name, $val, $text);
                    $subject = str_replace($name, $val, $subject);
                }
            }
          return mailer($data["{EMAIL_TO}"],$subject,$text);
         }
      }
  }else{

    if($code){

      $text = file_get_contents($_SERVER["DOCUMENT_ROOT"]."admin/files/mail/".$code);

        if(count($data) != 0 && $text){
            foreach($data AS $name => $val){
                $text = str_replace($name, $val, $text);
            }
        }
      return mailer($data["{EMAIL_TO}"],$subject,$text);
    }else{
       return false;
    }

  }


}

function replace($array1 = array(),$array2 = array(),$text = ""){
  return str_replace($array1,$array2,$text);
}

function geolocation($ip){
    global $SxGeo,$lang;
     $Geo = $SxGeo->getCityFull($ip);
       if($Geo["city"]["name_ru"] || $Geo["region"]["name_ru"]){
          $result[$Geo["city"]["name_ru"]] = $Geo["city"]["name_ru"];
          $result[$Geo["region"]["name_ru"]] = $Geo["region"]["name_ru"];
          if(implode(",",$result) != ','){
              return implode(",",$result);
          }else{
              return $lang["message_display_sys_title13"];
          }
       }else{ return $lang["message_display_sys_title13"]; }
}

function ending($number, $one, $two, $five)
{
    $number = $number % 100;

    if ( ($number > 4 && $number < 21) || $number == 0 )
    {
        $ending = $five;
    }
    else
    {
        $last_digit = substr($number, -1);

        if ( $last_digit > 1 && $last_digit < 5 )
            $ending = $two;
        elseif ( $last_digit == 1 )
            $ending = $one;
        else
            $ending = $five;
    }

    return $ending;
}

function detectRobots($user_agent){
  $bots = array(
    'rambler','googlebot','aport','yahoo','msnbot','turtle','mail.ru','omsktele',
    'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com',
    'megadownload.net','askpeter.info','igde.ru','ask.com','qwartabot','yanga.co.uk',
    'scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
    'dataparksearch','google-sitemaps','appEngine-google','feedfetcher-google',
    'liveinternet.ru','xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru',
    'googlealert.com','seo-rus.com','yaDirectBot','yandeG','yandex',
    'yandexSomething','Copyscape.com','AdsBot-Google','domaintools.com',
    'Nigma.ru','bing.com','dotnetdotcom','bots','robot','AhrefsBot'
  );
  foreach($bots as $bot){
    if(stripos($user_agent, $bot) !== false){
      return true;
    }
  }
 return false;
}

function getFile($dir){
  if(file_exists($dir)){

   $fp = @fopen($dir, 'r' );
    if ($fp) {
        $size = @filesize($dir);
        $content = @fread($fp, $size);
        @fclose ($fp);
    }

    return trim($content);
  }
}

function deleteFolder( $path ) {

 if ( file_exists( $path ) AND is_dir( $path ) ) {

    $dir = opendir($path);
    while ( false !== ( $element = readdir( $dir ) ) ) {

      if ( $element != '.' AND $element != '..' )  {
        $tmp = $path . '/' . $element;
        chmod( $tmp, 0777 );

        if ( is_dir( $tmp ) ) {

          deleteFolder( $tmp );

        } else {
          unlink( $tmp );
       }
     }
   }

    closedir($dir);

   if ( file_exists( $path ) ) {
     rmdir( $path );
   }
 }

}

function getContent($URI){
  global $array_data,$Cashed;

   // 1

    $get = routeServices($URI);

    if($get !== false){
       $array_data["services"] = $get;
       $_SESSION["route_name"] = "services";
    }else{
       $array_data["services"] = array();
    }

    if(count($array_data["services"]) > 0){
        return "route/services.php";
    }else{

      // 2

       $get = routeCity($URI);

        if($get !== false){
           $array_data["city"] = $get;
        }else{
           $array_data["city"] = array();
        }

       if(count($array_data["city"]) > 0){
          $_SESSION["route_name"] = "city";
          return "route/board.php";
       }else{

          // 3

           $get = routeCategory($URI);

            if($get !== false){
               $array_data["categories_board"] = $get;
            }else{
               $array_data["categories_board"] = array();
            }

           if(count($array_data["categories_board"]) > 0){
              $_SESSION["route_name"] = "category";
              return "route/board.php";
           }else{

               // 4

               $explode = explode("/", trim($URI, "/"));

               if(count($explode) == 1){

                $get = routeFilters($URI);

                  if($get !== false){
                     $array_data["filters_alias"] = $get;
                  }else{
                     $array_data["filters_alias"] = array();
                  }

                  if(count($array_data["filters_alias"]) > 0){
                    $_SESSION["route_name"] = "filters_alias";
                    return "route/board.php";
                  }else{

                  }

               }elseif(count($explode) == 2){

                $array_data["city"] = routeCity($explode[0]);
                $array_data["categories_board"] = routeCategory($explode[1]);

                  if($array_data["city"] !== false && $array_data["categories_board"] !== false){
                     $_SESSION["route_name"] = "city_category";
                     return "route/board.php";
                  }

               }elseif(count($explode) == 3){

                $array_data["city"] = routeCity($explode[0]);
                $array_data["categories_board"] = routeCategory($explode[1]);
                $array_data["ads"] = routeAds($explode);

                  if($array_data["ads"] !== false){
                     $_SESSION["route_name"] = "ads";
                     return "route/board-view.php";
                  }else{

                     $array_data["city"] = routeCity($explode[0]);
                     unset($explode[0]);
                     $array_data["categories_board"] = routeCategory(implode("/",$explode));

                      if($array_data["city"] !== false && $array_data["categories_board"] !== false){
                         $_SESSION["route_name"] = "city_category";
                         return "route/board.php";
                      }

                  }

               }else{

                   $array_data["city"] = routeCity($explode[0]);
                   unset($explode[0]);
                   $array_data["categories_board"] = routeCategory(implode("/",$explode));

                    if($array_data["city"] !== false && $array_data["categories_board"] !== false){
                       $_SESSION["route_name"] = "city_category";
                       return "route/board.php";
                    }

               }


           }

       }

    }


}


function routeCity($URI = ""){
  global $Cashed,$mysqli;

    $key  = "select *, (select country_status from uni_country where country_id = uni_city.country_id) as status, (select value from uni_multilanguage where id_content = uni_city.city_id and field='name' and table_name='uni_city' and lang='".lang()."' limit 1) as lang_city_name from uni_city where city_alias='".clear(trim($URI, "/"))."' HAVING status != 0;";
    $key  .= "select *, (select country_status from uni_country where country_id = uni_region.country_id) as status, (select value from uni_multilanguage where id_content = uni_region.region_id and field='name' and table_name='uni_region' and lang='".lang()."' limit 1) as lang_region_name from uni_region where region_alias='".clear(trim($URI, "/"))."' HAVING status != 0;";
    $key  .= "select *, (select value from uni_multilanguage where id_content = uni_country.country_id and field='name' and table_name='uni_country' and lang='".lang()."' limit 1) as lang_country_name from uni_country where country_alias='".clear(trim($URI, "/"))."' and country_status=1;";

    $get = $Cashed->get($key,"city");

    if($get !== false){
       return $get;
    }else{

        if ($mysqli->multi_query($key)) {
            do {

                if ($result = $mysqli->store_result()) {
                    while ($row = $result->fetch_assoc()) {
                        if($row['lang_city_name']) $row["name"] = urldecode($row['lang_city_name']);
                        if($row['lang_region_name']) $row["name"] = urldecode($row['lang_region_name']);
                        if($row['lang_country_name']) $row["name"] = urldecode($row['lang_country_name']);
                        $data = $row;
                    }
                    $result->free();
                }
            } while ($mysqli->next_result());
        }



       $Cashed->set($data,$key,"city");
    }

   if(count($data) > 0){
      return $data;
   }else{ return false; }
}


function routeCategory($URI = ""){
  global $Cashed;

   $key = "select * from uni_category_board where category_board_category_chain='".clear(trim($URI, "/"))."' or category_board_alias='".clear(trim($URI, "/"))."' AND category_board_visible = 1 ORDER BY category_board_id_position asc";
   $get = $Cashed->get($key,"categories_board");

    if($get !== false){
       return $get;
    }else{
       $data = db_query($key);

       if(count($data) > 0){

        $multilanguage_tables = multilanguage_tables(array("id_content" => $data["category_board_id"], "table_name" => "uni_category_board"));

        $data["category_board_name"] = !empty($multilanguage_tables['lang_name']) ? urldecode($multilanguage_tables['lang_name']) : $data['category_board_name'];
        $data["category_board_title"] = !empty($multilanguage_tables['lang_title']) ? urldecode($multilanguage_tables['lang_title']) : $data['category_board_title'];
        $data["category_board_description"] = !empty($multilanguage_tables['lang_description']) ? urldecode($multilanguage_tables['lang_description']) : $data['category_board_description'];
        $data["category_board_text"] = !empty($multilanguage_tables['lang_text']) ? urldecode($multilanguage_tables['lang_text']) : $data['category_board_text'];

        }

        $Cashed->set($data,$key,"categories_board");
    }

   if(count($data) > 0){
      return $data;
   }else{ return false; }
}


function routeServices($URI = ""){
  global $Cashed;
    $key = "select * from uni_pages where alias='".clear(trim($URI, "/"))."' AND visible = 1 ORDER BY id DESC";
    $get = $Cashed->get($key,"services");

    if($get !== false){
       return $get;
    }else{
       $data = db_query($key);

       if(count($data) > 0){

        $multilanguage_tables = multilanguage_tables(array("id_content" => $data["id"], "table_name" => "uni_pages"));

        $data["name"] = !empty($multilanguage_tables['lang_name']) ? urldecode($multilanguage_tables['lang_name']) : $data['name'];
        $data["title"] = !empty($multilanguage_tables['lang_title']) ? urldecode($multilanguage_tables['lang_title']) : $data['title'];
        $data["seo_text"] = !empty($multilanguage_tables['lang_description']) ? urldecode($multilanguage_tables['lang_description']) : $data['seo_text'];
        $data["text"] = !empty($multilanguage_tables['lang_text']) ? urldecode($multilanguage_tables['lang_text']) : $data['text'];

        }

        $Cashed->set($data,$key,"services");
    }

    if($data["id"]){
      return $data;
    }else{ return false; }
}

function routeFilters($URI = ""){
  global $Cashed;
    $key = "select * from uni_filters_alias where filters_alias_link='".clear(trim($URI, "/"))."'";
    $get = $Cashed->get($key,"filters_alias");

    if($get !== false){
       return $get;
    }else{
       $data = db_query($key);
       $Cashed->set($data,$key,"filters_alias");
    }

    if(count($data) > 0){
      return $data;
    }else{ return false; }
}

function routeAds($URI = ""){
  global $Cashed,$array_data,$Ads;

  if($URI[2]){
     $explode = explode("-",$URI[2]);
     $id = end($explode);
     $array_pop = array_pop($explode);
     $alias = clear(implode("-",$explode));
  }else{
     $id = 0;
     $alias = '';
  }

    $key = " where uni_ads.ads_id=".intval($id)." AND uni_ads.ads_alias='$alias' AND uni_ads.ads_id_cat='".$array_data["categories_board"]["category_board_id"]."' AND uni_clients.clients_id = uni_ads.ads_id_user";

    $data = $Ads->get($key);


    if(count($data) > 0){
      return $data;
    }else{ return false; }
}

function routeCategoryBlog($alias = ""){
  global $Cashed;

   $key = "select * from uni_category_blog where category_chain='".clear($alias)."' or alias='".clear($alias)."' AND visible = 1 ORDER BY id_position asc";
   $get = $Cashed->get($key,"categories_blog");

    if($get !== false){
       return $get;
    }else{
       $data = db_query($key);

       if(count($data) > 0){
       $multilanguage_tables = multilanguage_tables(array("id_content" => $data["id"], "table_name" => "uni_category_blog"));

       $data["name"] = !empty($multilanguage_tables['lang_name']) ? urldecode($multilanguage_tables['lang_name']) : $data['name'];
       $data["title"] = !empty($multilanguage_tables['lang_title']) ? urldecode($multilanguage_tables['lang_title']) : $data['title'];
       }

       $Cashed->set($data,$key,"categories_blog");
    }

   if($data["id"]){
      return $data;
   }else{ return false; }
}

function routeArticle($URI = ""){
  global $Cashed,$array_data;

  if($URI[2]){
     $explode = explode("-",$URI[2]);
     $id = end($explode);
     $array_pop = array_pop($explode);
     $alias = clear(implode("-",$explode));
  }else{
     $id = 0;
     $alias = '';
  }

    $key = "select *, (select name from uni_category_blog where id = uni_articles.id_cat) as name_category, (select alias from uni_category_blog where id = uni_articles.id_cat) as alias_category from uni_articles where id=".intval($id)." AND alias='$alias' AND id_cat='".$array_data["categories_blog"]["id"]."'";
    $get = $Cashed->get($key,"article");

    if($get !== false){
       return $get;
    }else{
       $data = db_query($key);

       if(count($data) > 0){
        $multilanguage_tables = multilanguage_tables(array("id_content" => $data["id"], "table_name" => "uni_articles"));

        $data["title"] = !empty($multilanguage_tables['lang_title']) ? urldecode($multilanguage_tables['lang_title']) : $data['title'];
        $data["description"] = !empty($multilanguage_tables['lang_description']) ? urldecode($multilanguage_tables['lang_description']) : $data['description'];
        $data["text"] = !empty($multilanguage_tables['lang_text']) ? urldecode($multilanguage_tables['lang_text']) : $data['text'];
       }

       if($data["id"]) $Cashed->set($data,$key,"article");
    }

    if($data["id"]){
      return $data;
    }else{ return false; }
}

function notifications($action, $param = array()){
   global $settings,$Admin,$Ads,$main_medium_image_ads,$no_image,$lang;

   if($action == "ads"){
      if($settings["notification_method_new_ads"]){
       $notification_method_new_ads = explode(",",$settings["notification_method_new_ads"]);
           if(in_array("email", $notification_method_new_ads)){

            if($settings["email_alert"]){
             $data = array("{ADS_TITLE}"=>$param["title"],
                           "{ADS_LINK}"=>$param["link"],
                           "{ADS_IMAGE}"=>'<img src="'.Exists($main_medium_image_ads,$param["image"],$no_image).'" width="200px" />',
                           "{EMAIL_TO}"=>$settings["email_alert"]
                           );

              $status = email_notification($data, "new-ads.html", $lang["function_notifications_title1"], true);
              if($status !== true){
                  $Admin->addLogs("Error send mail - ".$status);
              }
            }else{
               $Admin->addLogs("Error send mail - No email alert");
            }

           }
           if(in_array("sms", $notification_method_new_ads)){
              $status = sms($settings["phone_alert"],$lang["function_notifications_title1"].' '.$param["link"]);
              if($status !== true){
                  $Admin->addLogs("Error send sms - ".$status);
              }
           }
           if(in_array("telegram", $notification_method_new_ads)){
              $status = telegram($lang["function_notifications_title1"].' '.$param["link"]);
              if($status !== true){
                  $Admin->addLogs("Error send telegram - ".$status);
              }
           }
      }
   }elseif($action == "buy_order"){

      if($settings["notification_method_new_buy"]){
       $notification_method_new_buy = explode(",",$settings["notification_method_new_buy"]);
           if(in_array("email", $notification_method_new_buy)){

            if($settings["email_alert"]){
             $data = array("{ORDER_TITLE}"=>$param["title"],
                           "{ORDER_LINK}"=>$param["link"],
                           "{ORDER_PRICE}"=>$param["price"],
                           "{EMAIL_TO}"=>$settings["email_alert"]
                           );

              $status = email_notification($data, "new-order.html", $lang["function_notifications_title2"], true);
              if($status !== true){
                  $Admin->addLogs("Error send mail - ".$status);
              }
            }else{
               $Admin->addLogs("Error send mail - No email alert");
            }

           }
           if(in_array("sms", $notification_method_new_buy)){
              $status = sms($settings["phone_alert"],$lang["function_notifications_title2"].' '.$param["title"].', '.$param["price"].', '.$lang["function_notifications_title7"].' '.$settings["site_name"]);
              if($status !== true){
                  $Admin->addLogs("Error send sms - ".$status);
              }
           }
           if(in_array("telegram", $notification_method_new_buy)){
              $status = telegram($lang["function_notifications_title2"].' '.$param["title"].', '.$param["price"].', '.$lang["function_notifications_title7"].' '.$settings["site_name"]);
              if($status !== true){
                  $Admin->addLogs("Error send telegram - ".$status);
              }
           }
      }

   }elseif($action == "user"){

      if($settings["notification_method_new_user"]){
       $notification_method_new_user = explode(",",$settings["notification_method_new_user"]);
           if(in_array("email", $notification_method_new_user)){

            if($settings["email_alert"]){
             $data = array("{USER_NAME}"=>$param["user_name"],
                           "{USER_EMAIL}"=>$param["user_email"],
                           "{EMAIL_TO}"=>$settings["email_alert"]
                           );

              $status = email_notification($data, "new-user.html", $lang["function_notifications_title3"], true);
              if($status !== true){
                  $Admin->addLogs("Error send mail - ".$status);
              }
            }else{
               $Admin->addLogs("Error send mail - No email alert");
            }

           }
           if(in_array("sms", $notification_method_new_user)){
              $status = sms($settings["phone_alert"],$lang["function_notifications_title3"].' '.$param["user_name"].', '.$param["user_email"].' '.$lang["function_notifications_title7"].' '.$settings["site_name"]);
              if($status !== true){
                  $Admin->addLogs("Error send sms - ".$status);
              }
           }
           if(in_array("telegram", $notification_method_new_user)){
              $status = telegram($lang["function_notifications_title3"].' - '.$param["user_name"].', '.$param["user_email"].' '.$lang["function_notifications_title7"].' '.$settings["site_name"]);
              if($status !== true){
                  $Admin->addLogs("Error send telegram - ".$status);
              }
           }
      }

   }elseif($action == "shop"){

      if($settings["notification_method_new_shop"]){
       $notification_method_new_shop = explode(",",$settings["notification_method_new_shop"]);
           if(in_array("email", $notification_method_new_shop)){

            if($settings["email_alert"]){
             $data = array("{USER_NAME}"=>$param["user_name"],
                           "{USER_EMAIL}"=>$param["user_email"],
                           "{SHOP_NAME}"=>$param["title"],
                           "{SHOP_LINK}"=>$param["link"],
                           "{EMAIL_TO}"=>$settings["email_alert"]
                           );

              $status = email_notification($data, "new-shop.html", $lang["function_notifications_title4"], true);
              if($status !== true){
                  $Admin->addLogs("Error send mail - ".$status);
              }
            }else{
               $Admin->addLogs("Error send mail - No email alert");
            }

           }
           if(in_array("sms", $notification_method_new_shop)){
              $status = sms($settings["phone_alert"],$lang["function_notifications_title4"].' - '.$param["title"].', '.$lang["function_notifications_title7"].' '.$settings["site_name"]);
              if($status !== true){
                  $Admin->addLogs("Error send sms - ".$status);
              }
           }
           if(in_array("telegram", $notification_method_new_shop)){
              $status = telegram($lang["function_notifications_title4"].' - '.$param["title"].', '.$lang["function_notifications_title7"].' '.$settings["site_name"]);
              if($status !== true){
                  $Admin->addLogs("Error send telegram - ".$status);
              }
           }
      }

   }elseif($action == "feedback"){

      if($settings["notification_method_feedback"]){
       $notification_method_feedback = explode(",",$settings["notification_method_feedback"]);
           if(in_array("email", $notification_method_feedback)){

            if($settings["email_alert"]){
             $data = array("{USER_NAME}"=>$param["user_name"],
                           "{USER_EMAIL}"=>$param["user_email"],
                           "{TEXT}"=>$param["text"],
                           "{EMAIL_TO}"=>$settings["email_alert"]
                           );

              $status = email_notification($data, "feedback.html", $lang["function_notifications_title5"]." - ".$_SERVER["SERVER_NAME"], true);
              if($status !== true){
                  $Admin->addLogs("Error send mail - ".$status);
              }
            }else{
               $Admin->addLogs("Error send mail - No email alert");
            }

           }
           if(in_array("telegram", $notification_method_feedback)){
              $status = telegram($lang["function_notifications_title6"]." - ".$param["user_name"]." (".$param["user_email"]."), ".$param["text"]);
              if($status !== true){
                  $Admin->addLogs("Error send telegram - ".$status);
              }
           }
      }

   }

}

function sms($phone_to,$text=""){
   global $settings;
   if($settings["api_id_sms"]){
     if($phone_to){
      $body=file_get_contents_curl("http://sms.ru/sms/send?api_id=".$settings["api_id_sms"]."&to=".$phone_to."&text=".urlencode($text));
      return true;
     }else{
       return "No phone";
     }
   }else{
      return "No api_id";
   }
}

function telegram($text){
global $settings;

  $token = $settings["api_id_telegram"];
  $chat_id = $settings["chat_id_telegram"];

  if($token){
    if($chat_id){
      $sendToTelegram = file_get_contents_curl("https://api.telegram.org/bot".$token."/sendMessage?chat_id=".$chat_id."&parse_mode=html&text=".$text);
      if ($sendToTelegram) {
        return true;
      } else {
        return $sendToTelegram;
      }
    }else{
      return "No chat_id";
    }
  }else{
     return "No token";
  }


}

function getParam($URI){
   if($URI){
      return explode("/",trim($URI,"/"));
   }else{
      return array();
   }
}

function clearScript($text){
    $cut = array(
      "'<script[^>]*?>.*?</script>'si",
      "'<noscript[^>]*?>.*?</noscript>'si",
      "'<style[^>]*?>.*?</style>'si",
      "'<[\/\!]*?[^<>]*?>'si",
    );
    $to = array(" "," "," "," ");
    return preg_replace($cut, $to, $text);
}

function paymentParams($code = ""){
   if($code){
       $payment = db_query("select * from uni_payments where visible=1 AND code='$code'");
       if(count($payment) > 0){

          if($payment["param"] && $payment["param"] != "[]"){
            $param = json_decode($payment["param"], true);
          }else{
            $param = array();
          }

          return $param;

       }
   }
}

function uloginWidget($size = "small"){
  $id = "uLogin".mt_rand(1000,9000);
  return '
     <div id="'.$id.'" data-ulogin="display='.$size.';theme=flat;fields=first_name,last_name,photo_big,email,phone;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=;redirect_uri='.URL.'ulogin;mobilebuttons=0;"></div>
  ';
}

function outLanguage($tpl = ""){
  global $settings,$image_language;
    if($tpl){
     $sql = db_query_while("select * from uni_languages where status=1 order by id_position asc");
     if(count($sql) > 0){
      foreach ($sql as $key => $value) {

            if($_SESSION["langSite"]){
                if($value["iso"] == $_SESSION["langSite"]){
                    $active = 'active';
                }else{ $active = ''; }
            }

        $return .=  replace(array("{CODE}", "{ISO}", "{NAME}", "{ACTIVE}","{ICON}"),array($value["code"], $value["iso"], $value["name"], $active, URL.$image_language.$value["image"]),$tpl);
      }
     }
   return $return;
  }
}

function backup_tables($host, $user, $pass, $dbname, $tables = '*') {
    $link = mysqli_connect($host,$user,$pass, $dbname);

    if (mysqli_connect_errno())
    {
        exit;
    }

    mysqli_query($link, "SET NAMES 'utf8'");

    if($tables == '*')
    {
        $tables = array();
        $result = mysqli_query($link, 'SHOW TABLES');
        while($row = mysqli_fetch_row($result))
        {
            $tables[] = $row[0];
        }
    }
    else
    {
        $tables = is_array($tables) ? $tables : explode(',',$tables);
    }

    $return = '';

    foreach($tables as $table)
    {
        $result = mysqli_query($link, 'SELECT * FROM '.$table);
        $num_fields = mysqli_num_fields($result);
        $num_rows = mysqli_num_rows($result);

        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
        $counter = 1;

        for ($i = 0; $i < $num_fields; $i++)
        {
            while($row = mysqli_fetch_row($result))
            {
                if($counter == 1){
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                } else{
                    $return.= '(';
                }

                for($j=0; $j<$num_fields; $j++)
                {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                    if ($j<($num_fields-1)) { $return.= ','; }
                }

                if($num_rows == $counter){
                    $return.= ");\n";
                } else{
                    $return.= "),\n";
                }
                ++$counter;
            }
        }
        $return.="\n\n\n";
    }

    $fileName = $_SERVER["DOCUMENT_ROOT"].'files/backup_db/backup-'.date("d-m-Y-h:i",time()).'-'.(md5(time().implode(',',$tables))).'.sql';
    $handle = fopen($fileName,'w+');
    fwrite($handle,$return);
    if(fclose($handle)){
        exit;
    }
}

function multilanguage_tables($array = array()){
  global $Cashed;

  $data = array();

  if(count($array) > 0){

     $key = "select * from uni_multilanguage where id_content = ".$array["id_content"]." and table_name='".$array["table_name"]."' and lang='".lang()."'";
     $get = $Cashed->get($key,"multilanguage_tables");

      if($get !== false){
         return $get;
      }else{
         $data = db_query_while($key);

         if(count($data) > 0){
           foreach ($data as $key => $value) {

              if($value["field"] == "name") $data["lang_name"] = urldecode($value['value']);
              if($value["field"] == "title") $data["lang_title"] = urldecode($value['value']);
              if($value["field"] == "description") $data["lang_description"] = urldecode($value['value']);
              if($value["field"] == "text") $data["lang_text"] = urldecode($value['value']);

           }
         }

         $Cashed->set($data,$key,"multilanguage_tables");
      }


    return $data;

  }

}

function breadcrumb_count($content, $index = 2){

    preg_match_all ( '/<li.*?>(.*?)<\/li>/i' , $content , $matches);
    if(count($matches[0]) > 0){
      foreach ($matches[0] as $key => $value) {
        $return .= str_replace(array("{INDEX}"),array($key + $index),$value);
      }
    }

   if($return) return $return; else return $content;

}

?>