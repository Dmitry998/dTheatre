<?php
global $id;
if(isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['age']) && isset($_POST['experience']) && isset($_POST['oldPhoto']) && isset($_POST['biography']) && isset($_POST['id']))
{

	$photo =$_POST['oldPhoto'];
    if($_FILES['userfile']['tmp_name'])
    {
		$uploadfile = "Files/posters/".basename($_FILES['userfile']['name']);
		if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
		{
            if(getimagesize($uploadfile))
            {
                $photo = '../'.$uploadfile; // новое фото
            }
            else
            {
                $photo = $_POST['oldPhoto'];
                unlink($uploadfile);
            }
		}
    }
    $id = $_POST['id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $age = $_POST['age'];
    $experience = $_POST['experience'];
    $biography = $_POST['biography'];

	$mysqli = new mysqli('localhost','root','12345678','theatre');
	$stmt = $mysqli->prepare("UPDATE actors SET name=?, surname=?, age=?, experience=?, biography=?, photo=? WHERE id=?");
	$stmt->bind_param('ssiissi',$name,$surname,$age,$experience,$biography,$photo,$id);
	$stmt->execute();
}
Header("Location:/dTheatre/pages/actor.php?actor_id=$id");
