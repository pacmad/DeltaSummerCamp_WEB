<!DOCTYPE html>
<html>
<head>
<meta charset="windows-1251">
</head>
<body>
<h1>������������ � �������� ����� � MySQL</h1>
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
<title>���������� �� �����������!</title>
</head>

<body>
<p>���������� ��� �� ����������� � ������ ������-�������������� ������ ������� � �������!</p>
<p> ����� ������� ��������:</p>
<p><a href="http://delta.gorod.de/mycabinet.php?id=' . $id . '">http://delta.gorod.de/mycabinet.php?id=' . $id . '</a></p>
<p><b>����������, ������� � ������ ������� � �������� ������������� ���������!</b></p>
<p>�������� ��������: ������� ����� ������������� ��������� ����� ���������� �������� � ������������. ��� �� �������.
��� ��� ��������� � ������ ������� � �������� ����� ����������, ��������� �� ������������ ������ �� ���������� ��������
���������. � ������ ������� ������� ��������� ����� ��������� ����������� ����������������� ������ � ����� ������ ��������� 
����.</p>
<p>������������, ����������, ������, ��� �� �� ������� �� ���� 100%-���� ���������� ���� �����. ��� ������ ����� ���������
������, ��� �� ����������, ����� ���� ������ � ������ �� ����� ������� ������.</p>
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
$headers = 
'MIME-Version: 1.0' . "\r\n" .
'Content-type: text/html; charset=windows-1251' . "\r\n" .
'From: Delta <delta_mail_robot@cintra.ru>' . "\r\n" .
'To: ' . $to . "\r\n" .
'Cc: Dmitry Ablov <d.ablov@gmail.com>' . "\r\n" .
'Reply-To: ' . $reply_to . "\r\n" .
'X-Mailer: PHP/' . phpversion();
if (mail($to, $subject, $message, $headers)) {
	echo '������ ����������';
} else {
	echo '���-�� ����� �� ���...';
}
?>

</body>
</html>
