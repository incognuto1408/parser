<?php
  
  class Shop{
    
    function allMain(){
      global $array_data;
       $getCategories = $this->getCategories("where visible=1");

       if($array_data["categories_shops"]["id"]){
          $id = (int)$array_data["categories_shops"]["id"];
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

       function getImages($json = ""){
           if($json && $json != "[]"){
              return json_decode(urldecode($json), true);
           }else{
              return array();
           }
       }

       function clear($id){
        global $main_big_image_shop_goods,$main_medium_image_shop_goods,$main_small_image_shop_goods,$gallery_big_image_shop_goods,$gallery_medium_image_shop_goods,$gallery_small_image_shop_goods;
           
           $sql = db_query_while("SELECT image,id FROM uni_shops WHERE id_user=$id");
             
             if(count($sql) > 0){
                foreach($sql as $array){
                  @unlink($_SERVER['DOCUMENT_ROOT'].$main_big_image_shop_goods.$array["image"]);
                }
             }

           $sql = db_query_while("SELECT images FROM uni_shop_goods WHERE id_user=$id");
             
             if(count($sql) > 0){
                foreach($sql as $array){

                  $images = $this->getImages($array["images"]);

                  if(count($images) > 0){
                     foreach ($images as $key => $value) {

                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_big_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_medium_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_small_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_big_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_medium_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_small_image_shop_goods.$value);
                       
                     }
                  }

                }
             }

          db_insert_update("DELETE FROM uni_shops WHERE id_user=$id");
          db_insert_update("DELETE FROM uni_shop_goods WHERE id_user=$id");
          db_insert_update("DELETE FROM uni_shop_category WHERE id_user=$id");
          db_insert_update("DELETE FROM uni_shop_orders WHERE id_user=$id");
          db_insert_update("DELETE FROM uni_shop_pages WHERE id_user=$id");
          db_insert_update("DELETE FROM uni_shop_tariff_orders WHERE id_user=$id");
       }

       function getGoods($query){
        global $Cashed;
          $key = "SELECT *, (SELECT alias FROM uni_shops WHERE id = uni_ads.ads_id_shop) as alias_shop FROM uni_ads WHERE ads_visible=1 $query";
          $goods = $Cashed->get($key,"shop_goods");
          if($goods !== false){
              return $goods;
          }else{
              $sql = db_query_while($key);

   foreach ($sql as $key => &$value) {
   $city = db_query("SELECT * FROM uni_city WHERE city_id={$value["ads_city_id"]}");
   $board_cat = db_query("SELECT * FROM uni_category_board WHERE category_board_id={$value["ads_id_cat"]}");
   $value = array_merge($value,$city);
   $value = array_merge($value,$board_cat);
   }
              $Cashed->set($sql,$key,"shop_goods");
              return $sql;
          }          
       }

       function delete($id, $id_user = 0){
        global $main_big_image_shop_goods,$main_medium_image_shop_goods,$main_small_image_shop_goods,$gallery_big_image_shop_goods,$gallery_medium_image_shop_goods,$gallery_small_image_shop_goods;

          if($id_user){
            $query = " and id_user=$id_user";
          }
           
           $sql = db_query_while("SELECT image,id FROM uni_shops WHERE id=$id $query");
             
             if(count($sql) > 0){
                foreach($sql as $array){
                  @unlink($_SERVER['DOCUMENT_ROOT'].$main_big_image_shop_goods.$array["image"]);
                }
             }

           $sql = db_query_while("SELECT images FROM uni_shop_goods WHERE id_shop=$id $query");
             
             if(count($sql) > 0){
                foreach($sql as $array){

                  $images = $this->getImages($array["images"]);

                  if(count($images) > 0){
                     foreach ($images as $key => $value) {

                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_big_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_medium_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_small_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_big_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_medium_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_small_image_shop_goods.$value);
                       
                     }
                  }

                }
             }

          db_insert_update("DELETE FROM uni_shops WHERE id=$id $query");
          db_insert_update("DELETE FROM uni_shop_goods WHERE id_shop=$id $query");
          db_insert_update("DELETE FROM uni_shop_category WHERE id_shop=$id $query");
          db_insert_update("DELETE FROM uni_shop_orders WHERE id_shop=$id $query");
          db_insert_update("DELETE FROM uni_shop_pages WHERE id_shop=$id $query");
       }

       function deleteGoods($id, $id_user = 0){
        global $main_big_image_shop_goods,$main_medium_image_shop_goods,$main_small_image_shop_goods,$gallery_big_image_shop_goods,$gallery_medium_image_shop_goods,$gallery_small_image_shop_goods;

           $sql = db_query_while("SELECT images FROM uni_shop_goods WHERE id=$id and id_user={$_SESSION['profile']['id']}");
             
             if(count($sql) > 0){
                foreach($sql as $array){

                  $images = $this->getImages($array["images"]);

                  if(count($images) > 0){
                     foreach ($images as $key => $value) {

                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_big_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_medium_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$main_small_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_big_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_medium_image_shop_goods.$value);
                        @unlink($_SERVER['DOCUMENT_ROOT'].$gallery_small_image_shop_goods.$value);
                       
                     }
                  }

                }
             }

          db_insert_update("DELETE FROM uni_shop_goods WHERE id=$id and id_user={$_SESSION['profile']['id']}");
       }
      
      function countShops(){
        global $db_prefix,$sql_query;
          $count = db_query("SELECT count(uni_shops.id) as result_count FROM uni_shops, uni_shop_tariff_orders WHERE $sql_query");
          return (int)$count["result_count"];
      }
      
      function checkShop($link,$notId = 0){
        if(!empty($link)){
          if(!empty($notId)) $notId = " AND id NOT IN($notId)";  
          $sql = db_query("SELECT alias FROM uni_shops WHERE alias='$link' $notId  limit 1");
          if(empty($sql["alias"])){ return true; }else{ return false; }
        }else{ return false; }   
      }      
      
      function statusAll($status){
       global $languages_content;
         if($status == 0){
            return $languages_content["class-shop-title-1"];
         }elseif($status == 1){
            return $languages_content["class-shop-title-2"];
         }elseif($status == 2){
            return $languages_content["class-shop-title-3"];
         }else{
            return $languages_content["class-shop-title-1"];
         } 
      }

    function getCategoriesProducts($query = ""){
      global $Cashed;

        $key = "SELECT *, (select alias from uni_shops where id=uni_shop_category.id_shop) as alias_shop, (select count(*) from uni_shop_goods where id_cat=uni_shop_category.id) as count_goods FROM uni_shop_category WHERE id_shop={$_SESSION['ShopId']} $query ORDER By id_position ASC";
        $cats = $Cashed->get($key,"shop_products_categories");

        if($cats !== false){
            return $cats;
        }else{
            $sql = db_query_while($key);
            if (count($sql)>0) { 
                $cats = array();                            
                  foreach($sql AS $result){
                      $cats['id_parent'][$result['id_parent']][$result['id']] =  $result;
                      $cats['id'][$result['id']]['id_parent'] =  $result['id_parent'];
                      $cats['id'][$result['id']]['name'] =  $result['name'];
                      $cats['id'][$result['id']]['title'] =  $result['title'];
                      $cats['id'][$result['id']]['text'] =  $result['text'];
                      $cats['id'][$result['id']]['alias'] =  $result['alias'];
                      $cats['id'][$result['id']]['id'] =  $result['id']; 
                      $cats['id'][$result['id']]['description'] =  $result['description'];
                      $cats['id'][$result['id']]['category_chain'] =  $result['category_chain'];  
                      $cats['id'][$result['id']]['alias_shop'] =  $result['alias_shop'];
                      $cats['id'][$result['id']]['count_goods'] =  $result['count_goods'];        
                  }  
            }            
            $Cashed->set($cats,$key,"shop_products_categories");
            return $cats;
        }
            
    }

    function outCategoryProducts($id_parent=0, $level=0) {
      global $shop_prefix;
      $getCategories = $this->getCategoriesProducts();
      
        if (isset($getCategories["id_parent"][$id_parent])) {
            foreach ($getCategories["id_parent"][$id_parent] as $value) {
           
                $idsBuild = trim($this->idsBuildProducts($value["id"]), ",");
                
                if($id_parent != 0){ $visible = 'style="display: none;"'; $plus=''; } else { $visible = ''; $plus=' <i class="la la-plus"></i>'; }

                if($idsBuild){
                    $plus=' <i class="la la-plus"></i>';
                    $delete = '';
                    $explodeIdsBuild = explode(",", $idsBuild);
                    foreach ($explodeIdsBuild as $build_key => $build_value) {
                       $idsBuildItem[] = '#item'.$build_value;
                    }
                }else{
                    $idsBuildItem = array();
                    $plus='';
                    $delete = '<span class="btn btn-danger btn-sm shop_delete_category" data-id="' . $value["id"] . '" ><i class="fa fa-trash"></i></span>';
                }


                echo 
                '<tr id="item' . $value["id"] . '" parent-id="' . $value["id_parent"] . '" '.$visible.' >
                   <td><span class="icon-move move-sort-cat" ><i class="la la-arrows-v"></i></span></td>
                   <td><div style="margin-left:'. ($level * 15) .'px;" ><a style="position: relative; cursor:pointer;" class="open-podcat" uid="' . $value["id"] . '" status="hide" data-ids="'.implode(",",$idsBuildItem).'" >'.$value["name"].$plus.'</a></div></td> 
                   <td><a href="'.URL.$shop_prefix.'/'.$value["alias_shop"].'/'.$value["category_chain"].'" target="_blank" ><i class="fa fa-external-link" ></i> '.$value["category_chain"].'</a></td>    
                   <td>' . $value["count_goods"] . '</td>  
                   <td style="text-align: right;" >
                     <span class="btn btn-success btn-sm shop_load_edit_category" data-id="' . $value["id"] . '" ><i class="fa fa-pencil"></i></span>
                     '.$delete.'
                   </td>                                      
                </tr>';

                $level++;
                $this->outCategoryProducts($value["id"], $level);
                $level--;

            }
        }
    }
      
    function getCategories($query = "", $lang = true){
      global $Cashed;
      
      if($lang){
        $key = "SELECT *, (select value from uni_multilanguage where id_content = uni_shop_main_category.id and field='name' and table_name='uni_shop_main_category' and lang='".lang()."' limit 1) as lang_name, (select value from uni_multilanguage where id_content = uni_shop_main_category.id and field='title' and table_name='uni_shop_main_category' and lang='".lang()."' limit 1) as lang_title, (select value from uni_multilanguage where id_content = uni_shop_main_category.id and field='description' and table_name='uni_shop_main_category' and lang='".lang()."' limit 1) as lang_description, (select value from uni_multilanguage where id_content = uni_shop_main_category.id and table_name='uni_shop_main_category' and field='text' and lang='".lang()."' limit 1) as lang_text FROM uni_shop_main_category $query ORDER By id_position ASC";
      }else{
        $key = "SELECT * FROM uni_shop_main_category $query ORDER By id_position ASC";
      }

        $cats = $Cashed->get($key,"shop_main_categories");

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

                      $cats['category_chain'][$result['category_chain']] =  $result;

                      $cats['id_parent'][$result['id_parent']][$result['id']] =  $result;
                      $cats['id'][$result['id']]['id_parent'] =  $result['id_parent'];
                      $cats['id'][$result['id']]['name'] =  $result['name'];
                      $cats['id'][$result['id']]['title'] =  $result['title'];
                      $cats['id'][$result['id']]['text'] =  $result['text'];
                      $cats['id'][$result['id']]['alias'] =  $result['alias'];
                      $cats['id'][$result['id']]['id'] =  $result['id']; 
                      $cats['id'][$result['id']]['description'] =  $result['description'];
                      $cats['id'][$result['id']]['category_chain'] =  $result['category_chain'];          
                  }  
            }            
            $Cashed->set($cats,$key,"shop_main_categories");
            return $cats;
        }
            
    }

    function aliasBuild($cur_id=0){

      $getCategories = $this->getCategories();

      if($cur_id){

        if($getCategories["id"][$cur_id]['id_parent']!=0){ 
            $out .= $this->aliasBuild($getCategories["id"][$cur_id]['id_parent'])."-";            
        }
        $out .= $getCategories["id"][$cur_id]['alias'];

        return $out;
      }else{ return ""; } 
               
    }

    function aliasBuildProducts($cur_id=0){

      $getCategories = $this->getCategoriesProducts();

      if($cur_id){

        if($getCategories["id"][$cur_id]['id_parent']!=0){ 
            $out .= $this->aliasBuildProducts($getCategories["id"][$cur_id]['id_parent'])."-";            
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

    function idsBuildProducts($parent_id=0){

      $getCategories = $this->getCategoriesProducts(); 
                       
        if(isset($getCategories['id_parent'][$parent_id])){

              foreach($getCategories['id_parent'][$parent_id] as $cat){
                
                $ids .= ','.$cat['id'];                                                         
                $ids .= $this->idsBuildProducts($cat['id']);
                                                                    
              }

        }
      return $ids;
    }

    function outCategoryOptions($id_parent = 0, $level = "") {
      global $array_data;
      $getCategories = $this->getCategories("where visible=1");

        if (isset($getCategories["id_parent"][$id_parent])) {
            foreach ($getCategories["id_parent"][$id_parent] as $value) {

                if($array_data["id_cat"] == $value["id"]){
                  $selected = 'selected=""';
                }else{
                  $selected = "";
                }

                echo '<option '.$selected.' value="' . $value["id"] . '" >'.$level.$value["name"].'</option>';

                $level = $level.'-';
                $this->outCategoryOptions($value["id"], $level);
                if($id_parent) $level = '-'; else $level = '';
            }
        }
    }

    function outCategoryOptionsProducts($id_parent = 0, $level = "") {
      global $array_data;
      $getCategories = $this->getCategoriesProducts();

        if (isset($getCategories["id_parent"][$id_parent])) {
            foreach ($getCategories["id_parent"][$id_parent] as $value) {

                if($array_data["id_cat"] == $value["id"]){
                  $selected = 'selected=""';
                }else{
                  $selected = "";
                }

                echo '<option '.$selected.' value="' . $value["id"] . '" >'.$level.$value["name"].'</option>';

                $level = $level.'-';
                $this->outCategoryOptionsProducts($value["id"], $level);
                if($id_parent) $level = '-'; else $level = '';
            }
        }
    }

      function outMainCategory($tpl, $tpl_podcat){
        global $no_image,$image_category,$category_shop_prefix,$languages_content;
        $return = "";
        $getCategories = $this->getCategories("where visible=1");

          if (isset($getCategories["id_parent"][0])) {
              foreach ($getCategories["id_parent"][0] as $value) {
                 $parent = "";

                 $ids = $value["id"].$this->idsBuild($value["id"]);
                 $count_shops = db_query_while(SHOP_QUERY . " and uni_shops.id_cat IN(".$ids.")");

                 $item =  replace(array("{LINK}", "{IMAGE}", "{NAME}", "{COUNT_GOODS}"),array(URL.$category_shop_prefix."/".$value["alias"],Exists($image_category,$value["image"],$no_image),$value["name"], count($count_shops) . ' ' . ending(count($count_shops) , $languages_content["class-shop-title-4"], $languages_content["class-shop-title-5"], $languages_content["class-shop-title-6"]) ),$tpl);

                 if (isset($getCategories["id_parent"][$value["id"]])) {
                    foreach ($getCategories["id_parent"][$value["id"]] as $parent_value) {
                       $parent .= replace(array("{PARENT_LINK}", "{PARENT_IMAGE}", "{PARENT_NAME}"),array(URL.$category_shop_prefix."/".$parent_value["category_chain"],Exists($image_category,$parent_value["image"],$no_image),$parent_value["name"]),$tpl_podcat);
                    }
                 }

                $return .=  replace(array("{PARENT}"),array($parent),$item);

              }
          }                 
        return $return;
      } 
   function alias_ads($array=array()){
      return URL.$array["city_alias"]."/".$array["category_board_alias"]."/".$array["ads_alias"]."-".$array["ads_id"];
   }  
   function getImages_ads($json = ""){
       if($json && $json != "[]"){
        if($json == 'null')
          return URL.'/files/images/no-image-view-board.png';
        $ret = json_decode(urldecode($json), true);
          return URL.'files/media/images_boards/big/'.$ret[0];
       }else{
          return URL.'/files/images/no-image-view-board.png';
       }
   }
   function price_ads($array = array()){
    global $decimals,$dec_point,$thousands_sep,$settings,$CurrencyBoard;
      return $CurrencyBoard->currency_converter($array["ads_price"], $array["ads_currency"], $_SESSION["currency"]);
   }
      function outMainCashADS($tpl, $array){
        $array = implode(',', array_unique(json_decode($array, true)));
          $count_shops = db_query_while("SELECT * FROM uni_ads WHERE ads_id IN ($array)");
          $city = array();
          $board = array();
          /*
  $value = array_merge($value, db_query("SELECT * FROM uni_city WHERE city_id='".$value['ads_city_id']."'"));
  $value = array_merge($value, db_query("SELECT * FROM uni_category_board WHERE category_board_id='".$value['ads_id_cat']."'"));*/
          foreach ($count_shops as $key => &$value) {
            if(!array_key_exists($value['ads_city_id'], $city)){
              $city_local = db_query("SELECT * FROM uni_city WHERE city_id='".$value['ads_city_id']."'");
              $value = array_merge($value,  $city_local);
              $city[$value['ads_city_id']] = $city_local;
            }else{
              $value = array_merge($value,  $city[$value['ads_city_id']]);
            }
            if(!array_key_exists($value['ads_id_cat'], $board)){
              $board_local = db_query("SELECT * FROM uni_category_board WHERE category_board_id='".$value['ads_id_cat']."'");
              $value = array_merge($value,  $board_local);
              $board[$value['ads_id_cat']] = $board_local;
            }else{
              $value = array_merge($value,  $board[$value['ads_id_cat']]);
            }
          }
        $return = "";
          foreach ($count_shops as $key => $ad_value) {
                 $result .=  replace(array("{LINK}", "{IMAGE}", "{NAME}", "{COUNT_GOODS}"),array($this->alias_ads($ad_value), $this->getImages_ads($ad_value['ads_images']),$ad_value['ads_title'], $this->price_ads($ad_value)),$tpl);
          }







                      /*
'ads_id_cat' => $array_data['ads']['ads_id_cat'],
'city_id' => $array_data['ads']['city_id'],
'city_name' => $array_data['ads']['city_name'],
'ads_id' => $array_data['ads']['ads_id'],
'ads_images' => $array_data['ads']['ads_images'],
'top' => $array_data['ads']['top'],
'ads_title' => $array_data['ads']['ads_title'],
'city_alias' => $array_data['ads']['city_alias'],
'ads_datetime_add' => $array_data['ads']['ads_datetime_add']*/
        return $result;
      }

      function outMainCategoryGoods($tpl, $tpl_podcat){
        global $no_image,$image_category,$languages_content;
        $return = "";
        $getCategories = $this->getCategories("where visible=1");

          if (isset($getCategories["id_parent"][0])) {
              foreach ($getCategories["id_parent"][0] as $value) {
                 $parent = "";
                 $ids_shop = array();

                 $ids = $value["id"].$this->idsBuild($value["id"]);
                 $shops = db_query_while(SHOP_QUERY . " and uni_shops.id_cat IN(".$ids.")");

                 foreach ($shops as $shops_key => $shops_value) {
                     $ids_shop[] = $shops_value["id"];
                 }

                 if(count($ids_shop) > 0){
                     $count_shops = db_query("SELECT *, count(id) as total FROM uni_shop_goods WHERE id_shop IN(".implode(",", $ids_shop).") and status_stock=1 and ( (start_date < now() or start_date = '0000-00-00 00:00:00') and ( end_date > now() or end_date = '0000-00-00 00:00:00') )");
                 }else{
                     $count_shops["total"] = 0;
                 }

                 $item =  replace(array("{LINK}", "{IMAGE}", "{NAME}", "{COUNT_GOODS}"),array(URL."sales/".$value["alias"],Exists($image_category,$value["image"],$no_image),$value["name"], intval($count_shops["total"]) . ' ' . ending(intval($count_shops["total"]), $languages_content["class-shop-title-7"], $languages_content["class-shop-title-8"], $languages_content["class-shop-title-9"]) ),$tpl);

                 if (isset($getCategories["id_parent"][$value["id"]])) {
                    foreach ($getCategories["id_parent"][$value["id"]] as $parent_value) {
                       $parent .= replace(array("{PARENT_LINK}", "{PARENT_IMAGE}", "{PARENT_NAME}"),array(URL."sales/".$parent_value["category_chain"],Exists($image_category,$parent_value["image"],$no_image),$parent_value["name"]),$tpl_podcat);
                    }
                 }

                $return .=  replace(array("{PARENT}"),array($parent),$item);

              }
          }                 
        return $return;
      }

      function alias($array=array()){
        global $board_prefix,$shop_prefix;
          return URL.$array["city_alias"]."/".$array["category_board_alias"]."/".urldecode($array["ads_alias"])."-".$array['ads_id']; 
      }

      function outPrice($array){
        global $decimals,$dec_point,$thousands_sep,$getCurrency;
        $stock = $this->stock($array);
        if($stock){
          return '<span class="span_newPrice" >'.number_format($array["ads_new_price"],$decimals,$dec_point,$thousands_sep)." ".$getCurrency[$array["currency"]]["sign"].'</span><span class="span_oldPrice" >'.number_format($array["ads_price"],$decimals,$dec_point,$thousands_sep)." ".$getCurrency[$array["currency"]]["sign"].'</span>';
        }else{
          return '<span class="span_newPrice" >'.number_format($array["ads_price"],$decimals,$dec_point,$thousands_sep)." ".$getCurrency[$array["currency"]]["sign"].'</span>';
        }            
      }


      function getCountOrders($id_goods=0){
        if($id_goods){
          $sql = db_query("SELECT count(id) as result_count FROM uni_shop_orders WHERE id_goods LIKE '%{".$id_goods."}%'");
          return (int)$sql["result_count"];
        }else{ return 0; }  
      } 
      
      function getShopsUser($id_user){
        $return = array();
        if($id_user){
          $sql = db_query_while("SELECT id FROM uni_shops WHERE id_user='$id_user'");
          if(count($sql)>0){
            foreach($sql as $result){
                $return[$result["id"]] = $result["id"];
            }
          }
          return $return;
        }else{ return array(); }         
      } 
      
      function getCountGoods($id_shop,$id_user = 0){
        if($id_user){ $query = " and id_user='$id_user'"; }
        $sql = db_query("SELECT count(id) as result_count FROM uni_shop_goods WHERE id_shop='$id_shop' $query");
        return (int)$sql["result_count"]; 
      }

      function getCountCategory($id_shop,$id_user = 0){
        if($id_user){ $query = " and id_user='$id_user'"; }
        $sql = db_query("SELECT count(id) as result_count FROM uni_shop_category WHERE id_shop='$id_shop' $query");
        return (int)$sql["result_count"]; 
      }

      function getCountPages($id_shop,$id_user = 0){
        if($id_user){ $query = " and id_user='$id_user'"; }
        $sql = db_query("SELECT count(id) as result_count FROM uni_shop_pages WHERE id_shop='$id_shop' $query");
        return (int)$sql["result_count"]; 
      }

      function getCountShops($id_user){
        if($id_user){
          $sql = db_query("SELECT count(id) as result_count FROM uni_shops WHERE id_user='$id_user'");
          return (int)$sql["result_count"];
        }else{ return 0; }  
      }
      
    function loadStatOrders($id){ 
    $result = db_query_while("SELECT * FROM uni_shop_orders WHERE id_shop='$id' and id_user={$_SESSION["profile"]["id"]}");  
        if(count($result) > 0){
            
            foreach($result AS $data_array){              
                $count_order = db_query("SELECT count(id) as result_count FROM uni_shop_orders WHERE DATE(datetime_add) = '".date("Y-m-d",strtotime($data_array["datetime_add"]))."' AND id_shop='$id'");
                $count_order_buy = db_query("SELECT count(id) as result_count FROM uni_shop_orders WHERE DATE(datetime_add) = '".date("Y-m-d",strtotime($data_array["datetime_add"]))."' AND status_pay='ACCEPTED' AND id_shop='$id'");            
                $json[] = array("y"=>date('Y-m-d', strtotime($data_array["datetime_add"])),"order"=>$count_order["result_count"],"buy"=>$count_order_buy["result_count"]);            
            }
            
        } else {
            $json = array();
        }
    
        return json_encode($json);     
    }     
     
    function precessStatus($status){
    global $lang,$languages_content;    
       if($status == 1){
          return '<span class="badge badge-warning">'.$languages_content["class-shop-title-10"].'</span><br/>';
       }elseif($status == 2){
          return '<span class="badge badge-primary">'.$languages_content["class-shop-title-11"].'</span><br/>';
       }elseif($status == 3){
          return '<span class="badge badge-danger">'.$languages_content["class-shop-title-12"].'</span><br/>';
       }else{
          return '<span class="badge badge-success">'.$languages_content["class-shop-title-13"].'</span><br/>'; 
       } 
    }     
     
    function orderCountSum($num,$id_shop=0){
      global $db_prefix,$settings,$currency,$dec_point,$thousands_sep,$decimals;  
        if($num == 1){
           $sqli = db_query_while("SELECT * FROM {$db_prefix}shop_orders WHERE status_pay='ACCEPTED' AND id_shop='$id_shop'");
           if(count($sqli)>0){
               foreach($sqli AS $res){
                   $sum += $res["price"];
               }
           } 
        }elseif($num == 2){
           $sqli = db_query_while("SELECT * FROM {$db_prefix}shop_orders WHERE status_pay!='ACCEPTED' AND id_shop='$id_shop'");
           if(count($sqli)>0){
               foreach($sqli AS $res){
                   $sum += $res["price"];
               }
           }   
        }
        return number_format($sum,$decimals,$thousands_sep,$dec_point).' '.$currency;
    }     

    function orderCountNum($num,$id_shop=0){ 
        if($num == 1){
           $sql = db_query("SELECT count(id) as result_count FROM uni_shop_orders WHERE id_shop='$id_shop'"); 
           return $sql["result_count"];
        }elseif($num == 2){
           $sql = db_query("SELECT count(id) as result_count FROM uni_shop_orders WHERE status_pay='ACCEPTED' AND id_shop='$id_shop'"); 
           return $sql["result_count"]; 
        }elseif($num == 3){
           $sql = db_query("SELECT count(id) as result_count FROM uni_shop_orders WHERE status_pay!='ACCEPTED' AND id_shop='$id_shop'");
           return $sql["result_count"];  
        }elseif($num == 4){
           $sql = db_query("SELECT count(id) as result_count FROM uni_shop_goods WHERE id_shop='$id_shop'");
           return $sql["result_count"];  
        }elseif($num == 5){
           $sql = db_query("SELECT sum(price) as total_price FROM uni_shop_orders WHERE id_shop='$id_shop'");
           return $sql["total_price"];  
        }elseif($num == 6){
           $sql = db_query("SELECT sum(price) as total_price FROM uni_shop_orders WHERE id_shop='$id_shop' and status_pay='ACCEPTED'");
           return $sql["total_price"];  
        }
    }     

 
    function recalculationPriceOrder($id,$count){
       $sql = db_query("SELECT data_order FROM uni_shop_orders WHERE id='$id'"); 
       if(count($sql)>0){
           $price = 0;
           $data_order = json_decode($sql["data_order"],true);
           if(count($data_order)>0){
              foreach($data_order as $id_goods=>$data){
                  $sql_goods = db_query("SELECT price FROM uni_shop_goods WHERE id='$id_goods'");
                    $price += $sql_goods["price"] * $data["count"];
              }
           }
       }
       return round($price,2);
    }
    
    function parseOrderGoods($data_order,$link=true){
      global $languages_content;
        if($data_order != "" && $data_order != "[]"){
            $array = array();
            $data_order = json_decode($data_order,true);
               if(count($data_order)>0){
                  foreach($data_order as $id_goods=>$data){
                   $sql_goods = db_query("SELECT price,title,link,id_shop FROM uni_shop_goods WHERE id='$id_goods'");
                     if(count($sql_goods)>0){
                          if($link == true){
                            $array[] = '<li><a href="'.$this->alias($sql_goods).'" target="_blank" >'.$sql_goods["title"].'</a>, '.$languages_content["class-shop-title-14"].' '.$data["count"].'</li>';
                          }else{
                            $array[] = $sql_goods["title"].', кол-во: '.$data["count"];
                          }
                     }else{
                          if($link == true){
                            $array[] = '<li>'.urldecode($data["title"]).', кол-во: '.$data["count"].'</li>';
                          }else{
                            $array[] = urldecode($data["title"]).', кол-во: '.$data["count"];
                          }                        
                     } 
                  }
               }
          if($link == true){     
            return implode("",$array);
          }else{
            return implode(",",$array); 
          }                 
        }
    }
    
    function fixVisitShop($id){
        if(!isset($_SESSION["view-shop"][$id])){
            db_insert_update("UPDATE uni_shops SET visits=visits+1 WHERE id='$id'");
            $_SESSION["view-shop"][$id] = $id;
        }
    }

    function fixCountViewGoods($id){
        if(!isset($_SESSION["view-shop-goods"][$id])){
            db_insert_update("UPDATE uni_ads SET ads_count_view=ads_count_view+1 WHERE ads_id='$id'");
            $_SESSION["view-shop-goods"][$id] = 1;
        }
    }

    function getTariff($id){    
       if(!empty($id)){ 
         $sql = db_query("SELECT * FROM uni_shop_tariff WHERE id = '$id'");
         return $sql; 
       }else{ return array(); }
    }

    function getAlertTariff($id,$alert="",$html=true){
      global $languages_content;
       if($alert == "sites"){

          $getTariff = $this->getTariff($id);
          if($this->getCountShops($_SESSION['profile']['id']) >= intval($getTariff["sites"])){
            if($html == true){
                return '<div class="alert alert-warning" role="alert">'.$languages_content["class-shop-title-15"].'</div>';
            }else{
                return $languages_content["class-shop-title-15"];
            }  
          }else{ return false; }
 
       }elseif($alert == "goods"){

          $getTariff = $this->getTariff($id);
          if($this->getCountGoods($_SESSION["ShopId"],$_SESSION['profile']['id']) >= intval($getTariff["goods"])){
            if($html == true){
                return '<div class="alert alert-warning" role="alert">'.$languages_content["class-shop-title-16"].'</div>';
            }else{
                return $languages_content["class-shop-title-16"];
            }  
          }else{ return false; }

       }elseif($alert == "categories"){

          $getTariff = $this->getTariff($id);
          if($this->getCountCategory($_SESSION["ShopId"],$_SESSION['profile']['id']) >= intval($getTariff["categories"])){
            if($html == true){
                return '<div class="alert alert-warning" role="alert">'.$languages_content["class-shop-title-17"].'</div>';
            }else{
                return $languages_content["class-shop-title-17"];
            }  
          }else{ return false; }

       }elseif($alert == "pages"){

          $getTariff = $this->getTariff($id);
          if($this->getCountPages($_SESSION["ShopId"],$_SESSION['profile']['id']) >= intval($getTariff["pages"])){
            if($html == true){
                return '<div class="alert alert-warning" role="alert">'.$languages_content["class-shop-title-18"].'</div>';
            }else{
                return $languages_content["class-shop-title-18"];
            }  
          }else{ return false; }

       }

    }

    function outPriceStock($array){
        global $decimals,$dec_point,$thousands_sep,$currency, $settings;
        if($array["status_stock"]){
          if(!empty($array["new_price"])){
             return '<span class="span_newPrice" >'.number_format($array["new_price"],$decimals,$dec_point,$thousands_sep)." ".$settings["currency_sign"].'</span><span class="span_oldPrice" >'.number_format($array["price"],$decimals,$dec_point,$thousands_sep)." ".$settings["currency_sign"].'</span>';
          }else{
             return '<span class="span_newPrice" >'.number_format($array["price"],$decimals,$dec_point,$thousands_sep)." ".$settings["currency_sign"].'</span>';
          } 
        }else{
           return '<span class="span_newPrice" >'.number_format($array["price"],$decimals,$dec_point,$thousands_sep)." ".$settings["currency_sign"].'</span>';
        }             
    }

    function stock($array = array()){
       if(count($array) > 0){ 
          if($array["ads_status_stock"] && $array["ads_new_price"]){
            if($array["ads_method_stock"] == 1){
              return true;
            }elseif($array["ads_method_stock"] == 2){
              $n = date("w", mktime(0,0,0,date("m"),date("d"),date("Y")));
              if(in_array($n, explode(",", $array['weekday']))){
                 return true;
              }else{
                 return false;
              }
            }elseif($array["ads_method_stock"] == 3){
              if( strtotime($array["ads_start_date"]) <= time() && strtotime($array["ads_end_date"]) > time() ){
                 return true;
              }else{
                 return false;
              }
            }
          }else{
            return false; 
          }
       }
    }
 
    function getGoodsDiscount($array=array()){
        
        if(!empty($array["price"]) && !empty($array["new_price"])){
            return intval(100 - (($array["new_price"] / $array["price"]) * 100));
        }else{ return 0; }   
     
    }

    function getGoodsDay(){

        $shops = db_query_while(SHOP_QUERY);

        if(count($shops) > 0){
          foreach ($shops as $key => $value) {
             $ids_shop[] = $value["id"];
          }
          if(count($ids_shop) > 0){

            return db_query_while("SELECT *, (select alias from uni_shops where id=uni_shop_goods.id_shop) as alias_shop, (select currency from uni_shops where id = uni_shop_goods.id_shop) as currency, (select name from uni_shops where id=uni_shop_goods.id_shop) as name_shop,(select count(*) from uni_services_shop_order,uni_orders where uni_services_shop_order.services_shop_order_id_goods = uni_shop_goods.id AND uni_services_shop_order.services_shop_order_time_validity > NOW() AND uni_orders.id_order = uni_services_shop_order.services_shop_order_id_order AND uni_orders.status = 'ACCEPTED') as turbo FROM uni_shop_goods WHERE id_shop IN(".implode(",", $ids_shop).") HAVING turbo = 1 order by rand() limit 1");

          }
        }

    }

      
  }

 $Shop = new Shop();

?>