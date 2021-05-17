<?php
global $admin;
$admin = false;
global $login;
$login = null;

session_start();
if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');

    $res = $mysqli->query("SELECT login, role From users WHERE id='$userid'");
    $user = $res->fetch_object();
    $login = $user->login;
    $role = $user->role;
    if($role == 'admin'){
        $admin = true;
    }
}
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
		<a class="logo" href="../index.php"><img src="logoT.jpg" alt=""></a>
		<div class="top-menu">
			<ul>
				<li><a href="index.php">Главная</a></li>
				<li><a href="actors.php">Актёры</a></li>
				<li><a href="spectacles.php">Спектакли</a></li>
				<div class="auth">
					<?php
					if (isset($_SESSION['userid'])) {
						$userid = $_SESSION['userid'];
						$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');

						$res = $mysqli->query("SELECT login From users WHERE id='$userid'");
						$user = $res->fetch_object();
						$login = $user->login;

						echo "<li><a href='userTickets.php'>Вы вошли как $login </a></li>
					<li><a href='../exit.php'>Выход</a></li>";
					} else {
						echo "<li><a href='authForm.php'>Вход</a></li>
				<li><a href='../registrationForm.php'>Регистрация</a></li>";
					}
					?>
			</ul>
		</div>
	</div>
	</div>
	<div class="content">
		<?php
        if($admin){
            echo "<h2>Забронированные билеты</h2>";
        }
        else{
            echo "<h2>Ваши билеты</h2>";
        }
        if(!$admin){
            $res = $mysqli->query("SELECT places.id as idPlace, DATE_FORMAT(tickets.date_res, '%d.%m.%Y %H:%i') as date, tickets.pz as pz, places.row as r, places.place as place, places.price as price, places.number as number, spectacles.name as name FROM tickets INNER JOIN places ON tickets.place_id=places.id INNER JOIN sessions ON places.session_id=sessions.id INNER JOIN spectacles ON sessions.spectacle_id=spectacles.id WHERE tickets.user_id=$userid");
        }
        else {
            $res = $mysqli->query("SELECT users.login as login, users.email as email, places.id as idPlace, DATE_FORMAT(tickets.date_res, '%d.%m.%Y %H:%i') as date, tickets.pz as pz, places.row as r, places.place as place, places.price as price, places.number as number, spectacles.name as name FROM tickets INNER JOIN places ON tickets.place_id=places.id INNER JOIN sessions ON places.session_id=sessions.id INNER JOIN spectacles ON sessions.spectacle_id=spectacles.id INNER JOIN users ON tickets.user_id=users.id WHERE tickets.pz='Бронь'");
        }
		echo "<div id= allSessions>";
		echo "<table>";
        if(!$admin){
            echo "<tr><th>Спектакль</th><th>Дата сеанса</th><th>Ряд</th><th>Место</th><th>Цена</th><th>Статус</th></tr>";
        }
        else{
            echo "<tr><th>Логин пользователя</th><th>Спектакль</th><th>Дата сеанса</th><th>Ряд</th><th>Место</th><th>Цена</th><th>Статус</th></tr>";
        }
		while ($ticket =  $res->fetch_object()) {
			$date = $ticket->date;
            $pz = $ticket->pz;

            $row = $ticket->r;
            $place = $ticket->place;
            $price = $ticket->price;
            $id = $ticket->idPlace;
            
            if($admin){
                $login = $ticket->login;
            }

			$name = $ticket->name;
            if($pz === 'Бронь'){
                if($admin){
                    echo "<tr><td>$login</td><td>$name</td><td>$date</td><td>$row</td><td>$place</td><td>$price</td><td>$pz</td><td><button onclick=\"deleteReserv($id)\">Снять бронь</button></td></tr>";
 
                }
                else
                {
                    echo "<tr><td>$name</td><td>$date</td><td>$row</td><td>$place</td><td>$price</td><td>$pz</td><td><button onclick=\"deleteReserv($id)\">Снять бронь</button></td><td><button onclick=\"buyTicket($id)\">Оплатить</button></td></tr>";
                }
            }
            else{
                echo "<tr><td>$name</td><td>$date</td><td>$row</td><td>$place</td><td>$price</td><td>$pz</td></tr>";
            }
		}
		echo "</table></div>";
		?>
		<div class='mainPage'>
		</div>
	</div>
	<div class="footer">
		8(960)319-71-39 г. Заречный Пензенская область<br>
		Горячая линия
	</div>
</body>

<script>
	function deleteReserv(place_id) {
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function() {
			alertResponse(httpRequest);
		};

		httpRequest.open('POST', '../deleteReservPlace.php', true);
		httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		let value = "place_id=" + place_id;
		httpRequest.send(value);
	}

    function buyTicket(place_id) {
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function() {
			alertResponse(httpRequest);
		};

		httpRequest.open('POST', '../buyTicket.php', true);
		httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		let value = "place_id=" + place_id;
		httpRequest.send(value);
	}

	function alertResponse(httpRequest) {
		if (httpRequest.readyState == 4) {
			if (httpRequest.status == 200) {
				var responseDiv = document.getElementById('allSessions');
				responseDiv.innerHTML = httpRequest.responseText;
			} else {
				alert('Возникли проблемы с получением ответа от сервера.');
			}
		}
	}
</script>

</html>