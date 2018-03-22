/*
 *  Установка пользователю UID флага 'ststus' в значение s
 */
function setDateWorkSent(UID) {
	var xhttp = new XMLHttpRequest();
	xhttp.open("GET", "mycabinet.php?id=" + UID + "&SetStatus=2", true);
	xhttp.send();
}

$(document).ready(function () {

    $("#fileuploader").uploadFile({ //Подключение библиотеки jquery.uploadfile.js

        fileName: "file",
        sequential: true,
        sequentialCount:4,
        dragDropStr: "",
        uploadButtonClass: "uploader"
    });

    $("#imguploader").uploadFile({ // Подключаем библиотеку jquery.uploadfile.js для загрузки фото
        fileName: "file",
        multiple: false,
        maxFileCount: 1,
        dragDropStr: "",
        maxFileSize:10*1024*1024,
        onSuccess:function(files,data,xhr,pd){
            $(".ajax-file-upload-statusbar").hide();
            $("#photo").attr("src", $("#photo").attr("src") + "?" + new Date().getTime());
        }
    });

    /*
        Обработка подсветки элемента для передачи файлов при отсылке решений олимпиады
        .ajax-upload-dragdrop определяется в библиотеке jquery.uploadfile.js
     */

	var dropZone = $('.ajax-upload-dragdrop');

	dropZone[0].ondragover = function () {
        dropZone.addClass('hover');
        return false;
    };

	dropZone[0].ondragleave = function () {
        dropZone.removeClass('hover');
        return false;
    };

	dropZone[0].ondrop = function () {
		event.preventDefault();
        dropZone.removeClass('hover');
        return false;
    };

	// Функции заполнения анкеты
	$("#btnCertName").hide();
	$("#coming_submit").hide();
	$("#leaving_submit").hide();

	var options = {
//	    beforeSubmit: function (arr, $form, options) {
//            arr[2].value = encodeURIComponent($("#certName").val());
//        },
	    success: function (data, status) {
            if (status == "success") {
                $("#btnCertName").hide();
            }
        },
        contentType: "application/x-www-form-urlencoded;charset=windows-1251"
    };
	$("#certNameForm").ajaxForm(options);

    $("#certLang").on('change', function () {
        if ($("#certName").val() !== "") {
            $("#btnCertName").show();
        }
    });

	$("#certName").on('input', function () {
        if ($("#certName").val() !== "") {
            $("#btnCertName").show();
        } else {
            $("#btnCertName").hide();
        }
    });

    $("#coming_with").on('change', function () {
        if (this.value === "DimaAnya") {
            $("#coming_date").val("15/07/2018").attr('disabled', 'disabled');
            $("#coming_time").val("13:10").attr('disabled', 'disabled');
            $("#coming_place").append(new Option('Аэропорт "Домодедово"', 'DME', false, true)).attr('disabled', 'disabled');
            $("#coming_flightno").val("LH2529").attr('disabled', 'disabled');
        } else if (this.value === "Dubeniuk") {
            $("#coming_date").val("16/07/2018").attr('disabled', 'disabled');
            $("#coming_time").val("7:30").attr('disabled', 'disabled');
            $("#coming_place").append(new Option('Аэропорт "Внуково"', 'VKO', false, true)).attr('disabled', 'disabled');
            $("#coming_flightno").val("UT799").attr('disabled', 'disabled');
        } else {
            $("#coming_date").val("16/07/2018").removeAttr('disabled');
            $("#coming_time").val("").removeAttr('disabled');
            $("#coming_place").removeAttr('disabled');
            $("#coming_flightno").val("").removeAttr('disabled');
            $("#coming_place option[value='DME']").remove();
            $("#coming_place option[value='VKO']").remove();
        }
    });
    
    $("#leaving_with").on('change', function () {
        if (this.value === "DimaAnya") {
            $("#leaving_date").val("30/07/2018").attr('disabled', 'disabled');
            $("#leaving_time").val("23:55").attr('disabled', 'disabled');
            $("#leaving_place").append(new Option('Аэропорт "Домодедово"', 'DME', false, true)).attr('disabled', 'disabled');
            $("#leaving_flightno").val("LH2530").attr('disabled', 'disabled');
        } else if (this.value === "Dubeniuk") {
            $("#leaving_date").val("30/07/2018").attr('disabled', 'disabled');
            $("#leaving_time").val("17:40").attr('disabled', 'disabled');
            $("#leaving_place").append(new Option('Аэропорт "Внуково"', 'VKO', false, true)).attr('disabled', 'disabled');
            $("#leaving_flightno").val("UT800").attr('disabled', 'disabled');
        } else {
            $("#leaving_date").val("30/07/2018").removeAttr('disabled');
            $("#leaving_time").val("").removeAttr('disabled');
            $("#leaving_place").removeAttr('disabled');
            $("#leaving_flightno").val("").removeAttr('disabled');
            $("#leaving_place option[value='DME']").remove();
            $("#leaving_place option[value='VKO']").remove();
        }
    })
});