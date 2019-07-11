<!doctype html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Уголок администрарока</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/mycabinet.css" rel="stylesheet" type="text/css">
    <!-- jQuery  -->
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <!-- jQuery Form -->
    <script src="JS/lib/jquery.form.min.js"></script>

    <script>
        $(document).ready(function () {

        });
    </script>
</head>

<body>
<?php
if (!isset($_POST['password'])) {
    ?>
    <div class="container">
    <form id="newPass" action="newpass.php" method="post" class="form-signin" role="form">
        <h2 class="form-signin-heading">Привет, давай придумаем пароль!</h2>
        Введите новый пароль: <input type="password" name="pass_1" id="pass_1">
        Повторите пароль: <input type="password" name="pass_2" id="pass_2">
        <input type="submit" value="Сохранить пароль">
    </form>
    </div>
    <?php
}
?>
</body>
</html>