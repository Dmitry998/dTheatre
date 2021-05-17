<?php
session_start();
if (isset($_COOKIE['token'])) {
	$token = $_COOKIE['token'];
	ConnectDB();
	$res = $mysqli->query("SELECT * FROM users WHERE token = '$token'");
	if ($res->num_rows == 1) {
		$user = $res->fetch_object();
		LogIn($user->id);
	}
}


function ConnectDB()
{
	global $mysqli;
	$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
}

function LogIn($userid)
{
	global $mysqli;
	$_SESSION['userid'] = $userid;
	$token = sprintf('%08x%08x%08x%08x', rand(), rand(), rand(), rand());
	setcookie('token', $token, time() + 3600 * 24 * 30);
	$res = $mysqli->query("UPDATE users SET token = '$token' WHERE id = '$userid'");
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title> Театр </title>
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
						$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');

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
	</div>
	<div class="content">
		<?php
		echo "<h2>О театре</h2>";
		echo "<div class='about'><table><tr><td><img src='Files/theatreHouse.png' alt=''><br></td><td><P>Первый театральный сезон открылся 27 ноября 1993 премьерой спектакля «Приключения Буратино в Стране Дураков» на сцене ДК «Современник». Основа труппы театра — участники Народного театра юного зрителя при ДК «Современник» (руководитель Лидия Михайловна Ершова с 1971 г.). Собственное здание (бывший киноклуб «Дружба») театр получил в декабре 1995 г. и вселился в него, не ожидая реконструкции.</p></td></tr></table>

		<p>В ноябре 2003 года завершились ремонт и реконструкция театра, в результате которой к основному зданию был сооружён пристрой, позволивший организовать закулисное пространство и получить подсобные помещения. Зрительный зал театра рассчитан на 81 место.</p>
		
		<p> Ежегодно театр выпускает 5-6 премьер, каждая из которых становится заметным событием в культурной жизни города. За 27 творческих сезонов было поставлено более 170 спектаклей и представлений для детей и взрослых.</p></div>";
		echo "<h2>Все сеансы</h2>";
		$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
		$res = $mysqli->query("SELECT sessions.id as id, DATE_FORMAT(sessions.date, '%d.%m.%Y %H:%i') as date, spectacles.name as name FROM sessions INNER JOIN spectacles ON spectacles.id=sessions.spectacle_id ORDER BY date");
		echo "<div id= allSessions>";
		echo "<table>";

		echo "<tr><th>Спектакль</th><th>Дата сеанса</th></tr>";
		$indexRow = 0;
		while ($session =  $res->fetch_object()) {
			$id = $session->id;
			$name = $session->name;
			$date = $session->date;
			$photo = $actor->photo;
			echo "<tr><td><a id='actor' href='pages/session.php?session_id=$id'>$name</a></td><td><a id='actor' href='pages/session.php?session_id=$id'>$date</a></td></tr>";
		}
		echo "</tr></table></div>";
		?>
		<div class='mainPage'>
		</div>
	</div>
	<div class="footer">
		8(960)319-71-39 г. Заречный Пензенская область<br>
		Горячая линия
	</div>
</body>

</html>