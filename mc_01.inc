<div class="row">
    <div class="col-6">
        <h3>Здравствуйте! </h3>
        <p>Скачайте, пожалуйста, <a href="documents/assignments.pdf" title="Вступительная олимпиада." target="_blank"
                                    onclick='setDateWorkSent("<?php echo $row['UniqueId'] ?>");'>вступительную олимпиаду</a> (.pdf).</p>
        <p>Также Вы можете отправить файл с задачами себе на почту (<?php echo $row["Email"] ?>):</p>
        <form id="SentWorkByMail" name="SentWorkByMail" method="get">
            <input name="sbm" type="hidden" id="SendByMail">
            <input name="id" type="hidden" id="UniqueId" value="<?php echo $row['UniqueId'] ?>">
            <input type="submit" value="Выслать на почту!" onClick='document.getElementById("SendByMail").value = "yes";'>
            <div id="sendStatus"></div>
        </form>
    </div>
    <!-- col-6 -->
    <div class="col-6">
        <h3><?php echo $row["Name"] . " " . $row["MiddleName"] . " " . $row["Surname"]; ?></h3>
        <p><b>Email: </b><?php echo $row["Email"]?></p>
        <p><b>Телефон: </b><?php echo $row["Tel"]?></p>
        <p><b>Дата рождения: </b><?php echo date_format(date_create($row["Birthday"]),'d/m/Y');?>
        </p>
    </div> <!-- col-6 -->
</div> <!-- row -->
