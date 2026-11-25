(function(){
  var g=window.gsap, ST=window.ScrollTrigger; if(!g||!ST) return; g.registerPlugin(ST);
  var reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('[data-counters]').forEach(function(ul){
    if(ul.dataset.countBound==='1') return; ul.dataset.countBound='1';
    ul.querySelectorAll('[data-count-to]').forEach(function(el){
      var to=parseFloat(el.getAttribute('data-count-to')); if(isNaN(to)) return;
      var dec=(el.getAttribute('data-count-to').split('.')[1]||'').length, fmt=function(v){return v.toLocaleString('en-US',{minimumFractionDigits:dec,maximumFractionDigits:dec});};
      if(reduced){el.textContent=fmt(to);return;}
      var o={v:0}; el.textContent=fmt(0);
      g.to(o,{v:to,duration:1.6,ease:'power2.out',scrollTrigger:{trigger:ul,start:'top 85%',once:true},onUpdate:function(){el.textContent=fmt(o.v);},onComplete:function(){el.textContent=fmt(to);}});
    });
  });
})();
