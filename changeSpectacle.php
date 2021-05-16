<?php
global $id;
if(isset($_POST['name']) && isset($_POST['duration']) && isset($_POST['description']) && isset($_POST['id']) && isset($_POST['oldPhoto']))
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
    $duration = $_POST['duration'];
    $description = $_POST['description'];
	$mysqli = new mysqli('localhost','root','12345678','theatre');
	$stmt = $mysqli->prepare("UPDATE spectacles SET name=?, duration=?, description=?, poster=? WHERE id=?");
	$stmt->bind_param('sissi',$name,$duration,$description,$photo,$id);
	$stmt->execute();
}
Header("Location:/dTheatre/pages/spectacle.php?spectacle_id=$id");
?>