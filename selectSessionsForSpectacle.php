<?php
session_start();
require_once('json/JSON.php');
//require('pages/spectacle.php');

$items = array();

$json = new Services_JSON();

$spectacle_id = $_SESSION['spectacle_id'];

$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
$resSessions = $mysqli->query("SELECT id, DATE_FORMAT(date, '%d.%m.%Y %H:%i') as date FROM sessions WHERE spectacle_id=$spectacle_id");

$nrows = $resSessions->num_rows;
$items = array();

$priceSector1 = 0;
$priceSector2 = 0;
$priceSector3 = 0;

for($i=0; $i<$nrows; $i++)
{
    $session = $resSessions->fetch_object();
    $idSession = $session->id;

    $sql = "SELECT price FROM places WHERE session_id=$idSession AND places.number=1";

    $res = @$mysqli->query("SELECT price FROM places WHERE session_id=$idSession AND places.number=1");
    if($res){
        $answ = $res->fetch_object();
        $priceSector1 = $answ->price;
    }



    $res = @$mysqli->query("SELECT price FROM places WHERE session_id=$idSession AND places.number=28");
    if($res){
        $answ = $res->fetch_object();
        $priceSector2 = $answ->price;
    }

    $res = @$mysqli->query("SELECT price FROM places WHERE session_id=$idSession AND places.number=81");
    if($res){
        $answ = $res->fetch_object();
        $priceSector3 = $answ->price;
    }

    $items[$i] = array(
        'id' => $session->id,
        'date' => $session->date,
        'priceSector1' => $priceSector1,
        'priceSector2' => $priceSector2,
        'priceSector3' => $priceSector3

    );
}
/*while($actor = $res->fetch_object())
{
    $items[$i] = array(
        'surname' => $actor->surname,
        'name' => $actor->name,
        'age' => $actor->age,
        'experience' => $actor->experience
    );
    $i++;
}*/

$arr = array('items'=> $items, 'totalCount'=> $nrows);
$output = $json->encode($arr);
echo($output);
?>