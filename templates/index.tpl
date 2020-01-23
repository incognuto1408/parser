<?php

global $name_page;
?>
<!DOCTYPE>
<html>
    <head>
        <title>
            <?php echo $name_page; ?>
        </title>
        <link rel='stylesheet' type='text/css' href='css/bootstrap-4.3.1-dist/bootstrap.min.css'/>
        <link rel='stylesheet' type='text/css' href='css/bootstrap-4.3.1-dist/bootstrap.css'/>
        <link rel='stylesheet' type='text/css' href='css/bootstrap-4.3.1-dist/bootstrap-grid.css'/>
        <link rel='stylesheet' type='text/css' href='css/bootstrap-4.3.1-dist/bootstrap-grid.min.css'/>
        <link rel='stylesheet' type='text/css' href='css/bootstrap-4.3.1-dist/bootstrap-reboot.css'/>
        <link rel='stylesheet' type='text/css' href='css/bootstrap-4.3.1-dist/bootstrap-reboot.min.css'/>
        <link href="https://gorod-masterov.kz/templates/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://gorod-masterov.kz/templates/css/bootstrap-select.min.css" rel="stylesheet">
        <script type="text/javascript" src="https://gorod-masterov.kz/admin/files/js/jquery-1.11.1.min.js"></script>
        <script type='text/javascript' src='/js/jquery-3.2.1.min.js'></script>


        <!--sweetalert2-->
        <script type='text/javascript' src='/js/sweetalert2/sweetalert2.all.min.js'></script>
        <script type='text/javascript' src='/js/sweetalert2/sweetalert2.all.js'></script>
        <script src="/js/sweetalert2/sweetalert2.min.js"></script>
        <script type='text/javascript' src='/js/sweetalert2/sweetalert2.js'></script>
        <link rel="stylesheet" href="/js/sweetalert2/sweetalert2.min.css">


        <link rel='stylesheet' type='text/css' href='css/style.css'/>
        <meta charset="utf-8">
        <script>
            $(document).ready(function() {
                $(window).on("load", function () {
                    $(".loader").fadeOut();
                    $("#preloader").delay(150).fadeOut("slow")
                });
                window.onload = function () {

                    $.ajax({type: "POST",url: "/ajax/metrics/",data: "city=&region=&country=&enter="+location.href+"&referrer="+document.referrer+"&title="+$("title").html()+"&latitude=&longitude=", dataType: "html",cache: false,success: function (data) {

                            if(data != false){
                                $(".fade-change-city strong").html(data);
                                $(".fade-change-city").show();
                            }

                        }});
                }
            });
        </script>
    </head>
    <body>

    <div class="proccess_load" >
        <div class="canvas">
            <div class="spinner"></div>
        </div>
    </div>
<!--    <div id="preloader">
        <div class="canvas">
            <div class="spinner"></div>
        </div>
    </div>-->
    <div class="full">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar_logon">
                <?php
                if(empty($_SESSION['profile']['id'])) {
                    ?>
                    <button class='logon_left' id='logon'>Авторизоваться</button>
                    <?php
                }else{

                    ?>
                    <button class='logon_left' id='logon'><?php echo $settings['name_type_person'][$_SESSION['profile']['type_person']];?></button>
                    <?php
                }
                ?>
            </div>
            <?php
                if(!empty($_SESSION['profile']['id'])) {
                    ?>
                    <ul class="list">
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "profile") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=profile">
                                Мой профиль
                            </a>
                        </li>
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "domains-list") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=domains-list">
                                Показать базу доменов
                            </a>
                        </li>
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "black-list") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=black-list">
                                Черный список
                            </a>
                        </li>
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "favorites") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=favorites">
                                Избранное
                            </a>
                        </li>
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "users") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=users">
                                Пользователи
                            </a>
                        </li>
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "settings") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=settings">
                                Настройки
                            </a>
                        </li>
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "info-message-list") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=info-message-list">
                                Лог сообщений
                            </a>
                        </li>
                        <li id="list_li_1" class="menu_control <?php if ($_GET['tab'] == "metrics") {
                            echo "menu_active";
                        } ?>">
                            <a class="parse_db tab_a" href="?tab=metrics">
                                Посещение
                            </a>
                        </li>
                    </ul>
                    <?php
                }
            ?>
        </aside>
        <main class="center">
            <div class="auther">
                        <span class="menu_slide">
                            <span class="cc">
                            <hr><hr><hr>
                            </span>
                        </span>
                <?php
                $balance = json_decode(file_get_contents('https://api.mobizon.kz/service/user/getownbalance?output=json&api=v1&apiKey='.$secret_key_mobile_message), true);
                ?>

                <?php echo "<span style='color: green;padding: 12px;display: inline-block;'>(Баланс: ",$balance['data']['balance']."".$balance['data']['currency'].")</span>";?>
                <?php
                if(!empty($_SESSION['profile']['id'])) {
                    ?>
                    <a class="logon profile-exit" href="?logout=1">
                        <?php
                        echo $_SESSION['profile']['login'];
                        ?>
                    </a>
                    <?php
                }else{
                    ?>
                    <a class="logon" id="logon2">
                        Вход
                    </a>
                    <?php
                }
                ?>
            </div>
            <div class="ajax_input">
                <?php
                if(!empty($_SESSION['profile']['id'])) {
                    $routing = [
                        "domains-list" => "route/domains-list.php",
                        "info-message-list" => "route/info-message-list.php",
                        "settings" => "route/settings.php",
                        "profile" => "route/profile.php",
                        "users" => "route/users.php",
                        "logon" => "route/logon.php",
                        "add-user" => "route/add-user.php",
                        "metrics" => "route/metrics_list.php",
                        "black-list" => "route/black-list.php",
                        "favorites" => "route/favorites.php",
                    ];
                    if (!empty($_GET['tab'])) {
                        $page = $routing[$_GET['tab']];
                        if ($page) {
                            if (file_exists($page)) {
                                require_once $page;
                            } else {
                                require_once 'files/response/404/404_index.php';
                            }
                        } else {
                            require_once 'files/response/404/404_index.php';//Тут нужно запилить скрипт на объявления
                        }

                    } else {
                        require_once "route/index.php";
                    }
                }else{
                    require_once "route/logon.php";
                }
                ?>
            </div>
        </main>
    </div>

    <script type='text/javascript' src='/js/script.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script type='text/javascript' src='/js/bootstrap-4.3.1-dist/bootstrap.min.js'></script>
    <script type='text/javascript' src='/js/bootstrap-4.3.1-dist/bootstrap.js'></script>
    <script type='text/javascript' src='/js/bootstrap-4.3.1-dist/bootstrap-select.min.js'></script>
    </body>
</html>