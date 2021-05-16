<?php

if (isset($_POST['actor_id']) && isset($_POST['spectacle_id'])) {

    $actor_id = $_POST['actor_id'];
    $spectacle_id = $_POST['spectacle_id'];
    echo 'СПЕКтакль '.$spectacle_id;
    echo ' АКтер '.$actor_id;
    $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');

    $stmt = $mysqli->prepare("DELETE FROM spectales_has_actors WHERE actor_id=? AND spectacle_id=?");
    $stmt->bind_param('ii', $actor_id, $spectacle_id);
    $stmt->execute();
    echo '<div style="color: green;"> Актер удален из спектакля!</div><hr>';

    $res = $mysqli->query("SELECT actors.name as name, actors.surname as surname, actors.id as id from spectales_has_actors 
    INNER JOIN actors ON spectales_has_actors.actor_id=actors.id WHERE spectales_has_actors.spectacle_id=$spectacle_id");
    echo "<ul>";
    while ($actor = $res->fetch_object()) {
        echo "<li><a href='actor.php?actor_id=$actor->id'>$actor->name $actor->surname</a> <button onclick=\"deleteActorForSpectacle($spectacle_id, $actor->id)\">Удалить актера</button></li>";
    }
    echo "</ul>";
}
