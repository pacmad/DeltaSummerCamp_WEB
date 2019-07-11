<?php
if (isset($_FILES['myFile'])) {
    move_uploaded_file($_FILES['myFile']['tmp_name'], "uploads/" . $_FILES['myFile']['name']);
    exit();
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <style>
        #fileSelect {
            display: block;
            height: 150px;
            width: 270px;
            background-color: lightcyan;
            border: darkblue 1px solid;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            line-height: 150px;
        }

        #fileSelect:hover {
            cursor: pointer;
            color: darkred;
            border-color: blue;
            background-color: greenyellow;
        }

        #preview {
            padding: 5px;
            border: darkgray 1px solid;
        }

        .obj {
            max-width: 100px;
            max-height: 100px;
            margin: 5px;
        }

        #progress {
            width: 20em;
            border: darkred 1px solid;
            visibility: hidden;
        }

        #progress-bar {
            background-color: red;
            width: 0;
            height: 1em;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
<input id="fileElem" multiple type="file" accept="*" style="display: none">
<div id="fileSelect">Select some files</div>
<br>
<div id="preview"></div>
<div id="fileList">
    <p>No files selected!</p>
</div>
<div id="progress"><div id="progress-bar"></div></div>
</body>
<script>
    let fileElem = document.getElementById('fileElem');
    let fileSelect = document.getElementById('fileSelect');
    let fileList = document.getElementById("fileList");
    let preview = document.getElementById("preview");
    window.URL = window.URL || window.webkitURL;

    function handleFiles(files) {
        if (!files.length) {
            fileList.innerHTML = "<p>No files selected!</p>";
        } else {
            fileList.innerHTML = "";
            let list = document.createElement("ul");
            fileList.appendChild(list);

            for (let i = 0; i < files.length; i++) {
                let file = files[i];

//                if (!file.type.startsWith('image/')) continue;

                let li = document.createElement("li");
                list.appendChild(li);

                let img = document.createElement("img");
                img.classList.add("obj");
                img.file = file;
                preview.appendChild(img);

                let reader = new FileReader();
                reader.onload = (function (aImg) {
                    return function (e) {
                        aImg.src = e.target.result;
                    };
                })(img);
                reader.readAsDataURL(file);

                img.src = window.URL.createObjectURL(file);
                img.height = 60;
                img.onload = function () {
                    window.URL.revokeObjectURL(this.src);
                };

                li.appendChild(img);
                let info = document.createElement("span");
                info.innerHTML = file.name + ": " + file.size + " bytes";
                li.appendChild(info);

                sendFile(file);
            }
        }
    }

    function prepareFiles(e) {
        handleFiles(fileElem.files);
    }

    function sendFile(file) {
        let xhr = new XMLHttpRequest();
        let fd = new FormData();
        let progress = document.getElementById("progress");
        let progressBar = document.getElementById("progress-bar");
        let uri = "/test.php";
        
        xhr.upload.addEventListener("progress", function (e) {
            if (e.lengthComputable) {
                progress.style.visibility = "visible";
                let percentage = Math.round((e.loaded * 100) / e.total);
                progressBar.innerHTML = percentage + "%";
                progressBar.style.width = percentage + "%";
            }
        }, false);
        
        xhr.upload.addEventListener("load", function (e) {
            progressBar.innerHTML = "<p>100%</p>";
        }, false);

        xhr.open("POST", uri, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                progressBar.innerHTML = "Done!";
                progress.style.visibility = "hidden";
            }
        };

        fd.append('myFile', file);
        xhr.send(fd);
    }

    fileElem.addEventListener("change", prepareFiles, false);
    fileSelect.addEventListener("click", function (e) {
        if (fileElem) {
            fileElem.click();
        }
    }, false);
    fileSelect.addEventListener("dragenter", dragenter, false);
    fileSelect.addEventListener("dragover", dragover, false);
    fileSelect.addEventListener("dragleave", dragleave, false);
    fileSelect.addEventListener("drop", drop, false);
    function dragenter(e) {
        e.stopPropagation();
        e.preventDefault();

        document.getElementById('fileSelect').style.backgroundColor = 'greenyellow';
    }

    function dragover(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    function dragleave(e) {
        e.stopPropagation();
        e.preventDefault();

        document.getElementById('fileSelect').style.backgroundColor = 'lightcyan';
    }

    function drop(e) {
        e.stopPropagation();
        e.preventDefault();

        let files = e.dataTransfer.files;

        handleFiles(files);
    }
</script>
</html>