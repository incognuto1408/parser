<?php
$profile = $Profile->get_user_info($_SESSION['profile']['id']);
    if($profile['type_person'] >= 2) {
        echo OutTpl("settings.tpl");
    }else {
        require_once 'files/response/404/404_index.php';
    }
    ?>
