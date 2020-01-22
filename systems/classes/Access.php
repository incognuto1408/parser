<?php
/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */
 
class Access{
  
    function check(){
    	global $settings;

    	if($settings["access_site"] == "0"){

            if($settings["access_allowed_ip"]){
            	$explode = explode(",",$settings["access_allowed_ip"]);
              foreach ($explode as $key => $value) {
                $access_allowed_ip[] = trim($value);
              }
            }else{
            	$access_allowed_ip = array();
            }
            
            if($settings["access_action"] == "text"){

               if(!in_array($_SERVER["REMOTE_ADDR"], $access_allowed_ip)){	

                  include("{$_SERVER["DOCUMENT_ROOT"]}/files/response/access/locked.php");
                  exit();

               }

            }elseif($settings["access_action"] == "redirect"){

               if(!in_array($_SERVER["REMOTE_ADDR"], $access_allowed_ip)){	

                   if($settings["access_redirect_link"]) header("Location: ".$settings["access_redirect_link"]);

               }

            }

    	}

    }  
   
}

$Access = new Access();

?>