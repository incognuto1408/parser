<?php
include $_SERVER['DOCUMENT_ROOT'].'/db/db.php';
header('Content-Type: text/html; charset=utf-8', true);
function ToWhirlpool($hash){
	return hash ("whirlpool",$hash);
}
$login = $_POST['login'];
$password = $_POST['password'];
$captcha = $_POST['captcha'];
$captcha = mb_strtoupper($captcha, 'UTF-8');//ВЕРХ РЕГИСТР
$website = $_POST['website'];
$server = $_POST['server'];
$original_captcha = $_SESSION['captcha'];
$ToWhirlpool = ToWhirlpool($password);
$strl_login = strlen($login);
$strl_password = strlen($password);
if($website == 'https://awareproject.ru/' && $server == '217.106.106.178:7249'){
	if($strl_login >= 3 && $strl_login <= 20){
		if($strl_password >= 6 && $strl_password <= 25){
			if ($captcha == $original_captcha) {
				$sql = "SELECT * FROM users WHERE login='{$login}' LIMIT 1";
				$result = $db->query($sql);
				$user = $result->fetch(PDO::FETCH_ASSOC);
				$pass_db = $user['password'];
				$login_db = $user['login'];
				if($user){
					if($login_db == $login && $pass_db == $ToWhirlpool){
						$_SESSION['password'] = $password;
						$_SESSION['login'] = $login;
					}else{
						$_SESSION['error'] = "Логин либо пароль не верны!!!<br>";
					}
				}else{
					$_SESSION['error'] = "Логин либо пароль не верны!!!<br>";
				}/*
				echo "Капча норм<br>";*/
			}else{
				$_SESSION['error'] = "Капча введена не верно, попробуйте еще раз<br>";
			}
		}else{
			$_SESSION['error'] = "Пароль не может быть менее 6 символов и более 25!!!<br>";
		}
	}else{
		$_SESSION['error'] = "Логин не может быть менее 3 символов и более 20!!!<br>";
	}
}else{
 $_SESSION['error'] = "ПОПЫТКА ВЗЛОМА!!!<br>";
}
$server = $_SERVER['HTTP_REFERER'];
echo "<meta http-equiv='Refresh' content='0; URL=$server'>";
?>