<?php

class CategoryBoard{
   
    function alias($category_chain, $city = true){
       global $Geo; 
       if($city == true){
         if($Geo->getGeo()) return URL.$Geo->getGeo()."/".$category_chain; else return URL.$category_chain; 
       }else{
         if($Geo->getGeo()) return URL.$category_chain; else return URL.$category_chain;
       }
    }

    function allMain(){
      global $array_data;
       $getCategories = $this->getCategories("where category_board_visible=1");

       if($array_data["categories_board"]["category_board_id"]){
          $id = (int)$array_data["categories_board"]["category_board_id"];
       }else{
          $id = 0;
       }

       if (isset($getCategories["category_board_id_parent"][$id])) {
          foreach ($getCategories["category_board_id_parent"][$id] as $key => $value) {
             $return[] = $value['category_board_name'];
          }
          return mb_strtolower(implode(", ",$return), "UTF-8");
       }else{
           if (isset($getCategories["category_board_id"][$id])) {
              return mb_strtolower($getCategories["category_board_id"][$id]["category_board_name"], "UTF-8");
           }else{
              return "";
           }
       }
    }

    function getCategories($query = "", $lang = true){
      global $Cashed;
      
        $key = "SELECT *, (select COUNT(ads_id) from uni_ads where ads_id_cat = uni_category_board.category_board_id) as count_ads FROM uni_category_board $query ORDER By category_board_id_position ASC";

        if($lang == true){
 
          $lang_key = lang();

        }else{
          
          $lang_key = "";

        }

        $cats = $Cashed->get($key . $lang_key,"categories_board");

        if($cats !== false){
            return $cats;
        }else{
            $sql = db_query_while($key);
            if (count($sql)>0) { 
                $cats = array();                            
                  foreach($sql AS $result){

                  if($lang){
                    $multilanguage_tables = multilanguage_tables(array("id_content" => $result['category_board_id'], "table_name" => "uni_category_board"));
                    $result["category_board_name"] = !empty($multilanguage_tables['lang_name']) ? urldecode($multilanguage_tables['lang_name']) : $result['category_board_name'];
                    $result["category_board_title"] = !empty($multilanguage_tables['lang_title']) ? urldecode($multilanguage_tables['lang_title']) : $result['category_board_title'];
                    $result["category_board_description"] = !empty($multilanguage_tables['lang_description']) ? urldecode($multilanguage_tables['lang_description']) : $result['category_board_description'];
                    $result["category_board_text"] = !empty($multilanguage_tables['lang_text']) ? urldecode($multilanguage_tables['lang_text']) : $result['category_board_text'];                      
                  }
 
                      $cats['category_board_id_parent'][$result['category_board_id_parent']][$result['category_board_id']] =  $result;
                      $cats['category_board_id'][$result['category_board_id']]['category_board_id_parent'] =  $result['category_board_id_parent'];
                      $cats['category_board_id'][$result['category_board_id']]['category_board_name'] =  $result['category_board_name'];
                      $cats['category_board_id'][$result['category_board_id']]['category_board_title'] =  $result['category_board_title'];
                      $cats['category_board_id'][$result['category_board_id']]['category_board_description'] =  $result['category_board_description'];
                      $cats['category_board_id'][$result['category_board_id']]['category_board_image'] =  $result['category_board_image'];
                      $cats['category_board_id'][$result['category_board_id']]['category_board_text'] =  urldecode($result['category_board_text']);
                      $cats['category_board_id'][$result['category_board_id']]['category_board_alias'] =  $result['category_board_alias'];
                      $cats['category_board_id'][$result['category_board_id']]['category_board_id'] =  $result['category_board_id'];  
                      $cats['category_board_id'][$result['category_board_id']]['category_board_category_chain'] =  $result['category_board_category_chain'];
                      $cats['category_board_id'][$result['category_board_id']]['category_board_shop_id_cats'] =  $result['category_board_shop_id_cats'];  
                      $cats['category_board_id'][$result['category_board_id']]['count_ads'] =  $result['count_ads'];          
                  }  
            }            
            $Cashed->set($cats,$key . $lang_key,"categories_board");
            return $cats;
        }
            
    }

    function aliasBuild($cur_id=0){

      $getCategories = $this->getCategories();

      if($cur_id){

        if($getCategories["category_board_id"][$cur_id]['category_board_id_parent']!=0){ 
            $out .= $this->aliasBuild($getCategories["category_board_id"][$cur_id]['category_board_id_parent'])."/";            
        }
        $out .= $getCategories["category_board_id"][$cur_id]['category_board_alias'];

        return $out;
      }else{ return ""; } 
               
    }
    
    function outParentCategory($tpl, $tpl_parent = "", $sep = ""){
      global $no_image,$image_category, $array_data,$Ads;
      $return = "";
      $parent = array();
      $getCategories = $this->getCategories("where category_board_visible=1");
      
      if($array_data["categories_board"]["category_board_id"]){

        if (isset($getCategories["category_board_id_parent"][$array_data["categories_board"]["category_board_id"]])) {
            foreach ($getCategories["category_board_id_parent"][$array_data["categories_board"]["category_board_id"]] as $parent_value) {
              
               $parent[] = replace(array("{PARENT_LINK}", "{PARENT_IMAGE}", "{PARENT_NAME}", "{COUNT_AD}"),array($this->alias($parent_value["category_board_category_chain"]),Exists($image_category,$parent_value["category_board_image"],$no_image),$parent_value["category_board_name"], $Ads->countCategoryAds($parent_value["category_board_id"])),$tpl_parent);

               $return .=  replace(array("{PARENT_CATEGORY}"),array(implode($sep,$parent)),$tpl);
               $parent = array();

            }
        }else{

          $id_parent = $getCategories["category_board_id"][$array_data["categories_board"]["category_board_id_parent"]];
          if(isset($getCategories["category_board_id_parent"][$id_parent["category_board_id"]])){
            foreach ($getCategories["category_board_id_parent"][$id_parent["category_board_id"]] as $parent_value) {

              if($parent_value["category_board_id"] == $array_data["categories_board"]["category_board_id"]){
                $active = 'class="active"';
              }else{
                $active = '';
              }
              
               $parent[] = replace(array("{PARENT_LINK}", "{PARENT_IMAGE}", "{PARENT_NAME}", "{ACTIVE}", "{COUNT_AD}"),array($this->alias($parent_value["category_board_category_chain"]),Exists($image_category,$parent_value["category_board_image"],$no_image),$parent_value["category_board_name"],$active, $Ads->countCategoryAds($parent_value["category_board_id"])),$tpl_parent);

               $return .=  replace(array("{PARENT_CATEGORY}"),array(implode($sep,$parent)),$tpl);
               $parent = array();

            }
          }else{

            foreach ($getCategories["category_board_id_parent"][0] as $parent_value) {

              if($parent_value["category_board_id"] == $array_data["categories_board"]["category_board_id"]){
                $active = 'class="active"';
              }else{
                $active = '';
              }
              
               $parent[] = replace(array("{PARENT_LINK}", "{PARENT_IMAGE}", "{PARENT_NAME}", "{ACTIVE}", "{COUNT_AD}"),array($this->alias($parent_value["category_board_category_chain"]),Exists($image_category,$parent_value["category_board_image"],$no_image),$parent_value["category_board_name"],$active, $Ads->countCategoryAds($parent_value["category_board_id"])),$tpl_parent);

               $return .=  replace(array("{PARENT_CATEGORY}"),array(implode($sep,$parent)),$tpl);
               $parent = array();

            }

          }
        }                 
       return $return;

      }else{
          
          if(isset($getCategories["category_board_id_parent"][0])){
            foreach ($getCategories["category_board_id_parent"][0] as $parent_value) {
              
               $parent[] = replace(array("{PARENT_LINK}", "{PARENT_IMAGE}", "{PARENT_NAME}", "{COUNT_AD}"),array($this->alias($parent_value["category_board_category_chain"]),Exists($image_category,$parent_value["category_board_image"],$no_image),$parent_value["category_board_name"], $Ads->countCategoryAds($parent_value["category_board_id"])),$tpl_parent);

               $return .=  replace(array("{PARENT_CATEGORY}"),array(implode($sep,$parent)),$tpl);
               $parent = array();

            }
           return $return;
          }

      }

    }

    function outMainCategory($tpl, $tpl_parent = "", $sep = ""){
      global $no_image,$image_category,$Ads;
      $return = "";
      $parent = array();
      $getCategories = $this->getCategories("where category_board_visible=1");

        if (isset($getCategories["category_board_id_parent"][0])) {
            foreach ($getCategories["category_board_id_parent"][0] as $value) {
              
               if($getCategories["category_board_id_parent"][$value["category_board_id"]] && $tpl_parent){
                 foreach (array_slice($getCategories["category_board_id_parent"][$value["category_board_id"]], 0, 6) as $parent_value) {
                   $parent[] = replace(array("{PARENT_LINK}", "{PARENT_IMAGE}", "{PARENT_NAME}"),array($this->alias($parent_value["category_board_category_chain"]),Exists($image_category,$parent_value["category_board_image"],$no_image),$parent_value["category_board_name"]),$tpl_parent);
                 }
               }
           
               $return .=  replace(array("{LINK}", "{IMAGE}", "{NAME}", "{PARENT_CATEGORY}"),array($this->alias($value["category_board_alias"]),Exists($image_category,$value["category_board_image"],$no_image),$value["category_board_name"], implode($sep,$parent)),$tpl);
               $parent = array();
            }
        }                 
      return $return;
    }

    function idsBuild($parent_id=0){

      $getCategories = $this->getCategories(); 
                       
        if(isset($getCategories['category_board_id_parent'][$parent_id])){

              foreach($getCategories['category_board_id_parent'][$parent_id] as $cat){
                
                $ids .= ','.$cat['category_board_id'];                                                         
                $ids .= $this->idsBuild($cat['category_board_id']);
                                                                    
              }

        }
      return $ids;
    }
    

   function viewCategory($id=0){
    if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false){
      if($id){    
          if(!isset($_SESSION["view-category-ads"][$id])){
            db_insert_update("UPDATE uni_category_board SET category_board_count_view=category_board_count_view+1,category_board_datetime_view=NOW() WHERE category_board_id=$id"); 
            $_SESSION["view-category-ads"][$id] = 1;
          }  
      } 
    }   
   }

    function breadcrumb($id=0,$tpl="",$sep=""){

      $getCategories = $this->getCategories("where category_board_visible=1");

      if($id){

        if($getCategories["category_board_id"][$id]['category_board_id_parent']!=0){
            $return[] = $this->breadcrumb($getCategories["category_board_id"][$id]['category_board_id_parent'],$tpl,$sep);  
        }

        $return[] = replace(array("{LINK}", "{NAME}"),array($this->alias($getCategories["category_board_id"][$id]["category_board_category_chain"]),$getCategories["category_board_id"][$id]['category_board_name']),$tpl);

        return implode($sep,$return);

      } 
               
    }
    
    function outCategory($id_cat=0,$parent_id=0,$margin=""){
       global $array_formation_cat; 

         if(count($array_formation_cat["category_board_id_parent"])>0){                                      
                if(isset($array_formation_cat["category_board_id_parent"][$parent_id])){
                        foreach($array_formation_cat["category_board_id_parent"][$parent_id] as $cat){
                          $selected = ""; 
                          if($id_cat == $cat["category_board_id"]){  
                            $selected = 'selected="selected"';
                          }                                                   
                            $tree .= '<option value="' . $cat["category_board_id"] . '"  '.$selected.' >' . $margin . $cat['category_board_name'] . '</option>';
                            $margins = $margin.'--';
                            $tree .= $this->outCategory($id_cat,$cat["category_board_id"],$margins);                                                                    
                        }
                    
                }
                else return null;
                return $tree;
         }                 

    }
    

       
   
}

// ======= Инициализация класса

$CategoryBoard = new CategoryBoard(); 

?>