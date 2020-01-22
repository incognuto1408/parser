<?php


namespace CatecoryAll;


class CategoryAll
{
    function getNameTypePerson($query = "settings_name='name_type_person'"){
            $sql = db_query("SELECT * FROM settings WHERE $query");
            $array_sql = json_decode($sql['settings_array'],true);



            if (count($array_sql) > 0) {
                $cats = array();
                foreach($array_sql AS $key => $result){
                    $cats['id_parent'][$key] =  [
                        'id' => $key,
                        'name' => $result,
                    ];
                    $cats['id'][$key]['id'] =  $key;
                    $cats['id'][$key]['name'] =  $result;
                }
            }
            return $cats;

    }
    function outNameTypePerson($id_parent = 0, $level = "") {
        $getCategories = $this->getNameTypePerson();
        $out = "";
        if (isset($getCategories['id_parent'])) {
            foreach ($getCategories['id_parent'] as $value) {

                if($id_parent == $value["id"]){
                    $selected = 'selected=""';
                }else{
                    $selected = "";
                }

                $out .= '<option '.$selected.' value="' . $value["id"] . '" >'.$value["name"].'</option>';

            }
        }
        $sql = db_query("SELECT * FROM settings WHERE settings_name='name_type_person'");
        $array_sql = json_decode($sql['settings_array'], true);
        return $out;
    }
}
$CategoryAll = new CategoryAll();