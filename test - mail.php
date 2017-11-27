<!DOCTYPE html>
<html>
<head>
<meta charset="windows-1251">
</head>
<body>
<h1>Эксперименты с посылкой почты в MySQL</h1>
<?php
require_once 'phplib/dbConnect.php';
$db = new dbConnect();
$person = $db->getPerson('53135bbb71714168d83d7409c4713fda');

$id = $person['UniqueId'];
$to = $person['Email'];
$cc = 'd.ablov@gmail.com';
$reply_to = 'd.ablov@gmail.com';
$subject = 'Delta-2018 registration';
$message = '
<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<title>Благодарим за регистрацию!</title>
</head>

<body>
<p>Благодарим Вас за регистрацию в летний физико-математический лагерь «Дельта» в Мюнхене!</p>
<p> Адрес Личного кабинета:</p>
<p><a href="http://delta.gorod.de/mycabinet.php?id=' . $id . '">http://delta.gorod.de/mycabinet.php?id=' . $id . '</a></p>
<p><b>Пожалуйста, зайдите в Личный кабинет и скачайте вступительную олимпиаду!</b></p>
<p>Обратите внимание: условия задач вступительной олимпиады могут показаться сложными и непривычными. Это не страшно.
Так как программы в разных странах и возраста детей отличаются, олимпиада не предполагает знаний по конкретной школьной
программе. С другой стороны решение некоторых задач подтебует определённой исследовательской работы и может занять несколько 
дней.</p>
<p>Предупредите, пожалуйста, ребёнка, что мы не ожидаем от него 100%-ного выполнения всех задач. Нам прежде всего интересно
понять, как он рассуждает, какие идеи пришли в голову во время решения задачи.</p>
<p>Результаты можно представить в виде сканов (фотографий) работы или в электронном виде в течение <b>двух недель</b> после регистрации.</p>
<p>С уважением,<br>
Анна Семовская<br>
директор лагеря</p>
<p>+7(903)749-4851<br>
Skype: aselect1976<br>
<a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a> </p>
</body>
</html>
';
$headers = 
'MIME-Version: 1.0' . "\r\n" .
'Content-type: text/html; charset=windows-1251' . "\r\n" .
'From: Delta <delta_mail_robot@cintra.ru>' . "\r\n" .
'To: ' . $to . "\r\n" .
'Cc: Dmitry Ablov <d.ablov@gmail.com>' . "\r\n" .
'Reply-To: ' . $reply_to . "\r\n" .
'X-Mailer: PHP/' . phpversion();
if (mail($to, $subject, $message, $headers)) {
	echo 'Письмо отправлено';
} else {
	echo 'Что-то пошло не так...';
}
?>

</body>
</html>
