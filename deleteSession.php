<?php
require_once('json/JSON.php');

$items = array();

$json = new Services_JSON();

$idSession = $_POST['id'];


$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
$res = $mysqli->query("DELETE FROM sessions WHERE id=$idSession");

if(!$res){
    die($json->encode(array('success'=>2)));
}

$arr = array('success' => 1);
$response = $json->encode($arr);
echo $response;

?>