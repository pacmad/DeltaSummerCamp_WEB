<div class="row">
    <div class="col-6">
        <h3>������������! </h3>
        <p>��������, ����������, <a href="documents/assignments.pdf" title="������������� ���������." target="_blank"
                                    onclick='setDateWorkSent("<?php echo $row['UniqueId'] ?>");'>������������� ���������</a> (.pdf).</p>
        <p>����� �� ������ ��������� ���� � �������� ���� �� ����� (<?php echo $row["Email"] ?>):</p>
        <form id="SentWorkByMail" name="SentWorkByMail" method="get">
            <input name="sbm" type="hidden" id="SendByMail">
            <input name="id" type="hidden" id="UniqueId" value="<?php echo $row['UniqueId'] ?>">
            <input type="submit" value="������� �� �����!" onClick='document.getElementById("SendByMail").value = "yes";'>
            <div id="sendStatus"></div>
        </form>
    </div>
    <!-- col-6 -->
    <div class="col-6">
        <h3><?php echo $row["Name"] . " " . $row["MiddleName"] . " " . $row["Surname"]; ?></h3>
        <p><b>Email: </b><?php echo $row["Email"]?></p>
        <p><b>�������: </b><?php echo $row["Tel"]?></p>
        <p><b>���� ��������: </b><?php echo date_format(date_create($row["Birthday"]),'d/m/Y');?>
        </p>
    </div> <!-- col-6 -->
</div> <!-- row -->