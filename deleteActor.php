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
    if ($role == 'admin') {
        $admin = true;
    }
}

if (isset($_POST['actor_id'])) {
    $actor_id = $_POST['actor_id'];
    $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
    $res = $mysqli->query("DELETE FROM actors WHERE id=$actor_id");
    if(!$res){
        die;
    }
}

$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
$res = $mysqli->query('SELECT * From actors');
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
echo "</tr></table>";
