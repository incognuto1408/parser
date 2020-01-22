<?php
/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */
 
class CurrencyBoard{
  
    function out($tpl = "",$currency = ""){
    	global $settings;
        if($tpl){
         $sql = db_query_while("select * from uni_currency where visible=1");
         if(count($sql) > 0){
         	foreach ($sql as $key => $value) {
              if($currency){
                if($value["code"] == $currency){
                    $active = 'selected=""';
                }else{ $active = ''; }
              }else{
                if($_SESSION["currency"]){
                    if($value["code"] == $_SESSION["currency"]){
                        $active = 'active';
                    }else{ $active = ''; }
                }
              }
         		$return .=  replace(array("{CODE}", "{SIGN}", "{NAME}", "{ACTIVE}"),array($value["code"], $value["sign"], $value["name"], $active),$tpl);
         	}
         }
       return $return;
      }
    } 

    function getCurrency(){
    $array = array();
        $sql = db_query_while("select * from uni_currency where visible=1");
        if(count($sql) > 0){
            foreach ($sql as $key => $value) {
                $array[$value["code"]] = $value;
            }
        }
      return $array;
    } 
   
    function currency_converter($price=0,$currency_from="",$currency_to="",$format=true,$converter=true){
         global $decimals,$dec_point,$thousands_sep,$getCurrency,$settings; 
         
         if(!$currency_from){
           $currency_from = $settings["currency_code"];
         }

          if($converter == true){
             if(!empty($currency_to) && $currency_to != $currency_from){

                $currency = $currency_to;

                if(isset($getCurrency[$currency_from]["price"]) && isset($getCurrency[$currency]["price"])){
                   $course = round($getCurrency[$currency_from]["price"],6) / round($getCurrency[$currency]["price"],6);
                }else{
                   $course = 1;
                } 

                 $price = $price * $course; 

             }else{
               $currency = $currency_from;
             }
          }else{
            $currency = $currency_from;
          } 
                
          if($format==true){
            return number_format($price,$decimals,$dec_point,$thousands_sep)." ".$getCurrency[$currency]["sign"];  
          }else{
            return $price;
          }
               
    }

    function currency_deconverter($price=0,$currency_from="",$currency_to=""){
         global $decimals,$dec_point,$thousands_sep,$getCurrency,$settings; 
         
           if(!empty($currency_from) && $currency_from != $currency_to){

              if(isset($getCurrency[$currency_to]["price"])){
                 $course = round($getCurrency[$currency_to]["price"],6);
              }else{
                 $course = 1;
              } 

              return  $course; 

           }else{
             return $price;
           } 
                        
    }



}

$CurrencyBoard = new CurrencyBoard();
$getCurrency = $CurrencyBoard->getCurrency();

?>