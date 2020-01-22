<?php

/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */

class Geo{
    
   function getGeo(){
       return $_SESSION["geo"]["alias"];
   }

   function viewCity($id=0){
    if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false){
      if($id){    
          if(!isset($_SESSION["view-city"][$id])){
            db_insert_update("UPDATE uni_city SET city_count_view=city_count_view+1 WHERE city_id=$id"); 
            $_SESSION["view-city"][$id] = 1;
          }  
      }
    }    
   }

   function alias($alias = "", $request = ""){
        if($_SESSION["route_name"] == "city_category" || $_SESSION["route_name"] == "category"){
            if($request != "/board" && $request){
              if(isset($_SESSION["geo"])){

                 $uri = explode("/", trim($request, "/"));
                 $uri[0] = $alias;
                 return  "/" . implode("/",$uri);

              }else{
                 return  "/" . $alias . "/" . trim($request, "/");     
              }
            }else{ return "/" . $alias; }  
        }else{
            return  "/" . $alias;
        }
   }

   function getListCountry(){
    global $languages_content;
       $country = db_query_while("SELECT * FROM uni_country WHERE country_status=1");
       if(count($country) > 0){
          foreach ($country as $value) {

            $multilanguage_tables = multilanguage_tables(array("id_content" => $value["country_id"], "table_name" => "uni_country"));
            $value["country_name"] = !empty($multilanguage_tables['lang_name']) ? urldecode($multilanguage_tables['lang_name']) : $value['country_name'];

            if($_SESSION["geo"]["country-id"] == $value["country_id"]){
                $name = $value["country_name"];              
            }
            $out .= '<li><a href="'.$this->alias($value["country_alias"], REQUEST_URI).'">'.$value["country_name"].'</a></li>';
          }
       }
       if(count($country) == 1){
          foreach ($country as $value) {
          $_SESSION["geo"]["name"] = $value['country_name'];
          $_SESSION["geo"]["change"]["country"] = db_query("SELECT * FROM uni_country WHERE country_id='{".$value['country_id']."}'");
             return $languages_content["class-geo-title-1"].' - <a href="#">'.$value['country_name'].'</a> <ul><li><a href="/board">'.$languages_content["class-geo-title-2"].'</a></li>'.$out.'</ul>';
           }
       }
        if($name){

         if($_SESSION["route_name"] == "city_category" || $_SESSION["route_name"] == "category"){
            $explode = explode("/", trim(REQUEST_URI, "/"));
            unset($explode[0]);
             return $languages_content["class-geo-title-1"].' - <a href="#">'.$name.'</a> <ul><li><a href="/'.implode("/", $explode).'">'.$languages_content["class-geo-title-2"].'</a></li>'.$out.'</ul>
             ';            
         }else{
             return $languages_content["class-geo-title-1"].' - <a href="#">'.$name.'</a> <ul><li><a href="/board">'.$languages_content["class-geo-title-2"].'</a></li>'.$out.'</ul>
             ';           
         }

        }else{

         return $languages_content["class-geo-title-1"].' - <a href="/board">'.$languages_content["class-geo-title-2"].'</a> <ul>'.$out.'</ul>
         '; 

        } 

   }

   function outPopularCity(){
    global $settings;
    if($_SESSION["geo"]["country-id"]){

    $sql = db_query_while("SELECT * FROM uni_city WHERE country_id={$_SESSION["geo"]["country-id"]} order by city_count_view desc limit 20");

    }else{

    $sql = db_query_while("select *, (select country_alias from uni_country where country_id = uni_city.country_id) as country FROM uni_city HAVING country = '".$settings["country_default"]."' order by city_count_view desc limit 20");

    }

      if(count($sql) > 0){
        $index = 1;
         foreach ($sql as $key => $value) {

         $multilanguage_tables = multilanguage_tables(array("id_content" => $value["city_id"], "table_name" => "uni_city"));
         $value["city_name"] = !empty($multilanguage_tables['lang_name']) ? urldecode($multilanguage_tables['lang_name']) : $value['city_name'];

         $list .= '<a href="'.$this->alias($value["city_alias"], REQUEST_URI).'">'.$value["city_name"].'</a>';

            if($index == 5){
                $return .= '<div class="col-lg-3 col-md-4 col-sm-6 col-6" ><div class="list-footer-cities" >'.$list.'</div></div>';
                $index = 1;
                $list = "";
            }else{
                $index++;
            }

           
         } 

      }
     return $return;
   } 

   function detect($ip="", $array = array()){
    global $SxGeo;
     
     if(count($array) == 0){
        $array = $this->geoIp($ip, "array");
        if($array["city"]){
           $city = db_query("SELECT * FROM uni_city WHERE city_alias='".translite($array["city"])."'");
        }
     }

      return array("city" => $array["city"], "region" => $array["region"], "country" => $array["country"], "lat" => $array["lat"], "lon" => $array["lon"], "city_id" => $city["city_id"], "region_id" => $city["region_id"], "country_id" => $city["country_id"]);

   }

   function getCity($city_id = 0){
      if($city_id){
         return db_query("SELECT *, (select country_name from uni_country where country_id = uni_city.country_id) as country_name, (select region_name from uni_region where region_id = uni_city.region_id) as region_name FROM uni_city WHERE city_id={$city_id}");
      }else{
         return array();
      }
   } 

   function setGeo(){
    global $Cashed;

    $key = $_SESSION["geo"]["country-id"].$_SESSION["geo"]["region-id"].$_SESSION["geo"]["city-id"];
    $data = $Cashed->get($key,"city");

    if($data !== false){

       $_SESSION["geo"]["change"] = $data;

    }else{

       if($_SESSION["geo"]["country-id"] != 0){
          $_SESSION["geo"]["change"]["country"] = db_query("SELECT * FROM uni_country WHERE country_id='{$_SESSION["geo"]["country-id"]}'");
       }

       if($_SESSION["geo"]["region-id"] != 0){
          $_SESSION["geo"]["change"]["region"] = db_query("SELECT * FROM uni_region WHERE region_id='{$_SESSION["geo"]["region-id"]}'");
       }

       if($_SESSION["geo"]["city-id"] != 0){
          $_SESSION["geo"]["change"]["city"] = db_query("SELECT * FROM uni_city WHERE city_id='{$_SESSION["geo"]["city-id"]}'");
       }

       $Cashed->set($_SESSION["geo"]["change"],$key,"city");

    }

  }

  function geoIp($ip, $view = "array"){
     global $SxGeo;
     if($ip){
       $Geo = $SxGeo->getCityFull($ip);
       if($view == "array"){
          return array("city"=>$Geo["city"]["name_ru"],"region"=>$Geo["region"]["name_ru"],"country"=>$Geo["country"]["name_ru"]);
       }else{
           if(!empty($Geo["city"]["name_ru"]) && !empty($Geo["region"]["name_ru"])){
              return $Geo["city"]["name_ru"].', '.$Geo["region"]["name_ru"];
           }elseif(!empty($Geo["city"]["name_ru"])){
              return $Geo["city"]["name_ru"];
           }elseif(!empty($Geo["region"]["name_ru"])){
              return $Geo["region"]["name_ru"];
           }else{
              return '-';
           }          
       }
     }
  } 
  
  function vendorMap(){
    global $settings;

      if($settings["map_vendor"] == "google"){

        return '<script src="https://maps.googleapis.com/maps/api/js?key='.$settings["map_google_key"].'&libraries=places"></script><script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>';

      }elseif($settings["map_vendor"] == "yandex"){
        
        return '<script src="//api-maps.yandex.ru/2.1.75/?lang=ru_RU&apikey='.$settings["map_google_key"].'" type="text/javascript"></script>';

      }

  }

  function metrics(){
    ?>

    <script type="text/javascript">
        $(document).ready(function () {
          window.onload = function () {

             $.ajax({type: "POST",url: "/ajax/metrics/",data: "city=&region=&country=&enter="+location.href+"&referrer="+document.referrer+"&title="+$("title").html()+"&latitude=&longitude=", dataType: "html",cache: false,success: function (data) {

                if(data != false){
                   $(".fade-change-city strong").html(data);
                   $(".fade-change-city").show();
                }        

             }});    
          }
        
        });
    </script>
    <?php
  } 


    
}


$Geo = new Geo();

?>