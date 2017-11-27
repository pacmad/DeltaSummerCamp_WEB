/*
 *  Установка пользователю UID флага 'ststus' в значение s
 */
function setStatus(UID, s) {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "mycabinet.php?id=" + UID + "&SetStatus=" + s, true);
	xhttp.send();
}