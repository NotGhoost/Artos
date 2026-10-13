// Каталог Artos: живые фильтры без перезагрузки страницы.
// Данные берёт из api/paintings.php (см. index.php — без JS каталог тоже работает через обычную форму).
(function(){
  const form=document.getElementById('filters'); if(!form) return;
  const grid=document.getElementById('grid'), empty=document.getElementById('empty');
  const money=n=>Number(n).toLocaleString('ru-RU')+' ₽';
  const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
  const st={available:'В наличии',reserved:'Забронирована',sold:'Продана'};
  let timer;
  async function load(){
    const qs=new URLSearchParams(new FormData(form)).toString();
    history.replaceState(null,'','?'+qs);
    try{
      const r=await fetch('api/paintings.php?'+qs,{headers:{'Accept':'application/json'}});
      const list=await r.json();
      grid.innerHTML=list.map(p=>`<article class="card"><a href="painting.php?id=${p.id}"><div class="imgbox"><img src="${esc(p.photo)}" alt="${esc(p.title)}" loading="lazy"></div><h3>${esc(p.title)}</h3><div class="meta">${esc(p.artist_name||'')} · ${esc(p.year||'')} · ${esc(p.size||'')}</div><div class="price-row"><span class="price">${money(p.price)}</span><span class="status ${esc(p.status)}">${st[p.status]||''}</span></div></a></article>`).join('');
      empty.classList.toggle('hidden',list.length>0);
    }catch(e){ /* если API недоступно — оставляем серверный вывод */ }
  }
  form.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(load,250)});
  form.addEventListener('submit',e=>e.preventDefault());
})();
