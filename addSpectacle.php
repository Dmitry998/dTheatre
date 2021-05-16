<?php
$status = 1;
if(isset($_POST['name']) && isset($_POST['description']) && isset($_POST['duration']))
{
    $photo ="";
    if($_FILES['userfile']['tmp_name'])
    {
		$uploadfile = "Files/posters/".basename($_FILES['userfile']['name']);
		if(move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
		{
            if(getimagesize($uploadfile))
            {
                $photo = '../'.$uploadfile;
            }
            else
            {
                $photo ="";
                unlink($uploadfile);
                $status = 2;
            }
		}
    }
    if($status != 2) {
        $name = $_POST['name'];
        $duration = $_POST['duration'];
        $description = $_POST['description'];
        $mysqli = new mysqli('localhost','root','12345678','theatre');
        $stmt = $mysqli->prepare("INSERT INTO spectacles(name,description, duration, poster) VALUES (?,?,?,?)"); 
        $stmt->bind_param('ssis',$name, $description, $duration, $photo);
        $stmt->execute();
        echo "INSERT INTO spectacles(name,description, duration, poster) VALUES ($name, $description, $duration, $photo)";
    }
}
Header("Location:/dTheatre/pages/spectacles.php?status=$status");
?>