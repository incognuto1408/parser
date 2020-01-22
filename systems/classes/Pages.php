<?php

/**
 * UniSite CMS
 *
 * @copyright 	2018 Artur Zhur
 * @link 		https://unisitecms.ru
 * @author 		Artur Zhur
 *
 */

class Pages{
    
    function out_pages($code="", $class=""){ 
        global $Cashed;

        if($code){$code = "AND id_code = '$code'";}else{$code = "";}  

        $key = "SELECT *, (select value from uni_multilanguage where id_content = uni_pages.id and field='name' and table_name='uni_pages' and lang='".lang()."') as lang_name, (select value from uni_multilanguage where id_content = uni_pages.id and field='title' and table_name='uni_pages' and lang='".lang()."') as lang_title, (select value from uni_multilanguage where id_content = uni_pages.id and field='description' and table_name='uni_pages' and lang='".lang()."') as lang_description, (select value from uni_multilanguage where id_content = uni_pages.id and table_name='uni_pages' and field='text' and lang='".lang()."') as lang_text FROM uni_pages WHERE visible = '1' $code Order By id_position ASC";  
        $get = $Cashed->get($key,"services");
        
        if($get !== false){
            
            return $get;

        }else{

            $sql = db_query_while($key);     
             if(count($sql) > 0){               
                foreach($sql AS $result){   

                $result["name"] = !empty($result['lang_name']) ? urldecode($result['lang_name']) : $result['name'];
                $result["title"] = !empty($result['lang_title']) ? urldecode($result['lang_title']) : $result['title'];
                $result["seo_text"] = !empty($result['lang_description']) ? urldecode($result['lang_description']) : $result['seo_text'];
                $result["text"] = !empty($result['lang_text']) ? urldecode($result['lang_text']) : $result['text'];

                   $link = preg_match('/^(http|https|ftp):[\/]{2}/i', urldecode($result["alias"])) != "" ? urldecode($result["alias"]) : URL.$result["alias"]; 
                   $out .= '<li class="'.$class.'" ><a href="'.$link.'" >'.$result["name"].'</a></li>'; 

                };                
             }

            $Cashed->set($out,$key,"services");
            return $out; 

        }
    }    


    
}


$Pages = new Pages();

?>