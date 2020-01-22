<?php

  defined('unisitecms') or exit();

  $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_database);
  $mysqli->set_charset($db_charset);
  if ($mysqli->connect_error) {
    include('files/response/404/404.php');
  } 

  function db_query($query){
    global $mysqli;
    $sql = $mysqli->query("$query"); 
    if ($sql->num_rows)
     {  
        return $sql->fetch_assoc();
     }else{
        return array();
     }      
  }

  function db_insert_update($query){
    global $mysqli;
    $sql = $mysqli->query("$query");
    return $sql;
    $mysqli->close();        
  }  

  function db_query_count($query){
    global $mysqli;
    $sql = $mysqli->query("$query"); 
    return $sql->num_rows;      
  }  

  function db_query_while($query, $findOne = ""){
    global $mysqli;
    $sql = $mysqli->query("$query"); 
    if ($sql->num_rows)
     {
        
        $array = array();
        while($res = $sql->fetch_assoc()){
          if(empty($findOne))  $array[] = $res;  else $array[] = $res[$findOne];
        };        
        
        return $array;
     }else{
        return array();
     }
        
  }  

?>