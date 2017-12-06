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
    $reply_to = "d.ablov@gmail.com";
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
        'To: d.ablov@gmail.com_' . "\r\n" .
//        'Cc: anna.sem@gmail.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($reply_to, $subject, $error, $headers);

}
