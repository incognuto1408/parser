<?php

session_start();
define('unisitecms', true);
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/config.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/incognito1408.php");

if(isAjax() == true){

    $enter = clear($_POST["enter"]);
    $referrer = clear($_POST["referrer"]);
    $title = clear($_POST["title"]);

    //$geolocation = $Geo->detect($_SERVER['REMOTE_ADDR']);

    if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false){

        $check = db_query("SELECT ip,id FROM uni_metrics WHERE ip='".clear($_SERVER['REMOTE_ADDR'])."' limit 1");

        if(count($check) == 0 && empty($_SESSION["metrics_visits"]) && empty($_COOKIE["metrics_visits"])){

            db_insert_update("INSERT INTO uni_metrics(ip,date,page,referrer,user_agent,datetime_view)VALUES('".clear($_SERVER['REMOTE_ADDR'])."',NOW(),'".urlencode($enter)."','".urlencode($referrer)."','".urlencode($_SERVER['HTTP_USER_AGENT'])."',NOW())");

            $_SESSION["metrics_visits"] = $_SERVER['REMOTE_ADDR'];
            $_SESSION["metrics_visits_id"] = $mysqli->insert_id;
/*
            setcookie('metrics_visits',$_SERVER['REMOTE_ADDR'],time()+3600*24*31, "/");
            setcookie('metrics_visits_id',$mysqli->insert_id,time()+3600*24*31, "/");*/

        }else{

            if(count($check) >0){

                $_SESSION["metrics_visits_id"] =  $check["id"];

            }else{

                if(!empty($_COOKIE["metrics_visits"])){

                    $ip = clear($_COOKIE["metrics_visits"]);

                    if(!empty($_COOKIE["metrics_visits_id"])){
                        $_SESSION["metrics_visits_id"] = (int)$_COOKIE["metrics_visits_id"];
                    }

                }else{ $ip = clear($_SERVER['REMOTE_ADDR']); }

            }

            $check = db_query("SELECT ip,id FROM uni_metrics WHERE ip='$ip' OR id={$_SESSION["metrics_visits_id"]}");

            if(count($check) > 0){

                db_insert_update("UPDATE uni_metrics SET datetime_view=NOW(),page='".urlencode($enter)."' WHERE id={$check["id"]}");

            }else{

                db_insert_update("INSERT INTO uni_metrics(ip,date,page,referrer,user_agent,datetime_view)VALUES('".clear($_SERVER['REMOTE_ADDR'])."',NOW(),'".urlencode($enter)."','".urlencode($referrer)."','".urlencode($_SERVER['HTTP_USER_AGENT'])."',NOW())");

                $_SESSION["metrics_visits"] = $_SERVER['REMOTE_ADDR'];
                $_SESSION["metrics_visits_id"] = $mysqli->insert_id;
/*
                setcookie('metrics_visits',$_SERVER['REMOTE_ADDR'],time()+3600*24*31, "/");
                setcookie('metrics_visits_id',$mysqli->insert_id,time()+3600*24*31, "/");*/

            }

        }



    }


   /* if(empty($geolocation["city"]) || $geolocation["city"] == "undefined" || isset($_SESSION["geo"])){
        echo false;
    }else{*/
        echo "Ваш город ?";
    /*}*/


}

?>