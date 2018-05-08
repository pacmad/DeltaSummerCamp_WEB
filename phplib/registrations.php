<?php session_start(); ?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<style type="text/css">
body {
    background: #FFFFFF;
    color: #000000;
    font-family: Arial;
    font-size: 12pt;
}
A:link { color: #FF0000 }
A:visited { color: #800080 }
A:active { color: #0000FF }
.ThRows {
    background-color: #CEC6B5;
    color: #000000;
    font-weight: bold; text-align: center;
    font-family: Arial;
    font-size: 12pt;
}
.TrRows {
    background-color: #DEE7DE;
    color: #000000;
    font-family: Arial;
    font-size: 12pt;
}
.TrOdd  {
    background-color: #FFFBF0;
    color: #000000;
    font-family: Arial;
    font-size: 12pt;
}
.TrBC { background-color: #000000 }
</style>
</head>
<body>
<?php
  if (!login()) exit;
?>
<div style="float: right"><a href="registrations.php?a=logout">[ Logout ]</a></div>
<br>
<table width="100%"><tr><td class="ThRows">
Registrations database
</td></tr></table>

<?php
  $conn = connect();
  $showrecs = 100;
  $pagerange = 10;
  $a = @$_GET["a"];
  $recid = @$_GET["recid"];
  $page = @$_GET["page"];
  if (!isset($page)) $page = 1;

  $sql = @$_POST["sql"];

  switch ($sql) {
    case "insert":
      sql_insert();
      break;
    case "update":
      sql_update();
      break;
    case "delete":
      sql_delete();
      break;
  }

  switch ($a) {
    case "add":
      addrec();
      break;
    case "view":
      viewrec($recid);
      break;
    case "edit":
      editrec($recid);
      break;
    case "del":
      deleterec($recid);
      break;
    default:
      select();
      break;
  }
  $conn = null;
?>
<table width="100%"><tr><td class="ThRows">
Generated from SQL Manager fo MySQL
</td></tr></table>

</body>
</html>
<?php
function connect()
{
  $c = new PDO("mysql:host=localhost;port=3306;dbname=delta;charset=cp1251", $_SESSION['my_user'], $_SESSION['my_pass']);
  $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $c;
}
?>
<?php
function sql_getrecordcount()
{
  global $conn;
  return $conn->query("select count(*) from `registrations`")->fetchColumn();
}
?>
<?php
function sql_select($offset, $limit)
{
  global $conn;
  return $conn->query("SELECT * FROM `registrations` LIMIT $offset, $limit");
}

function select(){
  global $showrecs;
  global $pagerange;
  global $page;
  global $conn;
  $count = sql_getrecordcount();
  if ($count % $showrecs != 0) {
    $pagecount = intval($count / $showrecs) + 1;
  }
  else {
    $pagecount = intval($count / $showrecs);
  }
  $startrec = $showrecs * ($page - 1);
  $reccount = min($showrecs * $page, $count) - $startrec;
  if ($reccount > 0)
    $res = sql_select($startrec, $reccount);
?>
<?php showpagenav($page, $pagecount); ?>
<table width="100%" border="0" cellpadding="4" cellspacing="1">
  <tr>
    <td class="ThRows">&nbsp;</td>
    <td class="ThRows">&nbsp;</td>
    <td class="ThRows">&nbsp;</td>
    <td class="ThRows">UniqueId</td>
    <td class="ThRows">RegistrationTime</td>
    <td class="ThRows">UserIP</td>
    <td class="ThRows">Email</td>
    <td class="ThRows">Surname</td>
    <td class="ThRows">Name</td>
    <td class="ThRows">MiddleName</td>
    <td class="ThRows">Gender</td>
    <td class="ThRows">Birthday</td>
    <td class="ThRows">Class</td>
    <td class="ThRows">School</td>
    <td class="ThRows">City</td>
    <td class="ThRows">Country</td>
    <td class="ThRows">Languages</td>
    <td class="ThRows">Tel</td>
    <td class="ThRows">OwnTel</td>
    <td class="ThRows">Notes</td>
    <td class="ThRows">AppStatus</td>
    <td class="ThRows">DateOfWorkSent</td>
    <td class="ThRows">CertLang</td>
    <td class="ThRows">CertName</td>
    <td class="ThRows">ComingWith</td>
    <td class="ThRows">ComingDate</td>
    <td class="ThRows">ComingTime</td>
    <td class="ThRows">ComingFlight</td>
    <td class="ThRows">ComingPlace</td>
    <td class="ThRows">LeavingWith</td>
    <td class="ThRows">LeavingDate</td>
    <td class="ThRows">LeavingTime</td>
    <td class="ThRows">LeavingFlight</td>
    <td class="ThRows">LeavingPlace</td>
    <td class="ThRows">Health</td>
    <td class="ThRows">Insurance</td>
    <td class="ThRows">NotesText</td>
    <td class="ThRows">Visa</td>
    <td class="ThRows">Notebook</td>
    <td class="ThRows">Shirt</td>
  </tr>
<?php
if($reccount > 0) {
  for ($i = $startrec; $i < $startrec+$reccount; $i++)
  {
    $row = $res->fetch();
    $s = "TrOdd";
    if ($i % 2 == 0) {
      $s = "TrRows";
    }
?>
  <tr>
    <td class="<?php echo $s?>" width = "16"><a href="registrations.php?a=view&recid=<?php echo $i ?>" ><img src="ems_php_images\phpview.jpg" title="View record" border="0"></a></td>
    <td class="<?php echo $s?>" width = "16"><a href="registrations.php?a=edit&recid=<?php echo $i ?>" ><img src="ems_php_images\phpedit.jpg" title="Edit record" border="0"></a></td>
    <td class="<?php echo $s?>" width = "16"><a href="registrations.php?a=del&recid=<?php echo $i ?>" onclick="return confirm('Do you really want to delete row?')"><img src="ems_php_images\phpdrop.jpg" title="Delete record" border="0"></a></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['UniqueId'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['RegistrationTime'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['UserIP'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Email'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Surname'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Name'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['MiddleName'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Gender'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Birthday'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Class'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['School'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['City'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Country'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Languages'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Tel'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['OwnTel'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Notes'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['AppStatus'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['DateOfWorkSent'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['CertLang'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['CertName'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['ComingWith'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['ComingDate'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['ComingTime'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['ComingFlight'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['ComingPlace'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['LeavingWith'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['LeavingDate'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['LeavingTime'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['LeavingFlight'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['LeavingPlace'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Health'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Insurance'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['NotesText'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Visa'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Notebook'])?></td>
    <td class="<?php echo $s?>"><?php echo htmlspecialchars($row['Shirt'])?></td>
  </tr>
<?php }$res=null;}?>
</table>
<?php showpagenav($page, $pagecount); ?>
<?php }?>
<?php function showpagenav($page, $pagecount){ ?>
<table border="0" width="100%"
<tr>
<td><a href="registrations.php?a=add">Add record<br></a>
<?php if ($page > 1) { ?>
<a href="registrations.php?page=<?php echo $page - 1 ?>">&lt;&lt;&nbsp;Prev</a>&nbsp;
<?php } ?>
<?php
global $pagerange;
if ($pagecount > 1) {
  if ($pagecount % $pagerange != 0)
    $rangecount = intval($pagecount / $pagerange) + 1;
  else
    $rangecount = intval($pagecount / $pagerange);
  for ($i = 1; $i < $rangecount + 1; $i++) {
    $startpage = (($i - 1) * $pagerange) + 1;
    $count = min($i * $pagerange, $pagecount);
    if ((($page >= $startpage) && ($page <= ($i * $pagerange)))) {
      for ($j = $startpage; $j < $count + 1; $j++) {
        if ($j == $page) {
?>
<b><?php echo $j ?></b>
<?php } else { ?>
<a href="registrations.php?page=<?php echo $j ?>"><?php echo $j ?></a>
<?php } } } else { ?>
<a href="registrations.php?page=<?php echo $startpage ?>"><?php echo $startpage ."..." .$count ?></a>
<?php } } } ?>
<?php if ($page < $pagecount) { ?>
&nbsp;<a href="registrations.php?page=<?php echo $page + 1 ?>">Next&nbsp;&gt;&gt;</a>&nbsp;</td>
<?php } ?>
</tr>
</table>
<?php } ?>

<?php function showrecnav($a, $recid, $count)
{
?>
<table border="0" cellspacing="1" cellpadding="4">
<tr>
<td><a href="registrations.php">Index Page</a></td>
<?php if ($recid > 0) { ?>
<td><a href="registrations.php?a=<?php echo $a ?>&recid=<?php echo $recid - 1 ?>">Prior Record</a></td>
<?php } if ($recid < $count - 1) { ?>
<td><a href="registrations.php?a=<?php echo $a ?>&recid=<?php echo $recid + 1 ?>">Next Record</a></td>
<?php } ?>
</tr>
</table>
<hr size="1" noshade>
<?php } ?>

<?php function showrow($row, $recid)
  {
?> 
<table border="0" cellspacing="1" cellpadding="5" width="50%">
<tr>
<td class="ThRows"><?php echo htmlspecialchars("UniqueId")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["UniqueId"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("RegistrationTime")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["RegistrationTime"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("UserIP")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["UserIP"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Email")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["Email"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Surname")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Surname"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Name")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["Name"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("MiddleName")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["MiddleName"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Gender")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["Gender"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Birthday")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Birthday"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Class")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["Class"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("School")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["School"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("City")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["City"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Country")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Country"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Languages")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["Languages"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Tel")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Tel"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("OwnTel")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["OwnTel"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Notes")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Notes"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("AppStatus")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["AppStatus"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("DateOfWorkSent")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["DateOfWorkSent"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("CertLang")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["CertLang"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("CertName")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["CertName"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingWith")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["ComingWith"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingDate")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["ComingDate"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingTime")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["ComingTime"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingFlight")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["ComingFlight"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingPlace")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["ComingPlace"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingWith")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["LeavingWith"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingDate")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["LeavingDate"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingTime")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["LeavingTime"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingFlight")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["LeavingFlight"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingPlace")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["LeavingPlace"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Health")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["Health"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Insurance")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Insurance"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("NotesText")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["NotesText"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Visa")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Visa"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Notebook")."&nbsp;" ?></td>
<td class="TrOdd"><?php echo htmlspecialchars($row["Notebook"]) ?></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Shirt")."&nbsp;" ?></td>
<td class="TrRows"><?php echo htmlspecialchars($row["Shirt"]) ?></td>
</tr>
</table>
<?php } ?>

<?php
function select_fk_keys($tablename, $fieldname){
  global $conn;
  return $conn->query("SELECT $fieldname FROM $tablename");
}
?>
<?php function showroweditor($row, $iseditmode)
  {
?>
<table border="0" cellspacing="1" cellpadding="5"width="50%">
<tr>
<td class="ThRows"><?php echo htmlspecialchars("UniqueId")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="UniqueId" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["UniqueId"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("RegistrationTime")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="RegistrationTime" value="<?php echo str_replace('"', '&quot;', trim($row["RegistrationTime"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("UserIP")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="UserIP" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["UserIP"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Email")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Email" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Email"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Surname")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Surname" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Surname"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Name")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Name" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Name"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("MiddleName")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="MiddleName" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["MiddleName"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Gender")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Gender" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Gender"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Birthday")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="Birthday" value="<?php echo str_replace('"', '&quot;', trim($row["Birthday"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Class")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="Class" value="<?php echo str_replace('"', '&quot;', trim($row["Class"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("School")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="School" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["School"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("City")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="City" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["City"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Country")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Country" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Country"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Languages")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Languages" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Languages"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Tel")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Tel" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Tel"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("OwnTel")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="OwnTel" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["OwnTel"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Notes")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Notes" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Notes"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("AppStatus")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="AppStatus" value="<?php echo str_replace('"', '&quot;', trim($row["AppStatus"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("DateOfWorkSent")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="DateOfWorkSent" value="<?php echo str_replace('"', '&quot;', trim($row["DateOfWorkSent"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("CertLang")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="CertLang" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["CertLang"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("CertName")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="CertName" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["CertName"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingWith")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="ComingWith" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["ComingWith"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingDate")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="ComingDate" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["ComingDate"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingTime")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="ComingTime" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["ComingTime"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingFlight")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="ComingFlight" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["ComingFlight"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("ComingPlace")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="ComingPlace" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["ComingPlace"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingWith")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="LeavingWith" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["LeavingWith"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingDate")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="LeavingDate" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["LeavingDate"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingTime")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="LeavingTime" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["LeavingTime"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingFlight")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="LeavingFlight" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["LeavingFlight"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("LeavingPlace")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="LeavingPlace" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["LeavingPlace"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Health")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="Health" value="<?php echo str_replace('"', '&quot;', trim($row["Health"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Insurance")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="Insurance" value="<?php echo str_replace('"', '&quot;', trim($row["Insurance"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("NotesText")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="NotesText" value="<?php echo str_replace('"', '&quot;', trim($row["NotesText"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Visa")."&nbsp;" ?></td>
<td class="TrRows">
<input type="text" name="Visa" value="<?php echo str_replace('"', '&quot;', trim($row["Visa"])) ?>"></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Notebook")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Notebook" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Notebook"]))?></textarea></td>
</tr>
<tr>
<td class="ThRows"><?php echo htmlspecialchars("Shirt")."&nbsp;" ?></td>
<td class="TrRows">
<textarea name="Shirt" cols="36" rows="4"><?php echo str_replace('"', '&quot;', trim($row["Shirt"]))?></textarea></td>
</tr>
</tr>
</table>
<?php } ?>

<?php function addrec()
{
?>
<table border="0" cellspacing="1" cellpadding="4">
<tr>
<td><a href="registrations.php">Index Page</a></td>
</tr>
</table>
<hr size="1" noshade>
<form enctype="multipart/form-data" action="registrations.php" method="post">
<p><input type="hidden" name="sql" value="insert"></p>
<?php
$row = array(
  "UniqueId" => "",
  "RegistrationTime" => "",
  "UserIP" => "",
  "Email" => "",
  "Surname" => "",
  "Name" => "",
  "MiddleName" => "",
  "Gender" => "",
  "Birthday" => "",
  "Class" => "",
  "School" => "",
  "City" => "",
  "Country" => "",
  "Languages" => "",
  "Tel" => "",
  "OwnTel" => "",
  "Notes" => "",
  "AppStatus" => "",
  "DateOfWorkSent" => "",
  "CertLang" => "",
  "CertName" => "",
  "ComingWith" => "",
  "ComingDate" => "",
  "ComingTime" => "",
  "ComingFlight" => "",
  "ComingPlace" => "",
  "LeavingWith" => "",
  "LeavingDate" => "",
  "LeavingTime" => "",
  "LeavingFlight" => "",
  "LeavingPlace" => "",
  "Health" => "",
  "Insurance" => "",
  "NotesText" => "",
  "Visa" => "",
  "Notebook" => "",
  "Shirt" => "");
showroweditor($row, false);
?>
<p><input type="submit" name="action" value="Post"></p>
</form>
<?php } ?>

<?php function viewrec($recid)
{
  $row = sql_select($recid, 1)->fetch();
  $count = sql_getrecordcount();
  showrecnav("view", $recid, $count);
?>
<br>
<?php showrow($row, $recid) ?>
<br>
<hr size="1" noshade>
<table border="0" cellspacing="1" cellpadding="4">
<tr>
<td><a href="registrations.php?a=add">Add record</a></td>
<td><a href="registrations.php?a=edit&recid=<?php echo $recid ?>">Edit record</a></td>
<td><a href="registrations.php?a=del&recid=<?php echo $recid ?>">Delete record</a></td>
</tr>
</table>
<?php
  $res = null;
} ?>

<?php function editrec($recid)
{
  $row = sql_select($recid, 1)->fetch();
  $count = sql_getrecordcount();
  showrecnav("edit", $recid, $count);
?>
<br>
<form enctype="multipart/form-data" action="registrations.php" method="post">
<input type="hidden" name="sql" value="update">
<input type="hidden" name="xUniqueId" value="<?php echo $row["UniqueId"] ?>">
<?php showroweditor($row, true); ?>
<p><input type="submit" name="action" value="Post"></p>
</form>
<?php
  $res = null;
} ?>
<?php function deleterec($recid)
{
  $row = sql_select($recid, 1)->fetch();
  $count = sql_getrecordcount();
?>
<br>
<form name="delete_form" action="registrations.php" method="post">
<input type="hidden" name="sql" value="delete">
<input type="hidden" name="xUniqueId" value="<?php echo $row["UniqueId"] ?>">
<script type="text/javascript">
  document.forms["delete_form"].submit();
</script>
</form>
<?php
  $res = null;
} ?>
<?php
function sqlvalue($val, $quote)
{
  if ($quote)
    $tmp = sqlstr($val);
  else
    $tmp = $val;
  if ($tmp == "")
    $tmp = "NULL";
  elseif ($quote)
    $tmp = "'".$tmp."'";
  return $tmp;
}

function sqlstr($val)
{
  return str_replace("'", "''", $val);
}

function primarykeycondition()
{
  $pk = "";
  $pk .= "(`UniqueId`";
  if (@$_POST["xUniqueId"] == "") {
    $pk .= " IS NULL";
  } else {
  $pk .= " = " .sqlvalue(@$_POST["xUniqueId"], true);
  };
  $pk .= ")";
  return $pk;
}

function sql_insert()
{
  global $conn;
  $conn->query("insert into `registrations` (`UniqueId`, `RegistrationTime`, `UserIP`, `Email`, `Surname`, `Name`, `MiddleName`, `Gender`, `Birthday`, `Class`, `School`, `City`, `Country`, `Languages`, `Tel`, `OwnTel`, `Notes`, `AppStatus`, `DateOfWorkSent`, `CertLang`, `CertName`, `ComingWith`, `ComingDate`, `ComingTime`, `ComingFlight`, `ComingPlace`, `LeavingWith`, `LeavingDate`, `LeavingTime`, `LeavingFlight`, `LeavingPlace`, `Health`, `Insurance`, `NotesText`, `Visa`, `Notebook`, `Shirt`) values (".sqlvalue(@$_POST["UniqueId"], true).", ".sqlvalue(@$_POST["RegistrationTime"], true).", ".sqlvalue(@$_POST["UserIP"], true).", ".sqlvalue(@$_POST["Email"], true).", ".sqlvalue(@$_POST["Surname"], true).", ".sqlvalue(@$_POST["Name"], true).", ".sqlvalue(@$_POST["MiddleName"], true).", ".sqlvalue(@$_POST["Gender"], true).", ".sqlvalue(@$_POST["Birthday"], true).", ".sqlvalue(@$_POST["Class"], false).", ".sqlvalue(@$_POST["School"], true).", ".sqlvalue(@$_POST["City"], true).", ".sqlvalue(@$_POST["Country"], true).", ".sqlvalue(@$_POST["Languages"], true).", ".sqlvalue(@$_POST["Tel"], true).", ".sqlvalue(@$_POST["OwnTel"], true).", ".sqlvalue(@$_POST["Notes"], true).", ".sqlvalue(@$_POST["AppStatus"], false).", ".sqlvalue(@$_POST["DateOfWorkSent"], true).", ".sqlvalue(@$_POST["CertLang"], true).", ".sqlvalue(@$_POST["CertName"], true).", ".sqlvalue(@$_POST["ComingWith"], true).", ".sqlvalue(@$_POST["ComingDate"], true).", ".sqlvalue(@$_POST["ComingTime"], true).", ".sqlvalue(@$_POST["ComingFlight"], true).", ".sqlvalue(@$_POST["ComingPlace"], true).", ".sqlvalue(@$_POST["LeavingWith"], true).", ".sqlvalue(@$_POST["LeavingDate"], true).", ".sqlvalue(@$_POST["LeavingTime"], true).", ".sqlvalue(@$_POST["LeavingFlight"], true).", ".sqlvalue(@$_POST["LeavingPlace"], true).", ".sqlvalue(@$_POST["Health"], true).", ".sqlvalue(@$_POST["Insurance"], true).", ".sqlvalue(@$_POST["NotesText"], true).", ".sqlvalue(@$_POST["Visa"], false).", ".sqlvalue(@$_POST["Notebook"], true).", ".sqlvalue(@$_POST["Shirt"], true).")");
}
?>
<?php
function sql_update()
{
  global $conn;
  $conn->query("update `registrations` set `UniqueId`=".sqlvalue(@$_POST["UniqueId"], true).", `RegistrationTime`=".sqlvalue(@$_POST["RegistrationTime"], true).", `UserIP`=".sqlvalue(@$_POST["UserIP"], true).", `Email`=".sqlvalue(@$_POST["Email"], true).", `Surname`=".sqlvalue(@$_POST["Surname"], true).", `Name`=".sqlvalue(@$_POST["Name"], true).", `MiddleName`=".sqlvalue(@$_POST["MiddleName"], true).", `Gender`=".sqlvalue(@$_POST["Gender"], true).", `Birthday`=".sqlvalue(@$_POST["Birthday"], true).", `Class`=".sqlvalue(@$_POST["Class"], false).", `School`=".sqlvalue(@$_POST["School"], true).", `City`=".sqlvalue(@$_POST["City"], true).", `Country`=".sqlvalue(@$_POST["Country"], true).", `Languages`=".sqlvalue(@$_POST["Languages"], true).", `Tel`=".sqlvalue(@$_POST["Tel"], true).", `OwnTel`=".sqlvalue(@$_POST["OwnTel"], true).", `Notes`=".sqlvalue(@$_POST["Notes"], true).", `AppStatus`=".sqlvalue(@$_POST["AppStatus"], false).", `DateOfWorkSent`=".sqlvalue(@$_POST["DateOfWorkSent"], true).", `CertLang`=".sqlvalue(@$_POST["CertLang"], true).", `CertName`=".sqlvalue(@$_POST["CertName"], true).", `ComingWith`=".sqlvalue(@$_POST["ComingWith"], true).", `ComingDate`=".sqlvalue(@$_POST["ComingDate"], true).", `ComingTime`=".sqlvalue(@$_POST["ComingTime"], true).", `ComingFlight`=".sqlvalue(@$_POST["ComingFlight"], true).", `ComingPlace`=".sqlvalue(@$_POST["ComingPlace"], true).", `LeavingWith`=".sqlvalue(@$_POST["LeavingWith"], true).", `LeavingDate`=".sqlvalue(@$_POST["LeavingDate"], true).", `LeavingTime`=".sqlvalue(@$_POST["LeavingTime"], true).", `LeavingFlight`=".sqlvalue(@$_POST["LeavingFlight"], true).", `LeavingPlace`=".sqlvalue(@$_POST["LeavingPlace"], true).", `Health`=".sqlvalue(@$_POST["Health"], true).", `Insurance`=".sqlvalue(@$_POST["Insurance"], true).", `NotesText`=".sqlvalue(@$_POST["NotesText"], true).", `Visa`=".sqlvalue(@$_POST["Visa"], false).", `Notebook`=".sqlvalue(@$_POST["Notebook"], true).", `Shirt`=".sqlvalue(@$_POST["Shirt"], true)." where " .primarykeycondition());
}
?>
<?php
function sql_delete()
{
  global $conn;
  $sql = "delete from `registrations` where " .primarykeycondition();
  $conn->query($sql);
}
?>
<?php
function login()
{

  global $_GET;
  if (isset($_GET["a"]) && ($_GET["a"] == 'logout')) $_SESSION["logged_in"] = false;
  if (!isset($_SESSION["logged_in"])) $_SESSION["logged_in"] = false;
  if (!$_SESSION["logged_in"]) {
    $login = "";
    $password = "";
    if (isset($_POST["login"])) $login = @$_POST["login"];
    if (isset($_POST["password"])) $password = @$_POST["password"];

    if ($login != "") {
      $_SESSION["logged_in"] = true;
      $_SESSION['my_user'] = $login;
      $_SESSION['my_pass'] = $password;
    }
  }
if (isset($_SESSION["logged_in"]) && (!$_SESSION["logged_in"])) { ?>
<form action="registrations.php" method="post">
<table class="bd" border="0" cellspacing="1" cellpadding="4">
<tr>
<td>Login</td>
<td><input type="text" name="login" value="<?php echo $login ?>"></td>
</tr>
<tr>
<td>Password</td>
<td><input type="password" name="password" value="<?php echo $password ?>"></td>
</tr>
<tr>
<td><input type="submit" name="action" value="Login"></td>
</tr>
</table>
</form>
<?php
  }
  if (!isset($_SESSION["logged_in"])) $_SESSION["logged_in"] = false;
  return $_SESSION["logged_in"];
}
?>
