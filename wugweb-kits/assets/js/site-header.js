(function(){'use strict';
function initSiteHeader(){
  var nav=document.getElementById('site-nav'),toggle=document.getElementById('nav-toggle');
  if(!nav||!toggle||nav.dataset.navBound==='1')return;nav.dataset.navBound='1';
  var locked=false,lockedScrollY=0;
  function setScrollLock(lock){var body=document.body;if(lock===locked)return;
    if(lock){lockedScrollY=window.scrollY;body.style.position='fixed';body.style.top='-'+lockedScrollY+'px';body.style.insetInline='0';body.style.width='100%';locked=true;}
    else{body.style.position='';body.style.top='';body.style.insetInline='';body.style.width='';locked=false;void body.offsetHeight;window.scrollTo({top:lockedScrollY,left:0,behavior:'instant'});}}
  function setOpen(open){nav.dataset.open=open?'true':'false';toggle.setAttribute('aria-expanded',open?'true':'false');toggle.setAttribute('aria-label',open?'Close menu':'Open menu');setScrollLock(open&&window.matchMedia('(max-width: 767.98px)').matches);}
  toggle.addEventListener('click',function(){setOpen(nav.dataset.open!=='true');});
  nav.querySelectorAll('a').forEach(function(l){l.addEventListener('click',function(){setOpen(false);});});
  window.addEventListener('resize',function(){if(window.matchMedia('(min-width: 768px)').matches)setOpen(false);});
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initSiteHeader);else initSiteHeader();
})();
