<?php

if (isset($_POST['actor_id']) && isset($_POST['spectacle_id'])) {

    $actor_id = $_POST['actor_id'];
    $spectacle_id = $_POST['spectacle_id'];

    $mysqli = new mysqli('localhost','root','12345678','theatre');

    $res = $mysqli->query("SELECT * FROM spectales_has_actors WHERE spectacle_id=$spectacle_id AND actor_id=$actor_id");
    $record = $res->fetch_object();
    if($record==null)
    {
        $stmt = $mysqli->prepare("INSERT INTO spectales_has_actors(spectacle_id,actor_id) VALUES (?,?)"); 
        $stmt->bind_param('ii',$spectacle_id,$actor_id);
        $stmt->execute();
        echo '<div style="color: green;"> Актер добавлен!</div><hr>';
    }
    else
    {
        echo '<div style="color: red;"> Этот актер и так играет в этом спектакле.</div><hr>';
    }

    $res = $mysqli->query("SELECT actors.name as name, actors.surname as surname, actors.id as id from spectales_has_actors 
    INNER JOIN actors ON spectales_has_actors.actor_id=actors.id WHERE spectales_has_actors.spectacle_id=$spectacle_id");
    echo "<ul>";
    while ($actor = $res->fetch_object()) {
        echo "<li><a href='actor.php?actor_id=$actor->id'>$actor->name $actor->surname</a> <button onclick=\"deleteActorForSpectacle($spectacle_id, $actor->id)\">Удалить актера</button></li>";
    }
    echo "</ul>";
    echo "<h3>Добавить актера: </h3><select id='actors'>";
    $res = $mysqli->query("SELECT actors.name as name, actors.surname as surname, actors.id as id FROM actors");
    while ($actor = $res->fetch_object()) {
        echo "<option>$actor->id | $actor->name $actor->surname</option>";
    }
    echo "</select> <button onclick=\"addActorForSpectacle($spectacle_id)\">Добавить актера</button>";
}
