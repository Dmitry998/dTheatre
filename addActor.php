<?php
$status = 1;
if(isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['age']) && isset($_POST['experience']) && isset($_POST['biography']))
{
    $photo ="";
    if($_FILES['userfile']['tmp_name'])
    {
		$uploadfile = "Files/".basename($_FILES['userfile']['name']);
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
        if(empty($name)){
            $status = 3;
        }
        $surname = $_POST['surname'];
        if(empty($surname)){
            $status = 4;
        }
        $age = $_POST['age'];
        if(empty($age)){
            $status = 5;
        }
        $experience = $_POST['experience'];
        $biography = $_POST['biography'];

        if($status == 1){
            $mysqli = new mysqli('localhost','root','12345678','theatre');

            $stmt = $mysqli->prepare("INSERT INTO actors(name, surname, age, experience, biography, photo) VALUES (?,?,?,?,?,?)"); 
            $stmt->bind_param('ssiiss',$name, $surname, $age, $experience, $biography, $photo);
            $stmt->execute();
        }
    }
}
Header("Location:/dTheatre/pages/actors.php?status=$status");
?>