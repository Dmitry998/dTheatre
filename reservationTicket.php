<?php
if (isset($_POST['places']) && isset($_POST['session']) && isset($_POST['userId']) && isset($_POST['date'])) {
    $places = $_POST['places'];
    $session = (int)$_POST['session'];
    $userId = (int)$_POST['userId'];
    $date = $_POST['date'];
    $pz = 'Бронь';

    $placesArr = explode(',', $places);

    for ($i = 0; $i < count($placesArr); $i++) {
        $currentPlace = (int)$placesArr[$i];
        $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
        $res = $mysqli->query("SELECT id FROM places WHERE number=$currentPlace AND session_id=$session");
        $answ = $res->fetch_object();
        $placeId = $answ->id;
        $stmt = $mysqli->prepare("INSERT INTO tickets (pz, date_res, user_id, place_id) VALUES (?,?,?,?)");
        $stmt->bind_param('ssii', $pz, $date, $userId, $placeId);
        $stmt->execute();
    }

    $reservationPlaces = array();

    $res = $mysqli->query("SELECT places.number as number FROM places INNER JOIN tickets ON tickets.place_id=places.id WHERE places.session_id=$session");
    while ($places = $res->fetch_object()) {
        array_push($reservationPlaces, (int)$places->number);
    }
    echo json_encode($reservationPlaces); 
   /* echo '<div class="cinoteatr">';
    echo '<div id="count_"></div>';
    for ($i = 0; $i < 27; $i++) {
        echo '<div class="mesta">' . ($i + 1) . '</div>';
    }
    echo '</div>';*/
    /*echo '<br>Сеанс; ' . $session . '<br>';
    echo '<br>User id: ' . $userId . '<br>';
    echo ('<br>' . $date);
    /*var_dump($reservationPlaces);

    echo '<script>','ColorisePlaces();','</script>';*/
    /*for($i=0; $i < count($places); $i++) {
        $mysqli = new mysqli('localhost','root','12345678','theatre');
        $stmt = $mysqli->prepare("INSERT INTO tickets(pz, date_res, user_id, place_id) VALUES (?,?,?,?)");
        $stmt->bind_param('ssii',"Бронь", $date, $userId, $places[$i]);
        //$stmt->execute();
    }*/
}
?>