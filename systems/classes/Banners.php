<?php

/**
 * UniSite CMS
 *
 * @copyright 	2018 Artur Zhur
 * @link 		https://unisitecms.ru
 * @author 		Artur Zhur
 *
 */

class Banners{
    

    function getBanners(){ 
      global $Cashed;
      
        $key = "SELECT * FROM uni_banners WHERE visible = '1' ORDER By id_pos ASC";
        $data = $Cashed->get($key,"banners");

        if($data !== false){
            return $data;
        }else{
    
        $sql = db_query_while($key);
            if (count($sql) > 0) {

                   foreach($sql as $result){
                       $data[$result["banner_position"]][$result["id"]]["id"] = $result["id"];
                       $data[$result["banner_position"]][$result["id"]]["image"] = $result["image"];
                       if($result["ids_cat"]) $data[$result["banner_position"]][$result["id"]]["ids_cat"] = explode(",",$result["ids_cat"]); else $data[$result["banner_position"]][$result["id"]]["ids_cat"] = array();
                       $data[$result["banner_position"]][$result["id"]]["type_banner"] = $result["type_banner"];
                       $data[$result["banner_position"]][$result["id"]]["date_start"] = $result["date_start"];
                       $data[$result["banner_position"]][$result["id"]]["date_end"] = $result["date_end"];
                       if($result["ids_city"]) $data[$result["banner_position"]][$result["id"]]["ids_city"] = explode(",",$result["ids_city"]); else $data[$result["banner_position"]][$result["id"]]["ids_city"] = array();
                       if($result["ids_region"]) $data[$result["banner_position"]][$result["id"]]["ids_region"] = explode(",",$result["ids_region"]); else $data[$result["banner_position"]][$result["id"]]["ids_region"] = array();
                       $data[$result["banner_position"]][$result["id"]]["code_script"] = urldecode($result["code_script"]);
                       $data[$result["banner_position"]][$result["id"]]["index_out"] = $result["index_out"];
                       $data[$result["banner_position"]][$result["id"]]["out_podcat"] = $result["out_podcat"];
                       $data[$result["banner_position"]][$result["id"]]["link_site"] = urldecode($result["link_site"]);
                       $data[$result["banner_position"]][$result["id"]]["out_region"] = $result["out_region"];
                       $data[$result["banner_position"]][$result["id"]]["count_view"] = $result["count_view"];
                       $data[$result["banner_position"]][$result["id"]]["limit_view"] = $result["limit_view"];
                   } 

                $Cashed->set($data,$key,"banners");   
                return $data;

            }else{
                return array();
            }

        }

    }

    function click(){
      $id = (int)$_GET["banner_id"];  
        if($id){    
          if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false) db_insert_update("UPDATE uni_banners SET click=click+1 WHERE id='$id'");
          $link = db_query("SELECT link_site FROM uni_banners WHERE id='$id' AND visible='1'");
          if(count($link)>0){ header("Location: ".urldecode($link["link_site"])); }else{ header("Location: /"); } 
        }    
    }
       
    function checkCats($array = array(), $id_cat = 0, $pos = ""){
    global $CategoryBoard, $CategoryBlog;
       if(count($array["ids_cat"]) > 0){

       $ids = array(); 
         
          if($id_cat){ 

            if($array["out_podcat"] == 1){

               foreach($array["ids_cat"] as $id){

                if($pos == "blog_top" || $pos == "blog_bottom" || $pos == "blog_sidebar" || $pos == "article_sidebar" || $pos == "article_top" || $pos == "article_bottom"){
                  $expl =  explode(",",$id.$CategoryBlog->idsBuild($id));
                }else{
                  $expl =  explode(",",$id.$CategoryBoard->idsBuild($id));
                }

                  if(count($expl)>0){
                      foreach($expl as $id_){
                         $ids[$id_] = $id_; 
                      }
                  }else{
                      $ids[$id] = $id;
                  }
               }

            }else{

               $ids = $array["ids_cat"];

            }

           if(in_array($id_cat,$ids)){
               return true;  
           }else{
               return false;
           }

         }else{
            return false;
         }
         
       }else{
          return true;
       }    
    }
    
    function checkCity($array = array(), $geo = array()){

      if(count($array["ids_city"]) > 0){  

          if(in_array($geo["city_id"],$array["ids_city"])){
             return true;
          }else{
             if($array["out_region"] == 0){
                return false;
             }else{
                  if(in_array($geo["region_id"],$array["ids_region"])){
                      return true;
                  }else{
                      return false;
                  }                
             }  
          } 

      }else{
         return true;
      } 

    }


    function results($pos = "",$index = 0,$param = array()){

        global $image_banners, $array_data,$Geo;

        $getBanners = $this->getBanners();

        $geolocation = $Geo->detect($_SERVER['REMOTE_ADDR'], array("city"=>$_SESSION["geo"]["change"]["city"]["name"],"region"=>$_SESSION["geo"]["change"]["region"]["name"],"country"=>$_SESSION["geo"]["change"]["country"]["name"],"city_id"=>$_SESSION["geo"]["change"]["city"]["city_id"],"region_id"=>$_SESSION["geo"]["change"]["region"]["region_id"],"country_id"=>$_SESSION["geo"]["change"]["country"]["country_id"]));


        if(isset($getBanners[$pos])){

             foreach($getBanners[$pos] AS $id_banner => $array){
              
              if($index == $array["index_out"]){

                if($this->checkCats($array, $param["id_cat"], $pos) == true && $this->checkCity($array, $geolocation) == true && ($array["count_view"] < $array["limit_view"] || $array["limit_view"] == 0 ) && ( (time() >= strtotime($array["date_start"]) || strtotime($array["date_start"]) == "0000-00-00 00:00:00" ) && (time() < strtotime($array["date_end"]) || $array["date_end"] == "0000-00-00 00:00:00" ) )){

                        if($array["type_banner"] == 1){
                           
                           if(!empty($array["link_site"])) $href = 'href="/?banner_id='.$array["id"].'"'; else $href = "";
                            
                           if(!empty($array["image"]) && file_exists($_SERVER['DOCUMENT_ROOT'].$image_banners.$array["image"])){
                              $content .= '<li><a '.$href.' target="_blank" ><img src="'.URL.$image_banners.$array["image"].'" /></a></li>';
                           } 
  
                        }elseif($array["type_banner"] == 2){
                           
                            $content .= '<li>'.urldecode($array["code_script"]).'</li>';
                             
                        }  

                         
                        if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false) db_insert_update("UPDATE uni_banners SET count_view=count_view+1 WHERE id={$array["id"]}");    

                }

              }  

             }
             
            if($content) return '<div class="imagestyle" ><ul class="rotator-images" >'.$content.'</ul><div class="clr" ></div></div>';  
              
        }

    }

    function out($pos,$param = array()){
        global $image_banners, $array_data,$Geo;

        $getBanners = $this->getBanners();

        $geolocation = $Geo->detect($_SERVER['REMOTE_ADDR'], array("city"=>$_SESSION["geo"]["change"]["city"]["name"],"region"=>$_SESSION["geo"]["change"]["region"]["name"],"country"=>$_SESSION["geo"]["change"]["country"]["name"],"city_id"=>$_SESSION["geo"]["change"]["city"]["city_id"],"region_id"=>$_SESSION["geo"]["change"]["region"]["region_id"],"country_id"=>$_SESSION["geo"]["change"]["country"]["country_id"]));


        if(isset($getBanners[$pos])){

             foreach($getBanners[$pos] AS $id_banner => $array){

                if($this->checkCats($array, $param["id_cat"], $pos) == true && $this->checkCity($array, $geolocation) == true && ($array["count_view"] < $array["limit_view"] || $array["limit_view"] == 0 ) && ( (time() >= strtotime($array["date_start"]) || strtotime($array["date_start"]) == "0000-00-00 00:00:00" ) && (time() < strtotime($array["date_end"]) || $array["date_end"] == "0000-00-00 00:00:00" ) )){

                        if($array["type_banner"] == 1){
                           
                           if(!empty($array["link_site"])) $href = 'href="/?banner_id='.$array["id"].'"'; else $href = "";
                            
                           if(!empty($array["image"]) && file_exists($_SERVER['DOCUMENT_ROOT'].$image_banners.$array["image"])){
                              $content .= '<li><a '.$href.' target="_blank" ><img src="'.URL.$image_banners.$array["image"].'" /></a></li>';
                           } 
  
                        }elseif($array["type_banner"] == 2){
                           
                            $content .= '<li>'.urldecode($array["code_script"]).'</li>';
                             
                        }  

                         
                        if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false) db_insert_update("UPDATE uni_banners SET count_view=count_view+1 WHERE id={$array["id"]}");    

                }

             }
             
            if($content) return '<div class="imagestyle" ><ul class="rotator-images" >'.$content.'</ul><div class="clr" ></div></div>'; 
               
        }

    }

    function positions(){
      global $lang;
        return array(
          "result" => array("title" => $lang["display_banners_pos_title1"], "name" => $lang["display_banners_pos_title2"]),
          "board_sidebar" => array("title" => $lang["display_banners_pos_title3"], "name" => $lang["display_banners_pos_title4"]),
          "board_top" => array("title" => $lang["display_banners_pos_title3"], "name" => $lang["display_banners_pos_title5"]),
          "board_bottom" => array("title" => $lang["display_banners_pos_title3"], "name" => $lang["display_banners_pos_title6"]),
          "ad_top" => array("title" => $lang["display_banners_pos_title7"], "name" => $lang["display_banners_pos_title8"]),
          "ad_sidebar" => array("title" => $lang["display_banners_pos_title7"], "name" => $lang["display_banners_pos_title9"]),
          "ad_bottom" => array("title" => $lang["display_banners_pos_title7"], "name" => $lang["display_banners_pos_title10"]),
          "index_center" => array("title" => $lang["display_banners_pos_title11"], "name" => $lang["display_banners_pos_title12"]),
          "index_top" => array("title" => $lang["display_banners_pos_title11"], "name" => $lang["display_banners_pos_title13"]),
          "index_bottom" => array("title" => $lang["display_banners_pos_title11"], "name" => $lang["display_banners_pos_title14"]),
          "blog_top" => array("title" => $lang["display_banners_pos_title15"], "name" => $lang["display_banners_pos_title16"]),
          "blog_bottom" => array("title" => $lang["display_banners_pos_title15"], "name" => $lang["display_banners_pos_title17"]),
          "blog_sidebar" => array("title" => $lang["display_banners_pos_title15"], "name" => $lang["display_banners_pos_title18"]),  
          "article_sidebar" => array("title" => $lang["display_banners_pos_title19"], "name" => $lang["display_banners_pos_title20"]),
          "article_top" => array("title" => $lang["display_banners_pos_title19"], "name" => $lang["display_banners_pos_title21"]),
          "article_bottom" => array("title" => $lang["display_banners_pos_title19"], "name" => $lang["display_banners_pos_title22"]),
          "add_ad" => array("title" => $lang["display_banners_pos_title23"], "name" => $lang["display_banners_pos_title24"]),    
        );
    }

   function bannersPositions($id_key=0){
    $option = array(); $return = "";

      $banners_positions = $this->positions();

      foreach($banners_positions AS $key => $array){
          if($id_key == $key){ $selected = 'selected=""'; }else{ $selected = ""; }
          $option[$array["title"]][] .= '<option value="'.$key.'" '.$selected.' >'.$array["name"].'</option>';
      }           

      if(count($option)>0){
          foreach($option as $group_name=>$option_val){
              $return .= '<optgroup label="'.$group_name.'">'.implode("",$option_val).'</optgroup>';
          }
      }
    return $return;        
   }




        
}


$Banners = new Banners();

?>