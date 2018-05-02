<!doctype html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>������ ��������������</title>
    <link href="CSS/common.css" rel="stylesheet" type="text/css">
    <link href="CSS/admin.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- jQuery  -->
    <script src="https://code.jquery.com/jquery-3.3.0.min.js"
            integrity="sha256-RTQy8VOmNlT6b2PIRur37p6JEBZUE7o8wPgMvu18MC4="
            crossorigin="anonymous"></script>
    <!-- jQuery Form -->
    <script src="JS/lib/jquery.form.min.js"></script>
    <script src="JS/adm_functions.js"></script>
    <script src="JS/adm_main.js"></script>

    <?php
    $db = new dbConnect();
    $row = $db->admGet($_SESSION['UID']);
    $name = $row['Name'];
    $sname = $row['Surname'];
    if (!isset($_SESSION['SortBy'])) {
        $_SESSION['SortBy'] = "Name"; // ���������� �� ��������� - �� ��������, ������������ �� ���������� �� �����
    }
    if (!isset($_SESSION['View'])) {
        $_SESSION['View'] = "List"; // ��� ������: List, Details, Edit
    }
    ?>
</head>

<body>
<div class="title">
    <h1>������, <?php echo "$name $sname!"?></h1>
</div>
<div class="main">
    <div id="list_data"></div>
</div>
</body>
</html>