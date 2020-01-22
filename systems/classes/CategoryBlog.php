<?php
/**
 * UniSite CMS
 *
 * @copyright 	2018 Artur Zhur
 * @link 		https://unisitecms.ru
 * @author 		Artur Zhur
 *
 */
 
class CategoryBlog{

    function allMain(){
      global $array_data;
       $getCategories = $this->getCategories("where visible=1");

       if($array_data["categories_blog"]["id"]){
          $id = (int)$array_data["categories_blog"]["id"];
       }else{
          $id = 0;
       }

       if (isset($getCategories["id_parent"][$id])) {
          foreach ($getCategories["id_parent"][$id] as $key => $value) {
             $return[] = $value["name"];
          }
          return mb_strtolower(implode(", ",$return), "UTF-8");
       }else{
           if (isset($getCategories["id"][$id])) {
              return mb_strtolower($getCategories["id"][$id]["name"], "UTF-8");
           }else{
              return "";
           }
       }
    }

    function outCategory($id_parent = 0, $level = 0) {
      global $Blog, $array_data;
      $getCategories = $this->getCategories("where visible=1");

        if (isset($getCategories["id_parent"][$id_parent])) {
            foreach ($getCategories["id_parent"][$id_parent] as $value) {
           
                if($id_parent != 0){ $visible = 'display: none;'; $plus=''; } else { $visible = ''; $plus=' <i class="la la-plus"></i>'; }

                if($array_data["categories_blog"]["id"] == $value["id"]){ $active = 'class="active"'; }else{ $active = ''; }
                
                echo '<li style="margin-left:'. ($level * 15) .'px;" parent-id="' . $value["id_parent"] . '" ><a href="'.$this->alias($value["category_chain"]).'" style="position: relative; cursor:pointer;" '.$active.' >'.$value["name"].'</a></li>';

                $level++;
                $this->outCategory($value["id"], $level);
                $level--;

            }
        }
    }

    function alias($category_chain){
    global $blog_prefix;
      return URL.$blog_prefix."/".$category_chain;
    }
   
    function getCategories($query = "", $lang = true){
      global $Cashed;
      
      if($lang){
        $key = "SELECT *, (select value from uni_multilanguage where id_content = uni_category_blog.id and field='name' and table_name='uni_category_blog' and lang='".lang()."' limit 1) as lang_name, (select value from uni_multilanguage where id_content = uni_category_blog.id and field='title' and table_name='uni_category_blog' and lang='".lang()."' limit 1) as lang_title, (select value from uni_multilanguage where id_content = uni_category_blog.id and field='description' and table_name='uni_category_blog' and lang='".lang()."' limit 1) as lang_description, (select value from uni_multilanguage where id_content = uni_category_blog.id and table_name='uni_category_blog' and field='text' and lang='".lang()."' limit 1) as lang_text, (select COUNT(id) from uni_articles where id_cat = uni_category_blog.id) as count_article FROM uni_category_blog $query ORDER By id_position ASC";
      }else{
        $key = "SELECT *, (select COUNT(id) from uni_articles where id_cat = uni_category_blog.id) as count_article FROM uni_category_blog $query ORDER By id_position ASC";
      }

        $cats = $Cashed->get($key,"categories_blog");

        if($cats !== false){
            return $cats;
        }else{
            $sql = db_query_while($key);
            if (count($sql)>0) { 
                $cats = array();                            
                  foreach($sql AS $result){

                      $result["name"] = !empty($result['lang_name']) ? urldecode($result['lang_name']) : $result['name'];
                      $result["title"] = !empty($result['lang_title']) ? urldecode($result['lang_title']) : $result['title'];
                      $result["description"] = !empty($result['lang_description']) ? urldecode($result['lang_description']) : $result['description'];
                      $result["text"] = !empty($result['lang_text']) ? urldecode($result['lang_text']) : $result['text'];
                    
                      $cats['id_parent'][$result['id_parent']][$result['id']] =  $result;
                      $cats['id'][$result['id']]['id_parent'] =  $result['id_parent'];
                      $cats['id'][$result['id']]['name'] =  $result['name'];
                      $cats['id'][$result['id']]['title'] =  $result['title'];
                      $cats['id'][$result['id']]['description'] =  $result['description'];
                      $cats['id'][$result['id']]['image'] =  $result['image'];
                      $cats['id'][$result['id']]['text'] =  urldecode($result['text']);
                      $cats['id'][$result['id']]['alias'] =  $result['alias'];
                      $cats['id'][$result['id']]['id'] =  $result['id'];  
                      $cats['id'][$result['id']]['category_chain'] =  $result['category_chain'];  
                      $cats['id'][$result['id']]['count_article'] =  $result['count_article'];       
                  }  
            }            
            $Cashed->set($cats,$key,"categories_blog");
            return $cats;
        }
            
    }

    function getCategoriesTutorial($query = ""){

            $sql = db_query_while("SELECT * FROM uni_training_categories $query");
            if (count($sql)>0) {
                $cats = array();
                foreach($sql AS $result){
                    $cats['id'][$result['training_categories_id']]['id'] =  $result['training_categories_id'];
                    $cats['id'][$result['training_categories_id']]['name'] =  $result['training_categories_title'];
                    $cats['id'][$result['training_categories_id']]['active'] =  $result['training_categories_active'];
                }
            }
            return $cats;

    }
    function aliasBuild($cur_id=0){

      $getCategories = $this->getCategories();

      if($cur_id){

        if($getCategories["id"][$cur_id]['id_parent']!=0){ 
            $out .= $this->aliasBuild($getCategories["id"][$cur_id]['id_parent'])."/";            
        }
        $out .= $getCategories["id"][$cur_id]['alias'];

        return $out;
      }else{ return ""; } 
               
    }

    function idsBuild($parent_id=0){

      $getCategories = $this->getCategories(); 
                       
        if(isset($getCategories['id_parent'][$parent_id])){

              foreach($getCategories['id_parent'][$parent_id] as $cat){
                
                $ids .= ','.$cat['id'];                                                         
                $ids .= $this->idsBuild($cat['id']);
                                                                    
              }

        }
      return $ids;
    }
    

   function viewCategory($id=0){
    if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false){
      if($id){    
          if(!isset($_SESSION["view-category-blog"][$id])){
            db_insert_update("UPDATE uni_category_blog SET count_view=count_view+1,datetime_view=NOW() WHERE id=$id"); 
            $_SESSION["view-category-blog"][$id] = 1;
          }  
      }
    }    
   }

    function breadcrumb($id=0,$tpl="",$sep=""){

      $getCategories = $this->getCategories("where visible=1");

      $index = $index + 1;

      if($id){

        if($getCategories["id"][$id]['id_parent']!=0){
            $return[] = $this->breadcrumb($getCategories["id"][$id]['id_parent'],$tpl,$sep);  
        }

        $return[] = replace(array("{LINK}", "{NAME}"),array($this->alias($getCategories["id"][$id]["category_chain"]),$getCategories["id"][$id]['name']),$tpl);

        return implode($sep,$return);

      } 
               
    }
    
      
      

   
}

// ======= Инициализация класса

$CategoryBlog = new CategoryBlog(); 

?>