<?php session_start();
global $admin;
$admin = false;
global $login;
$login = null;

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title> Театр </title>
	<link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="header">
		<a class="logo" href="../index.php"><img src="../logoT.jpg" alt=""></a>
		<div class="top-menu">
			<ul>
				<li><a href="../index.php">Главная</a></li>
				<li><a href="actors.php">Актёры</a></li>
				<li><a href="spectacles.php">Спектакли</a></li>
				<div class="auth">
					<?php
					if (isset($_SESSION['userid'])) {
						$userid = $_SESSION['userid'];
						$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');

						$res = $mysqli->query("SELECT login, role From users WHERE id='$userid'");
						$user = $res->fetch_object();
						$login = $user->login;
						$role = $user->role;
						if ($role == 'admin') {
							$admin = true;
						}

						echo "<li><a href='basket.php'>Вы вошли как $login </a></li>
					<li><a href='exit.php'>Выход</a></li>";
					} else {
						echo "<li><a href='../authForm.php'>Вход</a></li>
					<li><a href='../registrationForm.php'>Регистрация</a></li>";
					}
					?>
			</ul>
		</div>
	</div>
	</div>
	<div class="content">
		<?php
		if (isset($_GET['actor_id'])) {
			$actorId = $_GET['actor_id'];
		}
		$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
		$res = $mysqli->query("SELECT * From actors WHERE id=$actorId");
		echo "<div id= tableActor>";
		$actor =  $res->fetch_object();
		$name = $actor->name;
		$surname = $actor->surname;
		$age = $actor->age;
		$experience = $actor->experience;
		$photo = $actor->photo;
		$biography = $actor->biography;
		if ($photo == "") {
			$photo = '../Files/noPhoto.jpg';
		}
		if ($admin) {
			echo "<div class= formInput>
		<form action='../changeActor.php' method ='post' enctype='multipart/form-data'> 
		<h2> Изменить данные об актере </h2>
		<p>Выберите фото<input type='file' class='chooseFile' name='userfile' accept='image/*' /></p>
			<input type='hidden' name ='id' value=$actorId>
			<input type='hidden' name ='oldPhoto' value=$photo>
		   <p>Имя <input type='text' name='name' value='$name'></p>
		   <p>Фамилия <input type='text' name='surname' value='$surname'></p>
		   <p>Возраст<input type='number' name='age' value='$age'></p>
		   <p>Стаж<input type='number' name='experience' value='$experience'></p>
		   <p>Биография<textarea type='text' name='biography'>$biography</textarea></p>
		   <button>Изменить данные актера</button>
		</form>";
		}
		echo "<h2>$name $surname</h2><br>";
		echo "<table><tr>";
		echo "<td><img src=$photo alt = 'фото актера' width='348' height='480'></img></td>";
		echo "<td>Возраст $age лет<br>";
		echo "Стаж $experience лет<br>";
		echo "$biography<br>";
		echo "<br>Играет в спектаклях:<br>";
		$res = $mysqli->query("SELECT spectacles.name as name, spectacles.id as spectacleId FROM spectales_has_actors INNER JOIN spectacles 
        ON spectales_has_actors.spectacle_id=spectacles.id WHERE spectales_has_actors.actor_id=$actorId");
		echo "<ul>";
		while ($spectacle = $res->fetch_object()) {
			echo "<li><a href='spectacle.php?spectacle_id=$spectacle->spectacleId'>$spectacle->name</a></li>";
		}
		echo "</ul></td></tr></table>";
		?>
	</div>
	<div class='mainPage'>
	</div>
	<div class="footer">
		8(960)319-71-39 г. Заречный Пензенская область<br>
		Горячая линия
	</div>
</body>

</html>