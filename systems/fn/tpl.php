<?php

function OutTpl($template){

    $html_footer  = true;
    global $secret_key_mobile_message;
    global $settings,$array_tpl,$array_data,$db_prefix,$sql_query,$url_pages,$mysqli,$Banners,$Pages,$Blog,$Category,$big_image_category,$shop_prefix;
    global $id,$no_avatar_admin,$LINK,$LINK_SORT,$output_content_index,$Profile,$big_images_avatar,$sort,$out_cat_info,$output_ads_content,$image_category, $CategoryAll;
    global $big_image_news,$no_image,$output_content,$small_image_news,$image_banners,$category_prefix,$cat_id_link,$CategoryBoard,$Filters,$default_body_img,$id_cat,$Cashed,$Geo,$blog_prefix,$big_image_blog,$small_image_blog,$medium_image_blog,$image_language,$CurrencyBoard,$REQUEST_URI,$no_image_ad,$no_image_grid,$category_shop_prefix,$languages_content,$lang;
    global $board_prefix,$Ads,$currency,$decimals,$dec_point,$thousands_sep,$no_image_avatar,$CategoryBlog,$Seo,$Shop,$big_image_board_shops,$currency_array,$array_formation_shop_cat,$main_image_logo,$main_big_image_ads,$main_medium_image_ads,$main_small_image_ads,$gallery_big_image_ads,$gallery_medium_image_ads,$gallery_small_image_ads,$main_big_image_shop_goods,$main_medium_image_shop_goods,$main_small_image_shop_goods,$gallery_big_image_shop_goods,$gallery_medium_image_shop_goods,$gallery_small_image_shop_goods,$settings_tpl,$getCurrency;

     ob_start();

     if(file_exists("templates/".$template)){
	   include_once("templates/".$template);
     }
 	 
     return ob_get_clean();   
    
}
?>