<!DOCTYPE html>
<html>
<head>
<meta charset="windows-1251">
</head>
<body>
<h1>������������ � �������� �����</h1>
<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once 'phplib\PHPMailer\PHPMailer.php';
require_once 'phplib\PHPMailer\SMTP.php';
require_once 'phplib\PHPMailer\Exception.php';

$from = 'delta_mail_robot@cintra.ru';
$to = 'd.ablov@gmail.com';
$subject = '�������� ������ PHPMailer';
$message = '
<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<title>��������</title>
</head>

<body>
����� ������
</body>
</html>
';

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 3;
    $mail->isSMTP();
    $mail->Host = 'mx.cintra.ru';
    $mail->SMTPAutoTLS = false;
    $mail->SMTPAuth = false;
    $mail->CharSet = 'windows-1251';

    $mail->setFrom($from, 'Delta Summer Camp');
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = '����� ������';

    $mail->send();

    return true;
}
catch (Exception $e){
    return false;
}
?>

</body>
</html>
