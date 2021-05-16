<?php session_start();
global $admin;
$admin = false;
global $login;
$login = null;
global $reservationPlaces;
$reservationPlaces = array();
global $price1;
$price1 = 0;
global $price2;
$price2 = 0;
global $price3;
$price3 = 0;
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

        $res = $mysqli->query("SELECT price FROM places WHERE number=1 AND session_id=$session_id");
        $price1 = $res->fetch_object();
        $price1 = $price1->price;

        $res = $mysqli->query("SELECT price FROM places WHERE number=28 AND session_id=$session_id");
        $price2 = $res->fetch_object();
        $price2 = $price2->price;

        $res = $mysqli->query("SELECT price FROM places WHERE number=81 AND session_id=$session_id");
        $price3 = $res->fetch_object();
        $price3 = $price3->price;

        echo "<div class='legend'><table>
        <tr><td><div class='mestaExample'></div></td>
        <td>Сектор А цена: $price1</td><tr>
        <tr><td><div class='mestaExample'></div></td>
        <td>Сектор В цена: $price2</td><tr>
        <tr><td><div class='mestaExample'></div></td>
        <td>Сектор С цена: $price3</td><tr>
        <tr><td><div class='mestaExample'></div></td>
        <td>Выбранные места</td><tr>
        <tr><td><div class='mestaExample'></div></td>
        <td>Занятые места</td><tr>    
        </table></div>";
        if ($login != null) {
            $userid = $_SESSION['userid'];
            echo "<p><button onclick=\"GetChoosenPlaces('../reservationTicket.php',$session_id,$userid)\">Забронировать</button></p>";
        } else {
            echo "<h2>Только зарегистрированные пользователи могут бронировать билеты</h2>";
        }

        $res = $mysqli->query("SELECT places.number as number FROM places INNER JOIN tickets ON tickets.place_id=places.id WHERE places.session_id=$session_id");
        while ($places = $res->fetch_object()) {
            array_push($reservationPlaces, (int)$places->number);
        }
        echo "<div id='count_'></div>";
        echo "<div id='status'></div>";
        $row = 1;
        for ($i = 1; $i < 82; $i++) {
            if ($i % 9 == 0) {
                echo "<div class='mesta'>$i</div>";
                $row++;
            } else {
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
    var exmaplePlaces = document.getElementsByClassName('mestaExample');
    for (i = 0; i < exmaplePlaces.length; i++) {
        switch (i) {
            case 0:
                exmaplePlaces[i].style.background = 'green';
                break;
            case 1:
                exmaplePlaces[i].style.background = 'yellow';
                break;
            case 2:
                exmaplePlaces[i].style.background = 'orange';
                break;
            case 3:
                exmaplePlaces[i].style.background = 'red';
                break;
            case 4:
                exmaplePlaces[i].style.background = 'black';
                break;
        }
    }
    var l = document.getElementsByClassName('mesta');

    for (let i = 0; i < l.length; i++) {
        if (l[i].innerHTML < 28) {
            l[i].style.background = 'green';
        }
        if (l[i].innerHTML > 27 && l[i].innerHTML < 55) {
            l[i].style.background = 'yellow';
        }
        if (l[i].innerHTML > 54) {
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

    function getSum(){
        var sum = 0;
        for (let i = 0; i < l.length; i++) {
            if (l[i].style.background == 'red') {
                sum += getPriceForPlace(Number(l[i].innerHTML));
            }
        }
        return sum;
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
            if (<?php echo json_encode($login); ?>) {
                if (background != 'black') {
                    if (background == 'red') {
                        if (l[i].innerHTML < 28) {
                            this.style.background = 'green';
                        }
                        if (l[i].innerHTML > 27 && l[i].innerHTML < 55) {
                            this.style.background = 'yellow';
                        }
                        if (l[i].innerHTML > 54) {
                            this.style.background = 'orange';
                        }

                    } else {
                        this.style.background = 'red';
                    }
                }
            } else {
                document.getElementById('status').innerHTML = 'Для бронирования мест необходимо авторизоваться!';
            }
            if(Count_Mest() > 0){
                document.getElementById('count_').innerHTML = 'Выбрано мест : ' + Count_Mest() + ' На сумму: ' + getSum() + ' рублей';
            } else{
                document.getElementById('count_').innerHTML = '';
            }
        }
    }

    function getPriceForPlace(placeNumber) {
        var price1 = Number(JSON.parse('<?php echo json_encode($price1); ?>'));
        var price2 = Number(JSON.parse('<?php echo json_encode($price2); ?>'));
        var price3 = Number(JSON.parse('<?php echo json_encode($price3); ?>'));
        if(placeNumber < 28){
            return price1;
        }
        if(placeNumber > 28 && placeNumber < 55){
            return price2;
        }
        return price3;
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
                var responseDiv = document.getElementById('status');
                var reservPlaces = JSON.parse(httpRequest.responseText);
                if (Count_Mest() > 1) {
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