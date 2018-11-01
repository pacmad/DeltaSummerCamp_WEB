/*
Подготовка ленты новостей. Скрываем все блоки "more",
Вместо них вставляем метку "далее..."
 */
document.addEventListener("DOMContentLoaded", function (e) {
    var blocks = document.getElementsByClassName('more');
    if(blocks && blocks != undefined) {
        var n, len, cont, style, arr;
        for (n = 0, len = blocks.length; n < len; ++n) {
            cont = document.createElement("p");
            blocks[n].parentNode.insertBefore(cont, blocks[n]);
            style = 'expandable';
            arr = blocks[n].parentNode.className.split(" ");
            if (arr.indexOf(style) == -1) {
                blocks[n].parentNode.className += " " + style;
            }
            cont.innerHTML = "<p>&#9660;далее...</p>";
            cont.className = ' continue';
            blocks[n].style.display = 'none';
        }
    }
});
/*
Инициализация библиотеки Masonry, используется при выводе проектов
 */
var msnry;
window.addEventListener("load", function (e) {
    var elem = document.querySelector('.projects');
    if(elem) {
        msnry = new Masonry(elem, {
            itemSelector: '.project',
            columnWidth: '.project-sizer',
            percentPosition: true
        });
    }
});
/*
Раскрываем новость
 */
function showMore(el) {
    el.querySelector('p.continue').style.display = 'none';
    el.querySelector('div.more').style.display = 'block';
    msnry.layout();
}
