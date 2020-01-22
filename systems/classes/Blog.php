<?php
/**
 * UniSite CMS
 *
 * @copyright 	2018 Artur Zhur
 * @link 		https://unisitecms.ru
 * @author 		Artur Zhur
 *
 */
 
class Blog{
   
   function countArticles($id_cat){
     global $CategoryBlog;
       $count = db_query("SELECT count(id) as result_count FROM uni_articles WHERE id_cat IN(".$id_cat.$CategoryBlog->idsBuild($id_cat).")"); 
       return intval($count["result_count"]);
   }

   function articleView($id){
    if(detectRobots($_SERVER['HTTP_USER_AGENT']) == false){
        if($id){    
            if(!isset($_SESSION["view-article"][$id])){
              db_insert_update("UPDATE uni_articles SET count_view=count_view+1,datetime_view=NOW() WHERE id={$id}"); 
              $_SESSION["view-article"][$id] = 1;
            }  
        }
    } 
   }
   
   function alias($array = array()){
    global $blog_prefix;
      return URL.$blog_prefix."/".$array["alias_category"]."/".urldecode($array["alias"])."-".$array["id"]; 
   }
   function alias_shop($array = array()){
    global $article_prefix;
      return URL.$article_prefix."/".$array["articles_shops_id"]."-".$array["articles_shops_alias"];
   }

   function delete($id){
    global $big_image_blog,$medium_image_blog,$small_image_blog;
      
      if($id){ 

       $sql = db_query("SELECT image FROM uni_articles WHERE id=$id");
         
         if(count($sql) > 0){
              @unlink($_SERVER['DOCUMENT_ROOT'].$big_image_blog.$sql["image"]);
              @unlink($_SERVER['DOCUMENT_ROOT'].$medium_image_blog.$sql["image"]);
              @unlink($_SERVER['DOCUMENT_ROOT'].$small_image_blog.$sql["image"]);

              db_insert_update("DELETE FROM uni_articles WHERE id=$id");
         }

      }

   }

   function get($query = ""){
        $sql = db_query_while("SELECT *, (select name from uni_category_blog where id = uni_articles.id_cat) as name_category, (select alias from uni_category_blog where id = uni_articles.id_cat) as alias_category, (select name from uni_category_blog where id = uni_articles.id_cat) as name_category, (select alias from uni_category_blog where id = uni_articles.id_cat) as alias_category, (select value from uni_multilanguage where id_content = uni_articles.id and field='title' and table_name='uni_articles' and lang='".lang()."') as lang_title, (select value from uni_multilanguage where id_content = uni_articles.id and field='description' and table_name='uni_articles' and lang='".lang()."') as lang_description, (select value from uni_multilanguage where id_content = uni_articles.id and table_name='uni_articles' and field='text' and lang='".lang()."') as lang_text, (select value from uni_multilanguage where id_content = uni_articles.id_cat and table_name='uni_category_blog' and field='name' and lang='".lang()."') as lang_name_category FROM uni_articles $query");
        return $sql;
   }



      
}

// ======= Инициализация класса

$Blog = new Blog(); 
 
?>