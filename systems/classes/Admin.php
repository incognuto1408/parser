<?php
/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */
 
class Admin{

	function getPages(){
	    global $settings;
	    $arr = array();
	      if($settings["array_pages"]){
	         $settings["array_pages"] = json_decode($settings["array_pages"],true);
	           foreach($settings["array_pages"] AS $pages=>$path){
	              $arr[$pages] = $path;
	           }
	         return $arr;  
	      }
	}

    function checkPages($page){
	   $array = $this->getPages();
	      if(isset($array[$page])){ 
	          return $array[$page]; 
	      }else{
	          return "/index/index"; 
	      }      
    } 

    function randColor(){
    	$arrays = array("blue", "red", "green", "black", "#990066" ,"#79553D", "#CED23A");
    	return  $arrays[mt_rand(0,6)];
    } 

	function setPrivileges($privileges){ 
	  if($privileges){
	    $exp = explode(",",$privileges);
	      if(count($exp)>0){  
	         foreach($exp AS $value){
	            $_SESSION["cp_".$value] = 1;
	         }
	      }
	  }
	}

	function lineReports(){
	  $result = db_query_while("SELECT * FROM uni_metrics group by date DESC limit 30");    
	    if(count($result) > 0){
	        
	        foreach($result AS $data_array){

	                $count_v = db_query("SELECT COUNT(*) as total FROM uni_metrics WHERE date = '{$data_array["date"]}'"); 
	                $count_b = db_query("SELECT COUNT(*) as total FROM uni_ads WHERE DATE(ads_datetime_add) = DATE('{$data_array["date"]}')"); 
	                $count_o = db_query("SELECT COUNT(*) as total FROM uni_orders WHERE DATE(date) = DATE('{$data_array["date"]}')");  
	                $count_s = db_query("SELECT COUNT(*) as total FROM uni_shops WHERE DATE(datetime_add) = DATE('{$data_array["date"]}')");                         
	                $dates[] = array("y"=>date('Y-m-d', strtotime($data_array["date"])),"count_v"=>$count_v["total"],"count_b"=>$count_b["total"],"count_o"=>$count_o["total"],"count_s"=>$count_s["total"]);            
	        }
	        
	    } else {
	        $dates = array();
	    }

	    return json_encode($dates);    
	}

    function addNotification($code = ""){
      global $lang;
      
         if($code == "ads"){

	         $sql = db_query("select * from uni_notifications where code='$code'");
	         if(count($sql) == 0){
	           db_insert_update("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES('{$lang["class_admin_title1"]}',NOW(),'$code','la la-thumb-tack','?route=board')");
	         }else{
	           db_insert_update("UPDATE uni_notifications SET count=count+1 WHERE id={$sql["id"]}");
	         }

         }elseif($code == "user"){

	         $sql = db_query("select * from uni_notifications where code='$code'");
	         if(count($sql) == 0){
	           db_insert_update("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES('{$lang["class_admin_title2"]}',NOW(),'$code','la la-user','?route=clients')");
	         }else{
	           db_insert_update("UPDATE uni_notifications SET count=count+1 WHERE id={$sql["id"]}");
	         }

         }elseif($code == "shop"){

	         $sql = db_query("select * from uni_notifications where code='$code'");
	         if(count($sql) == 0){
	           db_insert_update("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES('{$lang["class_admin_title3"]}',NOW(),'$code','la la-shopping-cart','?route=shops')");
	         }else{
	           db_insert_update("UPDATE uni_notifications SET count=count+1 WHERE id={$sql["id"]}");
	         }      

         }elseif($code == "buy"){

	         $sql = db_query("select * from uni_notifications where code='$code'");
	         if(count($sql) == 0){
	           db_insert_update("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES('{$lang["class_admin_title4"]}',NOW(),'$code','la la-money','?route=orders')");
	         }else{
	           db_insert_update("UPDATE uni_notifications SET count=count+1 WHERE id={$sql["id"]}");
	         }     

         }elseif($code == "complaint"){

	         $sql = db_query("select * from uni_notifications where code='$code'");
	         if(count($sql) == 0){
	           db_insert_update("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES('{$lang["class_admin_title5"]}',NOW(),'$code','la la-exclamation-triangle','?route=complaint')");
	         }else{
	           db_insert_update("UPDATE uni_notifications SET count=count+1 WHERE id={$sql["id"]}");
	         } 

         }elseif($code == "user-reviews"){

	         $sql = db_query("select * from uni_notifications where code='$code'");
	         if(count($sql) == 0){
	           db_insert_update("INSERT INTO uni_notifications(title,datetime,code,icon,link)VALUES('{$lang["class_admin_title6"]}',NOW(),'$code','la la-comment','?route=clients_reviews')");
	         }else{
	           db_insert_update("UPDATE uni_notifications SET count=count+1 WHERE id={$sql["id"]}");
	         }    

         }

    }

    function addLogs($text = ""){
      if($text)	db_insert_update("INSERT INTO uni_logs(datetime_add,logs)VALUES(NOW(),'".urlencode($text)."')");
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

	function order_count_num($num){
	  global $db_prefix;  
	    if($num == 1){
	       return (int)db_query_count("SELECT * FROM {$db_prefix}orders"); 
	    }elseif($num == 2){
	       return (int)db_query_count("SELECT * FROM {$db_prefix}orders WHERE status='ACCEPTED'");  
	    }elseif($num == 3){
	       return (int)db_query_count("SELECT * FROM {$db_prefix}orders WHERE status!='ACCEPTED'");  
	    }
	}

	function setMode(){
		db_insert_update("UPDATE uni_admin SET datetime_view = NOW() WHERE id={$_SESSION['cp_auth_id_user']}");
	}

	function order_count_sum($num){
	  global $db_prefix,$settings,$currency; 
	  $sum = 0; 
	    if($num == 1){
	       $sqli = db_query_while("SELECT * FROM {$db_prefix}orders WHERE status='ACCEPTED'");
	       if(count($sqli)>0){
	           foreach($sqli AS $res){
	               $sum += $res["price"];
	           }
	       } 
	    }elseif($num == 2){
	       $sqli = db_query_while("SELECT * FROM {$db_prefix}orders WHERE status!='ACCEPTED'");
	       if(count($sqli)>0){
	           foreach($sqli AS $res){
	               $sum += $res["price"];
	           }
	       }   
	    }
	    return number_format($sum,2,",",".").' '.$settings["currency_sign"];
	}

	function conversion_order(){
	     $count_order = db_query("SELECT COUNT(*) as total FROM uni_orders");
	     $count_order_buy = db_query("SELECT COUNT(*) as total FROM uni_orders WHERE status='ACCEPTED'");
	     if($count_order_buy["total"] && $count_order["total"]){
	        return round(($count_order_buy["total"] / $count_order["total"]) * 100,0);
	     }else{ return 0; }    
	}

	function load_stat_orders(){   
	$result = db_query_while("SELECT * FROM uni_orders group by date DESC limit 30");  
	    if(count($result) > 0){
	        
	        foreach($result AS $data_array){              
	            $count_order = db_query("SELECT COUNT(*) as total FROM uni_orders WHERE DATE(date) = '".date("Y-m-d",strtotime($data_array["date"]))."'");
	            $count_order_buy = db_query("SELECT COUNT(*) as total FROM uni_orders WHERE DATE(date) = '".date("Y-m-d",strtotime($data_array["date"]))."' AND status='ACCEPTED'");              
	                $data[] = array("y"=>date('Y-m-d', strtotime($data_array["date"])),"order"=>$count_order["total"],"buy"=>$count_order_buy["total"]);            
	        }
	        
	    } else {
	        $data = array();
	    }

	    return json_encode($data);     
	}
	function load_stat_orders_ads(){
	$result = db_query_while("SELECT * FROM uni_order_ads group by order_ads_datetime_add DESC limit 30");
	    if(count($result) > 0){

	        foreach($result AS $data_array){
	            $count_order = db_query("SELECT COUNT(*) as total FROM uni_order_ads WHERE DATE(order_ads_datetime_add) = '".date("Y-m-d",strtotime($data_array["order_ads_datetime_add"]))."'");
	            $count_order_1 = db_query("SELECT COUNT(*) as total FROM uni_order_ads WHERE DATE(order_ads_datetime_add) = '".date("Y-m-d",strtotime($data_array["order_ads_datetime_add"]))."' AND order_ads_status='0'");
	            $count_order_2 = db_query("SELECT COUNT(*) as total FROM uni_order_ads WHERE DATE(order_ads_datetime_add) = '".date("Y-m-d",strtotime($data_array["order_ads_datetime_add"]))."' AND order_ads_status='1'");
	            $count_order_3 = db_query("SELECT COUNT(*) as total FROM uni_order_ads WHERE DATE(order_ads_datetime_add) = '".date("Y-m-d",strtotime($data_array["order_ads_datetime_add"]))."' AND order_ads_status='2'");
	                $data[] = array("y"=>date('Y-m-d', strtotime($data_array["order_ads_datetime_add"])),"order"=>$count_order["total"],"buy"=>$count_order_1["total"],"buy1"=>$count_order_2["total"],"buy2"=>$count_order_3["total"]);
	        }

	    } else {
	        $data = array();
	    }

	    return json_encode($data);
	}

	function precess_status($status){
	global $lang;    
	   if($status == 1){
	      return '<span class="label label-warning">'.$lang["class_admin_title7"].'</span><br/>';
	   }elseif($status == 2){
	      return '<span class="label label-primary">'.$lang["class_admin_title8"].'</span><br/>';
	   }elseif($status == 3){
	      return '<span class="label label-danger">'.$lang["class_admin_title9"].'</span><br/>';
	   }else{
	      return '<span class="label label-success">'.$lang["class_admin_title10"].'</span><br/>'; 
	   } 
	}

	function watermark_pos(){
		global $settings, $main_image_watermark;

        if(file_exists($_SERVER["DOCUMENT_ROOT"].$main_image_watermark.$settings["watermark"])){

          list($w,$h,$t) = getimagesize($_SERVER["DOCUMENT_ROOT"].$main_image_watermark.$settings["watermark"]);
          $w = $w/2; $h = $h/2;
          if($settings["watermark_pos"] == 1){
             $pos = 'style="left:15px;top:15px"';
          }elseif($settings["watermark_pos"] == 2){
             $pos = 'style="right:15px;top:15px"';
          }elseif($settings["watermark_pos"] == 3){
             $pos = 'style="left:15px;bottom:15px"';
          }elseif($settings["watermark_pos"] == 4){
             $pos = 'style="right:15px;bottom:15px"';
          }elseif($settings["watermark_pos"] == 5){
             $pos = 'style="left:50%;top:50%; margin-left:-'.$w.'px; margin-top:-'.$h.'px"';
          }else{
             $pos = 'style="left:50%;top:50%; margin-left:-'.$w.'px; margin-top:-'.$h.'px"';
          }  

        }

        echo $pos;

	}

	function manager_filesize($filesize)
	{

	   if($filesize > 1024)
	   {
	       $filesize = ($filesize/1024);
	       if($filesize > 1024)
	       {
	            $filesize = ($filesize/1024);
	           if($filesize > 1024)
	           {
	               $filesize = ($filesize/1024);
	               $filesize = round($filesize, 1);
	               return $filesize." Gb";       
	           }
	           else
	           {
	               $filesize = round($filesize, 1);
	               return $filesize." Mb";   
	           }       
	       }
	       else
	       {
	           $filesize = round($filesize, 1);
	           return $filesize." Kb";   
	       }  
	   }
	   else
	   {
	       $filesize = round($filesize, 1);
	       return $filesize." byte";   
	   }
	}

	function manager_total_size(){
	    $dir = $_SERVER['DOCUMENT_ROOT']."/files/media/manager/";
	    if(is_dir($dir)){
	    	$name = scandir($dir);
	        for($i=2; $i<=(sizeof($name)-1); $i++) {
	           if(is_file($dir.$name[$i]) && $name[$i] != '.'){ 
	            $total += filesize($dir.$name[$i]);
	           }
	        }
	      return get_filesize($total);  
	    }     
	}

	function manager_count_file(){
	    $dir = $_SERVER['DOCUMENT_ROOT']."/files/media/manager/";
	    if(is_dir($dir)){
	    	$name = scandir($dir);
	        for($i=2; $i<=(sizeof($name)-1); $i++) {
	           if(is_file($dir.$name[$i]) && $name[$i] != '.'){ 
	            $total += 1;
	           }
	        }
	      return (int)$total;  
	    }     
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

	function warningSystems(){
	global $settings,$lang;
    
    if($settings["warning_systems"]){

	  if(file_exists($_SERVER["DOCUMENT_ROOT"]."robots.txt")){
			$content = getFile($_SERVER["DOCUMENT_ROOT"]."robots.txt");
            
            if($content){
	            $result = explode(PHP_EOL, $content);
                if(count($result) > 0){
                	foreach ($result as $key => $value) {
                		if(trim($value) == "Disallow: /"){
		 	                $warning = '
								  <div class="alert alert-warning alert-dissmissible fade show" role="alert">
								  <strong>'.$lang["class_admin_title11"].'</strong> '.$lang["class_admin_title12"].' <strong>Disallow: /</strong> '.$lang["class_admin_title13"].' <a href="?route=settings&tab=robots">robots.txt</a>
								  </div>
			                ';
			                break;               			
                		}
                	}
                }
		    }

	  }
       
       if(!$settings["site_name"] || !$settings["title"]){
       $warning .= '
				  <div class="alert alert-warning alert-dissmissible fade show" role="alert">
				    <strong>'.$lang["class_admin_title11"].'</strong> '.$lang["class_admin_title14"].'
				  </div>
       ';
       }
        
       if(!$settings["api_id_sms"]){
       $warning .= '
				  <div class="alert alert-warning alert-dissmissible fade show" role="alert">
				    <strong>'.$lang["class_admin_title11"].'</strong> '.$lang["class_admin_title15"].' <a href="/admin/?route=settings&tab=integrations" >'.$lang["class_admin_title16"].'</a>
				  </div>
       ';
       }

       if(!$settings["cron_sitemap_load_time"]){
       $warning .= '
				  <div class="alert alert-warning alert-dissmissible fade show" role="alert">
				    <strong>'.$lang["class_admin_title11"].'</strong> '.$lang["class_admin_title18"].'</a>
				  </div>
       ';
       }

       if(!$settings["cron_notifications_load_time"]){
       $warning .= '
				  <div class="alert alert-warning alert-dissmissible fade show" role="alert">
				    <strong>'.$lang["class_admin_title11"].'</strong> '.$lang["class_admin_title19"].'</a>
				  </div>
       ';
       }

       if(!$settings["cron_backup_db_load_time"]){
       $warning .= '
				  <div class="alert alert-warning alert-dissmissible fade show" role="alert">
				    <strong>'.$lang["class_admin_title11"].'</strong> '.$lang["class_admin_title20"].'</a>
				  </div>
       ';
       }

       $warning .= '
				  <div class="alert alert-info alert-dissmissible fade show" role="alert">
				  '.$lang["class_admin_title17"].'</div>
       ';

       return $warning . '<br>';

       }

	}

	function dir_size($dir) {
	   $totalsize=0;
	   if ($dirstream = @opendir($dir)) {
	      while (false !== ($filename = readdir($dirstream))) {
	         if ($filename!="." && $filename!=".."){
	            if (is_file($dir."/".$filename)) $totalsize+=filesize($dir."/".$filename);
	            if (is_dir($dir."/".$filename)) $totalsize+=$this->dir_size($dir."/".$filename);
	         }
	      }
	   }
	   closedir($dirstream);
	   return $totalsize;
	}

	function adminRole($id = 0){
	global $lang;
		$set = array(1 => $lang["display_admin_title12"], 2 => $lang["display_admin_title13"], 3 => $lang["display_admin_title14"], 4 => $lang["display_admin_title15"], 5 => $lang["display_admin_title16"], 6 => $lang["display_admin_title17"], 7 => $lang["display_admin_title18"]);
        if($id){
        	return $set[$id];
        }else{
        	return $set;
        }
	}

}

$Admin = new Admin();

?>