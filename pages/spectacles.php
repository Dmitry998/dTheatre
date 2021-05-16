<?php session_start();
global $admin;
$admin = false;
global $login;
$login = null;

/*	<link rel="stylesheet" type="text/css" href="/ext3/resources/css/ext-all.css"/>
	<script type="text/javascript" src="/ext3/adapter/ext/ext-base.js"></script> 
	<script type="text/javascript" src="/ext3/ext-all.js"></script>
	<script type="text/javascript" src="/ext3/examples/ux/RowExpander.js"></script>
	<script type='text/javascript' src='/ext3/src/locale/ext-lang-ru.js'></script>
	<script type="text/javascript" src="../js/GroupHeaderPlugin.js"></script>
	<script type="text/javascript" src="../js/init.js"></script>*/
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
		<h2> Спектакли </h2>
		<?php
		$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
		$res = $mysqli->query('SELECT * From spectacles');
		echo "<div id=ajaxDiv>";
		echo "<table>";
		echo "<tr>"; // new
		$indexRow = 0;
		while ($spectacle =  $res->fetch_object()) {
			$id = $spectacle->id;
			$photo = $spectacle->poster;
			$name = $spectacle->name;
			if ($indexRow > 4) {
				echo "<tr>";
				$indexRow = 0;
			}
			if ($photo == "") {
				$photo = '../Files/noPhoto.jpg';
			}
			if ($admin) {
				echo "<td><a id='actor' href='spectacle.php?spectacle_id=$id'><img src='$photo' alt = 'Фото афиши' width='290' height='400'></img>$name</a><br><a id='actor' href='spectacle.php?spectacle_id=$id&change=1'>Редактировать</a><br><button onclick=\"deleteSpectacle('../deleteSpectacle.php',$id)\">Удалить</button><br></td>";
			} else {
				echo "<td><a id='actor' href='spectacle.php?spectacle_id=$id'><img src='$photo' alt = 'Фото афиши' width='290' height='400'></img>$name</a></td>";
			}
			$photo_str = json_encode($photo);
			$indexRow++;
		}
		echo "</tr></table></div>";
		if ($admin) {
			if(isset($_GET['status'])){
				$status = $_GET['status'];
				switch($status){
					case 1: 
						echo '<p>Запись добавлена успешно </p>';
						break;
					case 2:
						echo '<p> Не удалось загрузить файл </p>';
						break;
					case 3:
						echo '<p> Возникла ошибка! </p>';
						break;
				}
			}
			echo "
	   	<div class= formInput>
		   <h2> Добавить спектакль </h2>
		   <form id='upload-container' action='../addSpectacle.php' method ='post' enctype='multipart/form-data'> 
		   <p>Выберите афишу<input type='file' class='chooseFile' name='userfile' accept='image/*' /></p>
		   <p>Название <input type='text' name='name'></p>
		   <p>Длительность <input type='number' name='duration'></p>
		   <p>Описание <textarea type='text' name='description'></textarea></p>
		   <button> Добавить спектакль </button>
		   </form>
	   </div>";
		}
		?>
	</div>
	<br>
	<div class="footer">
		8(960)319-71-39 г. Заречный Пензенская область<br>
		Горячая линия
	</div>
</body>

<script>
	function deleteSpectacle(url, spectacle_id) {
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function() {
			alertResponse(httpRequest);
		};

		httpRequest.open('POST', url, true);
		httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //тип передаваемых данных закодирован.
		let value = "spectacle_id=" + spectacle_id;
		httpRequest.send(value);
	}

	function alertResponse(httpRequest) {
		if (httpRequest.readyState == 4) {
			if (httpRequest.status == 200) {
				var responseDiv = document.getElementById('ajaxDiv');
				responseDiv.innerHTML = httpRequest.responseText;
			} else {
				alert('Возникли проблемы с получением ответа от сервера.');
			}
		}
	}
</script>

</html>