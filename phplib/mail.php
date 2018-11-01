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
 * Определяем глобальные переменные
 ***/
$from = 'delta_mail_robot@cintra.ru';
$cc =  'ablov@cintra.ru';
$bcc = 'ablov@cintra.ru';
$reply_to = 'summer.camp.delta@gmail.com';
$domain = 'delta.gorod.de';
$myCabinet = 'https://' . $domain . '/mycabinet.php';


/***
 * Отсылка письма об удадчной регистрации
 * @param array from delta(registrations)  $person
 * @throws \PHPMailer\PHPMailer\Exception
 *
 ***/
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
    global $from;
    $to = 'ablov@cintra.ru'; //$person['Email'];
    global $cc;
    global $bcc;
    global $reply_to;
    $subject = 'Delta-2019 pre-registration';
    global $myCabinet;
    global $domain;

    /*
     * Сначала отправляем строку для ввода в Excel
     */
    $debug = "
        <!doctype html>
        <html>
        <head>
        <meta charset=\"UTF-8\">
        <title>Новая регистрация!</title>
        </head>
        <body>
        $time<br>$ip<br>$email<br>$surname<br>$name<br>$m_name<br>$gender<br>$birthday<br>$class<br>$school<br>
        $city<br>$country<br>$lang<br>$tel<br>$notes
        </body>
        </html>
    ";
    $headers =
        'MIME-Version: 1.0' . "\r\n" .
        'Content-type: text/html; charset=UTF-8' . "\r\n" .
        'From: Delta <' . $from . '>' . "\r\n" .
        'To: ' . $reply_to . "\r\n" .
        'Cc: ' . $cc . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($reply_to, $subject, $debug, $headers);

    /*
     * Теперь основное письмо (предварительная регистрация!)
     */
    /* Комментарим основное письмо о регистрации
    $message = '
        <!doctype html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Благодарим за регистрацию!</title>
        </head>
        <body>
        <p>Благодарим Вас за регистрацию в летний физико-математический лагерь «Дельта» в Мюнхене!</p>
        <p> Адрес Личного кабинета:</p>
        <p><a href="' . $myCabinet . '?id=' . $id . '">' . $myCabinet . '?id=' . $id . '</a></p>
        <p><b>Пожалуйста, зайдите в Личный кабинет и скачайте вступительную олимпиаду!<br>
        Не теряйте адрес личного кабинета, в дальнейшем в нём будет выкладываться другая важная персональная информация.</b></p>
        <p>Несколько слов о задачах вступительной олимпиады.</p>
        <p>Возможно, вашему ребёнку условия задач покажутся непривычными, отличающимися от школьных задач или задач 
        математических олимпиад. Это не случайно. Так как Дельта – лагерь для детей разных возрастов из разных стран, 
        подходы к математике и физике в которых довольно сильно отличаются, мы не ставим цель проверить соответствие 
        знаний ребёнка какой-либо академической программе. Нам интересен ход мысли ребёнка, его интересы и способность 
        изложить решение. Некоторые задачи, возможно, будут решены не полностью, а некоторые – совсем не тем способом, 
        который мы предполагали, составляя олимпиаду.</p>
        <p>Именно поэтому на решение задач даётся две недели. Не стоит решать всё в последний день, лучше подумать 
        над задачами подольше.</p>
        <p>&nbsp;</p>
        <p>Для участников прошлых лет решение олимпиады не является обязательным, но оно желательно, так как результаты 
        учитываются при распределении в учебные группы и проекты.</p>
        <p>&nbsp;</p>
        <p>Результаты можно представить в виде сканов (фотографий) работы или в электронном виде по адресу:  
        summer.camp.delta@gmail.com в течение <b>двух недель</b> после получения олимпиады.</p>
        <p>&nbsp;</p>
        <p>С уважением,<br>
        Анна Семовская<br>
        директор лагеря</p>
        <p>+7(903)749-4851<br>
        E-mail: anna@sem@gmail.com<br>
        Skype: aselect1976<br>
        Facebook: <a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a><br>
        ВКонтакте: <a href="https://vk.com/summer_camp_delta">https://vk.com/summer_camp_delta</a></p>
        </body>
        </html>
    '; */
    $message = '
        <!doctype html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Благодарим за регистрацию!</title>
        </head>
        <body>
        <p>Благодарим Вас за предварительную регистрацию в летний физико-математический лагерь «Дельта» в Мюнхене!</p>
        <p>При открытии основной регистрации мы вышлем Вам письмо со вступительной олимпиадой и подробной информацией о 
        стоимости и сроках проведения лагеря.</p>
        <p>&nbsp;</p>
        <p>С уважением,<br>
        Анна Семовская<br>
        директор лагеря</p>
        <p>+7(903)749-4851<br>
        E-mail: anna@sem@gmail.com<br>
        Skype: aselect1976<br>
        Facebook: <a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a><br>
        ВКонтакте: <a href="https://vk.com/summer_camp_delta">https://vk.com/summer_camp_delta</a></p>
        </body>
        </html>
    ';
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addBCC($bcc);
    $mail->addReplyTo($reply_to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    /* Комментарим в предварительной регистрации
    $mail->AltBody = 'Здравствуйте! Вы зарегистрировались в Летний физико-математический лагерь "Дельта" в Мюнхене.\n\r
    Зайдите, пожалуйста, в личный кабинет по адресу: ' . $myCabinet . '?id=' . $id;
    */
    $mail->AltBody = 'Здравствуйте! Вы зарегистрировались в Летний физико-математический лагерь "Дельта" в Мюнхене.\n\r
    При открытии основной регистрации мы вышлем Вам письмо со вступительной олимпиадой и подробной информацией о 
    стоимости и сроках проведения лагеря.';

    $mail->send();
}

/***
 * Отсылка письма с вступительной олимпиадой
 * @param array from delta(registrations)  $person
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendAssignmentsMail($person) {
    global $from;
    $to = 'ablov@cintra.ru';//$person['Email'];
    global $bcc;
    global $reply_to;
    $subject = 'Вступительная олимпиада Delta-2018';
    $attachment = 'documents/assignments.pdf';
    global $domain;
    global $myCabinet;

    $message = '
        <!doctype html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Вступительная олимпиада</title>
        </head>
        <body>
        <p>Здравствуйте!</p>
        <p>В приложении к этому письму - вступительная олимпиада в Летний физико-математический лагерь "Дельта" в Мюнхене.</p>
        <p>Возможно, вашему ребёнку условия задач покажутся непривычными, отличающимися от школьных задач или задач 
        математических олимпиад. Это не случайно. Так как Дельта – лагерь для детей разных возрастов из разных стран, 
        подходы к математике и физике в которых довольно сильно отличаются, мы не ставим цель проверить соответствие 
        знаний ребёнка какой-либо академической программе. Нам интересен ход мысли ребёнка, его интересы и способность 
        изложить решение. Некоторые задачи, возможно, будут решены не полностью, а некоторые – совсем не тем способом, 
        который мы предполагали, составляя олимпиаду.</p>
        <p>Учтите, что решение некоторых задач потребует определённой исследовательской работы и может занять несколько 
        дней. Именно поэтому на решение задач даётся две недели. Не стоит решать всё в последний день, лучше подумать 
        над задачами подольше.</p>
        <p>&nbsp;</p>
        <p>Для участников прошлых лет решение олимпиады не является обязательным, но оно желательно, так как результаты 
        учитываются при распределении в учебные группы и проекты.</p>
        <p>&nbsp;</p>
        <p>Результаты можно прислать в течение <b>двух недель</b> после получения работы в виде сканов (фотографий) работы 
        или в электронном виде по адресу summer.camp.delta@gmail.com или загрузить в личном кабинете.</p>
        <p>&nbsp;</p>
        <p>С уважением,<br>
        Анна Семовская<br>
        директор лагеря</p>
        <p>+7(903)749-4851<br>
        Skype: aselect1976<br>
        Facebook: <a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a><br>
        ВКонтакте: <a href="https://vk.com/summer_camp_delta">https://vk.com/summer_camp_delta</a></p>
        <p>P.S.</p>
        <p> Адрес Вашего личного кабинета:</p>
        <p><a href="' . $myCabinet . '?id=' . $person['UniqueId'] . '">http://' . $domain .
        '/mycabinet.php?id=' . $person['UniqueId'] . '</a></p>
        </body>
        </html>
    ';
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addBCC($bcc);
    $mail->addReplyTo($reply_to);

    $mail->addAttachment($attachment);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = 'Здравствуйте! В приложении вступительная олимпиада в Летний физико-математический лагерь "Дельта" в Мюнхене';

    $mail->send();
}

/***
 *
 * Отсылка письма из формы обратной связи
 * @param array from delta(registrations)  $person
 * @throws \PHPMailer\PHPMailer\Exception
 *
 */
function sendFeedbackMail($email, $name, $message){
    $from = 'delta_mail_robot@cintra.ru';
    $to = 'summer.camp.delta@gmail.com';
    $cc = 'ablov@cintra.ru';
    $reply_to = $email;
    $subject = 'Сообщение с формы обратной связи Delta-2018';

    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addCC($cc);
    $mail->addReplyTo($reply_to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $name . ' say: ' . $message;

    $mail->send();
}

/***
 *
 * Письмо-уведомление о получении решения олимпиады
 * @param array from delta(registrations)  $person
 * @throws \PHPMailer\PHPMailer\Exception
 *
 */
function sendComfirmMail($person){
    global $from;
    $to = 'ablov@cintra.ru'; //$person['Email'];
    global $bcc;
    global $reply_to;
    $subject = 'Подтверждение получения решения олимпиады Delta-2018';
    global $domain;
    global $myCabinet;

    // Сначала высылаем подтверждение
    $message = '
        <!doctype html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Подтверждение получения решения олимпиады</title>
        </head>
        <body>
        <p>Здравствуйте!</p>
        <p>Мы получили решение вступительной олимпиады и в ближайшее время, после проверки работы, свяжемся с Вами.</p>
        <p>&nbsp;</p>
        <p>С уважением,<br>
        Анна Семовская<br>
        директор лагеря</p>
        <p>+7(903)749-4851<br>
        Skype: aselect1976<br>
        Facebook: <a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a><br>
        ВКонтакте: <a href="https://vk.com/summer_camp_delta">https://vk.com/summer_camp_delta</a></p>
        <p>P.S.</p>
        <p> Адрес Вашего личного кабинета:</p>
        <p><a href="' . $myCabinet . '?id=' . $person['UniqueId'] . '">http://' . $domain .
        '/mycabinet.php?id=' . $person['UniqueId'] . '</a></p>
        </body>
        </html>
    ';
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addBCC($bcc);
    $mail->addReplyTo($reply_to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = 'Здравствуйте! Подтверждаем получение решений вступительной олимпиады в Летний физико-математический лагерь "Дельта" в Мюнхене';

    $mail->send();

    // Теперь посылаем уведомление нам
    $mail->clearAddresses();
    $mail->addAddress("summer.camp.delta@gmail.com");
    $mail->Body = "Загружено решение олимпиады от " . $person['Name'] . " " . $person['Surname'] . ".<br>UID=" . $person['UniqueId'];
    $mail->send();
}

/***
 * Отсылка письма с "Приложением 2" к Договору
 * @param array from delta(registrations)  $person
 * @param string as attachment $attachment
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendApp2Mail($person, $attachment) {
    global $from;
    $to = 'ablov@cintra.ru';//$person['Email'];
    $cc = 'summer.camp.delta@gmail.com';
    global $bcc;
    global $reply_to;
    $subject = 'Приложение 2 к Договору на сопровождение Delta-2018';
    global $domain;
    global $myCabinet;

    $message = '
        <!doctype html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Приложение 2 к Договору на сопровождение Delta-2018</title>
        </head>
        <body>
        <p>Здравствуйте!</p>
        <p>В приложении к этому письму - Приложение 2 к Договору на сопровождение Delta-2018.</p>
        <p>&nbsp;</p>
        <p>С уважением,<br>
        Анна Семовская<br>
        директор лагеря</p>
        <p>+7(903)749-4851<br>
        Skype: aselect1976<br>
        Facebook: <a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a><br>
        ВКонтакте: <a href="https://vk.com/summer_camp_delta">https://vk.com/summer_camp_delta</a></p>
        <p>P.S.</p>
        <p> Адрес Вашего личного кабинета:</p>
        <p><a href="' . $myCabinet . '?id=' . $person['UniqueId'] . '">http://' . $domain .
        '/mycabinet.php?id=' . $person['UniqueId'] . '</a></p>
        </body>
        </html>
    ';
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addCC($cc);
    $mail->addBCC($bcc);
    $mail->addReplyTo($reply_to);

    $mail->addStringAttachment($attachment, 'app2.pdf', 'base64', 'application/pdf');

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = 'Приложение 2 к Договору на сопровождение Delta-2018';

    $mail->send();
}
