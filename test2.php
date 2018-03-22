<!doctype html>
<?php

if (isset($_POST) && is_array($_POST) && count($_POST) > 0) {
    if ($_POST['form'] === "CertName") {
        header("Content-type: text/html; charset=Windows-1251");
        echo testData($_POST['certName']);
        exit();
    }
}

function testData($data) {
    $value = iconv("UTF-8", "Windows-1251", $data);
    return $value;
}
?>
<html>
<head>
    <meta charset="Windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/mycabinet.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="JS/lib/jquery.form.min.js"></script>
    <script>
        $(document).ready(function () {
            var options = {
                beforeSubmit: function (arr, $form, options) {
//                    arr[2].value = encodeURIComponent($("#certName").val());
                },
                success: function (data, status) {
                    if (status == "success") {
                        alert(data);
                        $("#certName").val(data);
                    }
                },
                contentType: "application/x-www-form-urlencoded;charset=Windows-1251" // Не влияет?
            };
            $("#certNameForm").ajaxForm(options);
        })
    </script>
</head>
<body>
<form id="certNameForm" method="post">
    <input name="form" value="CertName" hidden>
    <p>Выберите язык сертификата:<br>
        <select name="certLang" id="certLang" required>
            <option value="" hidden disabled select>Выбрать...</option>
            <option value="ru">Русский</option>
            <option value="de">Deutsch</option>
            <option value="en">English</option>
        </select>
    </p>
    <p>Тестовая строка:<br>
        <input id="certName" name="certName" type="text" class="text-input"
            <?php if (isset($value) && $value  !== "") {
                echo (' value=' . $value);
            }
            ?>
               required>
        <input id="btnCertName" type="submit" value="&#x2714" class="smallbtn"></p>
</form>


</body>
</html>