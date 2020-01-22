<?php
/**
 * UniSite CMS
 *
 * @copyright 	2018 Artur Zhur
 * @link 		https://unisitecms.ru
 * @author 		Artur Zhur
 *
 */

class Profile{

   function getData($id_user){  
     global $Cashed;
     
     $key = "SELECT *,(select city_name from uni_city where city_id = uni_clients.clients_city_id) as city_name FROM uni_clients WHERE clients_id='".intval($id_user)."'";
     $data = $Cashed->get($key,"clients");

     if($data !== false){
        return $data;
     }else{
       $sql = db_query($key);
       $Cashed->set($sql,$key,"clients");
       return $sql;
     }

   }
    function load_stat_all_orders()
    {



        $res_all_ads = db_query_while("SELECT * FROM uni_ads WHERE ads_id_user='" . $_SESSION['profile']['id'] . "'");
        $result_phone = array();
        foreach ($res_all_ads as $key => $array_data){
            $sql = db_query_while("SELECT * FROM uni_ads_click_phone WHERE ads_click_phone_id_ads='" . $array_data['ads_id'] . "'");
            if(count($sql) > 0) {
                foreach ($sql as $item) {
                    $result_phone[] = $item;
                }
            }
        }
        $cc = (count($result_phone) - 1);
        $array_tmp = $result_phone;
        for($i = 0;$i < $cc; $i++){
            for($j = $i;$j < $cc; $j++){
                if(date("Y-m-d",strtotime($result_phone[$i]['ads_click_phone_datetime_add'])) == date("Y-m-d",strtotime($result_phone[$j+1]['ads_click_phone_datetime_add']))){
                    unset($array_tmp[$j+1]);
                }
            }

        }
        function mysort($a, $b) {
            return strtotime($b['ads_click_phone_datetime_add']) - strtotime($a['ads_click_phone_datetime_add']);
        }

        usort($result_phone, 'mysort');
        $sql_phone = "";
        for($i = 0;$i < count($result_phone); $i++){
            $sql_phone .= " OR ads_click_phone_id = '".$result_phone[$i]['ads_click_phone_id']."'";
        }
        $result_phone = array_values($array_tmp);

        $sql_phone = substr($sql_phone, 4);
        $data = array();
        if (count($result_phone) > 0) {
            foreach ($result_phone AS $data_array) {
                $count_order = db_query("SELECT COUNT(*) as total FROM uni_ads_click_phone WHERE date(ads_click_phone_datetime_add)='" . date("Y-m-d", strtotime($data_array["ads_click_phone_datetime_add"]))."' AND ($sql_phone) group by DATE(ads_click_phone_datetime_add) DESC");
                $data[] = array("y" => date('Y-m-d', strtotime($data_array["ads_click_phone_datetime_add"])), "phone" => $count_order["total"], "view" => "0");
            }
        }




        $result_view = array();
        foreach ($res_all_ads as $key => $array_data){
            $sql = db_query_while("SELECT * FROM uni_ads_view WHERE ads_view_id_ads='" . $array_data['ads_id'] . "'");
            if(count($sql) > 0) {
                foreach ($sql as $item) {
                    $result_view[] = $item;
                }
            }
        }
        $cc = (count($result_view) - 1);
        $array_tmp = $result_view;
        for($i = 0;$i < $cc; $i++){
            for($j = $i;$j < $cc; $j++){
                if(date("Y-m-d",strtotime($result_view[$i]['ads_view_datetime_add'])) == date("Y-m-d",strtotime($result_view[$j+1]['ads_view_datetime_add']))){
                    unset($array_tmp[$j+1]);
                }
            }

        }
        function mysort1($a, $b) {
            return strtotime($b['ads_view_datetime_add']) - strtotime($a['ads_view_datetime_add']);
        }

        usort($result_view, 'mysort1');
        $sql_view = "";
        for($i = 0;$i < count($result_view); $i++){
            $sql_view .= " OR ads_view_id = '".$result_view[$i]['ads_view_id']."'";
        }
        $sql_view = substr($sql_view, 4);
        $result_view = array_values($array_tmp);


        if (count($result_view) > 0) {
            foreach ($result_view AS $data_array) {
                $count_order = db_query("SELECT *,COUNT(*) as total FROM uni_ads_view WHERE date(ads_view_datetime_add)='" . date("Y-m-d", strtotime($data_array["ads_view_datetime_add"]))."' AND ($sql_view) group by DATE(ads_view_datetime_add) DESC");
                $date_v = date('Y-m-d', strtotime($data_array["ads_view_datetime_add"]));


                $flag = false;
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]['y'] == $date_v)
                        $flag = $i;
                }
                if ($flag !== false)
                    $data[$flag]['view'] = $count_order["total"];
                else
                    $data[] = array("y" => $date_v, "phone" => "0", "view" => $count_order["total"]);
            }
        }
        return json_encode($data);
    }
    function outCategoryDropdown($link="?route=orders_clients") {
        $cats['id'][0]['name'] = "Ожидающие";
        $cats['id'][1]['name'] = "Принятые";
        $cats['id'][2]['name'] = "Отмененные";
        if (isset($cats["id"])) {
            foreach ($cats["id"] as $key => $value) {
                if ((int)$_GET["type_orders"] == $key) {
                    $selected = 'active';
                } else {
                    $selected = "";
                }
                echo '<a class="dropdown-item ' . $selected . '" href="'.$link.'&type_orders=' . $key . '&type_orders_name=' . $value["name"] . '">' . $value["name"] . '</a>';
            }
        }
    }
   function mode(){
     if(!empty($_SESSION['profile']['id'])){
        db_insert_update("UPDATE uni_clients SET clients_datetime_view=NOW() WHERE clients_id='".intval($_SESSION['profile']['id'])."'");
     }
   }
   function coordinates_validate($coord){
/*       foreach ($coord as $key => $item){
           $coord[$key] = (string)$coord[$key];
           for ($i = (strlen($item) - 1); $i < 16; $i++){
               $coord[$key] = $coord[$key]."0";
           }
           $coord[$key] = (float)$coord[$key];
       }*/
       return $coord;
   }
    function get_user_info($id = false){
       if(!$id)
           $id = $_SESSION['profile']['id'];
               else
        return db_query("select * from users where id = '".$id."'");
    }

   function reg($array=array(), $auto = true){
    global $mysqli,$settings,$private,$Admin, $languages_content;

      if(!empty($array["email"])){
       $error = array();
       $sql = db_query("SELECT * FROM uni_clients WHERE clients_email='".clear($array["email"])."'");

          if(count($sql)==0){  
          
            if(empty($array["password"])){
                $error[] = "Поле `пароль` обязательно для заполнения";
            }else{
              $new_pass =  $array["password"];
            }

            $password =  password_hash($new_pass.$private, PASSWORD_DEFAULT); 

            if(!$array["type_person"]) $array["type_person"] = "user";     

            $insert = db_insert_update("INSERT INTO uni_clients(clients_pass,clients_email,clients_datetime_add,clients_datetime_view,clients_phone,clients_name,clients_ip,clients_name_company,clients_type_person,clients_city_id,clients_avatar,clients_social_identity,clients_tariff)VALUES('".$password."','".$array["email"]."',NOW(),NOW(),'".$array["phone"]."','".$array["name"]."','".clear($_SERVER["REMOTE_ADDR"])."','".$array["company_name"]."','".$array["type_person"]."','".intval($array["city_id"])."','".$array["avatar"]."','".$array["identity"]."','".$array["tariff"]."')");

            $insert_id = $mysqli->insert_id;
            
            if($insert){ 

             $hash = hash('sha256', $array["email"].$private);

             $data = array("{USER_EMAIL}"=>$array["email"],
                           "{USER_NAME}"=>$array["name"],
                           "{USER_PASS}"=>$new_pass,
                           "{ACTIVATION_LINK}"=>URL."profile?activation_hash=$hash&email=".$array["email"],
                           "{EMAIL_TO}"=>$array["email"]
                           );

              email_notification($data,"ACTIVATION_PROFILE");
              notifications("user", array("user_name" => $array["name"], "user_email" => $array["email"]));
              $Admin->addNotification("user");   

              $_SESSION['profile']['id'] = $insert_id;
              $_SESSION['profile']['fio'] = $array["name"];
              $_SESSION['profile']['email'] = $array["email"];


              $this->add_message_and_send(array("text"=>textReplace($languages_content["class-profile-title-2"]),"id_key"=>mt_rand(10000000, 90000000),"subject"=> $languages_content["class-profile-title-1"]." ".textReplace($languages_content["class-profile-title-2"]),"id_user"=>0,"id_user_to"=>$insert_id), false);

              if($auto == true) return array("id"=>$insert_id,"activation"=>0,"status" => "new");  else return array("id"=>0,"activation"=>0,"status" => "new");

            }else{ return array("id"=>0,"activation"=>0,"status" => "error"); }                                                                      
            }else{ return array("id"=>$sql["clients_id"],"activation"=>$sql["clients_activation"],"status" => "now"); }

      }else{
         return array("id"=>0,"status" => "error");
      }

   }
   

    function getTariffStatusPay($id_user,$id_tariff){
      global $db_prefix;  
        $sql = db_query("SELECT * FROM {$db_prefix}shop_tariff_orders WHERE (id_user='$id_user' AND id_tariff='$id_tariff') AND (status='ACCEPTED' OR price='0') AND (unix_timestamp(NOW()) <= unix_timestamp(datetime_pay)+count_day OR count_day='0')"); 
        if(count($sql)>0){
            return 1;
        }else{
            return 0;
        }
    }
    
    function add_message_and_send($array=array(), $mail = true){

      global $main_medium_image_ads, $no_image, $Ads;

      db_insert_update("INSERT INTO uni_messages(id_user,text,id_user_to,id_ads,datetime,id_parent,id_key)VALUES('".intval($array["id_user"])."','".urlencode($array["text"])."','".intval($array["id_user_to"])."','".intval($array["id_ads"])."',now(), '".$array["id_msg"]."', '".$array["id_key"]."')");  


      if($array["email"] && $mail){

        if($array["clients_datetime_view"]){
           if( (strtotime($array["clients_datetime_view"]) + 180) < time() ){
              $status = true;
           }else{
              $status = false;
           }
        }else{
           $status = true;
        }

        if( $status == true ){

           $param      = array("{USER_NAME}"=>$array["name"],
                               "{TEXT}"=>$array["text"],
                               "{EMAIL_TO}"=>$array["email"]); 

           email_notification($param,"NEW_MESSAGE",$array["subject"]);

        }

      }

      return true;

    }

    function outChatContacts($contacts, $msg_id = 0){
    global $settings,$main_image_logo;

      foreach ($contacts as $value){


        if($value["id_user_to"] == $_SESSION['profile']["id"]){
           $clients_list[$value["id_user"]] = $value;
        }elseif($value["id_user"] == $_SESSION['profile']["id"]){
           $clients_list[$value["id_user_to"]] = $value;
        }else{
           $clients_list[0] = $value;
        }
        

      }

      foreach ($clients_list as $id_user => $value){

        if($msg_id == $value["id_key"]){
           $active = 'active';
        }else{
           $active = '';
        }

        $status_view = db_query("select count(id) as result from uni_messages where id_key={$value["id_key"]} and status=0 and id_user_to={$_SESSION['profile']["id"]}");

          if($id_user != 0){
            $user = $this->getData($id_user);

            ?>
              <div class="profile-load-chat <?php if($status_view["result"] != 0){ echo 'new'; }else{ echo $active; } ?>" data-id="<?php echo $value["id_key"]; ?>" >
                  <div class="user-avatar-circle contact-card">

                  <?php if( (strtotime($user["clients_datetime_view"]) + 180) > time() ){ ?>
                    <span class="online badge-pulse-green-small"></span>
                  <?php }else{ ?>
                    <span class="online badge-pulse-red-small"></span>
                  <?php } ?>

                    
                    <div class="ava-img-rounded">
                    <span class="user-ava-cover" style="background: url(<?php echo $this->userAvatar($user["clients_avatar"]); ?>)"></span>
                    <img src="<?php echo $this->userAvatar($user["clients_avatar"]); ?>">
                    </div>

                  </div>
                  <div class="profile-contact-name" >
                  
                  <?php echo $user["clients_name"]; ?><br/><?php echo $user["clients_surname"]; ?>

                  </div>
              </div>
            <?php

          }else{
             
             ?>

              <div class="profile-load-chat <?php if($status_view["result"] != 0){ echo 'new'; }else{ echo $active; } ?>" data-id="<?php echo $value["id_key"]; ?>" >
                  <div class="user-avatar-circle contact-card">
                    
                    <div class="ava-img-rounded">
                    <span class="user-ava-cover" style="background: url(<?php echo URL.$main_image_logo.$settings["logo-image"]; ?>)"></span>
                    <img src="<?php echo URL.$main_image_logo.$settings["logo-image"]; ?>">
                    </div>

                  </div>
                  <div class="profile-contact-name" >
                  
                    <?php echo $settings["site_name"]; ?>

                  </div>
              </div>

             <?php
          }


       }

    }
    
    function profileBalance($array=array(),$action=""){
    global $settings,$main_image_logo,$decimals,$dec_point,$thousands_sep;    
       if(!empty($array["id_user"])){
        if($action == "+"){
          $check = db_query("select * from uni_history_balance where id_order={$array["id_order"]} AND id_user='{$array["id_user"]}'");  
          if(count($check) == 0){
             $res = db_insert_update("UPDATE uni_clients SET clients_balance=clients_balance+{$array["summa"]} WHERE clients_id='{$array["id_user"]}'"); 
             if($res){
                $this->profileAddHistoryBalance($array,"+");

                 $param      = array("{USER_NAME}"=>$array["name"],
                                     "{USER_EMAIL}"=>$array["email"],
                                     "{SUMMA}"=>number_format($array["summa"],$decimals,$dec_point,$thousands_sep).' '.$settings["currency_sign"],
                                     "{NOTE}"=>$array["note"],
                                     "{EMAIL_TO}"=>$array["email"]); 

                 email_notification($param,"BALANCE");

                return true;
             }else{ return false; }
          }else{ return false; }   
        }else{
            $res_balance = db_query("SELECT clients_balance FROM uni_clients WHERE clients_id='{$array["id_user"]}'");
         $res = db_insert_update("UPDATE uni_clients SET clients_balance=clients_balance-{$array["summa"]} WHERE clients_id='{$array["id_user"]}' AND uni_clients.clients_balance >= {$array["summa"]}");
             if($res && $res_balance['clients_balance'] >= $array["summa"]){
                $this->profileAddHistoryBalance($array,"-");

                 $param      = array("{USER_NAME}"=>$array["name"],
                                     "{USER_EMAIL}"=>$array["email"],
                                     "{SUMMA}"=>number_format($array["summa"],$decimals,$dec_point,$thousands_sep).' '.$settings["currency_sign"],
                                     "{NOTE}"=>$array["note"],
                                     "{EMAIL_TO}"=>$array["email"]); 

                 email_notification($param,"OFFS-BALANCE");

                return true;
             }else{ return false; }            
        } 
       }else{ return false; }   
    }
    
    function profileAddHistoryBalance($array=array(),$action=""){
     if(!empty($array["id_user"])){  

        db_insert_update("INSERT INTO uni_history_balance(id_user,summa,datetime,method,name,action)VALUES('{$array["id_user"]}','{$array["summa"]}',NOW(),'{$array["method"]}','{$array["title"]}','$action')"); 

     }else{
        return false;
     }
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

            /* $get_cashed = $Cashed->get($key_cashed,"ads");

             if($get_cashed !== false){
                 $results = $get_cashed;
             }else{
                 $results = db_query_while($key_cashed);
                 $Cashed->set($results,$key_cashed,"ads");
             }*/
            $results = db_query_while($key_cashed);
            $Cashed->set($results,$key_cashed,"ads");

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


    function getStatusSeller($id_user){
        $sql = db_query("SELECT clients_datetime_active FROM uni_clients WHERE clients_id='$id_user'");
        if(count($sql)>0){
            return $sql["clients_datetime_active"];
        }
        return "0000-00-00 00:00:00";
    }
    function addStatusSeller($array=array()){

        $status = $this->getStatusSeller($array["id_user"]);
        if (strtotime($status) > time()) {
            $count_day = date("Y-m-d H:i:s", strtotime($status) + ($array["count_day"] * 86400));
        }else {
            $count_day = date("Y-m-d H:i:s", time() + ($array["count_day"] * 86400));
        }


        db_insert_update("UPDATE uni_clients SET clients_datetime_active='$count_day' WHERE clients_id={$array["id_user"]}");
        $this->getQuery(" ", " limit 8");
        //$this->getQuery("", "  ORDER BY top DESC");
        return 1;
    }


    function addStatusSellerAdmin($array=array()){
        db_insert_update("UPDATE uni_clients SET clients_datetime_active='".$array["count_day"]."' WHERE clients_id={$array["id_user"]}");
        $this->getQuery(" ", " limit 8");
        return 1;
    }
    function getActivityPeriodStatus($id_status){
        if($id_status == 'all'){
            return db_query_while("SELECT * FROM uni_activity_period_status WHERE status_visible=1");
        }else {
            $res = db_query("SELECT * FROM uni_activity_period_status WHERE status_id='$id_status'");
            if ($res['status_visible']) {
                return array('status_count_day' => intval($res["status_count_day"]), 'status_visible' => intval($res["status_visible"]), 'status_price' => intval($res["status_price"]));
            }
        }
        return false;
    }
    function getBalance($id_user){   
       $sql = db_query("SELECT clients_balance FROM uni_clients WHERE clients_id='$id_user'"); 
       if(count($sql)>0){
          return intval($sql["clients_balance"]);
       }
       return 0;
    }
    
    function addTariffOrders($id_user,$id_tariff,$count_day=0,$price=0,$status=""){
      global $db_prefix;
       $sql = db_query("SELECT * FROM {$db_prefix}shop_tariff_orders WHERE id_user='$id_user' AND id_tariff='$id_tariff'"); 
       if(count($sql)==0){      
            db_insert_update("INSERT INTO {$db_prefix}shop_tariff_orders(id_user,id_tariff,datetime_pay,status,count_day,price,default_day)VALUES('$id_user','$id_tariff',NOW(),'$status','$count_day','$price','$count_day')");
       }else{
            db_insert_update("UPDATE {$db_prefix}shop_tariff_orders SET datetime_pay=NOW(),status='$status',count_day='$count_day',default_day='$count_day',price='$price' WHERE id_order='{$sql["id_order"]}'"); 
       }         
    } 
    

    function userAvatar($img){
      global $no_image_avatar,$big_images_avatar;  
        if(preg_match('/^(http|https|ftp):[\/]{2}/i', urldecode($img))){
            return urldecode($img);       
              }else{
            return Exists($big_images_avatar,$img,$no_image_avatar);
        }        
    }
    
   function outRating($rating){
       if($rating){
          if($rating == 1){
          return '
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
          ';
          }elseif($rating == 2){
          return '
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
          ';
          }elseif($rating == 3){
          return '
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
          ';
          }elseif($rating == 4){
          return '
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star-outline" ></span>
          ';            
          }elseif($rating == 5){
           return '
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
                 <span class="ion-ios-star" ></span>
          ';            
          }
       }else{
          return '
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
                 <span class="ion-ios-star-outline" ></span>
          ';
       }
   }

  function ratingBalls($id_user){
    global $Cashed;

  $key = "select *,(select count(*) from uni_clients_reviews where uni_clients_reviews.clients_reviews_id_user = {$id_user} AND uni_clients_reviews.clients_reviews_status = 1 AND uni_clients_reviews.clients_reviews_rating = 1) as rating_1,(select count(*) from uni_clients_reviews where uni_clients_reviews.clients_reviews_id_user = {$id_user} AND uni_clients_reviews.clients_reviews_status = 1 AND uni_clients_reviews.clients_reviews_rating = 2) as rating_2,(select count(*) from uni_clients_reviews where uni_clients_reviews.clients_reviews_id_user = {$id_user} AND uni_clients_reviews.clients_reviews_status = 1 AND uni_clients_reviews.clients_reviews_rating = 3) as rating_3,(select count(*) from uni_clients_reviews where uni_clients_reviews.clients_reviews_id_user = {$id_user} AND uni_clients_reviews.clients_reviews_status = 1 AND uni_clients_reviews.clients_reviews_rating = 4) as rating_4,(select count(*) from uni_clients_reviews where uni_clients_reviews.clients_reviews_id_user = {$id_user} AND uni_clients_reviews.clients_reviews_status = 1 AND uni_clients_reviews.clients_reviews_rating = 5) as rating_5 from uni_clients,uni_clients_reviews where uni_clients_reviews.clients_reviews_id_user = {$id_user}";

  $array = $Cashed->get($key,"clients_rating");

    if($array === false){

        $array = db_query($key);
        $Cashed->set($array,$key,"clients_rating");

    }

      $array["total_rating"] = $array["rating_1"] + $array["rating_2"] + $array["rating_3"] + $array["rating_4"] + $array["rating_5"];
      
      if($array["total_rating"]){
        $result = ($array["rating_1"]*1+$array["rating_2"]*2+$array["rating_3"]*3+$array["rating_4"]*4+$array["rating_5"]*5)/$array["total_rating"];
      }else{
        $result = 0;
      }

      if($result <= 5){
         return number_format($result, 0, '.', '');
      }else{
         return number_format(5, 0, '.', '');
      }


  }




         
}


$Profile = new Profile(); 

?>