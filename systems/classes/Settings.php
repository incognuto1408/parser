<?php
class Settings
{
    private
        $array = [];
    private
        $array_const = [
        "settings" => [
            "page_output" => 30,//по скольтко выводить
            "color_table_line" => "#415465",//цвет полей таблицы
            "color_table_line_2" => "#2b3e4f",//цвет четных полей таблицы
            "mailing" => true,//цвет четных полей таблицы
            "text_message" => "Поздравляем! Вы купили домен {name_domain} Мы поможем создать продающий сайт любой сложности и запустить рекламу trk.kz",
            "time_zone" => "3",//цвет четных полей таблицы
        ],
        "name_type_person" => [
            0 => "Пользователь",
            1 => "Модератор",
            2 => "Администратор",
            3 => "Суперпользователь",
        ]
    ];
    function __construct()
    {
        $arr = db_query_while("SELECT * FROM settings");
        if (count($arr) > 0){
            foreach ($arr as $item) {
                $this->array[$item['settings_name']] = json_decode($item['settings_array'], true);
            }
        }
        $this->check();
    }
    function set($name, $array){
        $sql = db_query_while("SELECT * FROM settings WHERE settings_name='".$name."'");
        if(count($sql) > 0)
            db_insert_update("UPDATE settings SET settings_array = '".json_encode($array, JSON_UNESCAPED_UNICODE)."' WHERE settings_name='".$name."'");
        else
            db_insert_update("INSERT INTO settings(settings_name,settings_array)VALUES('".$name."', '".json_encode($array, JSON_UNESCAPED_UNICODE)."')");
    }
    function setSettings($array){
        $sql = db_query_while("SELECT * FROM settings WHERE settings_name='settings'");
        //$array = array_merge($array, $sql['settings_array']);
        $res = 0;
        if(count($sql) > 0)
            $res = db_insert_update("UPDATE settings SET settings_array = '".json_encode($array, JSON_UNESCAPED_UNICODE)."' WHERE settings_name='settings'");
        return $res;
    }
    function check()
    {
        foreach ($this->array_const as $key_1 => $level_1) {
            $flag = false;
            if(!isset($this->array[$key_1])){
                $this->array[$key_1] = [];
                $this->set($key_1, $level_1);
            }
            foreach ($level_1 as $key_2 => $level_2) {
                if(!isset($this->array[$key_1][$key_2])){
                    $this->array[$key_1][$key_2] = $level_2;
                    $flag = true;
                }
            }
            if($flag){
                $this->set($key_1, $this->array[$key_1]);
            }
        }

        return $this->array;
    }
    function get($key = false){ return $key ? $this->array[$key] : $this->array; }

}

$Settings = new Settings();
$settings = $Settings->get();