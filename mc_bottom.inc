
<div class="row"><div class="col-12">
        <hr>
        <p>���� � ��� �������� ������� ��� �� �������� ���������� � ��������������� ������, ����������, �������������� ������
            �������� ����� ��� ��������� � ����:</p>
    </div>
</div>  <!-- col-12, row -->
<div class="row"><div class="col-6">
        <p><b>���� ���������</b><br>
            <?php printContact('sem');?>
        </p></div> <!-- col-6 -->
    <div class="col-6">
        <p><b>������� �����</b><br>
            <?php printContact('abl');?>
        </p>
    </div>
</div> <!-- col-6, row -->
<div class="row"><div class="col-8">
        <form method="post" action="feedback.php">
            <p>
                <input name="id" type="hidden" id="id" value="<?php echo $row['UniqueId'] ?>">
                <input name="name" type="hidden" id="name" value="<?php echo $row['Name'] . ' ' . $row['Surname'] ?>">
                <input name="email" type="hidden" id="email" value="<?php echo $row['Email'] ?>">
                <input type="submit" value="����� � ��������������">
            </p>
            <p>&nbsp; </p>
        </form>
    </div>
</div> <!-- col-8, row -->