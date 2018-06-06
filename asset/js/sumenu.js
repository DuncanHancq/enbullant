window.onload = function () {
  "use strict";

  // Gestion du menu du back-office
if(document.getElementById('btn-su-menu')){

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

if(document.getElementById('nav-icon')){

  // Gestion du menu front
  var navIcon = document.getElementById('nav-icon'),
      nav = document.getElementById("responsive"),
      pictFonc = document.getElementById('pict-fonc'),
      rsWrap = document.getElementById('rs-wrap');

  navIcon.onclick = function (){
      this.classList.toggle('o');
      nav.classList.toggle('o');
      pictFonc.classList.toggle('o');
      rsWrap.classList.toggle('o');

  }

}

}
