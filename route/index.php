<?php
if(isset($_GET["logout"])){
    unset($_SESSION['profile']);
}
$arr_name_page = [
        "domains-list" => "Таблица",
        "profile" => "Мой профиль",
        "settings" => "Общая тастройки",
        "users" => "Пользователи",
        "metrics" => "Посещение",
        "" => "Статистика",
];
    $name_page = $arr_name_page[$_GET['tab']];
echo OutTpl("index.tpl");
?>