/* via: http://stackoverflow.com/questions/13382516/getting-scroll-bar-width-using-javascript
   answered Nov 14 '12 at 16:25 by lostsource

   getScrollbarWidth()
*/
/*
function getScrollbarWidth() {
    var outer = document.createElement("div");
    outer.style.visibility = "hidden";
    outer.style.width = "100px";
    outer.style.msOverflowStyle = "scrollbar"; // needed for WinJS apps

    document.body.appendChild(outer);

    var widthNoScroll = outer.offsetWidth;
    outer.style.overflow = "scroll"; // force scrollbars

    // add innerdiv
    var inner = document.createElement("div");
    inner.style.width = "100%";
    outer.appendChild(inner);

    var widthWithScroll = inner.offsetWidth;

    // remove divs
    outer.parentNode.removeChild(outer);

    return widthNoScroll - widthWithScroll;
}
*/

// Это копия констант, определённых в PHP/common.inc
const startDay = new Date('2020-07-20');
const endDay = new Date('2020-07-03');

// Возвращает полное число лет от birthday до date
// noinspection SyntaxError
function age(birthday, startDay = new Date()) {
    const years = startDay.getFullYear()-birthday.getFullYear();
    if ((startDay.getMonth()-birthday.getMonth())<0) {
        return years-1;
    }
    if (startDay.getMonth() === birthday.getMonth() && startDay.getDate() < birthday.getDate()) {
        return years-1;
    }
    return years;
}

// Возвращает дату дня рождения, если пришлось на период лагеря
function happyBirthday(birthday) {
    let theDay = new Date(startDay.getFullYear(), birthday.getMonth(), birthday.getDate());
    if (theDay >= startDay && theDay <= endDay) {
        return (theDay.toDateString());
    }
    return '';
}
