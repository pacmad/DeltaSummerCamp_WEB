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
 * ������� ������ �� �������� �����������
 * ���������� false, ���� ���-�� ����� �� ���
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
        'From: Delta <delta_mail_robot@cintra.ru>' . "\r\n" .
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
<p><a href="http://test.ablov.ru/mycabinet.php?id=' . $id . '">http://test.ablov.ru/mycabinet.php?id=' . $id . '</a></p>
<p><b>����������, ������� � ������ ������� � �������� ������������� ���������!</b></p>
<p>�������� ��������: ������� ����� ������������� ��������� ����� ���������� �������� � ������������. ��� �� �������.
��� ��� ��������� � ������ ������� � �������� ����� ����������, ��������� �� ������������ ������ �� ���������� ��������
���������. �������� ������ ��������� �� ����� "������ �����", � ����������: ��� ���� ������, ����� ���� ���������� � ������, 
����� � ��� ��������, ����� ��������. ������ �� ����� �� ��������� �����, ������� ���������� ������������ � ������.</p>
<p>������, ��� ������� ��������� ����� ��������� ����������� ����������������� ������ � ����� ������ ��������� 
����.</p>
<p>������������, ����������, ������, ��� �� �� ������� �� ���� 100%-���� ���������� ���� �����. ��� ���������� ����, 
������� ������ ��� � ������, � �� ������!</p>
<p>���������� ����� ����������� � ���� ������ (����������) ������ ��� � ����������� ���� � ������� <b>���� ������</b> ����� �����������.</p>
<p>� ���������,<br>
���� ���������<br>
�������� ������</p>
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
        $mail->AltBody = '������������! �� ������������������ � ������ ������-�������������� ������ "������" � �������.\n\r
        �������, ����������, � ������ ������� �� ������: http://test.ablov.ru/mycabinet.php?id=' . $id;

        $mail->send();

        return true;
    }
    catch (Exception $e){
        return false;
    }
}

/***
 * ������� ������ � ������������� ����������
 * $person - ������ �� ������ (������)
 * ���������� false, ���� ���-�� ����� �� ���
 */
function sendAssignmentsMail($person) {
    $from = 'delta_mail_robot@cintra.ru';
    $to = $person['Email'];
    $cc = 'd.ablov@gmail.com';
    $reply_to = 'd.ablov@gmail.com';
    $subject = '������������� ��������� Delta-2018';
    $attachment = 'documents/assignments.pdf';

    $message = '
<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<title>������������� ���������</title>
</head>
<body>
<p>������������!</p>
<p>� ���������� � ����� ������ �� ������ ������������� ��������� � ������ ������-�������������� ������ "������" � �������.</p>
<p>�������� ��������: ������� ����� ������������� ��������� ����� ���������� �������� � ������������. ��� �� �������.
��� ��� ��������� � ������ ������� � �������� ����� ����������, ��������� �� ������������ ������ �� ���������� ��������
���������. �������� ������ ��������� �� ����� "������ �����", � ����������: ��� ���� ������, ����� ���� ���������� � ������, 
����� � ��� ��������, ����� ��������. ������ �� ����� �� ��������� �����, ������� ���������� ������������ � ������.</p>
<p>������, ��� ������� ��������� ����� ��������� ����������� ����������������� ������ � ����� ������ ��������� 
����.</p>
<p>������������, ����������, ������, ��� �� �� ������� �� ���� 100%-���� ���������� ���� �����. ��� ���������� ����, 
������� ������ ��� � ������, � �� ������!</p>
<p>���������� ����� ����������� � ���� ������ (����������) ������ ��� � ����������� ���� � ������� <b>���� ������</b> ����� �����������.</p>
<p>� ���������,<br>
���� ���������<br>
�������� ������</p>
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
        $mail->AltBody = '������������! � ���������� ������������� ��������� � ������ ������-�������������� ������ "������" � �������';

        $mail->send();

        return true;
    }
    catch (Exception $e){
        return false;
    }
}

/***
 *
 * ������� ������ �� ����� �������� �����
 *
 */
function sendFeedbackMail($email, $name, $message){
    $from = 'delta_mail_robot@cintra.ru';
    $to = 'dmitry@ablov.ru'; //'anna.sem@gmail.com';
    $cc = 'd.ablov@gmail.com';
    $reply_to = $email;
    $subject = '��������� � ����� �������� ����� Delta-2018';

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