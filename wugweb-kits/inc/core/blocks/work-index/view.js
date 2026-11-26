(function(){
  var chips=document.querySelectorAll('.work-filters .filter-chip');
  chips.forEach(function(chip){chip.addEventListener('click',function(){
    var cat=chip.dataset.category;
    chips.forEach(function(c){c.classList.remove('active');c.setAttribute('aria-pressed','false');});
    chip.classList.add('active');chip.setAttribute('aria-pressed','true');
    document.querySelectorAll('.work-grid li').forEach(function(item){
      if(cat==='All'||(item.dataset.categories||'').indexOf(cat)>-1)item.classList.remove('hidden');else item.classList.add('hidden');
    });
  });});
  var first=document.querySelector('.work-filters .filter-chip');if(first){first.classList.add('active');first.setAttribute('aria-pressed','true');}
})();
