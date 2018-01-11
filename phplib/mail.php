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
 * ���������� ���������� ����������
 ***/
$from = 'delta_mail_robot@cintra.ru';
$cc = 'ablov@cintra.ru';
$reply_to = 'anna.sem@gmail.com';
$domain = 'delta.gorod.de';
$myCabinet = 'http://' . $domain . '/mycabinet.php';


/***
 * ������� ������ �� �������� �����������
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
    $to = $person['Email'];
    global $cc;
    global $reply_to;
    $subject = 'Delta-2018 registration';
    global $myCabinet;
    global $domain;

    /*
     * ������� ���������� ������ ��� ����� � Excel
     */
    $debug = "
        <!doctype html>
        <html>
        <head>
        <meta charset=\"windows-1251\">
        <title>����� �����������!</title>
        </head>
        <body>
        $time;$ip;$email;$surname;$name;$m_name;$gender;$birthday;$class;$school;$city;$country;$lang;$tel;$notes
        </body>
        </html>
    ";
    $headers =
        'MIME-Version: 1.0' . "\r\n" .
        'Content-type: text/html; charset=windows-1251' . "\r\n" .
        'From: Delta <' . $from . '>' . "\r\n" .
        'To: ' . $reply_to . "\r\n" .
        'Cc: ' . $cc . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($reply_to, $subject, $debug, $headers);

    /*
     * ������ �������� ������
     */
    $message = '
        <!doctype html>
        <html>
        <head>
        <meta charset="windows-1251">
        <title>���������� �� �����������!</title>
        </head>
        <body>
        <p>���������� ��� �� ����������� � ������ ������-�������������� ������ ������� � �������!</p>
        <p> ����� ������� ��������:</p>
        <p><a href="' . $myCabinet . '?id=' . $id . '">' . $myCabinet . '?id=' . $id . '</a></p>
        <p><b>����������, ������� � ������ ������� � �������� ������������� ���������!</b></p>
        <p>��������� ���� � ������� ������������� ���������.</p>
        <p>��������, ������ ������ ������� ����� ��������� ������������, ������������� �� �������� ����� ��� ����� 
        �������������� ��������. ��� �� ��������. ��� ��� ������ � ������ ��� ����� ������ ��������� �� ������ �����, 
        ������� � ���������� � ������ � ������� �������� ������ ����������, �� �� ������ ���� ��������� ������������ 
        ������ ������ �����-���� ������������� ���������. ��� ��������� ��� ����� ������, ��� �������� � ����������� 
        �������� �������. ��������� ������, ��������, ����� ������ �� ���������, � ��������� � ������ �� ��� ��������, 
        ������� �� ������������, ��������� ���������.</p>
        <p>������ ������� �� ������� ����� ����� ��� ������. �� ����� ������ �� � ��������� ����, ����� �������� 
        ��� �������� ��������.</p>
        <p>&nbsp;</p>
        <p>���������� ����� ����������� � ���� ������ (����������) ������ ��� � ����������� ���� � ������� <b>���� 
        ������</b> ����� �����������.</p>
        <p>&nbsp;</p>
        <p>� ���������,<br>
        ���� ���������<br>
        �������� ������</p>
        <p>+7(903)749-4851<br>
        E-mail: anna@sem@gmail.com<br>
        Skype: aselect1976<br>
        <a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a> </p>
        </body>
        </html>
    ';
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;
    $mail->CharSet = 'windows-1251';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addCC($cc);
    $mail->addReplyTo($reply_to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = '������������! �� ������������������ � ������ ������-�������������� ������ "������" � �������.\n\r
    �������, ����������, � ������ ������� �� ������: ' . $myCabinet . '?id=' . $id;

    $mail->send();
}

/***
 * ������� ������ � ������������� ����������
 * @param array from delta(registrations)  $person
 * @throws \PHPMailer\PHPMailer\Exception
 */
function sendAssignmentsMail($person) {
    global $from;
    $to = $person['Email'];
    global $cc;
    global $reply_to;
    $subject = '������������� ��������� Delta-2018';
    $attachment = 'documents/assignments.pdf';
    global $domain;
    global $myCabinet;

    $message = '
        <!doctype html>
        <html>
        <head>
        <meta charset="windows-1251">
        <title>������������� ���������</title>
        </head>
        <body>
        <p>������������!</p>
        <p>� ���������� � ����� ������ - ������������� ��������� � ������ ������-�������������� ������ "������" � �������.</p>
        <p>��������, ������ ������ ������� ����� ��������� ������������, ������������� �� �������� ����� ��� ����� 
        �������������� ��������. ��� �� ��������. ��� ��� ������ � ������ ��� ����� ������ ��������� �� ������ �����, 
        ������� � ���������� � ������ � ������� �������� ������ ����������, �� �� ������ ���� ��������� ������������ 
        ������ ������ �����-���� ������������� ���������. ��� ��������� ��� ����� ������, ��� �������� � ����������� 
        �������� �������. ��������� ������, ��������, ����� ������ �� ���������, � ��������� � ������ �� ��� ��������, 
        ������� �� ������������, ��������� ���������.</p>
        <p>������, ��� ������� ��������� ����� ��������� ����������� ����������������� ������ � ����� ������ ��������� 
        ����. ������ ������� �� ������� ����� ����� ��� ������. �� ����� ������ �� � ��������� ����, ����� �������� 
        ��� �������� ��������.</p>
        <p>���������� ����� �������� � ������� <b>���� ������</b> ����� ��������� ������ � ���� ������ (����������) ������ 
        ��� � ����������� ���� �� ������ anna.sem@gmail.com ��� ��������� � ������ ��������.</p>
        <p>&nbsp</p>
        <p>� ���������,<br>
        ���� ���������<br>
        �������� ������</p>
        <p>+7(903)749-4851<br>
        Skype: aselect1976<br>
        <a href="https://www.facebook.com/Summer.Camp.Delta">https://www.facebook.com/Summer.Camp.Delta</a> </p>
        <p>P.S.</p>
        <p> ����� ������ ������� ��������:</p>
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
    $mail->CharSet = 'windows-1251';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addCC($cc);
    $mail->addReplyTo($reply_to);

    $mail->addAttachment($attachment);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = '������������! � ���������� ������������� ��������� � ������ ������-�������������� ������ "������" � �������';

    $mail->send();
}

/***
 *
 * ������� ������ �� ����� �������� �����
 * @param array from delta(registrations)  $person
 * @throws \PHPMailer\PHPMailer\Exception
 *
 */
function sendFeedbackMail($email, $name, $message){
    $from = 'delta_mail_robot@cintra.ru';
    $to = 'anna.sem@gmail.com';
    $cc = 'ablov@cintra.ru';
    $reply_to = $email;
    $subject = '��������� � ����� �������� ����� Delta-2018';

    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAuth = false;
    $mail->SMTPAutoTLS = false;
    $mail->CharSet = 'windows-1251';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);
    $mail->addCC($cc);
    $mail->addReplyTo($reply_to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $name . ' say: ' . $message;

    $mail->send();
}