<?php

/**
 * UniSite CMS
 *
 * @copyright   2018 Artur Zhur
 * @link    https://unisitecms.ru
 * @author    Artur Zhur
 *
 */

class Filters{


  function outVariants($array = array(), $cat_id = 0, $sort = "", $nav = true){
    global $CategoryBoard,$Ads;

      $flCount = 0;    
      $forming_multi_query = array();
      $forming = array();

        if(!empty($array["search"])){  
            $forming_multi_query["search"] = "MATCH(`ads_search_tags`) AGAINST('".clear($array["search"])."')"; 
        }

        if(!empty($array["price_start"]) && !empty($array["price_end"])){  
            $forming_multi_query["price"] = "(uni_ads.ads_price BETWEEN ".round($array["price_start"],2)." AND ".round($array["price_end"],2).")"; 
        }else{
            if(!empty($array["price_start"])){
               $forming_multi_query["price"] = "(uni_ads.ads_price >= ".round($array["price_start"],2).")";
            }elseif(!empty($array["price_end"])){
               $forming_multi_query["price"] = "(uni_ads.ads_price < ".round($array["price_end"],2).")";
            }
        }

        if(!empty($array["photo"])){  
            $forming_multi_query["image"] = "(uni_ads.ads_images != '' AND uni_ads.ads_images != 'null' AND uni_ads.ads_images != '[]')"; 
        }

        if(intval($array["user"]) == 1 && intval($array["company"]) == 0){
            $forming_multi_query["user"] = "uni_clients.clients_type_person='user'"; 
        }

        if(intval($array["company"]) == 1 && intval($array["user"]) == 0){
            $forming_multi_query["company"] = "uni_clients.clients_type_person='company'"; 
        }            

        if(intval($array["user"]) == 1 && intval($array["company"]) == 1){
            $forming_multi_query["company"] = "(uni_clients.clients_type_person='company' OR uni_clients.clients_type_person='user')"; 
        }

        if($cat_id){
            $cat_ids = intval($cat_id).$CategoryBoard->idsBuild(intval($cat_id));
            $forming_multi_query["id_cat"] = "uni_ads.ads_id_cat IN(".$cat_ids.")";
        }

        if($_SESSION["geo"]["query"]){
            $forming_multi_query["geo"] = $_SESSION["geo"]["query"];
        }
       
       if(count($array["filter"]) > 0){

           foreach($array["filter"] AS $id=>$val){
  
              if($id != "slider"){
                
                foreach($val AS $val2){
                   $val2 = trim($val2); 
                   if($val2 != "null" && !empty($val2)){
                       
                       if(count($forming[$id]) == 0) $flCount++;
                       $forming[$id][] = "(id_filter='".intval($id)."' AND value='".clear($val2)."')";
                       
                   } 
                } 
                
              }else{

                foreach($val AS $id2 => $val2){

                  if($val2[0] != "null" && $val2[0] != ""){

                    $sliderCount = explode(";",$val2[0]);

                      $flCount++;

                      if($sliderCount[1]){
                        $forming[$id2][] = "id_filter='".$id2."' AND (value BETWEEN ".intval($sliderCount[0])." AND ".intval($sliderCount[1]).")";
                      }else{
                        $forming[$id2][] = "id_filter='".$id2."' AND (value >= ".intval($sliderCount[1]).")";
                      }

                  }

                }
                                 
              }            
          
           }

       }
       
       if(count($forming)){
              
              foreach($forming as $id=>$arr){
                   foreach($arr as $i=>$val){
                        $forming_multi[] = $val;                  
                   } 
                $forming_filters[] = implode(" OR ",$forming_multi); 
                $forming_multi = array();            
              }
    
       }

      if(count($forming_filters) > 0){

        $ids = db_query_while("SELECT ads_id, count(ads_id) AS cnt FROM `uni_filters_variants` WHERE ".implode(" OR ",$forming_filters)."  GROUP BY ads_id HAVING COUNT(cnt)>=".$flCount, "ads_id");

         if(count($ids) > 0){
            $query = " AND uni_ads.ads_id IN(".implode(",",$ids).") AND ".implode(" AND ",$forming_multi_query);
            unset($forming_multi_query["price"]);
            $query_price = " AND uni_ads.ads_id IN(".implode(",",$ids).") AND ".implode(" AND ",$forming_multi_query);
         }else{
            $query = " AND uni_ads.ads_id IN(0)"; 
            $query_price = " AND uni_ads.ads_id IN(0)";
         }

      }else{

        if(count($forming_multi_query) > 0) { 
          $query = " AND ".implode(" AND ",$forming_multi_query); 
          unset($forming_multi_query["price"]);
          $query_price = " AND ".implode(" AND ",$forming_multi_query); 
        } else { 
          $query = ""; $query_price = "";
        }
         
      }


      $return = $Ads->getQuery($query,$sort, $nav);

      $return["price_min"] = $this->priceMin($query_price);
      $return["price_max"] = $this->priceMax($query_price);
      $return["query"] = $query;

      return $return;
  }

  function priceMin($query = ""){
    global $Ads;
    
    if($query){
        $sql = $Ads->get(" where ".AD_QUERY." $query order by uni_ads.ads_price asc limit 1");
    }else{
        $sql = $Ads->get(" where ".AD_QUERY." order by uni_ads.ads_price asc limit 1");
    }

     if(!empty($sql["ads_price"])) return intval($sql["ads_price"]); else return 0;

  }

  function priceMax($query = ""){
    global $Ads;
    
    if($query){
        $sql = $Ads->get(" where ".AD_QUERY." $query order by uni_ads.ads_price desc limit 1");
    }else{
        $sql = $Ads->get(" where ".AD_QUERY." order by uni_ads.ads_price desc limit 1");
    }

     if(!empty($sql["ads_price"])) return intval($sql["ads_price"]); else return 0;

  }

  function getFilters($query = ""){
    global $Cashed,$CategoryBoard;

      $key = "SELECT *, (select value from uni_multilanguage where id_content = uni_filters.id and field='name' and table_name='uni_filters' and lang='".lang()."') as lang_name, (select value from uni_filters_items where id_filter = uni_filters.id and name = uni_filters.name limit 1) as items, (select id from uni_filters_items where id_filter = uni_filters.id and name = uni_filters.name limit 1) as items_id FROM uni_filters $query ORDER By position ASC";
      $data = $Cashed->get($key,"filters_board");

      if($data !== false){
          return $data;
      }else{
          $sql = db_query_while($key);
          if (count($sql)>0) { 
              $data = array();                            
                foreach($sql AS $result){

                    $multilanguage_tables = multilanguage_tables(array("id_content" => $result["items_id"], "table_name" => "uni_filters_items"));

                    if($multilanguage_tables['lang_name']) $result["items_lang"] = explode("\n",urldecode($multilanguage_tables['lang_name'])); else $result["items_lang"] = explode("\n",urldecode($result["items"]));

                    $result["name"] = !empty($result['lang_name']) ? urldecode($result['lang_name']) : $result['name'];
                    if($result["items"]) $result["items"] = explode("\n",urldecode($result["items"])); else $result["items"] = array();

                    $data['id_parent'][$result['id_parent']][$result['id']] =  $result;

                    if($result['podcat']){
                        $ids = $CategoryBoard->idsBuild($result['id_cat']);
                        if($ids){
                           $ids_array = explode(",",trim($ids, ","));
                           foreach ($ids_array as $key => $value) {
                              if($value){
                                $data['id'][$result['id']]['id_cat'] =  $value;
                                $data['id_cat'][$value][$result['id_parent']][$result['id']] =  $result; 
                              }                           
                           }
                        }
                    }

                    $data['id'][$result['id']]['id_cat'] =  $result['id_cat'];
                    $data['id_cat'][$result['id_cat']][$result['id_parent']][$result['id']] =  $result;

                    $data['id'][$result['id']]['id_parent'] =  $result['id_parent'];
                    $data['id'][$result['id']]['name'] =  $result['name']; 
                    $data['id'][$result['id']]['id'] =  $result['id']; 
                    $data['id'][$result['id']]['always'] =  $result['always'];   
                    $data['id'][$result['id']]['type'] =  $result['type']; 
                    $data['id'][$result['id']]['ad_type'] =  $result['ad_type'];
                    $data['id'][$result['id']]['items'] =  $result['items'];

                }  
          }            
          $Cashed->set($data,$key,"filters_board");
          return $data;
      }
          
  }
   function ad_shops_filters($array_data){
   $ads = $array_data;
   $shop_list = db_query_while("SELECT * FROM uni_shops WHERE id_user='{$ads["clients_id"]}'");
   $return1 = "Начало";
   $name_shop = "Не привязан";
      $return1 .= '<option value="0" data-id="0">'.$name_shop.'</option>';
   if($shop_list){
    foreach ($shop_list as $key => $value) {
      if($ads['ads_id_shop'] == $value['id']){
        $name_shop = $value['name'];
        $id_shop = $value['id'];
      }
      if($value['id'])
        $return1 .= '<option value="'.$value['id'].'" data-id="'.$value['id'].'">'.$value['name'].'</option>';
    }
   }
   $ret = '<div class="form-group row">
                                   <label class="col-lg-4 col-form-label">Привязанный магазин</label>
                                   <div class="col-lg-8 col-xl-6">
                                         <div class="dropdown bootstrap-select change-filter-item"><select name="shop_variable" title="'.$name_shop.'" class="change-filter-item selectpicker" tabindex="0">
                                         
                               '.$return1.'
                              
                                         </select><div class="dropdown-menu " role="combobox"><div class="inner show" role="listbox" aria-expanded="false" tabindex="-1"><ul class="dropdown-menu inner show"></ul></div></div></div>
                                         <input type="hidden" name="" value="Привязанный магазин">
                                   </div>
                                </div>';
    return $ret;
   }
   function ad_load_filters_category($id, $variants = array()){
    global $languages_content;
       $getFilters = $this->getFilters("where visible=1");

       if(isset($getFilters["id_cat"][$id][0])){

          foreach ($getFilters["id_cat"][$id][0] as $key => $value) {

          $items = "";

             if($value["always"] == 1){
                 $always = '<span style="color: red;" >*</span>';
                 $always_input = '<input type="hidden" name="always['.$value["id"].']" value="'.$value["name"].'" />';
             }else{
                 $always = ''; $always_input = '';
             }

             if($value["ad_type"] == "select"){

                 foreach ($value["items"] as $item_key => $item_value) {

                    if($variants["results"][$value["id"]]){
                      if($this->checkVariantsSelected($value["id"],$item_value,$variants["results"]) == true){
                         $selected = 'selected=""';
                      }else{
                         $selected = '';
                      }
                    }

                    $items .= '
                     <option value="'.$item_value.'" '.$selected.' data-id="'.$value["id"].'" >'.$item_value.'</option>
                    ';
                 }

                 $return .= '
                      <div class="form-group row">
                         <label class="col-lg-4 col-form-label">'.$value["name"].$always.'</label>
                         <div class="col-lg-8 col-xl-6">
                               <select name="filter['.$value["id"].']" title="Не выбрано" class="change-filter-item selectpicker" >
                               '.$items.'
                               </select>
                               '.$always_input.'
                         </div>
                      </div>
                      <div class="ad-box-podfilters'.$value["id"].'" >'.$this->ad_load_podfilters_category($value["id"],$variants["value"][$value["id"]]["value"],$variants).'</div>
                 ';

             }elseif($value["ad_type"] == "select_multi"){

                 foreach ($value["items"] as $item_key => $item_value) {
                    $items .= '
                     <option value="'.$item_value.'"  data-id="'.$value["id"].'" >'.$item_value.'</option>
                    ';
                 }


                 $return .= '
                      <div class="form-group row">
                         <label class="col-lg-4 col-form-label">'.$value["name"].$always.'</label>
                         <div class="col-lg-8 col-xl-6">
                               <select name="filter['.$value["id"].']" title="Не выбрано" multiple="" class="change-filter-item selectpicker" >
                               '.$items.'
                               </select>
                               '.$always_input.'
                         </div>
                      </div>
                 ';

             }elseif($value["ad_type"] == "slider"){

              if($variants["value"][$value["id"]]["value"]){
                 $from = (int)$variants["value"][$value["id"]]["value"];
              }else{
                 $from = (int)$value["items"][0];
              }

                 $return .= '
                      <div class="form-group row">
                         <label class="col-lg-4 col-form-label">'.$value["name"].$always.'</label>
                         <div class="col-lg-8 col-xl-6">

                                <input type="text" class="slider'.$value["id"].'" name="filter['.$value["id"].']" value="0" />
                                <script type="text/javascript">
                                  $(document).ready(function(){

                                        $(".slider'.$value["id"].'").ionRangeSlider({
                                            min: '.intval($value["items"][0]).',
                                            max: '.intval($value["items"][1]).',
                                            type: "single",
                                            from: '.$from.',
                                            grid: true
                                        });


                                  }); 
                                </script>

                         </div>
                      </div>
                 ';

             }elseif($value["ad_type"] == "toggle"){

               if(count($value["items"]) > 0){
                   foreach ($value["items"] as $item_key => $item_value) {

                    if($variants["results"][$value["id"]]){
                      if($this->checkVariantsSelected($value["id"],$item_value,$variants["results"]) == true){
                         $selected = 'checked=""'; $active = 'active';
                      }else{
                         $selected = ''; $active = '';
                      }
                    }

                      $items .= '
                       <label class="btn btn-primary btn-sm '.$active.'"><input type="radio" '.$selected.' class="change-filter-item" name="filter['.$value["id"].']" data-id="'.$value["id"].'" value="'.$item_value.'" autocomplete="off">'.$item_value.'</label>
                      ';
                   }
               }

               $return .= '
                    <div class="form-group row">
                       <label class="col-lg-4 col-form-label">'.$value["name"].$always.'</label>
                       <div class="col-lg-8 col-xl-6">
                          <div class="btn-group btn-group-toggle" data-toggle="buttons">
                             '.$items.'
                             '.$always_input.'
                          </div>
                       </div>
                    </div>
                    <div class="ad-box-podfilters'.$value["id"].'" >'.$this->ad_load_podfilters_category($value["id"],$variants["value"][$value["id"]]["value"],$variants).'</div>               
               ';

             }elseif($value["ad_type"] == "input"){

                   $return .= '
                        <div class="form-group row">
                           <label class="col-lg-4 col-form-label">'.$value["name"].$always.'</label>
                           <div class="col-lg-2 col-xl-2">
                              <input type="text" class="form-control" maxlength="10"  oninput="this.value = this.value.replace(/[^.0-9]/gim, \'\')" name="filter['.$value["id"].']" placeholder="'.$languages_content["class-filters-title-34"].' '.$value["items"][0].' '.$languages_content["class-filters-title-35"].' '.$value["items"][1].'" value="'.$variants["value"][$value["id"]]["value"].'" />
                           </div>
                        </div>
                   ';                        

              }

          }

       }

     return $return;

   } 

   function ad_load_podfilters_category($id,$filter_name, $variants = array()){
    global $languages_content;
       $getFilters = $this->getFilters("where visible=1");

       if(isset($getFilters["id_parent"][$id])){

          foreach ($getFilters["id_parent"][$id] as $parent_value) {

            $sql = db_query_while("select *, (select value from uni_multilanguage where id_content = uni_filters_items.id and field='name' and table_name='uni_filters_items' and lang='".lang()."' limit 1) as lang_value from uni_filters_items where id_filter={$parent_value["id"]} and name='$filter_name'");

             if(count($sql) > 0){
                foreach ($sql as $key => $value) {

                $items = "";

                 if($parent_value["always"] == 1){
                     $always = '<span style="color: red;" >*</span>';
                     $always_input = '<input type="hidden" name="always['.$value["id"].']" value="'.$parent_value["name"].'" />';
                 }else{
                     $always = ''; $always_input = '';
                 }

                $explode = explode("\n",urldecode($value["value"]));
                $explode_lang = !empty($value['lang_value']) ? explode("\n",urldecode($value['lang_value'])) : explode("\n",urldecode($value["value"]));

                    if(count($explode) > 0){

                       if($parent_value["ad_type"] == "select"){

                           foreach ($explode as $item_key => $item_value) {

                              if($variants["results"][$value["id"]]){
                                if($this->checkVariantsSelected($value["id"],$item_value,$variants["results"]) == true){
                                   $selected = 'selected=""';
                                }else{
                                   $selected = '';
                                }
                              }

                              $items .= '
                               <option value="'.$item_value.'" '.$selected.' data-id="'.$parent_value["id"].'" >'.$explode_lang[$item_key].'</option>
                              ';
                           }


                           $return .= '
                                <div class="form-group row">
                                   <label class="col-lg-4 col-form-label">'.$parent_value["name"].$always.'</label>
                                   <div class="col-lg-8 col-xl-6">
                                         <select name="filter['.$value["id"].']" title="'.$languages_content["class-filters-title-32"].'" class="change-filter-item selectpicker" >
                                         '.$items.'
                                         </select>
                                         '.$always_input.'
                                   </div>
                                </div>
                                <div class="ad-box-podfilters'.$parent_value["id"].'" >'.$this->ad_load_podfilters_category($parent_value["id"],$variants["value"][$parent_value["id"]]["value"],$variants).'</div>
                           ';

                       }elseif($parent_value["ad_type"] == "select_multi"){

                           foreach ($explode as $item_key => $item_value) {

                              if($variants["results"][$value["id"]]){
                                if($this->checkVariantsSelected($value["id"],$item_value,$variants["results"]) == true){
                                   $selected = 'selected=""';
                                }else{
                                   $selected = '';
                                }
                              }

                              $items .= '
                               <option value="'.$item_value.'" '.$selected.' data-id="'.$parent_value["id"].'" >'.$explode_lang[$item_key].'</option>
                              ';
                           }


                           $return .= '
                                <div class="form-group row">
                                   <label class="col-lg-4 col-form-label">'.$parent_value["name"].$always.'</label>
                                   <div class="col-lg-8 col-xl-6">
                                         <select name="filter['.$value["id"].']" multiple="" title="'.$languages_content["class-filters-title-32"].'" class="change-filter-item selectpicker" >
                                         '.$items.'
                                         </select>
                                         '.$always_input.'
                                   </div>
                                </div>
                           ';

                       }elseif($parent_value["ad_type"] == "slider"){

                          if($variants["value"][$value["id"]]){
                             $from = (int)$variants["value"][$value["id"]]["value"];
                          }else{
                             $from = (int)$explode[0];
                          }

                           $return .= '
                                <div class="form-group row">
                                   <label class="col-lg-4 col-form-label">'.$parent_value["name"].$always.'</label>
                                   <div class="col-lg-8 col-xl-6">

                                          <input type="text" class="slider'.$value["id"].'-'.$parent_value["id"].'" name="filter['.$value["id"].']" value="0" />
                                          <script type="text/javascript">
                                            $(document).ready(function(){

                                                  $(".slider'.$value["id"].'-'.$parent_value["id"].'").ionRangeSlider({
                                                      min: '.intval($explode[0]).',
                                                      max: '.intval($explode[1]).',
                                                      type: "single",
                                                      from: '.$from.',
                                                      grid: true
                                                  });

                                            }); 
                                          </script>

                                   </div>
                                </div>
                           ';

                       }elseif($parent_value["ad_type"] == "toggle"){

                           foreach ($explode as $item_key => $item_value) {

                              if($variants["results"][$value["id"]]){
                                if($this->checkVariantsSelected($value["id"],$item_value,$variants["results"]) == true){
                                   $selected = 'checked=""'; $active = 'active';
                                }else{
                                   $selected = ''; $active = '';
                                }
                              }

                              $items .= '
                               <label class="btn btn-primary btn-sm '.$active.'"><input type="radio" '.$selected.' class="change-filter-item" name="filter['.$value["id"].']" data-id="'.$parent_value["id"].'" value="'.$item_value.'" autocomplete="off">'.$explode_lang[$item_key].'</label>
                              ';
                           }


                           $return .= '
                                <div class="form-group row">
                                   <label class="col-lg-4 col-form-label">'.$parent_value["name"].$always.'</label>
                                   <div class="col-lg-8 col-xl-6">
                                      <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                         '.$items.'
                                         '.$always_input.'
                                      </div>
                                   </div>
                                </div>
                                <div class="ad-box-podfilters'.$parent_value["id"].'" >'.$this->ad_load_podfilters_category($parent_value["id"],$variants["value"][$value["id"]]["value"],$variants).'</div>
                           ';

                       }elseif($parent_value["ad_type"] == "input"){

                           $return .= '
                                <div class="form-group row">
                                   <label class="col-lg-4 col-form-label">'.$parent_value["name"].$always.'</label>
                                   <div class="col-lg-2 col-xl-2">
                                      <input type="text" class="form-control" maxlength="10" oninput="this.value = this.value.replace(/[^.0-9]/gim, \'\')" name="filter['.$value["id"].']" placeholder="'.$languages_content["class-filters-title-34"].' '.$explode[0].' '.$languages_content["class-filters-title-35"].' '.$explode[1].'" value="'.$variants["value"][$value["id"]]["value"].'" />
                                   </div>
                                </div>
                           ';                        

                       }
                    }

                }
             }


          }

       }

     return $return;

   }


    function addVariants($array = array(),$cat_id=0,$ads_id=0, $reverse = false){
      $query = array();
      
      db_insert_update("DELETE FROM uni_filters_variants WHERE ads_id='$ads_id'");
        if(count($array) > 0){    
        if($reverse) $array = array_reverse($array, true);     
            foreach($array AS $f_id=>$f_val){
               if(!is_array($f_val)){ 
                 if($f_val != "null" && !empty($f_val)) { $query[] = "('".$f_id."','".$f_val."','".$cat_id."','".$ads_id."')"; }
               }else{
                   foreach($f_val AS $key => $f_podval){
                      if($f_id && !empty($f_podval) && $f_podval != "null") $query[] = "('".$f_id."','".$f_podval."','".$cat_id."','".$ads_id."')";  
                   }
               }        
            }
            if(isset($query)){

              db_insert_update("INSERT INTO uni_filters_variants(id_filter,value,cat_id,ads_id)VALUES ".implode(",", $query));

            }
          $query = array();                      
        }
    }

    function addVariantsImport($array = array(),$cat_id=0,$ads_id=0, $reverse = false){
      $query = array();
      
      db_insert_update("DELETE FROM uni_filters_variants WHERE ads_id='$ads_id'");
        if(count($array) > 0){    
        if($reverse) $array = array_reverse($array, true);     
            foreach($array AS $f_id=>$f_val){
               if(!is_array($f_val)){ 
                   $query[] = "('".$f_id."','".$f_val."','".$cat_id."','".$ads_id."')";
               }else{
                   foreach($f_val AS $key => $f_podval){
                      $query[] = "('".$f_id."','".$f_podval."','".$cat_id."','".$ads_id."')";  
                   }
               }        
            }
            if(isset($query)){

              db_insert_update("INSERT INTO uni_filters_variants(id_filter,value,cat_id,ads_id)VALUES ".implode(",", $query));

            }
          $query = array();                      
        }
    }

   function catalog_load_filters_category($id){
    global $languages_content;

       $getFilters = $this->getFilters("where visible=1");

       if(isset($getFilters["id_cat"][$id][0])){

          foreach ($getFilters["id_cat"][$id][0] as $key => $value) {

          $items = "";

             if($value["type"] == "select"){

               if(count($value["items"]) > 0){
                   foreach ($value["items"] as $item_key => $item_value) {

                    if($this->checkSelected($value["id"],$item_value) == true){
                       $selected = 'selected=""';
                    }else{
                       $selected = '';
                    }

                      $items .= '
                       <option '.$selected.' value="'.$item_value.'" >'.$value["items_lang"][$item_key].'</option>
                      ';
                   }
               }

               $return .= '
                   <li id-filter="'.$value["id"].'" main-id-filter="0" >
                    <select data-width="100%" title="'.$value["name"].'" id-filter="'.$value["id"].'" class="catalog-change-filter selectpicker" name="filter['.$value["id"].'][]" >
                      <option value="null" >'.$languages_content["class-filters-title-33"].'</option>
                      '.$items.'
                    </select>
                   </li>                              
               '.$this->catalog_load_podfilters_category($value["id"],$_GET["filter"][$value["id"]][0]);


             }elseif($value["type"] == "select_multi"){


               if(count($value["items"]) > 0){
                   foreach ($value["items"] as $item_key => $item_value) {

                    if($this->checkSelected($value["id"],$item_value) == true){
                       $selected = 'selected=""';
                    }else{
                       $selected = '';
                    }

                      $items .= '
                       <option '.$selected.' value="'.$item_value.'" >'.$value["items_lang"][$item_key].'</option>
                      ';
                   }
               }

               $return .= '
                   <li id-filter="'.$value["id"].'" main-id-filter="0" >
                    <select data-width="100%" id-filter="'.$value["id"].'" title="'.$value["name"].'" class="catalog-change-filter selectpicker" multiple="" name="filter['.$value["id"].'][]" >
                      '.$items.'
                    </select>
                   </li>                              
               ';


             }elseif($value["type"] == "slider"){


                if(isset($_GET["filter"]["slider"][$value["id"]])){
                   $sliderCount = explode(";",$_GET["filter"]["slider"][$value["id"]][0]); 
                   $slideStart = intval($sliderCount[0]);
                   $slideEnd = intval($sliderCount[1]);
                }else{
                   $slideStart = intval($value["items"][0]);
                   $slideEnd = intval($value["items"][1]);                           
                }


                   $return .= '
                       <li id-filter="'.$value["id"].'" main-id-filter="0" >

                       <strong style="font-size: 13px; color: #999; position: absolute; top: 0px; left: 0px;" >'.$value["name"].' ('.$languages_content["class-filters-title-34"].' <span class="slider-start-hint'.$value["id"].'" >'.$slideStart.'</span> '.$languages_content["class-filters-title-35"].' <span class="slider-end-hint'.$value["id"].'" >'.$slideEnd.'</span>)</strong>

                            <input type="text" id-filter="'.$value["id"].'" class="slider'.$value["id"].'" name="filter[slider]['.$value["id"].'][]" value="null" />
                            <script type="text/javascript">
                              $(document).ready(function(){

                                   function update_filters_counts_ads(form){
                                      $(".button-filter-result").html(\'<i style="font-size: 15px" class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>\');
                                      $.ajax({type: "POST",url: "/ajax/ads",data: form+"&action=catalog-update-filter-count-ads",dataType: "json",cache: false,
                                          success: function (data) {
                                             if(data["count"] > 0) $(".button-filter-result").html(data["button"]).attr("disabled", false); else $(".button-filter-result").html(data["button"]).attr("disabled", true);
                                          }
                                      });    
                                   }
                                    
                                    var $range = $(".slider'.$value["id"].'");
                                    $range.ionRangeSlider({
                                        min: '.intval($value["items"][0]).',
                                        max: '.intval($value["items"][1]).',
                                        type: "double",
                                        from: '.$slideStart.',
                                        to: '.$slideEnd.',
                                        grid: false,
                                        hide_min_max: true,
                                        hide_from_to: true,
                                        onFinish: function (data) {

                                            var form_data = $(".filter-form").serialize();           

                                            var hashes = window.location.href.split("?");
                                            history.pushState("", "", hashes[0]+"?"+form_data);

                                            update_filters_counts_ads(form_data);
                                        }                                                
                                    });

                                    $range.on("change", function () {
                                        var $this = $(this),
                                            value = $this.prop("value").split(";");
                                            $(".slider-start-hint'.$value["id"].'").html(value[0]);
                                            $(".slider-end-hint'.$value["id"].'").html(value[1]);
                                    });


                              }); 
                            </script>                    
                       </li>                              
                   ';


             }elseif($value["type"] == "toggle"){

               if(count($value["items"]) > 0){
                   foreach ($value["items"] as $item_key => $item_value) {
                      $items .= '
                       <label class="btn btn-primary btn-sm"><input type="radio" class="change-filter-item" name="filter['.$value["id"].'][]" id="'.translite($value["name"]).$item_key.'" data-id="'.$value["id"].'" value="'.$item_value.'" autocomplete="off">'.$value["items_lang"][$item_key].'</label>
                      ';
                   }
               }

               $return .= '
                    <div class="form-group row">
                       <label class="col-lg-4 col-form-label">'.$value["name"].$always.'</label>
                       <div class="col-lg-8 col-xl-6">
                          <div class="btn-group btn-group-toggle" data-toggle="buttons">
                             '.$items.'
                             '.$always_input.'
                          </div>
                       </div>
                    </div>
                    <div class="ad-box-podfilters" ></div>               
               ';

             }

          }

       }

     return $return;

   }

   function catalog_load_podfilters_category($id,$filter_name){
    global $languages_content;
       $getFilters = $this->getFilters("where visible=1");

       if(isset($getFilters["id_parent"][$id])){

          foreach ($getFilters["id_parent"][$id] as $parent_value) {

            $sql = db_query_while("select *, (select value from uni_multilanguage where id_content = uni_filters_items.id and field='name' and table_name='uni_filters_items' and lang='".lang()."' limit 1) as lang_value from uni_filters_items where id_filter={$parent_value["id"]} and name='$filter_name'");
             
             if(count($sql) > 0){
                foreach ($sql as $key => $value) {

                $items = "";

                $explode = explode("\n",urldecode($value["value"]));
                $explode_lang = !empty($value['lang_value']) ? explode("\n",urldecode($value['lang_value'])) : explode("\n",urldecode($value["value"]));
                
                    if(count($explode) > 0){

                       if($parent_value["type"] == "select"){

                           foreach ($explode as $item_key => $item_value) {

                              if($this->checkSelected($value["id"],$item_value) == true){
                                 $selected = 'selected=""';
                              }else{
                                 $selected = '';
                              }

                              $items .= '
                               <option '.$selected.' value="'.$item_value.'" >'.$explode_lang[$item_key].'</option>
                              ';
                           }

                           $return .= '
                               <li id-filter="'.$parent_value["id"].'" main-id-filter="'.$id.'" >
                                <select data-width="100%" title="'.$parent_value["name"].'" class="catalog-change-filter selectpicker" id-filter="'.$parent_value["id"].'" name="filter['.$value["id"].'][]" >
                                  <option value="null" >'.$languages_content["class-filters-title-33"].'</option>
                                  '.$items.'
                                </select>
                               </li>                              
                           '.$this->catalog_load_podfilters_category($parent_value["id"],$_GET["filter"][$value["id"]][0]);

                       }elseif($parent_value["type"] == "select_multi"){


                           foreach ($explode as $item_key => $item_value) {

                              if($this->checkSelected($value["id"],$item_value) == true){
                                 $selected = 'selected=""';
                              }else{
                                 $selected = '';
                              }

                              $items .= '
                               <option '.$selected.' value="'.$item_value.'" >'.$explode_lang[$item_key].'</option>
                              ';
                           }


                           $return .= '
                               <li id-filter="'.$value["id"].'" main-id-filter="'.$id.'" >
                                <select data-width="100%" title="'.$parent_value["name"].'" id-filter="'.$value["id"].'" class="catalog-change-filter selectpicker" multiple="" name="filter['.$value["id"].'][]" >
                                  '.$items.'
                                </select>
                               </li>                              
                           ';

                       }elseif($parent_value["type"] == "slider"){


                        if(isset($_GET["filter"]["slider"][$value["id"]])){
                           $sliderCount = explode(";",$_GET["filter"]["slider"][$value["id"]][0]); 
                           $slideStart = intval($sliderCount[0]);
                           $slideEnd = intval($sliderCount[1]);
                        }else{
                           $slideStart = intval($explode[0]);
                           $slideEnd = intval($explode[1]);                           
                        }


                           $return .= '
                               <li id-filter="'.$value["id"].'" main-id-filter="'.$id.'" >

                               <strong style="font-size: 13px; color: #999; position: absolute; top: 0px; left: 0px;" >'.$parent_value["name"].' ('.$languages_content["class-filters-title-34"].' <span class="slider-start-hint'.$value["id"].'-'.$parent_value["id"].'" >'.$slideStart.'</span> '.$languages_content["class-filters-title-35"].' <span class="slider-end-hint'.$value["id"].'-'.$parent_value["id"].'" >'.$slideEnd.'</span>)</strong>

                                    <input type="text" id-filter="'.$value["id"].'" class="slider'.$value["id"].'-'.$parent_value["id"].'" name="filter[slider]['.$value["id"].'][]" value="null" />
                                    <script type="text/javascript">
                                      $(document).ready(function(){

                                           function update_filters_counts_ads(form){
                                              $(".button-filter-result").html(\'<i style="font-size: 15px" class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>\');
                                              $.ajax({type: "POST",url: "/ajax/ads",data: form+"&action=catalog-update-filter-count-ads",dataType: "json",cache: false,
                                                  success: function (data) {
                                                     if(data["count"] > 0) $(".button-filter-result").html(data["button"]).attr("disabled", false); else $(".button-filter-result").html(data["button"]).attr("disabled", true);
                                                  }
                                              });    
                                           }
                                            
                                            var $range = $(".slider'.$value["id"].'-'.$parent_value["id"].'");
                                            $range.ionRangeSlider({
                                                min: '.intval($explode[0]).',
                                                max: '.intval($explode[1]).',
                                                type: "double",
                                                from: '.$slideStart.',
                                                to: '.$slideEnd.',
                                                grid: false,
                                                hide_min_max: true,
                                                hide_from_to: true,
                                                onFinish: function (data) {

                                                    var form_data = $(".filter-form").serialize();          

                                                    var hashes = window.location.href.split("?");
                                                    history.pushState("", "", hashes[0]+"?"+form_data);

                                                    update_filters_counts_ads(form_data);
                                                    
                                                }                                                
                                            });

                                            $range.on("change", function () {
                                                var $this = $(this),
                                                    value = $this.prop("value").split(";");
                                                    $(".slider-start-hint'.$value["id"].'-'.$parent_value["id"].'").html(value[0]);
                                                    $(".slider-end-hint'.$value["id"].'-'.$parent_value["id"].'").html(value[1]);
                                            });


                                      }); 
                                    </script>                    
                               </li>                              
                           ';


                       }elseif($parent_value["type"] == "toggle"){

                           foreach ($explode as $item_key => $item_value) {

                              if($this->checkSelected($value["id"],$item_value) == true){
                                 $selected = 'checked=""';
                                 $active = 'active';
                              }else{
                                 $selected = '';
                                 $active = '';
                              }

                              $items .= '
                             <label class="btn btn-primary btn-sm '.$active.'"><input type="radio" '.$selected.' class="catalog-change-filter" name="filter['.$value["id"].'][]" id-filter="'.$parent_value["id"].'" value="'.$item_value.'" autocomplete="off">'.$explode_lang[$item_key].'</label>
                            ';
                           }

                           $return .= '
                               <li id-filter="'.$parent_value["id"].'" main-id-filter="'.$id.'" >
                                  <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                     '.$items.'
                                  </div>                               
                               </li>                              
                           '.$this->catalog_load_podfilters_category($parent_value["id"],$_GET["filter"][$value["id"]][0]);


                       }
                    }

                }
             }


          }

       }

     return $return;

   }

   function checkSelected($id = 0,$value = ""){
       if(isset($_GET["filter"][$id])){
           foreach ($_GET["filter"][$id] as $key => $fl_value) {
               if($fl_value == $value) return true;
           }
       }
   }

   function fastCheckSelected($filter=array(), $id = 0,$value = ""){
       if(isset($filter[$id])){
           foreach ($filter[$id] as $key => $fl_value) {
               if($fl_value == $value) return true;
           }
       }
   }

   function checkVariantsSelected($id = 0,$value = "",$variants = array()){
       if(isset($variants[$id])){
           foreach ($variants[$id] as $name => $fl_value) {
               if($name == $value) return true;
           }
       }
   }

    function queryString($string = ""){
       if($string){
         parse_str($string, $query_params);
         unset($query_params["_"]);
         unset($query_params["page"]);
         unset($query_params["currency"]);
         unset($query_params["action"]);
         return http_build_query($query_params, 'flags_');
       }
    }

    function fastFilters($id_cat = 0){
    
      if($id_cat){
          $filters = db_query_while("select *, (select value from uni_multilanguage where id_content = uni_filters.id and field='name' and table_name='uni_filters' and lang='".lang()."') as lang_name from uni_filters where id_cat=$id_cat and visible=1 and type != 'slider' limit 3");
          if(count($filters) > 0){
            foreach ($filters as $key => $value) {

              $value["name"] = !empty($value['lang_name']) ? urldecode($value['lang_name']) : $value['name'];

              if(!$value["id_parent"]){
                $items = db_query("select *, (select value from uni_multilanguage where id_content = uni_filters_items.id and field='name' and table_name='uni_filters_items' and lang='".lang()."' limit 1) as lang_value from uni_filters_items where id_filter={$value["id"]}");
                
                if(count($items) == 0){
                  $disabled = 'disabled=""';
                }else{ $disabled = ''; $explode = explode("\n",urldecode($items["value"])); $explode_lang = !empty($items['lang_value']) ? explode("\n",urldecode($items['lang_value'])) : explode("\n",urldecode($items["value"])); }
                    
                ?>
                  <div>
                    <select name="filter[<?php echo $value["id"]; ?>][]" class="fast-filter-change-value" <?php echo $disabled; ?> ><option value="null" ><?php echo $value["name"]; ?></option>

                      <?php
                      if(count($explode) > 0){
                        foreach ($explode as $item_key => $item_value) {

                          if($this->fastCheckSelected($_POST["filter"],$value["id"],$item_value) == true){
                            $selected = 'selected=""';
                          }else{ $selected = ''; }

                          ?>
                           <option <?php echo $selected; ?> value="<?php echo $item_value; ?>" ><?php echo $explode_lang[$item_key]; ?></option>
                          <?php
                        }
                      }
                      ?>

                    </select>
                  </div>
                <?php
              }else{

                $items = db_query("select *, (select value from uni_multilanguage where id_content = uni_filters_items.id and field='name' and table_name='uni_filters_items' and lang='".lang()."' limit 1) as lang_value from uni_filters_items where id_filter={$value["id"]} and name='".clear($_POST["filter"][$value["id_parent"]][0])."'");

                if(count($items) == 0){
                  $disabled = 'disabled=""';
                }else{ $disabled = ''; $explode = explode("\n",urldecode($items["value"])); $explode_lang = !empty($items['lang_value']) ? explode("\n",urldecode($items['lang_value'])) : explode("\n",urldecode($items["value"])); }
                    
                ?>
                  <div>
                    <select name="filter[<?php echo $items["id"]; ?>][]" class="fast-filter-change-value" <?php echo $disabled; ?> ><option value="null" ><?php echo $value["name"]; ?></option>

                      <?php
                      if(count($explode) > 0){
                        foreach ($explode as $item_key => $item_value) {

                          if($this->fastCheckSelected($_POST["filter"],$items["id"],$item_value) == true){
                            $selected = 'selected=""';
                          }else{ $selected = ''; }

                          ?>
                           <option <?php echo $selected; ?> value="<?php echo $item_value; ?>" ><?php echo $explode_lang[$item_key]; ?></option>
                          <?php
                        }
                      }
                      ?>

                    </select>
                  </div>
                <?php                
              }

            }
          }
      }

    }


}


$Filters = new Filters();

?>