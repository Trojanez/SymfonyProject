var buttons = document.getElementById('pagination').getElementsByTagName('button');

for(var i = 0; i < buttons.length; i++) {
    buttons[0].addEventListener('click', showPrev);
    buttons[1].addEventListener('click', showNext);
}

var images = document.getElementById("images").getElementsByTagName('img');
var span = document.getElementById("first");
var i = 0;

function showNext() {
    images[i].className = '';
    i++;

    if (i > images.length - 1) {
        i = 0;
    }

    images[i].className = 'visible';
}

function showPrev() {
    images[i].className = '';
    i--;

    if (i < 0) {
        i = images.length - 1;
    }

    images[i].className = 'visible';
}

    var elem = document.getElementById('first'), num = +elem.innerHTML;

    function up()
    {
        num++;
        elem.innerHTML = num;
        if(num == 4)
        {
            elem.innerHTML = num = 1;
        }
    }

function down()
{
    num--;
    elem.innerHTML = num;
    if(num == 0)
    {
        elem.innerHTML = num = 3;
    }
}

