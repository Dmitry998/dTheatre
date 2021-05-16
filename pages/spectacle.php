<?php session_start();
global $admin;
$admin = false;
global $login;
$login = null;

global $spectacle_id;
$spectacle_id = null;
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title> Театр </title>
	<link rel="stylesheet" type="text/css" href="/ext3/resources/css/ext-all.css"/>
	<link href="../style.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="/ext3/adapter/ext/ext-base.js"></script> 
	<script type="text/javascript" src="/ext3/ext-all.js"></script>
	<script type="text/javascript" src="/ext3/examples/ux/SliderTip.js"></script>
	<script type="text/javascript" src="../js/sessionsForm.js"></script>

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

						$res = $mysqli->query("SELECT * From users WHERE id='$userid'");
						$user = $res->fetch_object();
						$login = $user->login;
						$role = $user->role;
						if ($role == 'admin') {
							$admin = true;
						}
						echo "<li><a href='basket.php'>Вы вошли как $login </a></li>
					<li><a href='../exit.php'>Выход</a></li>";
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
		if (isset($_GET['spectacle_id'])) {
			$spectacle_id = $_GET['spectacle_id'];
			$_SESSION['spectacle_id'] = $spectacle_id;
		}
		$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
		$res = $mysqli->query("SELECT * From spectacles WHERE id=$spectacle_id");
		echo "<div id= tableActor>";
		$spectacle =  $res->fetch_object();
		$name = $spectacle->name;
		$poster = $spectacle->poster;
		$description = $spectacle->description;
		$duration = $spectacle->duration;

		if ($admin) {
			echo "<div class= formInput>
		<form action='../changeSpectacle.php' method ='post' enctype='multipart/form-data'> 
		<h2> Изменить спектакль </h2>
		<p>Заменить афишу<input type='file' class='chooseFile' name='userfile' accept='image/*' value=''/></p>
		<input type='hidden' name ='id' value=$spectacle_id>
		<input type='hidden' name ='oldPhoto' value=$poster>
		<p>Название <input type='text' name ='name' value='$name'></p>
		<p>Длительность <input type='number' name ='duration' value=$duration></p>
		<p>Описание <textarea type='text' name='description'>$description</textarea></p>
		<button> Изменить запись </button></div>
		</form>";
		}
		if ($poster == "") {
			$poster = '../Files/noPhoto.jpg';
		}
		echo "<h2>$name</h2><br>";
		echo "<table><tr>";
		echo "<td><img src=$poster alt = 'фото актера' width='348' height='480'></img></td>";
		echo "<td>$description<br>";
		echo "<br>Длительность: $duration часов<br>";

		echo "<br><h3>Актеры:</h3>";
		$res = $mysqli->query("SELECT actors.name as name, actors.surname as surname, actors.id as id from spectales_has_actors 
		INNER JOIN actors ON spectales_has_actors.actor_id=actors.id WHERE spectales_has_actors.spectacle_id=$spectacle_id");
		echo "<div id='actorsFromSpectacle'><ul>";
		while ($actor = $res->fetch_object()) {
			if ($admin) {
				echo "<li><a href='actor.php?actor_id=$actor->id'>$actor->name $actor->surname</a> <button onclick=\"deleteActorForSpectacle($spectacle_id, $actor->id)\">Удалить актера</button></li>";
			} else {
				echo "<li><a href='actor.php?actor_id=$actor->id'>$actor->name $actor->surname</a></li>"; //<a href='#'> | Удалить</a>
			}
		}
		echo "</ul><br>";
		if ($admin) {
			echo "<h3>Добавить актера: </h3><select id='actors'>";
			$res = $mysqli->query("SELECT actors.name as name, actors.surname as surname, actors.id as id FROM actors");
			while ($actor = $res->fetch_object()) {
				echo "<option>$actor->id | $actor->name $actor->surname</option>";
			}
			echo "</select> <button onclick=\"addActorForSpectacle($spectacle_id)\">Добавить актера</button>";
		}
		echo '</div><br>';
		if($admin) {
			echo "<div id='sessions'></div>";
		}
		echo "<h3>Сеансы:</h3>";
		echo "<div id='seansForSpectacle'><ul>";
		$res = $mysqli->query("SELECT id, DATE_FORMAT(date, '%d.%m.%Y %H:%i') as date FROM sessions Where spectacle_id=$spectacle_id");
		while ($session = $res->fetch_object()) {
			echo "<li><a href='session.php?session_id=$session->id'>$session->date</a></li>";
		}
		echo "</ul></div>";
		if ($admin) {
			echo "<h3>Добавить сеанс: </h3>";
			echo "Дата и время <input type='datetime'></input><br>";
			echo "Цена для мест 1 ряда <input type='number'></input><br>";
			echo "Цена для мест 2 ряда <input type='number'></input><br>";
			echo "Цена для мест 3 ряда <input type='number'></input><br>";
			echo "<button onclick=\"addSeansForSpectacle($spectacle_id)\">Добавить сеанс</button>";
		}
		echo "</td></tr></table>";
		?>
	</div>
	<br>
	<div class="footer">
		8(960)319-71-39 г. Заречный Пензенская область<br>
		Горячая линия
	</div>
</body>


<script>

	function addActorForSpectacle(idSpectacle) {
		var idActor = document.getElementById('actors').value;
		idActor = idActor.split('|')[0];
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function() {
			alertResponse(httpRequest);
		};
		httpRequest.open('POST', '../addActorForSpectacle.php', true);
		httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		let value = "actor_id=" + idActor + "&spectacle_id=" + idSpectacle;
		httpRequest.send(value);
	}

	function deleteActorForSpectacle(idSpectacle, idActor) {
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function() {
			alertResponse(httpRequest);
		};
		httpRequest.open('POST', '../deleteActorForSpectacle.php', true);
		httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		let value = "actor_id=" + idActor + "&spectacle_id=" + idSpectacle;
		httpRequest.send(value);
	}

	function alertResponse(httpRequest) {
		if (httpRequest.readyState == 4) {
			if (httpRequest.status == 200) {
				var responseDiv = document.getElementById('actorsFromSpectacle');
				responseDiv.innerHTML = httpRequest.responseText;
			} else {
				alert('Возникли проблемы с получением ответа от сервера.');
			}
		}
	}
</script>

</html>