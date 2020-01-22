<?php
/**
 * UniSite CMS
 *
 * @copyright 	2018 Artur Zhur
 * @link 		https://unisitecms.ru
 * @author 		Artur Zhur
 *
 */

class Subscription{

    function add($array = array()){
        if(count($array) > 0){
        if($array["id_cat"]) $category = " and subscription_id_cat=".$array["id_cat"];
        $check = db_query("select * from uni_subscription where subscription_email='{$array["email"]}' and subscription_type='{$array["type"]}' $category");
          if(count($check) == 0){
             db_insert_update("INSERT INTO uni_subscription(subscription_email,subscription_datetime_add,subscription_ip,subscription_name,subscription_user_id,subscription_type,subscription_id_cat,	subscription_status,subscription_ad_cat_datetime_update)VALUES('".$array["email"]."',NOW(),'".clear($_SERVER["REMOTE_ADDR"])."','".$array["name"]."','".$array["user_id"]."','".$array["type"]."','".$array["id_cat"]."','".$array["status"]."',NOW())");
             return true;
          }else{
          	 return false;
          }          
        }      
    }
       
}

$Subscription = new Subscription(); 

?>