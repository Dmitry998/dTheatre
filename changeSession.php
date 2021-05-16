<?php
session_start();
require_once('json/JSON.php');

$items = array();

$json = new Services_JSON();

$spectacle_id = $_SESSION['spectacle_id'];

$dateTime = $_POST['dateTime'];

$price1 = $_POST['price1'];
$price2 = $_POST['price2'];
$price3 = $_POST['price3'];
$id = $_POST['id'];

$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
$res = $mysqli->query("UPDATE sessions SET date='$dateTime' WHERE id=$id");

if(!$res){
    die($json->encode(array('success'=>2)));
}

$countPlaces = 81;
$lengthRow = 9;

for($i=1; $i < $countPlaces + 1; $i++){

    $row = intdiv($i, $lengthRow);//($i % $lengthRow) + 1;
    if($i % 9 != 0){
        $row++;
    }
    $price = $price1;

    if($i > 27 && $i < 55){
        $price = $price2;
    }
    if($i >= 55){
        $price = $price3;
    }

    if($row > 2) {
        $placeInRow = $i - (($row-1)*$lengthRow);
    } else {
        $placeInRow = $i;
    }

    $res = $mysqli->query("UPDATE places SET price=$price WHERE session_id=$id AND number=$i");
    if(!$res) {
        die($json->encode(array('success'=>4)));
    }
}

$arr = array('success' => 1);
$response = $json->encode($arr);
echo $response;

?>