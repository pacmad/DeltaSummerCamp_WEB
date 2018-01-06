/*
���������� ����� ��������. �������� ��� ����� "more",
������ ��� ��������� ����� "�����..."
 */
function initNews() {
    var blocks = document.getElementsByClassName('more');
    if(blocks && blocks != undefined) {
        var n, len, cont;
        for (n = 0, len = blocks.length; n < len; ++n) {
            cont = document.createElement("p");
            blocks[n].parentNode.insertBefore(cont, blocks[n]);
            cont.innerHTML = "<p>�����...</p>";
            cont.className += 'continue';
            blocks[n].style.display = 'none';
        }
    }
}
/*
���������� �������
 */
function showMore(el) {
    el.querySelector('p.continue').style.display = 'none';
    el.querySelector('div.more').style.display = 'block';
}
