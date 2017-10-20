<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Регистрация</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/form.css" rel="stylesheet" type="text/css">
<script src="JS/form_functions.js"></script>
<?php require_once 'phplib/dbConnect.php' ?>
</head>

<body>
<?php 
if (!isset($_POST["ALL_DONE"])) {
?>
<div class="row"><div class="col-12">
<h1>Регистрация в летний физико-математический лагерь "Дельта"</h1>
<p class="explanation">Поля, помеченные звёздочкой <span class="required">*</span> обязательны.</p>
</div></div> <!-- col12, row -->

<form id="form" name="form" method="post" onsubmit="return checkForm()">
  <div class="row">
  <div class="col-4">
  <p>E-Mail <span class="required">*</span><br>
    <input class="text-input" name="email" type="email" required id="email" placeholder="ivan@ivanov.earth" title="Адрес электронной почты">
    <br>
  <span class="explanation">Ваш адрес электронной почты. На него придёт подтверждение регистрации.</span></p>
  </div> <!-- col- -->
  <div class="col-4">
  <p> Контактный телефон <span class="required">*</span><br>
    <input name="tel" type="tel" required class="text-input" id="tel" title="Телефон для связи">
  </p>
  </div> <!-- col- -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>Фамилия <span class="required">*</span><br>
    <input class="text-input" name="surname" type="text" required id="surname" placeholder="Иванов" title="Фамилия ребёнка">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>Имя <span class="required">*</span><br>
    <input class="text-input" name="name" type="text" required id="name" placeholder="Иван" title="Имя ребёнка">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>Отчество<br>
    <input class="text-input" name="middlename" type="text" id="middlename" placeholder="Иванович" title="Отчество ребёнка">
  </p>
  </div> <!-- col-4 -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>Дата рождения <span class="required">*</span><br>
    <input class="text-input" name="birthday" type="text" required id="birthday" placeholder="dd/mm/yyyy"
           title="Дата рождения" onChange="checkDate(this);">
    <span class="hidden_error" id="date_error">Неверный формат даты!<br>Используйте "день/месяц/год"<br>Например: 27/03/1999</span>
    <span class="explanation"><span class="age" id="age"></span></span>
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <div id="gender">
  <p>Пол <span class="required">*</span><br>
    <label>
      <input name="gender" type="radio" required id="female" value="f">девочка</label>&nbsp;&nbsp;
    <label>
      <input name="gender" type="radio" required id="male" value="m">мальчик</label>
  </p>
  </div> <!-- gender -->
  </div> <!-- col-3 -->
  </div> <!-- raw -->
  <div class="row">
  <div class="col-3">
  <p>Класс<br>
    <input class="text-input" name="class" type="number" id="class" title="Класс">
    <br>
  <span class="explanation">Из расчёта 11-летней системы или год обучения ребёнка в школе.</span> </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>Школа <span class="required">*</span><br>
    <input class="text-input" name="school" type="text" required id="school" title="Школа">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>Город <span class="required">*</span><br>
    <input class="text-input" name="city" type="text" required id="sity" title="Город проживания">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>Страна <span class="required">*</span><br>
    <input class="text-input" name="country" type="text" required id="country" title="Страна проживания">
  </p>
  </div> <!-- col-3 -->
  </div> <!-- row -->
  <div class="row"> <div class="col-6">
  <p>Языки <span class="required">*</span><br>
    <input class="text-input" name="langs" type="text" required id="langs" title="Какими языками владеет ребёнок" value="русский" size="60">
    <br>
  <span class="explanation">Языки, которыми владеет ребёнок. Перечислите через запятую.</span></p>
  <p>Дополнительная информация<br>
    <textarea class="text-input" name="notes" cols="60" rows="4" id="notes" title="Замечания"></textarea>
    <br>
  <span class="explanation">Всё, что Вы ещё хотели бы нам сообщить. Также Вы можете задать свои вопросы письмом на delta@mathbaby.ru</span></p>
  <div class="indented" id="agree"><p>На основании ст.64 п.1 Семейного кодекса РФ даю свое согласие на   обработку указанных выше данных моего ребенка для участия в выездной   школе, получения информации о школе, отъезде, возвращении. Использование   данных для других целей не предусмотрено.<br>
  Согласен<span class="required" title="Обязательно для заполнения.">*</span>: <input name="agree" type="checkbox" required id="agree" title="Согласие"></p></div>
  <p>
    <input name="submit" type="submit" id="submit" value="Зарегистрировать">
  </p>
  </div></div> <!-- col-6 row -->
  <p><input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok"><br>
  </p>
</form>
<?php
} elseif ($_POST["ALL_DONE"] !== 'Ok') {
    echo 'Что-то пошло не так...';
} else { ?>

<?php
    $db = new dbConnect();
    $db->putRegData();
}
?>
</body>
</html>