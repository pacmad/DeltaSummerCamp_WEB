<!DOCTYPE html>
<html>
<head>
<meta charset="windows-1251">
<style>
#myDate {
	border: thin solid gray;
}
</style>
<script src="JS/test.js"></script>
<script>
function Click() {
    document.getElementById('demo').innerHTML = document.getElementById('myDate').value;
}
</script>

</head>
<body>
Date:
<input type="tel" id="myDate" name="bday" onchange='checkDate(this)'/>
<br>
<button onclick='Click()'>Try it</button>

<p id="demo"></p>

</body>
</html>
