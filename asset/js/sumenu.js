window.onload = function () {
    
    console.log('init | sumenu.js');

    var button = document.getElementById('btn-su-menu'),
        containerSu = document.getElementById('container-su-menu'),
        menuSu = document.getElementById('su-menu'),
        isOver; 

    button.onclick = function ()
    {
        containerSu.classList.add('o');
    }

    menuSu.onmouseover = function ()
    {
        isOver = false;
    }
    
    menuSu.onmouseout = function ()
    {
        isOver = true;
    }
    
    containerSu.onclick = function ()
    {
        if(isOver == true)
        {
            containerSu.classList.remove('o');
        }
    }


}