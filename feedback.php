<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>�������� �����</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/form.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php

$name = '';
$email = '';

if (isset($_POST["id"])) {
      $name = $_POST['name'];
      $email = $_POST['email'];
}

/*
 * ����� �������� �������� �����
 *
 */ ?>
<div class="row"><div class="col-12">
        <h1>����� �������� ����� � �������������� ������� ������-��������������� ������ &quot;������&quot; � �������</h1>
</div></div>
<div class="main">
    <form id="feedback" name="feedback" method="post" action="feedback-status.php">
    <div class="row"><div class="col-8">
	    <h3>������������! </h3>
        <p>���������, ����������, ���� �����. �� �������� � ����, ��� ������ ������� ���� ������.</p>
	</div></div> <!-- col-8, row -->
    <div class="row">
        <div class="col-4">
        	<p>
	        <b>E-Mail: </b><br>
            <input name="email" type="email" id="email" class="text-input" value="<?php echo $email?>">
            </p>
        </div> <!-- col-4 -->
        <div class="col-4">
        	<p>
	        <b>��� ����� ������: </b><br>
            <input name="name" type="text" id="name" class="text-input" value="<?php echo $name?>">
            </p>
        </div> <!-- col-4 -->
    </div> <!-- row -->
    <div class="row"><div class="col-8">
    <p><b>���������:</b><br>
    <textarea name="message" rows="4" class="text-input" id="message"></textarea>
    </p>
    <p><input type="submit" value="���������!"></p>
    </div></div> <!-- row --><!-- col-8-->
    </form>
</div><!-- main -->
</body>
</html>
