<!doctype html>
<html>
<head>
<meta charset="windows-1251">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>�����������</title>
<link href="CSS/common.css" rel="stylesheet" type="text/css">
<link href="CSS/form.css" rel="stylesheet" type="text/css">
<script src="JS/form_functions.js"></script>
<?php require_once 'phplib/dbConnect.php' ?>
</head>

<body>
<?php 
if (!isset($_POST["ALL_DONE"])) {
?>
<div class="row"><div class="col-12">
<h1>����������� � ������ ������-�������������� ������ "������"</h1>
<p class="explanation">����, ���������� ��������� <span class="required">*</span> �����������.</p>
</div></div> <!-- col12, row -->

<form id="form" name="form" method="post" onsubmit="return checkForm()">
  <div class="row">
  <div class="col-4">
  <p>E-Mail <span class="required">*</span><br>
    <input class="text-input" name="email" type="email" required id="email" placeholder="ivan@ivanov.earth" title="����� ����������� �����">
    <br>
  <span class="explanation">��� ����� ����������� �����. �� ���� ����� ������������� �����������.</span></p>
  </div> <!-- col- -->
  <div class="col-4">
  <p> ���������� ������� <span class="required">*</span><br>
    <input name="tel" type="tel" required class="text-input" id="tel" title="������� ��� �����">
  </p>
  </div> <!-- col- -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>������� <span class="required">*</span><br>
    <input class="text-input" name="surname" type="text" required id="surname" placeholder="������" title="������� ������">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>��� <span class="required">*</span><br>
    <input class="text-input" name="name" type="text" required id="name" placeholder="����" title="��� ������">
  </p>
  </div> <!-- col-4 -->
  <div class="col-3">
  <p>��������<br>
    <input class="text-input" name="middlename" type="text" id="middlename" placeholder="��������" title="�������� ������">
  </p>
  </div> <!-- col-4 -->
  </div> <!-- row -->
  <div class="row">
  <div class="col-3">
  <p>���� �������� <span class="required">*</span><br>
    <input class="text-input" name="birthday" type="text" required id="birthday" placeholder="dd/mm/yyyy"
           title="���� ��������" onChange="checkDate(this);">
    <span class="hidden_error" id="date_error">�������� ������ ����!<br>����������� "����/�����/���"<br>��������: 27/03/1999</span>
    <span class="explanation"><span class="age" id="age"></span></span>
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <div id="gender">
  <p>��� <span class="required">*</span><br>
    <label>
      <input name="gender" type="radio" required id="female" value="f">�������</label>&nbsp;&nbsp;
    <label>
      <input name="gender" type="radio" required id="male" value="m">�������</label>
  </p>
  </div> <!-- gender -->
  </div> <!-- col-3 -->
  </div> <!-- raw -->
  <div class="row">
  <div class="col-3">
  <p>�����<br>
    <input class="text-input" name="class" type="number" id="class" title="�����">
    <br>
  <span class="explanation">�� ������� 11-������ ������� ��� ��� �������� ������ � �����.</span> </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>����� <span class="required">*</span><br>
    <input class="text-input" name="school" type="text" required id="school" title="�����">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>����� <span class="required">*</span><br>
    <input class="text-input" name="city" type="text" required id="sity" title="����� ����������">
  </p>
  </div> <!-- col-3 -->
  <div class="col-3">
  <p>������ <span class="required">*</span><br>
    <input class="text-input" name="country" type="text" required id="country" title="������ ����������">
  </p>
  </div> <!-- col-3 -->
  </div> <!-- row -->
  <div class="row"> <div class="col-6">
  <p>����� <span class="required">*</span><br>
    <input class="text-input" name="langs" type="text" required id="langs" title="������ ������� ������� ������" value="�������" size="60">
    <br>
  <span class="explanation">�����, �������� ������� ������. ����������� ����� �������.</span></p>
  <p>�������������� ����������<br>
    <textarea class="text-input" name="notes" cols="60" rows="4" id="notes" title="���������"></textarea>
    <br>
  <span class="explanation">��, ��� �� ��� ������ �� ��� ��������. ����� �� ������ ������ ���� ������� ������� �� delta@mathbaby.ru</span></p>
  <div class="indented" id="agree"><p>�� ��������� ��.64 �.1 ��������� ������� �� ��� ���� �������� ��   ��������� ��������� ���� ������ ����� ������� ��� ������� � ��������   �����, ��������� ���������� � �����, �������, �����������. �������������   ������ ��� ������ ����� �� �������������.<br>
  ��������<span class="required" title="����������� ��� ����������.">*</span>: <input name="agree" type="checkbox" required id="agree" title="��������"></p></div>
  <p>
    <input name="submit" type="submit" id="submit" value="����������������">
  </p>
  </div></div> <!-- col-6 row -->
  <p><input name="ALL_DONE" type="hidden" id="ALL_DONE" value="Ok"><br>
  </p>
</form>
<?php
} elseif ($_POST["ALL_DONE"] !== 'Ok') {
    echo '���-�� ����� �� ���...';
} else { ?>

<?php
    $db = new dbConnect();
    $db->putRegData();
}
?>
</body>
</html>