<?php
require_once('json/JSON.php');

$items = array();

$json = new Services_JSON();

$mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');
$res = $mysqli->query('SELECT * From actors');

$nrows = $res->num_rows;
$items = array();

for($i=0; $i<$nrows; $i++)
{
    $actor = $res->fetch_object();
    $items[$i] = array(
        'id' => $actor->id,
        'name' => $actor->name,
        'surname' => $actor->surname,
        'age' => $actor->age,
        'experience' => $actor->experience
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