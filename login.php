<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    

    $login = $_POST['login'];
    $password = $_POST['password'];

    $db_servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "chatcom_db";

    try {
        // Tworzenie połączenia z bazą danych za pomocą PDO
        $conn = new PDO("mysql:host=$db_servername;dbname=$db_name", $db_username, $db_password);
        // Ustawienie trybu raportowania błędów na wyjątki
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT user_password, user_password_salt, id_user, user_name, user_surname FROM users WHERE user_email=:login";
        $stmt = $conn->prepare($sql);

        // Przypisanie wartości parametrów
        $stmt->bindParam(':login', $login);

        // Wykonanie zapytania
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($result['user_password']) or empty($result['user_password_salt'])) {
            throw new PDOException('Błędny login lub hasło');
        }

        $hashedPassword = hash('sha512', $_POST['password'] . $result['user_password_salt']);

        if ($hashedPassword === $result['user_password']) {
            session_start();
            echo "Zalogowano";
            $_SESSION['id_user']=$result['id_user'];
            $_SESSION['user_name']=$result['user_name'];
            $_SESSION['user_surname']=$result['user_surname'];
            header('Location: ./second_page.php');
            exit();

        } else {
            echo 'Błędny login lub hasło';
        }
    } catch (PDOException $e) {
        echo "Wystąpił błąd: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHAT.COM · LOGOWANIE</title>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="icon" href="./src/chat-icon.png">
    <meta name="title" content="CHAT.COM · LOGOWANIE">
    <meta name="description" content="CHAT.COM to szybki, bezpieczny i zaawansowany komunikator. Przesyłaj wiadomości oraz kontaktuj się z najbliższymi. Dołącz do Chat.com już teraz!">
    <meta name="keywords" content="komunikator, czat, aplikacja, wiadomości, rozmowy, komunikacja online, współpraca, bezpieczeństwo, chat, messenger, chatapp">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body{
            background-color: #141414;
            text-align: center;
            color: white;
        }
        .back-icon{
            position: absolute;
            transition: 0.2s;
            top: 5%;
            left: 5%;
            cursor: pointer;
        }
        .back-icon:active{
            scale: 0.8;
        }
        h2{
            position: absolute;
            font-size: 30px;
            top: 15%;
            margin-left: auto;
            margin-right: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-family: 'Inter';
        }
        form{
            margin-top: 300px;
            text-align: center;
        }
        input{
            background-color: #141414;
            color:  white;
            height: 50px;
            width: 280px;
            border: #8F38FE 1px solid;
            border-radius: 5px;
            margin: 20px;
            font-size: 20px;
            transition: 0.5s;
        }
        input:focus{
            border-radius: 15px;
        }
        form sub{
            font-family: 'Inter';
            color: #9E9E9E;
        }
        h4{
            position: absolute;
            font-size: 20px;
            top: 3%;
            left: 80%;
            font-family: 'Inter';
        }
        form button{
            margin-top: 70px;
            background: rgb(20,20,20);
            background: linear-gradient(0deg, rgba(20,20,20,1) 0%, rgba(86,43,141,1) 1%, rgba(145,64,248,1) 49%, rgba(145,64,248,1) 90%);
            -webkit-filter: brightness(90%) contrast(90%) blur(2px) grayscale(10%);
            filter: brightness(90%) contrast(90%)  grayscale(10%);
            color: white;
        }
        form button{
            position: relative;
            background-color: #8F38FE;
            user-select:none;
            text-align: center;
            text-decoration: none;
            transition-duration: 0.4s;
            -webkit-transition-duration: 0.4s;
            height: 71px;
            width: 250px;
            font-family: Tahoma;
            border-radius: 25px;
            font-size: 20px;
        }
        form button:after {
            content: "";
            display: block;
            position: absolute;
            border-radius: 4em;
            left: 0;
            top:0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: all 0.5s;
            box-shadow: 0 0 10px 40px white;
        }

        form button:active:after {
            box-shadow: 0 0 0 0 white;
            position: absolute;
            border-radius: 4em;
            left: 0;
            top:0;
            opacity: 1;
            transition: 0s;
        }
        a{
            transition: 0.1s;
        }
        a:active{
            font-size: 15px;
        }
        form button:active {
            top: 1px;
        }
        @media only screen and (max-width: 600px) {
            h4 {position: absolute;
            font-size: 12px;
            top: 5%;
            margin-left: auto;
            margin-right: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-family: 'Inter';
            }
        }
    </style>
</head>
<body>
    <!-- ZMIEŃ HREFA-->
    <a  href="./main_page.html">
        <img src="./src/back-icon.png" alt="powrót" class="back-icon">
    </a>
    <h2>ZALOGUJ SIĘ</h2>
    <form method="post" action="./login.php">
        <sub> E-MAIL</sub><br>
        <input type="email" autofocus="" required="required" name="login" id="login"><br>
        <sub>HASŁO</sub><br>
        <input type="password" name="password" autocomplete="current-password" required="" id="id_password">
        <i class="far fa-eye" id="togglePassword" style="margin-left: -10px; cursor: pointer;"></i><br><br>
        <a href="#"><sub>ZAPOMNIAŁEM HASŁA</sub></a><br>
        <button type="submit">ZALOGUJ SIĘ</button>
    </form>
    </div>
    <h4>CHAT.COM</h4>
    <script defer>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#id_password');

        togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
    </script>

</body>
</html>