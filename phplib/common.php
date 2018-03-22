<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 24.10.2017
 * Time: 21:11
 */

/*
 * Обработка ошибок
 * @param string $err
 */
function error($err) {
    // Запись в журнал
    $servername = "localhost";
    $username = "PHP";
    $password = "DeltaDB";
    try {
        $PDO = new PDO("mysql:host=$servername;dbname=delta;charset=CP1251", $username, $password);
        $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set the PDO error mode to exception
        $sql = 'INSERT INTO log (text) VALUES ("' . $err . '")';
        $PDO->exec($sql);
    }
    catch (PDOException $exception) {
        syslog(LOG_ERR, "DeltaWebServer " . $err);
    }
    $PDO = null;

    // Уведомление почтой
    $subject = "Ошибка на сайте Дельты!";
    $reply_to = "ablov@cintra.ru";
    $error = "
        <!doctype html>
        <html>
        <head>
        <meta charset=\"windows-1251\">
        <title>Ошибка!</title>
        </head>
        <body>
        $err
        </body>
        </html>
    ";
    $headers =
        'MIME-Version: 1.0' . "\r\n" .
        'Content-type: text/html; charset=windows-1251' . "\r\n" .
        'From: Delta <delta_mail_robot@cintra.ru>' . "\r\n" .
        'To: ablov@cintra.ru' . "\r\n" .
//        'Cc: anna.sem@gmail.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($reply_to, $subject, $error, $headers);

}

/*
 * Печатает стандартно контакты организатора
 * @param string $person - 'abl' or 'sem'
 */
function printContact($person) {
    if($person == 'sem') {
        echo '
                +7(903)749-4851 (<a title="Телефон" href="tel:+79037494851" target="_blank">телефон</a>,
                <a title="Telegram" href="https://t.me/annasemovskaya" target="_blank">Telegram</a>,
                <a title="WhatsApp" href="whatsapp://send?phone=+79037494851" target="_blank">WhatsApp</a>)<br>
                <a title="E-mail" href="mailto:anna.sem@gmail.com" target="_blank">anna.sem@gmail.com</a><br>
                Skype: <a title="Skype" href="skype:aselect1976?chat" target="_blank">aselect1976</a>
        ';
    }
    elseif ($person == 'abl') {
        echo '
                +7(903)795-4223 (<a title="Телефон" href="tel:+79037954223" target="_blank">телефон</a>,
                <a title="Telegram" href="https://t.me/d_ablov" target="_blank">Telegram</a>,
                <a title="Viber" href="viber://add?number=+79037954223" target="_blank">Viber</a>,
                <a title="WhatsApp" href="whatsapp://send?phone=+79037954223" target="_blank">WhatsApp</a>)<br>
                <a title="E-Mail" href="mailto:d.ablov@gmail.com" target="_blank">d.ablov@gmail.com</a><br>
                Skype: <a title="Skype" href="skype:d.ablov?chat" target="_blank">d.ablov</a>       
        ';
    }
}
