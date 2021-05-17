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

if(isset($_POST['spectacle_id'])) {
    $id = $_POST['spectacle_id'];
    $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
    $stmt = $mysqli->prepare("DELETE FROM spectacles WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
$res = $mysqli->query('SELECT * From spectacles');
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
        echo "<td><a id='actor' href='spectacle.php?spectacle_id=$id'><img src='$photo' alt = 'Фото афиши' width='290' height='400'></img>$name</a><br><button onclick=\"deleteSpectacle('../deleteSpectacle.php',$id)\">Удалить</button></td>";
    } else {
        echo "<td><a id='actor' href='spectacle.php?spectacle_id=$id'><img src='$photo' alt = 'Фото афиши' width='290' height='400'></img>$name</a></td>";
    }
    $photo_str = json_encode($photo);
    $indexRow++;
}
echo "</tr></table>";
