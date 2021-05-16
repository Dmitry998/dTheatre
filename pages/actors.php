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
		<h1>Актёры нашего театра</h1>
		<?php
		$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
		if ($admin) {
			echo "
	   	<div class= formInput>
		   <h2> Добавить Актера </h2>
		   <form id='upload-container' action='../addActor.php' method ='post' enctype='multipart/form-data'>";
			if (isset($_GET['status'])) {
				$status = $_GET['status'];
				switch ($status) {
					case 1:
						echo '<div style="color: green;"> Актер добавлен!</div><hr>';
						break;
					case 2:
						echo '<div style="color: red;"> Не удалось загрузить фото</div><hr>';
						break;
					case 3:
						echo '<div style="color: red;"> Вы не ввели имя актеру</div><hr>';
						break;
					case 4:
						echo '<div style="color: red;"> Вы не ввели фамилию актеру</div><hr>';
						break;
					case 5:
						echo '<div style="color: red;"> Вы не возраст актеру</div><hr>';
						break;
				}
			}
			echo "<p>Выберите фото<input type='file' class='chooseFile' name='userfile' accept='image/*' /></p>
		   <p>Имя <input type='text' name='name'></p>
		   <p>Фамилия <input type='text' name='surname'></p>
		   <p>Возраст<input type='number' name='age'></p>
		   <p>Стаж<input type='number' name='experience'></p>
		   <p>Биография<textarea type='text' name='biography'></textarea></p>
		   <button> Добавить актера </button>
		   </form>
	   </div>";
		}
		$res = $mysqli->query('SELECT * From actors');
		echo "<div id=ajaxDiv>";
		echo "<table>";

		echo "<tr>"; // new
		$indexRow = 0;
		while ($actor =  $res->fetch_object()) {
			$id = $actor->id;
			$photo = $actor->photo;
			if ($indexRow > 3) {
				echo "<tr>";
				$indexRow = 0;
			}
			if ($photo == "") {
				$photo = '../Files/noPhoto.jpg';
			}
			if ($admin) {
				echo "<td><a id='actor' href='actor.php?actor_id=$id'><img src='$photo' alt = 'фото актера' width='290' height='400'></img> $actor->name $actor->surname</a><button onclick=\"deleteActor($id)\">Удалить</button><br></td>";
			} else {
				echo "<td><a id='actor' href='actor.php?actor_id=$id'><img src='$photo' alt = 'фото актера' width='290' height='400'></img> $actor->name $actor->surname</a></td>";
			}
			$photo_str = json_encode($photo);
			$indexRow++;
		}
		echo "</tr></table></div>";
		?>
	</div>
	<div class='mainPage'>
	</div>
	<div class="footer">
		8(960)319-71-39 г. Заречный Пензенская область<br>
		Горячая линия
	</div>
</body>

<script>
	function deleteActor(actor_id) {
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function() {
			alertResponse(httpRequest);
		};

		httpRequest.open('POST', '../deleteActor.php', true);
		httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		let value = "actor_id=" + actor_id;
		httpRequest.send(value);
	}

	function alertResponse(httpRequest) {
		if (httpRequest.readyState == 4) {
			if (httpRequest.status == 200) {
				alert('Ответ получен.');
				var responseDiv = document.getElementById('ajaxDiv');
				responseDiv.innerHTML = httpRequest.responseText;
			} else {
				alert('Возникли проблемы с получением ответа от сервера.');
			}
		}
	}
</script>

</html>