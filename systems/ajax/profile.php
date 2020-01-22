<?php
if(isAjax() == true) {

    if ($_POST["action"] == "reg") {

        $error = array();

        if (empty($_POST["name"])) {
            $error[] = $languages_content["ajax-profile-title-1"];
        }
        if (validateEmail($_POST["email"]) == false) {
            $error[] = $languages_content["ajax-profile-title-2"];
        }
        if (mb_strlen($_POST["pass"], "UTF-8") < 6 || mb_strlen($_POST["pass"], "UTF-8") > 12) {
            $error[] = $languages_content["ajax-profile-title-3"];
        }

        if (!$_POST["conditions"]) {
            $error[] = $languages_content["ajax-profile-title-4"];
        }

        if (count($error) == 0) {
            $status = $Profile->reg(array("email" => $_POST["email"], "name" => $_POST["name"], "pass" => $_POST["pass"]), false);

            if ($status["id"]) {
                echo $languages_content["ajax-profile-title-5"];
            } else {
                echo true;
            }

        } else {
            echo implode("<br/>", $error);
        }

    }
    if ($_POST['action'] == "auth") {
        $error = array();

        $login = clear($_POST["login"]);
        $pass = $_POST["password"];

        if (empty($_POST["login"])) {
            $error[] = "Пожалуйста, укажите логин!";
        }
        if (empty($_POST["password"])) {
            $error[] = "Пожалуйста, укажите пароль!";
        }

        if (count($error) == 0) {

            $sql = db_query("SELECT * FROM users WHERE login = '$login' LIMIT 1");

            if (password_verify($pass . $private, $sql["password"])) {

                $_SESSION['profile']['id'] = $sql["id"];
                $_SESSION['profile']['fio'] = $sql["name"] . ' ' . $sql["surname"];
                $_SESSION['profile']['name'] = $sql["name"];
                $_SESSION['profile']['surname'] = $sql["surname"];
                $_SESSION['profile']['phone'] = $sql["phone"];
                $_SESSION['profile']['status'] = $sql["status"];
                $_SESSION['profile']['email'] = $sql["email"];
                $_SESSION['profile']['login'] = $sql["login"];
                $_SESSION['profile']['type_person'] = $sql["type_person"];
                echo true;

            } else {
                echo "Не верный логин и(или) пароль!";
            }

        } else {
            echo implode("<br/>", $error);
        }
    }

    if ($_POST['action'] == "update") {
        $error = [];
        if (strlen($_POST['name']) > 25)
            $error[] = "Имя не может содержать более 25 символов!";
        if (strlen($_POST['surname']) > 25)
            $error[] = "Фамилия не может содержать более 25 символов!";
        if (strlen($_POST['phone']) > 25)
            $error[] = "Телефон не может содержать более 25 символов!";
        if(!empty($_POST['email'])) {
            if(trim($_POST['email']) != "") {
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                    $error[] = "Введен не верный формат почты!";
                }else{
                    $email = $_POST['email'];
                }
            }
        }

            $sql = db_query("SELECT * FROM users WHERE id={$_SESSION['profile']['id']}");
            if (count($sql) == 0)
                $error[] = "Ошибка!!! Аккаунта не существует!!!!!";
        if(trim($_POST['password']) != "") {
            if (strlen($_POST['password']) < 6)
                $error[] = "Пароль не может содержать менее 6 символов!";
            if (strlen($_POST['password']) > 35)
                $error[] = "Пароль не может содержать более 35 символов!";
            $password =  password_hash($_POST['password'].$private, PASSWORD_DEFAULT);
        }else{
            $password = $sql['password'];
        }
        $ip = clear($_SERVER["REMOTE_ADDR"]);
        if(count($error) == 0){
            $update = db_insert_update("UPDATE users SET password='".$password."', name='".$_POST['name']."', surname='".$_POST['surname']."', phone='".$_POST['phone']."', email='".$_POST['email']."' WHERE id={$_SESSION['profile']['id']}");
            //$insert = db_insert_update("INSERT INTO users(login, password, name, surname, ip, email, avatar, status, type_person, datetime_add, phone, user_added)VALUES('".$_POST['login']."','".$password."','".$_POST['name']."','".$_POST['surname']."','".$ip."','".$_POST['email']."','".""."','".$_POST['status']."','".$_POST['type_person']."', NOW(),'".$_POST['phone']."','".$_POST['user_added']."')");
            echo $update;
        }else{
            echo implode("<br/>", $error);
        }
    }

    if ($_POST['action'] == "add") {
        $error = [];
        $_POST['type_person'] = (int)$_POST['type_person'];
        $_POST['status'] = (int)$_POST['status'];
        $strong = 0;
        if(empty($_POST['password']))
            $error[] = "Поле `Пароль` обязательно для заполнения!";

        if(empty($_POST['login']))
            $error[] = "Поле `Логин` обязательно для заполнения!";
        if (strlen($_POST['password']) < 6)
            $error[] = "Пароль не может содержать менее 6 символов!";
        if (strlen($_POST['password']) > 35)
            $error[] = "Пароль не может содержать более 35 символов!";
        /*if(!preg_match("/^[a-z][0-9]/i", $_POST['password']))
            $error[] = "Пароль должен содержать как минимум буквы и цифры!";*/
        if (strlen($_POST['login']) < 6)
            $error[] = "Логин не может содержать менее 6 символов!";
        if (strlen($_POST['login']) > 15)
            $error[] = "Логин не может содержать более 15 символов!";
        if (strlen($_POST['name']) > 25)
            $error[] = "Имя не может содержать более 25 символов!";
        if (strlen($_POST['surname']) > 25)
            $error[] = "Фамилия не может содержать более 25 символов!";
        if (strlen($_POST['phone']) > 25)
            $error[] = "Телефон не может содержать более 25 символов!";
        if(!empty($_POST['email'])) {
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                $error[] = "Введен не верный формат почты!";
            }
        }

        if(!empty($_POST["login"])) {
            $sql = db_query("SELECT * FROM users WHERE login='" . clear($_POST['login']) . "'");
            if (count($sql) > 0)
                $error[] = "Аккаунт с таким логином уже существует";
        }
        if(empty($_POST['type_person'])){
            $_POST['type_person'] = 0;
        }else{
            if($_POST['type_person'] >= (int)$_SESSION['profile']['type_person']){
                $error[] = "Недостаточно прав, для выдачи такого ранга пользователю! Ранг должен быть меньше вашего!!!";
            }
        }
        if($_POST['status'] != 0)
            $_POST['status'] = 1;
        $ip = clear($_SERVER["REMOTE_ADDR"]);
        $password =  password_hash($_POST['password'].$private, PASSWORD_DEFAULT);
        if(count($error) == 0){
            $insert = db_insert_update("INSERT INTO users(login, password, name, surname, ip, email, avatar, status, type_person, datetime_add, phone, user_added)VALUES('".$_POST['login']."','".$password."','".$_POST['name']."','".$_POST['surname']."','".$ip."','".$_POST['email']."','".""."','".$_POST['status']."','".$_POST['type_person']."', NOW(),'".$_POST['phone']."','".$_POST['user_added']."')");
            echo $insert;
        }else{
            echo implode("<br/>", $error);
        }
    }
}