/*
Подготовка ленты новостей. Скрываем все блоки "more",
Вместо них вставляем метку "далее..."
 */
document.addEventListener("DOMContentLoaded", function (e) {
    var blocks = document.getElementsByClassName('more');
    if(blocks && blocks != undefined) {
        var n, len, cont;
        for (n = 0, len = blocks.length; n < len; ++n) {
            cont = document.createElement("p");
            blocks[n].parentNode.insertBefore(cont, blocks[n]);
            cont.innerHTML = "<p>далее...</p>";
            cont.className += 'continue';
            blocks[n].style.display = 'none';
        }
    }
});
var msnry;
window.addEventListener("load", function (e) {
    var elem = document.querySelector('.projects');
    msnry = new Masonry(elem, {
        itemSelector: '.project',
        columnWidth: '.project-sizer',
        percentPosition: true
    });
});
/*
Раскрываем новость
 */
function showMore(el) {
    el.querySelector('p.continue').style.display = 'none';
    el.querySelector('div.more').style.display = 'block';
    msnry.layout();
}
