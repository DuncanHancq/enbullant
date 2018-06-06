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

window.fbAsyncInit = function() {
    FB.init({
      appId      : '391491267999051',
      xfbml      : true,
      version    : 'v3.0'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = 'https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v3.0&appId=391491267999051&autoLogAppEvents=1';
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  FB.ui({
    method: 'share',
    href: window.location.href,
  }, function(response){});

}
