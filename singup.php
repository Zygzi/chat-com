<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $imie = $_POST['firstname'];
    $nazwisko = $_POST['surname'];
    $data_urodzenia = $_POST['date_of_birth'];
    $email = $_POST['email'];
    $salt = bin2hex(random_bytes(32));
    $hashedPassword = hash('sha512', $_POST['password'].$salt);

    // Dane do połączenia z bazą danych
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chatcom_db";

    try {
        
    // Sprawdzenie długości hasła
        if (strlen($_POST['password']) < 8) {
            throw new Exception("Hasło jest zbyt krótkie. Wymagane jest minimum 8 znaków");
        }

        // Sprawdzenie, czy hasło zawiera zarówno liczby, znaki tekstowe i znaki specjalne
        if (!preg_match('/\d/', $_POST['password']) || !preg_match('/[A-Za-z]/', $_POST['password']) || !preg_match('/[^A-Za-z0-9]/', $_POST['password'])) {
            throw new Exception("Hasło musi zawierać zarówno liczby, litery oraz znaki specjalne.");
        }

    // Sprawdzenie wieku
        $dob = new DateTime($data_urodzenia);
        $today = new DateTime();
        $difference = $today->diff($dob);
        $age = $difference->y;

        if ($age < 15) {
            throw new Exception("Musisz mieć co najmniej 15 lat, aby się zarejestrować.");
        }

    // Tworzenie połączenia z bazą danych za pomocą PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Ustawienie trybu raportowania błędów na wyjątki
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $check_for_user = $conn->prepare("SELECT id_user FROM users WHERE user_email = :email");
        $check_for_user->bindParam(':email', $email);
        $check_for_user->execute();

        if ($check_for_user->rowCount() != 0) {
            throw new Exception("W bazie danych znajduje się użytkownik o adresie email: ". $email);
        }

    // Przygotowanie zapytania SQL z parametrami
        $sql = "INSERT INTO users (user_name, user_surname, user_email, user_password, user_password_salt, user_dateofbirth) VALUES (:name, :surname, :email, :password, :salt, :dateofbirth)";
        $stmt = $conn->prepare($sql);

        // Przypisanie wartości parametrów
        $stmt->bindParam(':name', $imie);
        $stmt->bindParam(':surname', $nazwisko);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':salt', $salt);
        $stmt->bindParam(':dateofbirth', $data_urodzenia);

    // Wykonanie zapytania
        $stmt->execute();

        header('Location: ./singup_done.html');
    } catch (Exception $e) {
        echo "Wystąpił błąd: " . $e->getMessage();
    }

    // Zamknięcie połączenia
    $conn = null;
}
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHAT.COM · REJESTRACJA</title>
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
        h4{
            position: absolute;
            font-size: 20px;
            top: 3%;
            left: 80%;
            margin-left: auto;
            font-family: 'Inter';

        }
        form sub{
            font-family: 'Inter';
            color: #9E9E9E;
        }
        h2{
            position: absolute;
            font-size: 40px;
            top: 10%;
            margin-left: auto;
            margin-right: auto;
            left: 0;
            right: 0;
            text-align: center;
            font-family: 'Inter';
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
            color-scheme: dark;
        }
        input:focus{
            border-radius: 15px;
        }
        form{
            margin-top: 250px;
            text-align: center;
        }
        form button{
            margin-top: 30px;
            background: rgb(20,20,20);
            background: linear-gradient(0deg, rgba(20,20,20,1) 0%, rgba(86,43,141,1) 1%, rgba(145,64,248,1) 49%, rgba(145,64,248,1) 90%);
            -webkit-filter: brightness(90%) contrast(90%) blur(2px) grayscale(10%);
            filter: brightness(90%) contrast(90%)  grayscale(10%);
            color: white;
        }
        form button{
            background-color: #8F38FE;
            user-select:none;
            text-align: center;
            text-decoration: none;
            transition-duration: 0.4s;
            -webkit-transition-duration: 0.4s;
            height: 71px;
            width: 250px;
            font-family: Tahoma;
            border-radius: 15px;
            font-size: 20px;
        }
        form button:hover{
            border-radius: 25px;
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
        @media only screen and (max-width: 600px){
            h4{
            position: absolute;
            font-size: 12px;
            top: 3%;
            margin-left: auto;
            margin-right: auto;
            left: 0;
            right: 0;
            text-align: center;
            }
            h2{
                position: absolute;
                font-size: 30px;
                top: 10%;
                margin-left: auto;
                margin-right: auto;
                left: 0;
                right: 0;
                text-align: center;
                font-family: 'Inter';
            }
            form{
                margin-top: 200px;
            }
            .user-info{
                width: 115px;
                font-size: 20px;

            }

        }
    </style>
</head>
<body>
    <!-- ZMIEŃ HREFA-->
    <a href="./main_page.html">
        <img src="./src/back-icon.png" alt="powrót" class="back-icon">
    </a>
    <h4>CHAT.COM</h4>
    <h2>REJESTRACJA</h2>
    <form id="signup-form" method="post" action="">
        <sub>PODAJ SWOJE IMIĘ ORAZ NAZWISKO</sub><br>
        <input type="text" max="15" required="required" placeholder="IMIĘ" class="user-info" name="firstname"><input type="text" max="15" required="required" placeholder="NAZWISKO" class="user-info" name="surname"><br>
        <sub>PODAJ DATĘ URODZENIA</sub><br>
        <input type="date" class="birth-date" required="required" name="date_of_birth"><br>
        <sub>PODAJ SWÓJ E-MAIL</sub><br>
        <input type="email" required="required" name="email"><br>
        <sub>HASŁO</sub><br>
        <input type="password" name="password" required="required" id="id_password" name="password">
        <i class="far fa-eye" id="togglePassword" style="margin-left: -10px; cursor: pointer;"></i><br>
        <sub>POTWIERDŹ HASŁO</sub><br>
        <input type="password" name="password" required="required" id="id_password_confirm"><br>
        <button type="submit">ZAREJESTRUJ SIĘ</button>
    </form>

</body>
    <script defer>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#id_password');

        togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
        });

        document.getElementById("signup-form").addEventListener("submit", function(event) {
            event.preventDefault();

            var password1 = document.getElementById("id_password").value;
            var password2 = document.getElementById("id_password_confirm").value;

            if (password1 === password2) {
                this.submit();
            } else {
                alert("Hasła się różnią. Proszę wprowadzić takie same hasła.");
                document.getElementById("id_password").value = "";
                document.getElementById("id_password_confirm").value = "";
            }
        });
    </script>
</html>
