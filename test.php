<!DOCTYPE html>
<html>
<head>
<meta charset="windows-1251">
<style>
#myDate {
	border: thin solid gray;
}
</style>
<script>
    document.addEventListener("DOMContentLoaded", function(e) {
        bdayInput = document.getElementById("myDate");
//        bdayInput.onkeydown = kdHandler;
        bdayInput.onfocus = focus;
//        bdayInput.onblur = blur;
    });


    var focus = function(elnt) {
        elnt.style.border = "thin solid blue";
        elnt.style.backgroundColor = "#CFC";

        if (elnt.value == "") {
            elnt.value = "__/__/____";
    }

    function Click() {
        document.getElementById('demo').innerHTML = document.getElementById('myDate').value;
    }
</script>

</head>
<body>
Date:
<input type="tel" id="myDate" name="bday"/>
<br><br>
<button onclick='Click()'>Try it</button>

<p id="demo"></p>

</body>
</html>
