<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<?php
include 'phplib/yandex.metrika.inc';
include 'phplib/google.analytics.inc';
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Обратная связь</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/mycabinet.css" rel="stylesheet" type="text/css">
<?php
    require_once 'phplib/common.inc';
?>
</head>
<body>
<div class="logo"></div>
<?php

$name = '';
$email = '';

if (isset($_POST["id"])) {
      $name = $_POST['name'];
      $email = $_POST['email'];
}

/*
 * Вывод страницы обратной связи
 *
 */ ?>
<div class="title">
    <h1>Связаться с организаторами летнего физико-математического лагеря &quot;Дельта&quot; в Мюнхене</h1>
</div>
<div class="main">
    <form id="feedback" name="feedback" method="post" action="feedback-status.php">
    <div class="row"><div class="col-8">
	    <h3>Здравствуйте! </h3>
        <p>Заполните, пожалуйста, поля формы. Мы свяжемся с вами, как только получим этот запрос.</p>
	</div></div> <!-- col-8, row -->
    <div class="row">
        <div class="col-4">
        	<p>
	        <b>E-Mail: </b><br>
            <input name="email" type="email" id="email" class="text-input" value="<?php echo $email?>" required>
            </p>
        </div> <!-- col-4 -->
        <div class="col-4">
        	<p>
	        <b>Как зовут ребёнка: </b><br>
            <input name="name" type="text" id="name" class="text-input" value="<?php echo $name?>">
            </p>
        </div> <!-- col-4 -->
    </div> <!-- row -->
    <div class="row"><div class="col-8">
    <p><b>Сообщение:</b><br>
    <textarea name="message" rows="4" class="text-input" id="message"></textarea>
    </p>
    <p><input type="submit" value="Отправить!"></p>
    </div></div> <!-- row --><!-- col-8-->
    </form>
    <hr>
    <div id="persons" class="row">
        <div class="col-6">
            <p><b>Анна Семовская</b><br>
                <?php printContact('sem'); ?>
            </p></div> <!-- col-6 -->
        <div class="col-6">
            <p><b>Дмитрий Аблов</b><br>
                <?php printContact('abl'); ?>
            </p>
        </div>
    </div> <!-- col-6, row -->

</div><!-- main -->
</body>
</html>
