<?php
/**
 * UniSite CMS
 *
 * @copyright 	2016 Artur Zhur
 * @link 		http://unisitecms.ru
 * @author 		Artur Zhur
 *
 */

class Ads{
   function get($query = ""){
        
        $sql_ads = db_query("SELECT *,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(4,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as promptly,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(1,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as top,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(2,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as vip  FROM `uni_ads` 
        INNER JOIN `uni_city` ON `uni_city`.city_id = `uni_ads`.ads_city_id
        INNER JOIN `uni_region` ON `uni_region`.region_id = `uni_ads`.ads_region_id
        INNER JOIN `uni_country` ON `uni_country`.country_id = `uni_ads`.ads_country_id
        INNER JOIN `uni_category_board` ON `uni_category_board`.category_board_id = `uni_ads`.ads_id_cat
        INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads`.ads_id_user $query");

        return $sql_ads;

   }
   function getQuery($query = "", $sort = "", $nav = false){
    global $settings,$Cashed;

        $key_cashed = "SELECT count(uni_ads.ads_id) as result_count FROM `uni_ads` 
        INNER JOIN `uni_city` ON `uni_city`.city_id = `uni_ads`.ads_city_id
        INNER JOIN `uni_region` ON `uni_region`.region_id = `uni_ads`.ads_region_id
        INNER JOIN `uni_country` ON `uni_country`.country_id = `uni_ads`.ads_country_id
        INNER JOIN `uni_category_board` ON `uni_category_board`.category_board_id = `uni_ads`.ads_id_cat
        INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads`.ads_id_user WHERE ".AD_QUERY." $query";

        $get_count = $Cashed->get($key_cashed,"ads");

        if($get_count !== false){
            $count = $get_count;
        }else{
            $count = db_query($key_cashed);
            $Cashed->set($count,$key_cashed,"ads");
        }


        if($nav == false){

            $key_cashed = "SELECT *,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(4,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as promptly,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(1,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as top,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(2,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as vip FROM `uni_ads` 
            INNER JOIN `uni_city` ON `uni_city`.city_id = `uni_ads`.ads_city_id
            INNER JOIN `uni_region` ON `uni_region`.region_id = `uni_ads`.ads_region_id
            INNER JOIN `uni_country` ON `uni_country`.country_id = `uni_ads`.ads_country_id
            INNER JOIN `uni_category_board` ON `uni_category_board`.category_board_id = `uni_ads`.ads_id_cat
            INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads`.ads_id_user WHERE ".AD_QUERY." $query $sort";

            $get_cashed = $Cashed->get($key_cashed,"ads");

            if($get_cashed !== false){
                $results = $get_cashed;
            }else{
                $results = db_query_while($key_cashed);
                $Cashed->set($results,$key_cashed,"ads");
            }

        }else{

            $key_cashed = "SELECT *,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(4,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as promptly,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(1,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as top,(select count(*) from uni_services_order,uni_orders where uni_services_order.id_ads = uni_ads.ads_id AND uni_services_order.vuid IN(2,3) AND uni_services_order.time_validity > NOW() AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status = 'ACCEPTED') as vip  FROM `uni_ads` 
            INNER JOIN `uni_city` ON `uni_city`.city_id = `uni_ads`.ads_city_id
            INNER JOIN `uni_region` ON `uni_region`.region_id = `uni_ads`.ads_region_id
            INNER JOIN `uni_country` ON `uni_country`.country_id = `uni_ads`.ads_country_id
            INNER JOIN `uni_category_board` ON `uni_category_board`.category_board_id = `uni_ads`.ads_id_cat
            INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads`.ads_id_user WHERE ".AD_QUERY." $query $sort ".navigation_offset($count["result_count"],$settings["catalog_out_content"]);

            $get_cashed = $Cashed->get($key_cashed,"ads");

            if($get_cashed !== false){
                $results = $get_cashed;
            }else{
                $results = db_query_while($key_cashed);
                $Cashed->set($results,$key_cashed,"ads");
            }

        }

        return array("count_ads" => intval($count["result_count"]), "ads" => $results);
   }

  function getVariants($ads_id = 0){
    global $Cashed;

      $key = "SELECT * FROM uni_filters_variants WHERE ads_id=$ads_id and value !=''";
      $data = $Cashed->get($key,"ads_variants");

      if($data !== false){
          return $data;
      }else{
          $sql = db_query_while($key);
          if (count($sql)>0) { 
              $data = array();                            
                foreach($sql AS $result){

                  $multilanguage_tables = multilanguage_tables(array("id_content" => $result["id_filter"], "table_name" => "uni_filters_variants"));
                  $result["name"] = !empty($multilanguage_tables['lang_name']) ? urldecode($multilanguage_tables['lang_name']) : $result["name"];

                    $data["results"][$result['id_filter']][$result["value"]]  =  $result;
                    $data["value"][$result['id_filter']]  =  $result;
                    
                    $sql = db_query("select *, (select value from uni_multilanguage where id_content = uni_filters.id and field='name' and table_name='uni_filters' and lang='".lang()."') as lang_name from uni_filters where id={$result['id_filter']}");
                    if(count($sql) == 0){
                       $sql = db_query("select *,(select name from uni_filters where id = uni_filters_items.id_filter) as name, (select value from uni_multilanguage where id_content = uni_filters_items.id_filter and field='name' and table_name='uni_filters' and lang='".lang()."') as lang_name from uni_filters_items where id={$result['id_filter']}");
                    }

                    $sql['name'] = !empty($sql['lang_name']) ? $sql['lang_name'] : $sql['name'];

                    $data["ad"][$sql['name']][$result["value"]]  =  $result;

                }  
          }            
          $Cashed->set($data,$key,"ads_variants");
          return $data;
      }
          
  }

  function adVariants($id = 0){
    global $Ads;
     $variants = $this->getVariants($id);
     
     if($variants["ad"]){

        foreach ($variants["ad"] as $name_filter => $array) {

        $items = array();
          foreach ($array as $key => $value) {
             $items[] = $value["value"];
          }

          if(isset($items)) $html .= '<div><span>'.$name_filter.'</span>'.implode(", ",$items).'</div>';
        }

     }

     return $html;
  }

   function getImages($json = ""){
       if($json && $json != "[]"){
          return json_decode(urldecode($json), true);
       }else{
          return array();
       }
   }

   function outServices($id = 0){
       if($id){
           $sql = db_query_while("select *, (select name from uni_services_ads where uid = uni_services_order.vuid) as services_name from uni_services_order, uni_orders where uni_services_order.id_ads={$id} AND uni_orders.id_order = uni_services_order.id_order AND uni_orders.status='ACCEPTED' AND uni_services_order.time_validity > NOW()");
           if(count($sql) > 0){
              foreach ($sql as $key => $value) {
                 $out .= '<li title="'.$value["services_name"].'" ><img src="'.URL.'files/images/services'.$value["vuid"].'.png" width="18px" /></li>';
              }
           }
          return $out;
       }
   }

   function countdownServices(){
       global $settings;
       if(strtotime($settings["countdown"]) <= time()){
          $countdown = date("Y/m/d h:i:s", time() + 80000);
          db_insert_update("UPDATE uni_settings SET value='$countdown' WHERE name='countdown'");
          return $countdown;
       }else{
         return $settings["countdown"];
       }
   }

   function getAdServices($id = 0){
       if($id){
           $sql = db_query_while("select * from uni_services_order where id_ads={$id}");
           if(count($sql) > 0){
              foreach ($sql as $key => $value) {
                 $out[$value["vuid"]] = $value["vuid"];
              }
           }
          return $out;
       }
   }

   function outMarkers($id = 0){
       if($id){
           $sql = db_query("select *, (select status from uni_orders where id_order = uni_markers.id_order) as status from uni_markers where id_ads={$id} HAVING status='ACCEPTED'");
           if(count($sql) > 0){
            $markers = json_decode(urldecode($sql["markers"]), true);
              if(count($markers) > 0){
                foreach ($markers as $key => $value) {
                   $out .= '<li>'.$value.'</li>';
                }
              }
           }
          return $out;
       }
   }

   function outSorting($uri = ""){
    global $languages_content;

    if(!empty($uri)){
       if($uri == "default"){
          $title = $languages_content["class-ads-title-1"];
       }elseif($uri == "price-asc"){
          $title = $languages_content["class-ads-title-2"];
       }elseif($uri == "price-desc"){
          $title = $languages_content["class-ads-title-3"];
       }elseif($uri == "news"){
          $title = $languages_content["class-ads-title-4"];
       }else{
          $title = $languages_content["class-ads-title-1"];
       }
    }else{
       $title = $languages_content["class-ads-title-1"];
    }

        return '
            <span class="board-link-sort" >'.$title.'

              <div class="dropdown-sort">
                <div class="dropdown-menu-sort">
                    <a href="#" data-name="default" >'.$languages_content["class-ads-title-1"].'</a>
                    <a href="#" data-name="price-asc" >'.$languages_content["class-ads-title-2"].'</a>
                    <a href="#" data-name="price-desc" >'.$languages_content["class-ads-title-3"].'</a>
                    <a href="#" data-name="news" >'.$languages_content["class-ads-title-4"].'</a>                  
                </div>
              </div>

            </span>        
        ';
        
   }

   function delete($array = array()){
    global $main_big_image_ads,$main_medium_image_ads,$main_small_image_ads,$gallery_big_image_ads,$gallery_medium_image_ads,$gallery_small_image_ads,$a1;
      
      if($array["id"]){

      if($array["id_user"]){
        $query = " AND ads_id_user=".$array["id_user"];
      } 

       $sql = db_query("SELECT ads_images FROM uni_ads WHERE ads_id={$array["id"]} $query");
         
         if(count($sql) > 0){

              $images = $this->getImages($sql["ads_images"]);

              if(count($images) > 0){
                 foreach ($images as $key => $value) {

                    @unlink($_SERVER['DOCUMENT_ROOT'].$main_big_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$main_medium_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$main_small_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_big_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_medium_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_small_image_ads.$value);
                   
                 }
              }

            db_insert_update("DELETE FROM uni_ads WHERE ads_id={$array["id"]}");
            db_insert_update("DELETE FROM uni_filters_variants WHERE ads_id={$array["id"]}");
            db_insert_update("DELETE FROM uni_ads_complain WHERE id_ads={$array["id"]}");
            db_insert_update("DELETE FROM uni_services_order WHERE id_ads={$array["id"]}");
            db_insert_update("DELETE FROM uni_markers WHERE id_ads={$array["id"]}");
            db_insert_update("DELETE FROM uni_favorites WHERE favorites_id_ads={$array["id"]}");

         }

     }else{

       $sql = db_query_while("SELECT ads_images,ads_id FROM uni_ads WHERE ads_id_user={$array["id_user"]}");
         
         if(count($sql) > 0){

          foreach ($sql as $ad_key => $ad_value) {

              $images = $this->getImages($ad_value["ads_images"]);

              if(count($images) > 0){
                 foreach ($images as $key => $value) {

                    @unlink($_SERVER['DOCUMENT_ROOT'].$main_big_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$main_medium_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$main_small_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_big_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_medium_image_ads.$value);
                    @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_small_image_ads.$value);
                   
                 }
              }

            db_insert_update("DELETE FROM uni_ads WHERE ads_id={$ad_value["ads_id"]}");
            db_insert_update("DELETE FROM uni_filters_variants WHERE ads_id={$ad_value["ads_id"]}");
            db_insert_update("DELETE FROM uni_ads_complain WHERE id_ads={$ad_value["ads_id"]}");  
            db_insert_update("DELETE FROM uni_services_order WHERE id_ads={$ad_value["ads_id"]}"); 
            db_insert_update("DELETE FROM uni_markers WHERE id_ads={$ad_value["ads_id"]}"); 
            db_insert_update("DELETE FROM uni_favorites WHERE favorites_id_ads={$ad_value["ads_id"]}");          
           
          }

         }

     }

   }

   function countCategoryAds($id, $admin = false){
       global $CategoryBoard;
    
        if($admin == false){
           if($_SESSION["geo"]["query"]){
              $query = "AND {$_SESSION["geo"]["query"]} AND uni_ads.ads_id_cat IN(".$id.$CategoryBoard->idsBuild($id).")";
           }else{
              $query = "AND uni_ads.ads_id_cat IN(".$id.$CategoryBoard->idsBuild($id).")";
           }

           $count = db_query("SELECT count(uni_ads.ads_id) as result_count FROM uni_ads,uni_clients WHERE ".AD_QUERY." $query");
        }else{
           $query = "uni_ads.ads_id_cat IN(".$id.$CategoryBoard->idsBuild($id).")";
           $count = db_query("SELECT count(uni_ads.ads_id) as result_count FROM uni_ads WHERE $query");
        }

       return intval($count["result_count"]);
   }

   function viewAds($id = 0){
      if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false){
        if($id){    
            if(!isset($_SESSION["view-ads"][$id])){
              db_insert_update("UPDATE uni_ads SET ads_count_view=ads_count_view+1,ads_datetime_view=NOW() WHERE ads_id=$id");
                db_insert_update("INSERT INTO uni_ads_view(ads_view_id_ads,ads_view_datetime_add,ads_view_ip)VALUES('".$id."', now(),'".clear($_SERVER['REMOTE_ADDR'])."')");
                $_SESSION["view-ads"][$id] = 1;
            }  
        }
      }   
   }

   function alias($array=array()){
      return URL.$array["city_alias"]."/".$array["category_board_alias"]."/".$array["ads_alias"]."-".$array["ads_id"];
   }  

   function price($array = array()){
    global $decimals,$dec_point,$thousands_sep,$settings,$CurrencyBoard;
      return $CurrencyBoard->currency_converter($array["ads_price"], $array["ads_currency"], $_SESSION["currency"]);
   }

   function statusCount($status, $id_user = 0){

    if($id_user) $id_user = " AND ads_id_user={$id_user}"; else $id_user = "";
    
    if($status == 3){
      $sql = db_query("SELECT count(ads_id) as result_count FROM uni_ads WHERE ads_datetime_publication < now() $id_user");
      return (int)$sql["result_count"];
    }elseif($status == 5){
      $sql = db_query("SELECT count(ads_id) as result_count FROM uni_ads WHERE (ads_images='' or ads_images='null' or ads_images='[]') $id_user");
      return (int)$sql["result_count"];      
    }elseif($status == 1){
      $sql = db_query("SELECT count(ads_id) as result_count FROM uni_ads WHERE ads_status = 1 and ads_datetime_publication > now() $id_user");
      return (int)$sql["result_count"];
    }else{
      $sql = db_query("SELECT count(ads_id) as result_count FROM uni_ads WHERE ads_status=$status $id_user");
      return (int)$sql["result_count"];      
    }
        
   }
   

   function adMap($latitude, $longitude){
    global $settings;

    if($latitude && $longitude){
        
        if($settings["map_vendor"] == "google"){

          $data = '
            <div id="mapAd" ></div>
            <script type="text/javascript">

                function initMap() {
                  var myLatLng = {lat: '.$latitude.', lng: '.$longitude.'};
                  var map = new google.maps.Map(document.getElementById("mapAd"), {
                    zoom: 14,
                    center: myLatLng
                  });
                  var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map
                  });
                }
                
                google.maps.event.addDomListener(window, "load", initMap);

            </script>
          ';

        }elseif($settings["map_vendor"] == "yandex"){

          $data = '
            <div id="mapAd" ></div>
            <script type="text/javascript">
                ymaps.ready(init);

                var myMap, 
                    myPlacemark;

                $( window ).resize(function() {
                  myMap.destroy();  
                  ymaps.ready(init);
                });
        
                function init(){ 
                    myMap = new ymaps.Map("mapAd", {
                        center: ['.$latitude.', '.$longitude.'],
                        zoom: 14
                    }); 
                    myMap.behaviors.enable("scrollZoom");

                    myPlacemark = new ymaps.Placemark(['.$latitude.', '.$longitude.']);
                    
                    myMap.geoObjects.add(myPlacemark);
                }
            </script>       
          ';

        }
         
    }
       
    return $data;

   }
   
   function statusAll($status, $html = false){
    global $languages_content;
    if($html == false){
       if($status == 0){
          return $languages_content["class-ads-title-5"];
       }elseif($status == 1){
          return $languages_content["class-ads-title-6"];
       }elseif($status == 2){
          return $languages_content["class-ads-title-7"];  
       }else{
          return $languages_content["class-ads-title-8"];
       }
    }else{
       if($status == 0){
          return '<span class="badge badge-warning">'.$languages_content["class-ads-title-5"].'</span>';
       }elseif($status == 1){
          return '<span class="badge badge-primary">'.$languages_content["class-ads-title-6"].'</span>';
       }elseif($status == 2){
          return '<span class="badge badge-danger">'.$languages_content["class-ads-title-7"].'</span>';  
       }else{
          return '<span class="badge badge-warning">'.$languages_content["class-ads-title-8"].'</span>';
       }      
    }
   }
   
   function addServicesOrder($array=array()){

      $service = $this->getServices($array["id_services"]);

      if(count($service) > 0){
        
            if(!$array["top"]){
              if($service["variant"] == 1){ $array["count_day"] = $service["count_day"]; }else{ if($array["count_day"] == 0){ $array["count_day"] = 1; } }
            }

            $sql_order = db_query("SELECT * FROM uni_services_order WHERE id_ads='{$array["id_ads"]}' AND vuid='{$array["id_services"]}'");

            if($array["status"] == "ACCEPTED"){
               $time_validity = date("Y-m-d H:i:s",time() + ($array["count_day"] * 86400));
            }else{
               $time_validity = '0000-00-00 00:00:00';
            }

            if(count($sql_order)==0){

              db_insert_update("INSERT INTO uni_services_order(id_ads,vuid,count_day,id_order,time_validity)VALUES('{$array["id_ads"]}','{$array["id_services"]}','{$array["count_day"]}','{$array["id_order"]}','$time_validity')"); 
            
            }

      }

   }


    function get_map_lon_lat($city,$address){
      if(!empty($city) && !empty($address)){  
        $content = file_get_contents_curl("https://geocode-maps.yandex.ru/1.x/?geocode=$city,$address&format=json&results=1");
        if(!empty($content)){
            $content = json_decode($content,true);
            $array = $content["response"]["GeoObjectCollection"]["featureMember"][0]["GeoObject"]["Point"]["pos"];
            if(!empty($array)) $array = explode(" ",$array); 
            return array("lon"=>$array[0],"lat"=>$array[1]);
        }else{
            return array("lon"=>"","lat"=>"");
        }
      }else{
            return array("lon"=>"","lat"=>"");
        }  
    }
    

  function br2nl($string)
  {
    return str_replace(array("<br>", "<br />", "<br/>"), "", $string);
  }   

  function video($link){
      if(!empty($link)){

        if(strpos($link, "embed") === false){
           $link_ = explode("=", $link);
            if(!empty($link_[1])){
                return "https://www.youtube.com/embed/".$link_[1];
            }else{
                $link_ = explode("/", $link); 
                return "https://www.youtube.com/embed/".$link_[3];
            }         
          }else{
             return $link;
          }
          
      }
  }

  function validationAdForm($array = array(), $edit = false){
    global $getCurrency, $CategoryBoard, $languages_content;

    $error = array();

        if(!intval($array["change_id_category"])){ $error[] = $languages_content["class-ads-title-9"]; }else{
           $getCategories = $CategoryBoard->getCategories("where category_board_visible=1");
           if(!isset($getCategories["category_board_id"][$array["change_id_category"]])){
              $error[] = $languages_content["class-ads-title-9"];
           }
        }


        if(count($array["always"])>0){
          foreach($array["always"] AS $alw_id=>$alw_name){
             if(empty($array["filter"][$alw_id])){
                $error[] = $languages_content["class-ads-title-10"]." $alw_name";
             }
          }
        }    
        
        if($edit == false && !$_SESSION['profile']['id']){
          if(empty($array["type_person"])){ $error[] = $languages_content["class-ads-title-11"]; }else{
            if($array["type_person"] == "company"){
              if(empty($array["user_name_company"])){
                $error[] = $languages_content["class-ads-title-12"];
              }
            }
          }
        }

        if(empty($array["title"])){ $error[] = $languages_content["class-ads-title-13"]; }

        if(empty($array["change_currency"])){ $error[] = $languages_content["class-ads-title-14"]; }else{
          if(!isset($getCurrency[$array["change_currency"]])){
             $error[] = $languages_content["class-ads-title-14"];
          }
        }

        if(empty($array["text"])){ $error[] = $languages_content["class-ads-title-15"]; }
        if(empty($array["city_id"])){ $error[] = $languages_content["class-ads-title-16"]; }else{
          $getCity = db_query("select * from uni_city where city_id=".intval($array["city_id"]));
          if(count($getCity) == 0){
            $error[] = $languages_content["class-ads-title-16"];
          }
        }
        if(empty($array["address"])){ $error[] = $languages_content["class-ads-title-17"]; }

        if($this->adChangeUsers() == false){
        
        if($edit == false && !$_SESSION['profile']['id']){
          if(empty($array["user_name"])){ $error[] = $languages_content["class-ads-title-18"]; }
          if(validateEmail($array["user_email"]) == false){ $error[] = $languages_content["class-ads-title-19"]; }
          if(empty($array["user_phone"])){ $error[] = $languages_content["class-ads-title-20"]; }else{
            
            if($_SESSION["verify_code"] && $_SESSION["verify_phone"] == $array["user_phone"]){
             if($_SESSION["verify_code"] != $array["verify_sms"]){
               $error[] = $languages_content["class-ads-title-21"];
             }
            }else{
               $error[] = $languages_content["class-ads-title-22"];
            }

          }
        }

        }else{
           if(empty($array["id_user"])){ $error[] = $languages_content["class-ads-title-23"]; }
        }

        if(count($error) == 0){
          return array();
        }else{
          return $error;
        }

  }

  function addOrder($array=array()){
    global $Admin, $settings;

    db_insert_update("INSERT INTO uni_orders(date,id_order,title,price,id_ads,id_user,status)VALUES(NOW(),'{$array["id_order"]}','{$array["title"]}','{$array["price"]}','{$array["id_ads"]}','{$array["id_user"]}','{$array["status"]}')");

    if($array["status"] == "ACCEPTED"){
       notifications("buy_order", array( "title" => $array["title"], "price" => intval($array["price"]) . ' ' . $settings["currency_sign"] ));
       $Admin->addNotification("buy");      
    }

  }

  function servicesActivation($array = array()){
    global $Admin,$format_order,$settings,$languages_content;

      $sqlOrder = db_query("select * from uni_orders where id_order='{$array["id_order"]}'");
      if(count($sqlOrder) != 0){

            $services_order = db_query("select * from uni_services_order where id_order={$array["id_order"]}");
            if(count($services_order) > 0){

            db_insert_update("UPDATE uni_orders SET status='{$array["status"]}' WHERE id_order='{$array["id_order"]}'");

            if($services_order["time_validity"] == "0000-00-00 00:00:00"){
              
              $time_validity = date("Y-m-d H:i:s",time() + ($services_order["count_day"] * 86400));
              db_insert_update("UPDATE uni_services_order SET time_validity='$time_validity' WHERE id_order={$array["id_order"]}");
 
              notifications("buy_order", array( "title" => $sqlOrder["title"], "price" => $sqlOrder["price"] . ' ' . $settings["currency_sign"] ));
              $Admin->addNotification("buy");

            }else{

              $service = $this->getServices($services_order["vuid"]);
              $itogPrice = $this->servicesItogPrice(array("id" => $services_order["vuid"], "count" => $services_order["count_day"]));

              if(strtotime($services_order["time_validity"]) < time()){

                 $this->addOrder( array("id_order" => $format_order, "price" => $itogPrice, "id_user" => $_SESSION['profile']['id'], "title" => $languages_content["class-ads-title-24"]." - " . $service["name"], "id_services"=>$services_order["vuid"],"id_ads"=>$services_order["id_ads"], "count_day" => $services_order["count_day"], "status" => $array["status"]) );

                 db_insert_update("DELETE FROM uni_services_order WHERE id_order={$array["id_order"]}");

                 $this->addServicesOrder( array("id_order" => $format_order, "id_user" => $_SESSION['profile']['id'], "id_services"=>$services_order["vuid"],"id_ads"=>$services_order["id_ads"], "count_day" => $services_order["count_day"], "status" => $array["status"]) );

              } 

            }

          }
            
      }else{

            db_insert_update("DELETE FROM uni_services_order WHERE id_ads={$array["id_ads"]} and vuid={$array["id_services"]}");

            $this->addServicesOrder($array);
            $this->addOrder($array);

      }
    
  }

  function servicesItogPrice($array = array()){
    global $settings;

      if($array["markers"]){
        $markers_price = $settings["ad_marker_price"] * $array["markers"];
      }else{
        $markers_price = 0;
      }

      if($array["count"] == 0) $array["count"] = 1;

      if($array["id"]){
        
        $service = $this->getServices($array["id"]);

        if($service["new_price"]){
           $prices = ($array["count"] * $service["new_price"]) + $markers_price;
        }else{
           $prices = ($array["count"] * $service["price"]) + $markers_price; 
        }

      }else{

         $prices = $markers_price;

      }
      
      return $prices;

  }

  function servicesPrice($service = array(), $count = 1){
    global $settings;

        if($service["new_price"]){
          if($service["variant"] == 2) return $count * $service["new_price"]; else return $service["new_price"];
        }else{
          if($service["variant"] == 2) return $count * $service["price"]; else return $service["price"]; 
        }

  }

  function markersPrice($markers = array()){
    global $settings;

      if(count($markers) > 0){
        return $settings["ad_marker_price"] * count($markers);
      }else{
        return 0;
      }

  }

  function addMarkers($id_order = 0,$id_ads = 0,$array = array()){
     if(isset($array)){

        foreach (array_slice($array, 0, 3) as $key => $value) {
           $return[] = clear($value); 
        }

        $sql = db_query("select * from uni_markers where id_ads='$id_ads'");
        if(count($sql) == 0){

          db_insert_update("INSERT INTO uni_markers(id_ads,id_order,markers)VALUES ('".$id_ads."','".$id_order."','".urlencode(json_encode_cyr($return))."')");        
        }else{

           db_insert_update("UPDATE FROM uni_markers SET markers='".urlencode(json_encode_cyr($return))."' WHERE id={$sql["id"]}");
           return $sql["id_order"];

        }

     }
  }

  function adChangeUsers(){
    global $settings_tpl;

     if(!$_SESSION['cp_auth_admin']){
       return false;
     }else{
        if($settings_tpl["add_edit_ad"]["users"] == 0){
           return false;
        }else{
           return true;
        }
     }     
  }

  function adAnswer($pay, $action){
   global $Banners,$settings,$languages_content;

     if($action == "add"){
        $title = $languages_content["class-ads-title-25"];
     }else{
        $title = $languages_content["class-ads-title-26"];
     }
   
        if($settings["ads_publication_moderat"]){
        return '
                  <div class="text-center">
                      <i class="fa fa-smile-o text-success" style="font-size:120px;"></i>
                      <h4>'.$title.'</h4>
                  </div>

                   <p>'.$languages_content["class-ads-title-27"].'<br/>'.$languages_content["class-ads-title-28"].' <a href="/profile" >'.$languages_content["class-ads-title-29"].'</a></p>
                   
                   '.$Banners->out("add_ad");
        }else{
        return '

                  <div class="text-center">
                      <i class="fa fa-smile-o text-success" style="font-size:120px;"></i>
                      <h4>'.$title.'</h4>
                  </div>

                  <p>'.$languages_content["class-ads-title-28"].' <a href="/profile" >'.$languages_content["class-ads-title-29"].'</a></p>
                   
                   '.$Banners->out("add_ad");          
        }

  }

  function getVip($id_cat = 0){
  global $CategoryBoard,$b1;
  
  $query = array();

    if($id_cat){
      $query[] = "ads_id_cat IN(".intval($id_cat).$CategoryBoard->idsBuild(intval($id_cat)).")";
    }

    if($_SESSION["geo"]["query"]){
      $query[] = $_SESSION["geo"]["query"];
    }

    if(count($query) > 0) $query_sql = " AND ".implode(" AND ",$query);

    return db_query_while("select *, (select status from uni_orders where id_order = uni_services_order.id_order) as status,(select city_alias from uni_city where city_id = uni_ads.ads_city_id) as city_alias,(select category_board_alias from uni_category_board where category_board_id = uni_ads.ads_id_cat) as category_board_alias from uni_ads, uni_services_order where uni_ads.ads_id = uni_services_order.id_ads AND uni_services_order.time_validity > NOW() AND uni_services_order.vuid IN(3,2) {$query_sql} HAVING status='ACCEPTED' order by rand() limit 4");

  }
   function GetIP() {
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
      return $ip;
    }
   function count_click_phone_ads($ads_id){
        $ip_verif = db_query_while("SELECT * FROM uni_ads_click_phone WHERE ads_click_phone_id_ads='{$ads_id}'");
        return count($ip_verif);
   }

  function mapCluster($array = array(), $search = false){
    global $main_medium_image_ads, $no_image, $settings;
   
   if(count($array["ads"]) > 0){

   if($settings["map_vendor"] == "yandex"){


    foreach ($array["ads"] as $key => $ad_value) {

     $ad_value["city_name"] = !empty($ad_value['lang_city_name']) ? urldecode($ad_value['lang_city_name']) : $ad_value['city_name'];
     $ad_value["category_board_name"] = !empty($ad_value['lang_category_name']) ? urldecode($ad_value['lang_category_name']) : $ad_value['category_board_name'];

	   if(empty($_SESSION['profile']["favorit-ad"][$ad_value["ads_id"]])){
	      $favorite = '<i class="fa fa-heart-o" aria-hidden="true"></i>';
	   }else{
	      $favorite = '<i class="fa fa-heart" aria-hidden="true"></i>';
	   }

        $image = $this->getImages($ad_value["ads_images"]);

          if($ad_value["ads_latitude"] && $ad_value["ads_longitude"]){
            $ads["balloonContentBody"][] = '

              {
                hintContent: `'.$ad_value["ads_title"].'`,
                balloonContentHeader: `'.$ad_value["ads_title"].'`,
                balloonContentBody: `
                  <div class="ballon-point">
                     <div>
                        <div data-id="'. $ad_value["ads_id"].'" class="grid-item-favorit add-favorite">'.$favorite.'<span></span></div>
                        <img src="'.Exists($main_medium_image_ads,$image[0],$no_image).'" height="100px;" />
                     </div>
                     <a class="ballon-point-link" href="/'.$ad_value["city_alias"].'"><i class="fa fa-location-arrow" aria-hidden="true"></i> '.$ad_value["city_name"].'</a>
                     <a class="ballon-point-link" href="/'.$ad_value["city_alias"].'/'.$ad_value["category_board_category_chain"].'"><i class="la la-bookmark-o" aria-hidden="true"></i> '.$ad_value["category_board_name"].'</a>
                     <a class="ballon-point-title" href="'.$this->alias($ad_value).'" >'.$ad_value["ads_title"].'</a>
                  </div>
                `
              },
          
            ';
            $ads["points"][] = '['.$ad_value["ads_latitude"].', '.$ad_value["ads_longitude"].'],';
          }
    }

    if($search == false){

       return '
	    <script type="text/javascript">
	      ymaps.ready(function () {
	        var myMap = new ymaps.Map("map", {
	            center: ['.$settings["country_lat"].', '.$settings["country_lng"].'],
	            zoom: 6,
	            behaviors: ["default", "scrollZoom"]
	          }),

	          clusterer = new ymaps.Clusterer({
	            preset: "islands#invertedDarkBlueClusterIcons",
	            groupByCoordinates: false,
	            clusterDisableClickZoom: true,
	            clusterHideIconOnBalloonOpen: false,
	            geoObjectHideIconOnBalloonOpen: false
	          }),

	          getPointData = [
	             '.implode("" ,$ads["balloonContentBody"]).'
	          ],

	          getPointOptions = function () {
	            return {
	              preset: "islands#redDotIcon"
	            };
	          },

	          points = [
	             '.implode("" ,$ads["points"]).'
	          ],
	          geoObjects = [];

	        for (var i = 0, len = points.length; i < len; i++) {
	          geoObjects[i] = new ymaps.Placemark(points[i], getPointData[i], getPointOptions());
	        }

	        clusterer.options.set({
	          gridSize: 70,
	          clusterDisableClickZoom: false
	        });

	        clusterer.add(geoObjects);
	        myMap.geoObjects.add(clusterer);

	        myMap.setBounds(clusterer.getBounds(), {
	          checkZoomRange: true
	        });
	        myMap.behaviors.disable("scrollZoom");
	      });
	    </script>

       ';

    }else{
       
       return '

	    <script type="text/javascript">
	      ymaps.ready(function () {
	        var myMap = new ymaps.Map("map", {
	            center: ['.$settings["country_lat"].', '.$settings["country_lng"].'],
	            zoom: 6,
	            controls: []           
	          }),

	          clusterer = new ymaps.Clusterer({
	            preset: "islands#invertedDarkBlueClusterIcons",
	            groupByCoordinates: false,
	            clusterDisableClickZoom: true,
	            clusterHideIconOnBalloonOpen: false,
	            geoObjectHideIconOnBalloonOpen: false
	          }),

	          getPointData = [
	             '.implode("" ,$ads["balloonContentBody"]).'
	          ],

	          getPointOptions = function () {
	            return {
	              preset: "islands#redDotIcon"
	            };
	          },

	          points = [
	             '.implode("" ,$ads["points"]).'
	          ],
	          geoObjects = [];

	        for (var i = 0, len = points.length; i < len; i++) {
	          geoObjects[i] = new ymaps.Placemark(points[i], getPointData[i], getPointOptions());
	        }

	        clusterer.options.set({
	          gridSize: 70,
	          clusterDisableClickZoom: false
	        });

	        clusterer.add(geoObjects);
	        myMap.geoObjects.add(clusterer);

	        myMap.setBounds(clusterer.getBounds(), {
	          checkZoomRange: true
	        });
	       
	      });
	    </script>

       ';

    }

    }elseif($settings["map_vendor"] == "google"){

    foreach ($array["ads"] as $key => $ad_value) {

     $ad_value["city_name"] = !empty($ad_value['lang_city_name']) ? urldecode($ad_value['lang_city_name']) : $ad_value['city_name'];
     $ad_value["category_board_name"] = !empty($ad_value['lang_category_name']) ? urldecode($ad_value['lang_category_name']) : $ad_value['category_board_name'];

     if(empty($_SESSION['profile']["favorit-ad"][$ad_value["ads_id"]])){
        $favorite = '<i class="fa fa-heart-o" aria-hidden="true"></i>';
     }else{
        $favorite = '<i class="fa fa-heart" aria-hidden="true"></i>';
     }

        $image = $this->getImages($ad_value["ads_images"]);

          if($ad_value["ads_latitude"] && $ad_value["ads_longitude"]){

            $ads["balloonContentBody"][] = '

                new google.maps.Marker({
                    position: new google.maps.LatLng('.$ad_value["ads_latitude"].','.$ad_value["ads_longitude"].'),
                    map: map,
                    title: "'.$ad_value["ads_title"].'",
                    content: `
                          <div class="ballon-point">
                             <div>
                                <div data-id="'. $ad_value["ads_id"].'" class="grid-item-favorit add-favorite">'.$favorite.'<span></span></div>
                                <img src="'.Exists($main_medium_image_ads,$image[0],$no_image).'" height="100px;" />
                             </div>
                             <a class="ballon-point-link" href="/'.$ad_value["city_alias"].'"><i class="fa fa-location-arrow" aria-hidden="true"></i> '.$ad_value["city_name"].'</a>
                             <a class="ballon-point-link" href="/'.$ad_value["city_alias"].'/'.$ad_value["category_board_category_chain"].'"><i class="la la-bookmark-o" aria-hidden="true"></i> '.$ad_value["category_board_name"].'</a>
                             <a class="ballon-point-title" href="'.$this->alias($ad_value).'" >'.$ad_value["ads_title"].'</a>
                          </div>
                        `
                }),

            ';

          }

    }

    return '

        
        <script type="text/javascript">

        var gMapsLoaded = false;
        window.gMapsCallback = function(){
            gMapsLoaded = true;
            $(window).trigger("gMapsLoaded");
        }
        window.loadGoogleMaps = function(){
            if(gMapsLoaded) return window.gMapsCallback();
            var script_tag = document.createElement("script");
            script_tag.setAttribute("type","text/javascript");
            script_tag.setAttribute("src","https://maps.googleapis.com/maps/api/js?key='.$settings["map_google_key"].'&callback=gMapsCallback");
            (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
        }

        $(window).bind("gMapsLoaded", initMap);
        window.loadGoogleMaps();

        function initMap() {

            var marker;
            var map;
            var markerArray = [];

            var infoWindow = new google.maps.InfoWindow();
            var marker, i;
            var clusterMarkers = [

                '.implode("",$ads["balloonContentBody"]).'
                
            ]
            
            var options_googlemaps = {
                zoom: 6,
                center: new google.maps.LatLng('.$settings["country_lat"].','.$settings["country_lng"].'),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            map = new google.maps.Map(document.getElementById("map"), options_googlemaps);

            var markerCluster = new MarkerClusterer(map, clusterMarkers,
                        { imagePath: "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m" });

            for(i = 0; i < clusterMarkers.length; i++) {
               var marker = clusterMarkers[i];

                google.maps.event.addListener(marker, "click", (function(marker) {
                    return function() {
                     infoWindow.setContent(this.content);
                     infoWindow.open(map, this);
                    }
                })(marker));
            }

        }

        google.maps.event.addDomListener(window, "load", initMap);


        </script>
    ';


    }

   }

  }

  function checkSaveSearch(){

    if(REQUEST_URI){

       parse_str(REQUEST_URI, $query_params);
       unset($query_params["_"]);
       unset($query_params["page"]);
       unset($query_params["currency"]);
       unset($query_params["sort"]);

       $http_build_query = trim(urldecode(http_build_query($query_params, 'flags_')), "=");

         $check = db_query("select count(*) as count from uni_save_search where save_search_params = '".urlencode($http_build_query)."' and save_search_id_user={$_SESSION['profile']['id']}");
         if($check["count"]){
             return true;
         }else{
             return false;
         } 

    }

  }

  function addSearchTags($id = 0){
  	global $Ads;
  	if($id){
        $array = $Ads->get(" where ads_id=$id");
  		$tags = $array["ads_title"] . ' ' . $array["city_name"] . ' ' . $array["region_name"] . ' ' . $array["country_name"] . ' ' . $array["category_board_name"];
  		db_insert_update("UPDATE uni_ads SET ads_search_tags='$tags' WHERE ads_id=$id");
  	}

  }

  function getServices($id){
    global $Cashed;

      $key = "select * from uni_services_ads where uid=".$id;
      $services = $Cashed->get($key,"services");

      if($services !== false){
          return $services;
      }else{
          $sql = db_query($key);            
          $Cashed->set($sql,$key,"services");
          return $sql;
      }

    
  }

  function buttonTop($array = array()){
    global $settings;

      if(!$array["top"] && $array["ads_id_pos"] != 1){  
        $service = $this->getServices(1);
        $prices = $this->priceTop($service);
        $services = $this->getAdServices($array["ads_id"]);
        if(!$services[3]){
          return '<button style="margin-top: 10px;" data-price="'.number_format($prices,$decimals,$dec_point,$thousands_sep).' '.$settings["currency_sign"].'" data-id="'.$array["ads_id"].'" class="btn btn-success btn-sm profile-button-top-ads"><i class="la la-long-arrow-up"></i> Поднять на 1 день за '.number_format($prices,$decimals,$dec_point,$thousands_sep).' '.$settings["currency_sign"].'</button>';
        }
      }

  }

  function buttonExtend($array = array()){
    global $settings,$languages_content;

      if($array["ads_status"] == 1 && strtotime($array["ads_datetime_publication"]) < time()){  

          //return '<button style="margin-top: 10px;" data-id="'.$array["ads_id"].'" class="btn btn-warning btn-sm profile-button-extend-ads"><i class="la la-long-arrow-up"></i> '.$languages_content["class-ads-title-30"].'</button>';

      }

  }

  function buttonAddService($array = array()){
    global $settings,$languages_content;
    
    if($array["ads_status"] == 1 && strtotime($array["ads_datetime_publication"]) > time()){
  
        $services = $this->getAdServices($array["ads_id"]);
        if(count($services) > 0){
          if(!$services[3]){
            if(!$services[1] || !$services[2] || !$services[4]){
             return '<button style="margin-top: 10px;" data-id="'.$array["ads_id"].'" class="btn btn-primary btn-sm profile-add-services"><i class="la la-plus"></i> '.$languages_content["class-ads-title-31"].'</button>';
            }
          }
        }else{
           return '<button style="margin-top: 10px;" data-id="'.$array["ads_id"].'" class="btn btn-primary btn-sm profile-add-services"><i class="la la-plus"></i> '.$languages_content["class-ads-title-31"].'</button>';
        }
        
    }

  }
  function buttonAddStickers($array = array()){
    global $languages_content;

    if($array["ads_status"] == 1 && strtotime($array["ads_datetime_publication"]) > time()){

        $bottom = $top = 0;
        $stickers_order = $this->getAdStickers($array["ads_id"]);
        foreach ($stickers_order as $order) {
            if($order['stickers_line'] == "top")
                $top++;
            else
                $bottom++;
        }
        if($top < 2 || $bottom < 2)
            return '<button style="margin-top: 10px;" data-id="'.$array["ads_id"].'" class="btn btn-primary btn-sm profile-add-stickers"><i class="la la-plus"></i> Добавить стикер</button>';


    }

  }

    function getAdStickers($id = 0){
        if($id){
            $sql_sticker_order = db_query_while("select stickers_order_id_sticker, stickers_order_datetime_finish, (select stickers_line, stickers_side from uni_stickers where uni_stickers.stickers_id = uni_stickers_order.stickers_order_id_sticker) from uni_stickers_order where stickers_order_id_ads={$id} AND stickers_order_datetime_finish < DATE()");
            return $sql_sticker_order;
        }
    }
  function priceTop($service = array()){

    if($service["new_price"]){
       $prices = $service["new_price"];
    }else{
       $prices = $service["price"]; 
    }

    if($service["variant"] == 2){
      return $prices;
    }else{
       if($service["count_day"] == 1){
          return $prices;
       }else{
          return $prices / $service["count_day"];             
       }
    }

  }

  function posAd($filter = array(),$id_cat = 0,$id_city = 0){
    global $Filters,$CategoryBoard;
    
    $forming_multi_query = array();
    $new_filter = array();

      if(count($filter) > 0){

        foreach ($filter as $main_id => $value) {
           $sql = db_query("select *, (select type from uni_filters where id=uni_filters_items.id_filter) as type from uni_filters_items where id={$main_id}");
           if($sql["type"] == "slider") {
              $item = explode("\n",urldecode($sql["value"]));
              $new_filter["slider"][$main_id][] = $item[0].';'.$item[1]; 
           } else {
              $new_filter[$main_id][] = $value;
           }
        }

      }

      if($id_cat){
          $cat_ids = $id_cat.$CategoryBoard->idsBuild($id_cat);
          $forming_multi_query = "uni_ads.ads_id_cat IN(".$cat_ids.")";
      }
      
      if(count($new_filter) > 0){
         foreach($new_filter AS $id=>$val){
               
            if($id != "slider"){
              
              foreach($val AS $val2){
                 $val2 = trim($val2); 
                 if($val2 != "null" && !empty($val2)){
                     
                     if(count($forming[$id]) == 0) $flCount++;
                     $forming[$id][] = "id_filter='".intval($id)."' AND value='".clear($val2)."'";
                     
                 } 
              } 
              
            }else{

              foreach($val AS $id2 => $val2){

                if($val2[0] != "null" && $val2[0] != ""){

                  $sliderCount = explode(";",$val2[0]);

                    $flCount++;

                    if($sliderCount[1]){
                      $forming[$id2][] = "id_filter='".$id2."' AND (value BETWEEN ".intval($sliderCount[0])." AND ".intval($sliderCount[1]).")";
                    }else{
                      $forming[$id2][] = "id_filter='".$id2."' AND (value >= ".intval($sliderCount[1]).")";
                    }

                }

              }
                               
            }            
        
         }


         if(count($forming)){
                
                foreach($forming as $id=>$arr){
                     foreach($arr as $i=>$val){
                          $forming_multi[] = $val;                  
                     } 
                  $forming_filters[] = "(".implode(" OR ",$forming_multi).")"; 
                  $forming_multi = array();            
                }
      
         }

        if(count($forming_filters) > 0){

          $ids = db_query_while("SELECT ads_id, count(ads_id) AS cnt FROM `uni_filters_variants` WHERE ".implode(" OR ",$forming_filters)."  GROUP BY ads_id HAVING COUNT(cnt)>=".$flCount, "ads_id");

           if(count($ids) > 0){
              $query = " AND uni_ads.ads_id IN(".implode(",",$ids).") AND ".$forming_multi_query;
           }else{
              $query = " AND uni_ads.ads_id IN(0)"; 
           }

        }else{

          if($forming_multi_query) { 
            $query = " AND ".$forming_multi_query; 
          } else { 
            $query = "";
          }
           
        }

       }else{

          $query = " AND ".$forming_multi_query;

       }

      $results = $this->getQuery($query,"", false);
      if(!$results["count_ads"]) return 1; else return $results["count_ads"] + 1;

  }

  function mapAdAddress($lat = 0, $lon = 0){
    global $settings;

    if(!$lat) $lat = $settings["country_lat"];
    if(!$lon) $lon = $settings["country_lng"];

     if($settings["map_vendor"] == "yandex"){

      ?>

      <?php

     }elseif($settings["map_vendor"] == "google"){

       ?>

       <script type="text/javascript">

        var a,lat,long;

        function initialize() {

            var searchBox = document.getElementById('searchMapAddress');

            var myLatlng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>);

            var myOptions = {
                zoom: 12,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                animation:google.maps.Animation.BOUNCE
            };
            map = new google.maps.Map(document.getElementById("mapAddress"), myOptions);

            var marker = new google.maps.Marker({
                draggable: true,
                position: myLatlng,
                map: map
            });

            google.maps.event.addListener(marker, 'dragend', function (event) {

                $("input[name=map_lat]").val(event.latLng.lat());
                $("input[name=map_lon]").val(event.latLng.lng());

                geocoder.geocode({
                  'latLng': event.latLng
                }, function(results, status) {

                  if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                      $(".searchMapAddress").val(results[0].formatted_address);
                    }
                  }

                });

            });

            var geocoder = new google.maps.Geocoder();

            google.maps.event.addListener(map, 'click', function(event) {

              marker.setPosition(event.latLng);

              $("input[name=map_lat]").val(event.latLng.lat());
              $("input[name=map_lon]").val(event.latLng.lng());

              geocoder.geocode({
                'latLng': event.latLng
              }, function(results, status) {

                if (status == google.maps.GeocoderStatus.OK) {
                  if (results[0]) {
                    $(".searchMapAddress").val(results[0].formatted_address);
                  }
                }

              });

            });

            var defaultBounds = new google.maps.LatLngBounds(new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $lon; ?>));

            var input = document.getElementById('searchMapAddress');
            var autocomplete = new google.maps.places.Autocomplete(searchBox,defaultBounds);
          
            google.maps.event.addListener(autocomplete, 'place_changed', function () {

              var place = autocomplete.getPlace();

              marker.setPosition(place.geometry.location);

              a = place.formatted_address;
              lat = place.geometry.location.lat();
              long = place.geometry.location.lng();
             
              $("input[name=map_lat]").val(lat);
              $("input[name=map_lon]").val(long);

            });
             
        };

        google.maps.event.addDomListener(window, 'load', initialize);

       </script>

       <?php

     }

  }


      
}


$Ads = new Ads();

?>