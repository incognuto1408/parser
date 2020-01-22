<?php
/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */
 
class Cashed{

    function addChacheFavorit($name="", $content=""){
        global $time_cache_ads_favorite;
        setcookie($name, $content, time() + $time_cache_ads_favorite, "/");
        return true;
    }
    function getChacheFavorit($name=""){
        return $_COOKIE[$name];
    }
    function deleteChacheFavorit($name=""){
        return setcookie($name, '', time() - 10000, "/");
    }
    function issetChacheFavorit($name=""){
        return isset($_COOKIE[$name]);
    }
    function set($data,$key, $label){
      global $settings,$private;
      
      if($settings["cashed"]){
        
        if($data){
          $dir = $_SERVER["DOCUMENT_ROOT"]."temp/cash/".$label."/".md5($key.$private).".cash";

          if(!is_dir($_SERVER["DOCUMENT_ROOT"]."temp/cash/".$label."/")){ @mkdir($_SERVER["DOCUMENT_ROOT"]."temp/cash/".$label."/", 0777); }

          $content["time"] = time() + intval($settings["cashed_time"]);
          $content["data"] = $data;
            
            if(file_put_contents( $dir, serialize($content) )){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        } 
        
      }  

    }  

    function get($key, $label){
      global $settings,$private;

      $dir = $_SERVER["DOCUMENT_ROOT"]."temp/cash/".$label."/".md5($key.$private).".cash";
      
      if($settings["cashed"]){ 
        if(file_exists($dir)){
            $content = unserialize(ob_get($dir));
            if(time() <= $content["time"]){
                return $content["data"];
            }
            unlink($dir);
            return false;
        }else{
            return false;
        }
      }else{
         return false;
      }

    }
 
    function delete($label){

      $dir = $_SERVER["DOCUMENT_ROOT"]."temp/cash/".$label."/";
      deleteFolder( $dir );

    }

    function clear(){

      $dir = $_SERVER["DOCUMENT_ROOT"]."temp/cash/";
      deleteFolder( $dir );
      mkdir($_SERVER["DOCUMENT_ROOT"]."temp/cash/", 0777);

    }

}

$Cashed = new Cashed();

?>