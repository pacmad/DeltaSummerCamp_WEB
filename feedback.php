<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Обратная связь</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/form.css" rel="stylesheet" type="text/css">
</head>
<body>
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
<div class="row"><div class="col-12">
        <h1>Форма обратной связи с организаторами летнего физико-математического лагеря &quot;Дельта&quot; в Мюнхене</h1>
</div></div>
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
            <input name="email" type="email" id="email" class="text-input" value="<?php echo $email?>">
            </p>
        </div> <!-- col-4 -->
        <div class="col-4">
        	<p>
	        <b>Как звать ребёнка: </b><br>
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
</div><!-- main -->
</body>
</html>
