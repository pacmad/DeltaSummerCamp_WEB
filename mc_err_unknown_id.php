<div class="title">
    <div class="row"><div class="col-12">
            <h2>������������� ������������ �� ������.</h2>
        </div></div></div>
<div class="row"><div class="col-8"><div class="main">
            <p>���� �� ������������������ � ������ ������-�������������� ������ "������" � ������� � ������ �� ��� �������� �� ������
                � ������ � �������������� �����������, ���������� ��������� � ��������������"</p>
            <form method="post" action="feedback.php">
                <p>
                    <input name="id" type="hidden" id="id" value="<?php echo $row['UniqueId'] ?>">
                    <input name="name" type="hidden" id="name" value="<?php echo $row['Name'] . ' ' . $row['Surname'] ?>">
                    <input name="email" type="hidden" id="email" value="<?php echo $row['Email'] ?>">
                    <input type="submit" value="����� � ��������������">
                </p>
            </form>
        </div> </div>
</div>
