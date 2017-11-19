<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 26.10.2017
 * Time: 18:17
 */

use PHPMailer\PHPMailer\PHPMailer;

require_once 'PHPMailer\PHPMailer.php';
require_once 'PHPMailer\SMTP.php';
require_once 'PHPMailer\Exception.php';

/***
 * Отсылка письма об удадчной регистрации
 * Возвращает false, если что-то пошло не так
 */
function sendRegMail($person) {
    $id = $person['UniqueId'];
    $time = $person['RegistrationTime'];
    $ip = $person['UserIP'];
    $email = $person['Email'];
    $surname = $person['Surname'];
    $name = $person['Name'];
    $m_name = $person['MiddleName'];
    $gender = $person['Gender'];
    $birthday = $person['Birthday'];
    $class = $person['Class'];
    $school = $person['School'];
    $city = $person['City'];
    $country = $person['Country'];
    $lang = $person['Languages'];
    $tel = $person['Tel'];
    $notes = $person['Notes'];
    $from = 'delta_mail_robot@cintra.ru';
    $to = $person['Email'];
    $cc = 'd.ablov@gmail.com';
    $reply_to = 'd.ablov@gmail.com';
    $subject = 'Delta-2018 registration';

    /*
     * Сначала отправляем строку для ввода в Excel
     */
    $debug = "
<!doctype html>
<html>
<head>
<meta charset=\"windows-1251\">
<title>Новая регистрация!</title>
</head>
<body>
$time;$ip;$email;$surname;$name;$m_name;$gender;$birthday;$class;$school;$city;$country;$lang;$tel;$notes
</body>
</html>
    ";
    $headers =
        'MIME-Version: 1.0' . "\r\n" .
        'Content-type: text/html; charset=windows-1251' . "\r\n" .
        'From: Delta <delta_mail_robot@cintra.ru>' . "\r\n" .
        'To: ' . $reply_to . "\r\n" .
        'Cc: ' . $cc . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($reply_to, $subject, $debug, $headers);

    /*
     * Теперь основное письмо
     */
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
<p><a href="http://test.ablov.ru/mycabinet.php?id=' . $id . '">http://test.ablov.ru/mycabinet.php?id=' . $id . '</a></p>
<p><b>Пожалуйста, зайдите в Личный кабинет и скачайте вступительную олимпиаду!</b></p>
<p>Обратите внимание: условия задач вступительной олимпиады могут показаться сложными и непривычными. Это не страшно.
Так как программы в разных странах и возраста детей отличаются, олимпиада не предполагает знаний по конкретной школьной
программе. Основная задача Олимпиады не отсев "слабых детей", а информация: нам надо понять, какие дети собираются в Дельту, 
какие у них интересы, какой кругозор. Исходя из этого мы планируем курсы, которые собираемся рассказывать в лагере.</p>
<p>Учтите, что решение некоторых задач подтебует определённой исследовательской работы и может занять несколько 
дней.</p>
<p>Предупредите, пожалуйста, ребёнка, что мы не ожидаем от него 100%-ного выполнения всех задач. Нам интесерсны идеи, 
которые придут ему в голову, а не ответы!</p>
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
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'mx.cintra.ru';
        $mail->SMTPAuth = false;
        $mail->CharSet = 'windows-1251';

        $mail->setFrom($from, 'Delta Summer Camp');
        $mail->addAddress($to);
        $mail->addCC($cc);
        $mail->addReplyTo($reply_to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = 'Здравствуйте! Вы зарегистрировались в Летний физико-математический лагерь "Дельта" в Мюнхене.\n\r
        Зайдите, пожалуйста, в личный кабинет по адресу: http://test.ablov.ru/mycabinet.php?id=' . $id;

        $mail->send();

        return true;
    }
    catch (Exception $e){
        return false;
    }
}

/***
 * Отсылка письма с вступительной олимпиадой
 * $person - данные по ребёнку (массив)
 * Возвращает false, если что-то пошло не так
 */
function sendAssignmentsMail($person) {
    $from = 'delta_mail_robot@cintra.ru';
    $to = $person['Email'];
    $cc = 'd.ablov@gmail.com';
    $reply_to = 'd.ablov@gmail.com';
    $subject = 'Вступительная олимпиада Delta-2018';
    $attachment = 'documents/assignments.pdf';

    $message = '
<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<title>Вступительная олимпиада</title>
</head>
<body>
<p>Здравствуйте!</p>
<p>В приложении к этому письму Вы найдёте вступительную олимпиаду в Летний физико-математический лагерь "Дельта" в Мюнхене.</p>
<p>Обратите внимание: условия задач вступительной олимпиады могут показаться сложными и непривычными. Это не страшно.
Так как программы в разных странах и возраста детей отличаются, олимпиада не предполагает знаний по конкретной школьной
программе. Основная задача Олимпиады не отсев "слабых детей", а информация: нам надо понять, какие дети собираются в Дельту, 
какие у них интересы, какой кругозор. Исходя из этого мы планируем курсы, которые собираемся рассказывать в лагере.</p>
<p>Учтите, что решение некоторых задач подтебует определённой исследовательской работы и может занять несколько 
дней.</p>
<p>Предупредите, пожалуйста, ребёнка, что мы не ожидаем от него 100%-ного выполнения всех задач. Нам интесерсны идеи, 
которые придут ему в голову, а не ответы!</p>
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
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'mx.cintra.ru';
        $mail->SMTPAuth = false;
        $mail->CharSet = 'windows-1251';

        $mail->setFrom($from, 'Delta Summer Camp');
        $mail->addAddress($to);
        $mail->addCC($cc);
        $mail->addReplyTo($reply_to);

        $mail->addAttachment($attachment);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = 'Здравствуйте! В приложении вступительная олимпиада в Летний физико-математический лагерь "Дельта" в Мюнхене';

        $mail->send();

        return true;
    }
    catch (Exception $e){
        return false;
    }
}

/***
 *
 * Отсылка письма из формы обратной связи
 *
 */
function sendFeedbackMail($email, $name, $message){
    $from = 'delta_mail_robot@cintra.ru';
    $to = 'dmitry@ablov.ru'; //'anna.sem@gmail.com';
    $cc = 'd.ablov@gmail.com';
    $reply_to = $email;
    $subject = 'Сообщение с формы обратной связи Delta-2018';

    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'mx.cintra.ru';
        $mail->SMTPAuth = false;
        $mail->CharSet = 'windows-1251';

        $mail->setFrom($from, 'Delta Summer Camp');
        $mail->addAddress($to);
        $mail->addCC($cc);
        $mail->addReplyTo($reply_to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $name . ' say: ' . $message;

        $mail->send();

        return true;
    }
    catch (Exception $e){
        return false;
    }
}