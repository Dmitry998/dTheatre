<?php session_start();
global $admin;
$admin = false;
global $login;
$login = null;
global $reservationPlaces;
$reservationPlaces = array();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title> Театр </title>
    <link href="../style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="header">
        <a class="logo" href="../index.php"><img src="../logoT.jpg" alt=""></a>
        <div class="top-menu">
            <ul>
                <li><a href="../index.php">Главная</a></li>
                <li><a href="actors.php">Актёры</a></li>
                <li><a href="spectacles.php">Спектакли</a></li>
                <div class="auth">
                    <?php
                    if (isset($_SESSION['userid'])) {
                        $userid = $_SESSION['userid'];
                        $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');

                        $res = $mysqli->query("SELECT login From users WHERE id='$userid'");
                        $user = $res->fetch_object();
                        $login = $user->login;

                        echo "<li><a href='basket.php'>Вы вошли как $login </a></li>
                            <li><a href='exit.php'>Выход</a></li>";
                    } else {
                        echo "<li><a href='../authForm.php'>Вход</a></li>
                            <li><a href='../registrationForm.php'>Регистрация</a></li>";
                    }
                    ?>
            </ul>
        </div>
    </div>
    </div>
    <div class="content">
        <?php
        if (isset($_GET['session_id'])) {
            $session_id = $_GET['session_id'];
        }
        $mysqli = new mysqli('localhost', 'root', '12345678', 'theatre');

        $res = $mysqli->query("SELECT spectacles.name as spectacle_name, DATE_FORMAT(sessions.date, '%d.%m.%Y %H:%i') as date FROM sessions 
        INNER JOIN spectacles ON spectacles.id=sessions.spectacle_id WHERE sessions.id = $session_id");

        $session =  $res->fetch_object();
        echo "<div class='cinoteatr'>";
        echo "<h2>$session->spectacle_name</h2>";
        echo "<h2>Дата и время сеанса: $session->date</h2>";
        if ($login != null) {
            $userid = $_SESSION['userid'];
            echo "<p><button onclick=\"GetChoosenPlaces('../reservationTicket.php',$session_id,$userid)\">Забронировать</button></p>";
        }

        $res = $mysqli->query("SELECT places.number as number FROM places INNER JOIN tickets ON tickets.place_id=places.id WHERE places.session_id=$session_id");
        while ($places = $res->fetch_object()) {
            array_push($reservationPlaces, (int)$places->number);
        }
        echo "<div id='count_'></div>";
        $row = 1;
        for($i=1; $i < 82; $i++) {
            if($i % 9 == 0){
                echo "<div class='mesta'>$i</div>";  
                $row++;
            }
            else {
                echo "<div class='mesta'>$i</div>";
            }
        }
        echo '</div>'
        ?>
        <div id="AjaxDiv"></div>
    </div>
    <div class='mainPage'>
	</div>
    <div class="footer">
        8(960)319-71-39 г. Заречный Пензенская область<br>
        Горячая линия
    </div>
</body>


<script>
    var l = document.getElementsByClassName('mesta');

    for (let i = 0; i < l.length; i++) {
        if(l[i].innerHTML < 28){
            l[i].style.background = 'green';
        }
        if(l[i].innerHTML > 27 && l[i].innerHTML < 55){
            l[i].style.background = 'yellow';
        }
        if(l[i].innerHTML > 54){
            l[i].style.background = 'orange';
        }
    }
    var reservPlaces = JSON.parse('<?php echo json_encode($reservationPlaces); ?>');
    console.log(reservPlaces);
    for (let i = 0; i < l.length; i++) {
        for (let j = 0; j < reservPlaces.length; j++) {
            if (l[i].innerHTML == reservPlaces[j]) {
                l[i].style.background = 'black';
            }
        }
    }

    function Count_Mest() {
        let z_mest = 0;
        for (let i = 0; i < l.length; i++) {
            if (l[i].style.background == 'red') {
                z_mest++;
            }
        }
        return z_mest;
    }

    function GetPlaces() {
        var places = [];
        for (let i = 0; i < l.length; i++) {
            if (l[i].style.background == 'red') {
                places.push(l[i].innerHTML);
            }
        }
        return places;
    }
    for (let i = 0; i < l.length; i++) {
        l[i].onclick = function() {
            let background = this.style.background;
            if (background != 'black') {
                if (background == 'red') {
                    if(l[i].innerHTML < 28){
                        this.style.background = 'green';
                    }
                    if(l[i].innerHTML > 27 && l[i].innerHTML < 55){
                        this.style.background = 'yellow';
                    }
                    if(l[i].innerHTML > 54){
                        this.style.background = 'orange';
                    }
                    
                } else {
                    this.style.background = 'red';
                }
            }
            document.getElementById('count_').innerHTML = 'Выбрано мест : ' + Count_Mest();
        }
    }


    function GetChoosenPlaces(url, session, userId) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function() {
            alertResponse(httpRequest);
        };
        var places = GetPlaces();
        var now = new Date();
        var month = now.getMonth() + 1;
        if (month < 10) {
            month = '0' + month;
        }
        var day = now.getDate();
        if (day < 10) {
            day = '0' + day;
        }
        var dateNow = now.getFullYear() + '-' + month + '-' + day + ' ' + now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
        console.log(places);
        httpRequest.open('POST', url, true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); //тип передаваемых данных закодирован.
        let value = "places=" + places + "&session=" + session + "&userId=" + userId + "&date=" + dateNow;
        httpRequest.send(value);
    }

    function alertResponse(httpRequest) {
        if (httpRequest.readyState == 4) {
            if (httpRequest.status == 200) {
                alert('Ответ получен');
                var responseDiv = document.getElementById('AjaxDiv');
                var reservPlaces = JSON.parse(httpRequest.responseText);
                if(reservPlaces.length > 1){
                    responseDiv.innerHTML = "Места успешно забронированы";
                } else {
                    responseDiv.innerHTML = "Место успешно забронировано";
                }
                console.log(reservPlaces);
                for (let i = 0; i < l.length; i++) {
                    for (let j = 0; j < reservPlaces.length; j++) {
                        if (l[i].innerHTML == reservPlaces[j]) {
                            l[i].style.background = 'black';
                        }
                    }
                }
            } else {
                alert('Возникли проблемы с получением ответа от сервера.');
            }
        }
    }
</script>

</html>