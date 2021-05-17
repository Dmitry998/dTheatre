<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Театр </title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="header">
		<a class="logo" href="index.php"><img src="logoT.jpg" alt=""></a>
		<div class="top-menu">
			<ul>
				<li><a href="index.php">Главная</a></li>
				<li><a href="pages/actors.php">Актёры</a></li>
				<li><a href="pages/spectacles.php">Спектакли</a></li>
				<div class="auth">
					<?php
					if (isset($_SESSION['userid'])) {
						$userid = $_SESSION['userid'];
						$mysqli = new mysqli('localhost', 'root', '12345678', 'Shoe_store');

						$res = $mysqli->query("SELECT login From users WHERE id='$userid'");
						$user = $res->fetch_object();
						$login = $user->login;

						echo "<li><a href='pages/userTickets.php'>Вы вошли как $login </a></li>
				<li><a href='exit.php'>Выход</a></li>";
					} else {
						echo "<li><a href='authForm.php'>Вход</a></li>
				<li><a href='registrationForm.php'>Регистрация</a></li>";
					}
					?>
			</ul>
		</div>
	</div>
	<div class="content">
		<div class='formInput'>
			<h2>Регистрация</h2>
			<?php
			if (isset($_GET['error'])) {
				$error = $_GET['error'];
				echo '<div style="color: red;">' . $error . '</div><hr>';
			}
			if (isset($_GET['access'])) {
				echo '<div style="color: green;">Регистрация прошла успешно!</div><hr>';
			}
			echo "<form action='registration.php' method ='post'> 
            <p>Логин <input type='text' name ='login' value=''></p>
			<p>Почта <input type='text' name ='email' value=''></p>
            <p>Пароль <input type='password' name ='password' value=''></p>
            <p>Проверка пароля <input type='password' name ='password2' value=''></p>
            <p><button>Зарегистироваться</button></p>
            </form>";
			?>
		</div>
	</div>
	<div class="footer">
		   г. Пенза телефон 65-55-21<br>
		Горячая линия
	</div>
</body>

</html>