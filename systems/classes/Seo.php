<?php
 Class Seo{
    
   function replace($array = array() ){
     global $array_data,$Geo,$settings,$CategoryBoard,$CategoryBlog,$Shop;
     
   if(count($array) > 0){

    $geolocation = $Geo->detect($_SERVER['REMOTE_ADDR'], array("city"=>$_SESSION["geo"]["change"]["city"]["city_name"],"region"=>$_SESSION["geo"]["change"]["region"]["region_name"],"country"=>$_SESSION["geo"]["change"]["country"]["country_name"],"city_id"=>$_SESSION["geo"]["change"]["city"]["city_id"],"region_id"=>$_SESSION["geo"]["change"]["region"]["region_id"],"country_id"=>$_SESSION["geo"]["change"]["country"]["country_id"]));


    if($geolocation["city"]){
       $geo_name = $geolocation["city"];
    }elseif($geolocation["region"]){
       $geo_name = $geolocation["region"];
    }elseif($geolocation["country"]){
       $geo_name = $geolocation["country"];
    }


    $array1 = array(
      "{domen}",
      "{url}",
      "{country}",
      "{city}",
      "{region}",
      "{site_name}",
      "{board_main_categories}",
      "{board_category_name}",
      "{board_category_title}",
      "{geo}",
      "{board_category_meta_desc}",
      "{board_category_text}",
      "{ad_title}",
      "{ad_meta_desc}",
      "{ad_city}",
      "{ad_region}",
      "{ad_country}",
      "{blog_main_categories}",
      "{blog_category_name}",
      "{blog_category_title}",
      "{blog_category_meta_desc}",
      "{blog_category_text}",      
      "{article_title}",
      "{article_meta_desc}",
      "{shops_main_categories}",
      "{shops_category_name}",
      "{shops_category_title}",
      "{shops_category_meta_desc}",
      "{shops_category_text}", 
    );

    $array2 = array(
      $_SERVER["SERVER_NAME"],
      URL,
      $geolocation["country"],
      $geolocation["city"],
      $geolocation["region"],
      $settings["site_name"],
      $CategoryBoard->allMain(),
      $array_data["categories_board"]["category_board_name"],
      $array_data["categories_board"]["category_board_title"],
      $geo_name,
      urldecode($array_data["categories_board"]["category_board_description"]),
      urldecode($array_data["categories_board"]["category_board_text"]),
      $array_data["ads"]["ads_title"],
      urldecode($array_data["ads"]["ads_description"]),
      $array_data["ads"]["city_name"],
      $array_data["ads"]["region_name"],
      $array_data["ads"]["country_name"],
      $CategoryBlog->allMain(),
      $array_data["categories_blog"]["name"],
      $array_data["categories_blog"]["title"],
      urldecode($array_data["categories_blog"]["description"]),
      urldecode($array_data["categories_blog"]["text"]),
      urldecode($array_data["article"]["title"]),
      urldecode($array_data["article"]["description"]),
      $Shop->allMain(),
      $array_data["categories_shops"]["name"],
      $array_data["categories_shops"]["title"],
      urldecode($array_data["categories_shops"]["description"]),
      urldecode($array_data["categories_shops"]["text"])  
    );    
    
      if(!empty($array["page"])){    
        $sql = db_query("SELECT *, (select value from uni_multilanguage where field='meta_title' and table_name='uni_seo' and lang='".lang()."' and id_content=uni_seo.id) as lang_title, (select value from uni_multilanguage where field='meta_desc' and table_name='uni_seo' and lang='".lang()."' and id_content=uni_seo.id) as lang_desc, (select value from uni_multilanguage where field='meta_key' and table_name='uni_seo' and lang='".lang()."' and id_content=uni_seo.id) as lang_key, (select value from uni_multilanguage where field='text' and table_name='uni_seo' and lang='".lang()."' and id_content=uni_seo.id) as lang_text, (select value from uni_multilanguage where field='h1' and table_name='uni_seo' and lang='".lang()."' and id_content=uni_seo.id) as lang_h1, (select value from uni_multilanguage where field='h2' and table_name='uni_seo' and lang='".lang()."' and id_content=uni_seo.id) as lang_h2, (select value from uni_multilanguage where field='h3' and table_name='uni_seo' and lang='".lang()."' and id_content=uni_seo.id) as lang_h3 FROM uni_seo WHERE page='".clear($array["page"])."'");
          if(count($sql)>0){

            if($sql["lang_title"]){$sql["meta_title"] = $sql["lang_title"];} 
            if($sql["lang_desc"]){$sql["meta_desc"] = $sql["lang_desc"];} 
            if($sql["lang_key"]){$sql["meta_key"] = $sql["lang_key"];}
            if($sql["lang_text"]){$sql["text"] = $sql["lang_text"];}
            if($sql["lang_h1"]){$sql["h1"] = $sql["lang_h1"];}
            if($sql["lang_h2"]){$sql["h2"] = $sql["lang_h2"];}
            if($sql["lang_h3"]){$sql["h3"] = $sql["lang_h3"];}

            if(!empty($sql[$array["field"]])){
                if(!$array["text"]){
                    return str_replace($array1, $array2, urldecode($sql[$array["field"]]));
                }else{
                    $replace = str_replace($array1, $array2, urldecode($sql[$array["field"]]));
                    return str_replace($array1, $array2, $replace);
                }
            }

          }
      }else{
         if($array["text"]) return str_replace($array1, $array2, urldecode($array["text"]));
      }


    }
   
   } 

    
 }
 
 $Seo = new Seo();
?>