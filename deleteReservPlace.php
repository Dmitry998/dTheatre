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

if(isset($_POST['place_id'])) {
    $id = $_POST['place_id'];
    $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
    $stmt = $mysqli->prepare("DELETE FROM tickets WHERE place_id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
echo '<div class=formInput> <h3>Бронь снята<h3></div><hr>';
$res = $mysqli->query("SELECT users.login as login, users.email as email, places.id as idPlace, DATE_FORMAT(tickets.date_res, '%d.%m.%Y %H:%i') as date, tickets.pz as pz, places.row as r, places.place as place, places.price as price, places.number as number, spectacles.name as name FROM tickets INNER JOIN places ON tickets.place_id=places.id INNER JOIN sessions ON places.session_id=sessions.id INNER JOIN spectacles ON sessions.spectacle_id=spectacles.id INNER JOIN users ON tickets.user_id=users.id WHERE tickets.pz='Бронь'");
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
            echo "<tr><td>$name</td><td>$date</td><td>$row</td><td>$place</td><td>$price</td><td>$pz</td><td><button onclick=\"deleteReserv($id)\">Снять бронь</button></td><td><button>Оплатить</button></td></tr>";
        }
    }
    else{
        echo "<tr><td>$name</td><td>$date</td><td>$row</td><td>$place</td><td>$price</td><td>$pz</td></tr>";
    }
}
echo "</table>";