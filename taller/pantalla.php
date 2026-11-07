<?php declare(strict_types=1); header('Cache-Control: no-store'); ?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Laboratorio de IA · Pantalla · 5° Encuentro</title><link rel="icon" href="../favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<style>
:root{--ink:#0C1646;--ink2:#14215E;--ink3:#1D2C74;--sun:#FFC20E;--flame:#F26A21;--teal:#13A79A;--pink:#EE7DB9;--violet:#6A47B8;--paper:#F7F4EE;--muted:#C9CDEA;--f:"elza","Avenir Next","Segoe UI",system-ui,sans-serif}
*{box-sizing:border-box}html,body{height:100%;margin:0}
body{font:1.6vh/1.3 var(--f);background:var(--ink);color:#fff;height:100vh;overflow:hidden;display:grid;grid-template-rows:auto 1fr 34vh;gap:2.2vh;padding:2.4vh 2.6vw 2.8vh;position:relative}
/* Facetas de papel de fondo: luz y sombra de un pliegue, sin cuadrícula. */
body::before{content:"";position:fixed;inset:0;background:linear-gradient(118deg,rgba(255,255,255,.045) 0 34%,transparent 34%),linear-gradient(242deg,rgba(255,255,255,.03) 0 22%,transparent 22%),linear-gradient(20deg,rgba(0,0,0,.18) 0 18%,transparent 18%);pointer-events:none}
header{display:flex;align-items:center;gap:1.4vw;position:relative}
header .n{font:900 6.4vh/1 var(--f);color:var(--sun)}header .t{font-weight:900;font-size:2.1vh;line-height:1.1}header .t small{display:block;font-weight:700;color:var(--muted);font-size:1.6vh}
.ruta{margin-left:auto;display:flex;gap:.5vw}
.ruta div{width:8.6vw;padding:.9vh .7vw 1vh;background:rgba(255,255,255,.07);clip-path:polygon(0 0,100% 0,92% 100%,0 100%);font-weight:800;font-size:1.35vh;color:var(--muted);transition:background .4s,color .4s}
.ruta div b{display:block;font-size:1.1vh;letter-spacing:.1em;text-transform:uppercase;opacity:.8}
.ruta div.ok{background:rgba(19,167,154,.35);color:#fff}.ruta div.ya{background:var(--sun);color:var(--ink)}

main{display:grid;grid-template-columns:1fr 38vh;gap:3vw;align-items:center;min-height:0;position:relative}
.k{font-weight:900;letter-spacing:.14em;text-transform:uppercase;font-size:1.9vh;color:var(--sun)}
.preg{font:900 clamp(3vh,5.6vh,7vh)/1.08 var(--f);letter-spacing:-.02em;margin:1.2vh 0 1.6vh;max-width:24ch;text-wrap:balance}
.preg.xl{font-size:8.4vh;max-width:16ch}
.sub{font-size:2.3vh;color:var(--muted);max-width:52ch;font-weight:600}
.sub b{color:#fff}
.reloj{position:relative;width:38vh;height:38vh}
.reloj svg{width:100%;height:100%;transform:rotate(-90deg)}.reloj circle{fill:none;stroke-width:9}.reloj .bg{stroke:rgba(255,255,255,.1)}.reloj .fg{stroke:var(--sun);stroke-linecap:round;transition:stroke-dashoffset 1s linear,stroke .4s}
.reloj .c{position:absolute;inset:0;display:grid;place-content:center;text-align:center}.reloj .c b{font:900 9.6vh/1 var(--f);font-variant-numeric:tabular-nums;letter-spacing:-.03em}.reloj .c span{font-weight:800;color:var(--muted);font-size:1.8vh;letter-spacing:.1em;text-transform:uppercase}
.reloj.fin .fg{stroke:var(--flame)}.reloj.fin{animation:late 1s ease-in-out infinite}.reloj.cero .c b{color:var(--flame)}
@keyframes late{50%{transform:scale(1.035)}}
.qr{width:38vh;background:var(--paper);color:var(--ink);border-radius:2.4vh;padding:2.6vh;text-align:center;position:relative;box-shadow:0 2vh 5vh rgba(0,0,0,.35)}
.qr::after{content:"";position:absolute;right:0;top:0;width:6vh;height:6vh;background:linear-gradient(225deg,var(--ink) 0 50%,#DCD6CB 50%);border-top-right-radius:2.4vh}
.qr #qr{width:100%;aspect-ratio:1}.qr #qr svg{width:100%;height:100%;display:block}
.qr b{display:block;font-size:2.2vh;margin-top:1.2vh}.qr span{font-size:1.6vh;font-weight:700;color:#4B537F}

.grupos{display:grid;grid-template-columns:repeat(3,1fr);gap:1.6vw;min-height:0;position:relative}
.g{--c:var(--sun);--c2:#D9A200;background:var(--c);color:var(--ink);border-radius:2.4vh;padding:2vh 1.4vw 0;position:relative;overflow:hidden;display:flex;flex-direction:column;min-height:0}
.g:nth-child(2){--c:var(--teal);--c2:#0E8479}.g:nth-child(3){--c:var(--pink);--c2:#D2589C}
.g::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.26) 0 42%,transparent 42%);pointer-events:none}
.g::after{content:"";position:absolute;right:0;top:0;width:5.5vh;height:5.5vh;background:linear-gradient(225deg,var(--ink) 0 50%,var(--c2) 50%)}
.g .cab{display:flex;align-items:flex-start;gap:1vw;position:relative;z-index:1}
.g .lab{font-weight:900;font-size:1.5vh;letter-spacing:.12em;text-transform:uppercase}.g h2{margin:.3vh 0 0;font:900 3vh/1.05 var(--f)}
.g .num{margin-left:auto;margin-right:3.4vh;text-align:right;font:900 7.4vh/.9 var(--f);letter-spacing:-.04em;transition:transform .3s}.g .num small{display:block;font-size:1.4vh;letter-spacing:.06em;font-weight:800;text-transform:uppercase}
.g .num.salta{transform:scale(1.18)}
.g .cels{display:flex;gap:.5vw;flex-wrap:wrap;margin-top:1vh;position:relative;z-index:1}.g .cels span{background:var(--ink);color:#fff;border-radius:999px;padding:.4vh .9vw;font-weight:800;font-size:1.4vh}
.g .gente{margin-top:auto;height:19vh;position:relative;z-index:1;display:flex;align-items:flex-end;justify-content:space-evenly;gap:1vw}
.g .gente img{height:100%;width:auto;max-width:17%;object-fit:contain;object-position:bottom;filter:drop-shadow(0 .6vh .8vh rgba(0,0,0,.25));transform-origin:bottom center}
.g .gente img.salta{animation:brinca .7s ease-out}@keyframes brinca{40%{transform:translateY(-14%)}}
.g .gente img.nueva{animation:sube .9s cubic-bezier(.2,.9,.3,1.25) both}
@keyframes sube{from{transform:translateY(70%) scale(.85);opacity:0}}
.g .vacio{margin-top:auto;margin-bottom:2.4vh;font-weight:800;font-size:1.9vh;opacity:.75;position:relative;z-index:1}
.g .dest{margin:1.4vh 0 2vh;background:var(--paper);border-radius:1.6vh;padding:1.6vh 1.2vw;font-size:1.9vh;line-height:1.3;position:relative;z-index:1;overflow:hidden;flex:1;min-height:0}
.g .dest .r{font-size:1.4vh;font-weight:900;letter-spacing:.06em;text-transform:uppercase;color:#4B537F;display:block;margin-bottom:.6vh}.g .dest .pp{display:block;margin-top:.8vh;font-weight:800}

body.voto{grid-template-rows:auto auto 1fr}
/* Muro final de compromisos (Cierre) */
body.muro{grid-template-rows:auto 1fr}body.muro .grupos{display:none}
body.muro main{grid-template-columns:30vw 1fr;align-items:stretch;gap:2.4vw}
body.muro #izq{display:flex;flex-direction:column;gap:2vh;justify-content:flex-start;padding-top:2vh;height:100%}
body.muro .preg.xl{font-size:7vh;max-width:12ch}
.mq{display:flex;gap:1.4vw;align-items:center}.mq .qr{width:19vh;padding:1.6vh}.mq .qr b{font-size:1.6vh}.mq .qr span{display:none}
.cifras{display:flex;gap:2vw}.cifras div{font:900 7vh/.9 var(--f);color:var(--sun)}.cifras small{display:block;font-size:1.5vh;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-top:.6vh}
.muro-notas{columns:3;column-gap:1.2vw;height:100%;overflow:hidden;position:relative;mask-image:linear-gradient(#000 88%,transparent)}
.nota{break-inside:avoid;margin:0 0 1.2vw;padding:2vh 1.2vw 1.6vh;border-radius:.6vh;color:var(--ink);position:relative;box-shadow:0 1.2vh 2.4vh rgba(0,0,0,.28);transform:rotate(var(--r,0deg));transform-origin:top center}
.nota::after{content:"";position:absolute;right:0;bottom:0;width:3.2vh;height:3.2vh;background:linear-gradient(315deg,rgba(12,22,70,.35) 0 50%,rgba(255,255,255,.35) 50%)}
.nota p{margin:0;font-weight:800;font-size:var(--fs,2.15vh);line-height:1.3}
.comunidad{display:flex;align-items:flex-end;justify-content:space-between;gap:.6vw;height:16vh;margin-top:auto}.comunidad img{height:100%;width:auto;max-width:18%;object-fit:contain;object-position:bottom;filter:drop-shadow(0 .6vh .8vh rgba(0,0,0,.35))}.nota small{display:block;margin-top:1.2vh;font-weight:800;font-size:1.45vh;opacity:.8}
.nota.nueva{animation:despliega .9s cubic-bezier(.2,.8,.2,1) both}
.muro-vacio{display:grid;place-items:center;height:100%;font-weight:800;font-size:3vh;color:var(--muted);text-align:center}
body.voto main{grid-template-columns:1fr 21vh;gap:2vw}
body.voto .preg{font-size:5.2vh;max-width:30ch;margin:.6vh 0 .8vh}body.voto .sub{font-size:2vh}
body.voto .reloj{width:21vh;height:21vh}body.voto .reloj .c b{font-size:5.4vh}body.voto .reloj .c span{font-size:1.3vh}
body.voto .g{padding:2vh 1.4vw 1.8vh}
.g .votos{margin-top:1.6vh;background:var(--ink);color:#fff;border-radius:1.8vh;padding:1.8vh 1.2vw;position:relative;z-index:1;flex:1;min-height:0;display:flex;flex-direction:column;justify-content:space-evenly;gap:.9vh}
.g .vf{display:grid;grid-template-columns:5.4vh 1fr;align-items:center;gap:.9vw}
.g .vf .cn{width:5.4vh;height:5.4vh;border-radius:1.4vh;background:rgba(255,255,255,.1);display:grid;place-items:center;font:900 2.4vh/1 var(--f)}
.g .vf .br{position:relative;height:5.4vh;border-radius:1.4vh;background:rgba(255,255,255,.07);overflow:hidden;display:flex;align-items:center}
.g .vf .br b{position:absolute;inset:0 auto 0 0;background:color-mix(in srgb,var(--c) 55%,transparent);transition:width .7s cubic-bezier(.2,.8,.2,1)}
.g .vf .br span{position:relative;padding:0 1vw;font-weight:800;font-size:1.8vh;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1}
.g .vf .br em{position:relative;font-style:normal;font-weight:900;font-size:2.6vh;padding:0 1vw}
.g .vf.lider .cn{background:var(--c);color:var(--ink)}.g .vf.lider .br{box-shadow:inset 0 0 0 .35vh var(--c)}
.g .vf.lider .br span{flex:none;margin-left:1vw;padding:.4vh .8vw;border-radius:999px;background:var(--c);color:var(--ink);font-weight:900}.g .vf .br em{margin-left:auto}
.g .tot{font-weight:800;font-size:1.6vh;color:var(--muted);text-align:right}
.g .dest h3{font:900 3.4vh/1.05 var(--f);margin:.4vh 0 .6vh}
.capa{position:fixed;inset:0;z-index:20;background:var(--sun);color:var(--ink);display:grid;place-content:center;text-align:center;perspective:120vh}
.capa .h{transform-origin:top center;animation:despliega 1s cubic-bezier(.2,.8,.2,1) both}
.capa .h b{display:block;font-weight:900;letter-spacing:.16em;text-transform:uppercase;font-size:3vh}.capa .h div{font:900 22vh/.9 var(--f);letter-spacing:-.05em}
.capa::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.3) 0 40%,transparent 40%),linear-gradient(225deg,rgba(0,0,0,.08) 0 30%,transparent 30%)}
@keyframes despliega{from{transform:rotateX(-88deg);opacity:0}}
.capa.sale{animation:sale .6s ease-in forwards}@keyframes sale{to{opacity:0}}
.full{position:fixed;left:1.2vw;bottom:1.2vh;z-index:40;display:inline-flex;align-items:center;gap:.6vw;border:0;border-radius:999px;padding:1.2vh 1.4vw;background:var(--sun);color:var(--ink);font:900 1.8vh/1 var(--f);cursor:pointer;box-shadow:0 1vh 3vh rgba(0,0,0,.35);transition:opacity .4s,transform .2s}
.full svg{width:2.4vh;height:2.4vh}.full:hover{transform:scale(1.04)}.full:focus-visible{outline:.4vh solid #fff;outline-offset:.4vh}
body.quieto .full{opacity:0;pointer-events:none}body.quieto.en-full{cursor:none}
.startin-credit{position:fixed;right:1vw;bottom:.8vh;font:600 1.2vh/1.2 var(--f);color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.9);border-radius:999px;padding:.4vh .8vw;z-index:30}.startin-credit b{color:#16162B;font-weight:800}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
</style></head><body>
<header><span class="n">5°</span><div class="t">Laboratorio de IA aplicada a procesos de inclusión<small>Encuentro Diversidades e Inclusión · 11 de noviembre</small></div><div class="ruta" id="ruta" aria-hidden="true"></div></header>
<main><div id="izq"></div><div id="der"></div></main>
<section class="grupos" id="grupos"></section>
<div id="capa"></div>
<button class="full" id="full" type="button" aria-label="Ver en pantalla completa"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg><span>Pantalla completa</span></button>
<a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener">by <b>Startin*Lab</b></a>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>
<script>
(function(){const $=s=>document.querySelector(s);const esc=s=>String(s??"").replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]));
const FIG=["afro","ciego","indigena","lgbt","mayor","migrante","mujer","nina","silla"];
const reducido=matchMedia("(prefers-reduced-motion: reduce)").matches;
function qrSvg(url){try{const q=qrcode(0,"M");q.addData(url);q.make();return q.createSvgTag({cellSize:4,margin:0,scalable:true});}catch(e){return"";}}
const QR_T=qrSvg("https://diversidad.startinlab.org/taller/"),QR_C=qrSvg("https://diversidad.startinlab.org/#evento");
let d=null,off=0,faseVista,vistos={},modo="";
async function cargar(){try{const r=await fetch("../api/taller.php?solo=pantalla",{cache:"no-store"});const j=await r.json();if(j.ok){d=j;off=j.estado.ahora-Date.now()/1000;pintar();}}catch(e){}}
function aviso(f){if(reducido)return;const c=$("#capa");c.innerHTML=`<div class="capa"><div class="h"><b>${f.n>=1&&f.n<=3?"Fase "+f.n+" de 3":"Ahora"}</b><div>${esc(f.nombre)}</div></div></div>`;setTimeout(()=>{const x=c.firstChild;if(x){x.classList.add("sale");setTimeout(()=>c.innerHTML="",600);}},3200);}
function reloj(){return`<div class="reloj" id="reloj"><svg viewBox="0 0 100 100"><circle class="bg" cx="50" cy="50" r="44"/><circle class="fg" id="arco" cx="50" cy="50" r="44" stroke-dasharray="276.5" stroke-dashoffset="0"/></svg><div class="c"><b id="t">–</b><span id="tl">minutos</span></div></div>`;}
function pintar(){const e=d.estado,f=e.fase;
  $("#ruta").innerHTML=e.fases.map((x,i)=>`<div class="${f&&i<f.n?"ok":""}${f&&i===f.n?" ya":""}"><b>${i>=1&&i<=3?"Fase "+i:(i===0?"Inicio":"Cierre")}</b>${esc(x.nombre)}</div>`).join("");
  let m=!f?"inicio":(f.id==="plenaria"?"plenaria":(f.id==="cierre"?"cierre":"fase"));
  document.body.classList.toggle("voto",m==="plenaria");document.body.classList.toggle("muro",m==="cierre");
  const firma=m+(f?f.id:"")+(e.formado?1:0);
  if(firma!==modo){modo=firma;
    if(m==="inicio"){$("#izq").innerHTML=`<div class="k">${e.formado?"Las células están listas":"Bienvenida"}</div><div class="preg xl">De identificar barreras a experimentar soluciones.</div><div class="sub">${e.formado?"Reúnete con tu célula. <b>Tu reto ya está en tu celular.</b>":"Escanea el código, entra con tu escarapela o tu cédula y descubre <b>tu grupo, tu célula y tu reto</b>."}</div>`;
      $("#der").innerHTML=`<div class="qr"><div id="qr">${QR_T}</div><b>Entra a Mi taller</b><span>diversidad.startinlab.org/taller</span></div>`;}
    else if(m==="cierre"){$("#izq").innerHTML=`<div class="k">Cierre · Muro de compromisos</div><div class="preg xl">Ahora, dobla tu compromiso.</div><div class="sub">Escríbelo en <b>Mi taller</b>, en la pestaña Fase. Aparece aquí al instante.</div><div class="mq"><div class="qr"><div id="qr">${QR_T}</div><b>Mi taller</b></div><div class="cifras"><div id="mTot">0<small>compromisos</small></div><div id="mOrg">0<small>organizaciones</small></div></div></div><div class="comunidad" aria-hidden="true">${[0,3,6,1,8].map(i=>`<img src="../assets/fig/${FIG[i]}.webp?v=x" alt="">`).join("")}</div>`;
      $("#der").innerHTML=`<div class="muro-notas" id="muro"></div>`;vistosMuro={};cargarMuro();}
    else{$("#izq").innerHTML=`<div class="k">${f.n>=1&&f.n<=3?"Fase "+f.n+" de 3 · ":""}${esc(f.nombre)}</div><div class="preg">${f.id==="plenaria"?"¿Qué idea presenta cada grupo?":esc(f.pregunta||f.texto||"")}</div><div class="sub">${f.pregunta?`Escríbanla en la ficha de su célula y lleven el post-it al <b>backing de ${esc(f.nombre)}</b>. Pliego los acompaña desde el celular.`:(f.id==="plenaria"?"Abran <b>Mi taller</b> y voten por la idea de otra célula de su grupo. Cuando la persona facilitadora confirme, aquí aparece la que presenta.":"")}</div>`;
      $("#der").innerHTML=reloj();}
  }
  if(f&&faseVista!==undefined&&faseVista!==f.id)aviso(f);faseVista=f?f.id:null;
  // Grupos: una figura de papel por persona; en la puesta en común, la idea destacada.
  if(m==="cierre"){tick();return;}
  const g=$("#grupos");if(!g.children.length)g.innerHTML=d.grupos.map(x=>`<article class="g" id="g${x.grupo}"></article>`).join("");
  d.grupos.forEach(x=>{const el=$("#g"+x.grupo),plen=m==="plenaria"||m==="cierre";const cs=Object.entries(x.celulas);
    const prev=vistos[x.grupo]||0;
    let h=`<div class="cab"><div><div class="lab">Grupo ${x.grupo}</div><h2>${esc(x.eje)}</h2></div><div class="num${x.personas>prev&&prev?" salta":""}">${x.personas}<small>personas</small></div></div>`;
    if(cs.length&&!plen)h+=`<div class="cels">${cs.map(([c,n])=>`<span>Célula ${c} · ${n}</span>`).join("")}</div>`;
    if(plen&&x.destacada){const t=x.destacada,L=t.landing;h+=`<div class="dest"><span class="r">★ Presenta la célula ${t.celula} · ${esc(t.reto)}</span>${L?`<h3>${esc(L.titulo)}</h3>${esc(L.lema)}`:esc(t.solucion||t.barrera||"")}${t.primer_paso?`<span class="pp">Primer paso: ${esc(t.primer_paso)}</span>`:""}</div>`;}
    else if(plen){// Votos en vivo mientras el grupo elige.
      const vs=Object.keys(x.celulas).map(c=>[+c,(x.votos&&x.votos[c])||0]),tot=vs.reduce((a,v)=>a+v[1],0),max=Math.max(0,...vs.map(v=>v[1]));
      h+=`<div class="votos">${vs.map(([c,n])=>`<div class="vf${n&&n===max?" lider":""}"><div class="cn">C${c}</div><div class="br"><b style="width:${tot?Math.round(n/tot*100):0}%"></b><span>${n&&n===max?"★ Va ganando":""}</span><em>${n}</em></div></div>`).join("")}<div class="tot">${tot} de ${x.personas} personas ya votaron</div></div>`;}
    else if(!x.personas){h+=`<div class="vacio">Esperando a las primeras personas…</div>`;}
    else{// Pocas figuras, separadas: representan al grupo; el número grande dice cuántas personas hay.
      const n=Math.min(x.personas,5),nuevo=x.personas>prev&&prev>=5;
      h+=`<div class="gente">${Array.from({length:n},(_,i)=>`<img src="../assets/fig/${FIG[(i*2+x.grupo*3)%9]}.webp?v=x" alt="" class="${i>=prev?"nueva":""}${nuevo&&i===(x.personas%n)?" salta":""}"${i>=prev?` style="animation-delay:${(i-prev)*140}ms"`:""}>`).join("")}</div>`;}
    const sig=h.replace(/nueva| salta|animation-delay:\d+ms/g,"");if(el.dataset.sig===sig&&x.personas===prev){return;}
    el.dataset.sig=sig;el.innerHTML=h;vistos[x.grupo]=x.personas;
    const nm=el.querySelector(".num.salta");if(nm)setTimeout(()=>nm.classList.remove("salta"),400);});
  tick();}
// Muro final: notas de papel con el color del pliegue de cada persona; las nuevas se despliegan.
const COLOR={"Personas mayores":"#F0A04B","Género y cuidado":"#E6DDBF","Educación inclusiva":"#5DBB52","Discapacidad y trabajo":"#E0736B","La ley ya cambió":"#62C3CA","Población afrodescendiente":"#C98A57","Pueblos indígenas y Rrom":"#E2A93B","Personas LGBTIQ+":"#B48AE6","Migración y conflicto":"#7FA7F2"};
const RESERVA=["#FFC20E","#13A79A","#EE7DB9","#F7F4EE","#F0A04B","#62C3CA"];let vistosMuro={};
async function cargarMuro(){const box=$("#muro");if(!box)return;try{const r=await fetch("../api/muro.php?n=30",{cache:"no-store"});const j=await r.json();if(!j.ok)return;
  $("#mTot").firstChild.textContent=j.total;$("#mOrg").firstChild.textContent=j.organizaciones;
  if(!j.items.length){box.innerHTML=`<div class="muro-vacio">El muro espera el primer compromiso…</div>`;return;}
  const primera=!Object.keys(vistosMuro).length,n=j.items.length;
  box.style.setProperty("--fs",n<=6?"3.4vh":n<=12?"2.7vh":n<=20?"2.2vh":"1.9vh");box.style.columns=n<=4?2:3;
  box.innerHTML=j.items.map((it,i)=>{const nueva=!primera&&!vistosMuro[it.id];const col=COLOR[it.pliegue]||RESERVA[it.id%RESERVA.length];const rot=((it.id*37)%5-2)*.6;
    return`<div class="nota${nueva?" nueva":""}" style="background:${col};--r:${rot}deg"><p>${esc(it.texto)}</p><small>— ${it.nombre?esc(it.nombre)+" · ":""}${esc(it.organizacion)}</small></div>`;}).join("");
  j.items.forEach(it=>vistosMuro[it.id]=1);}catch(e){}}
setInterval(()=>{if(document.body.classList.contains("muro"))cargarMuro();},5000);
function tick(){const el=$("#t");if(!d||!el)return;const f=d.estado.fase;if(!f)return;const tot=f.min*60,s=Math.max(0,Math.round(f.fin-(Date.now()/1000+off)));
  el.textContent=s?`${Math.floor(s/60)}:${String(s%60).padStart(2,"0")}`:"¡Tiempo!";$("#tl").textContent=s?(s<60?"último minuto":"minutos"):"";
  $("#arco").style.strokeDashoffset=276.5*(1-s/tot);$("#reloj").classList.toggle("fin",s>0&&s<60);$("#reloj").classList.toggle("cero",!s);}
setInterval(tick,1000);setInterval(cargar,4000);cargar();
// Pantalla completa para transmitir: botón o tecla F. El botón y el cursor se esconden si el mouse no se mueve,
// y la pantalla del PC no se apaga mientras esta página esté abierta.
const bf=$("#full"),txt=bf.querySelector("span");let quieto;
function esFull(){return !!(document.fullscreenElement||document.webkitFullscreenElement);}
async function alternar(){try{if(esFull()){(document.exitFullscreen||document.webkitExitFullscreen).call(document);}else{const el=document.documentElement;await (el.requestFullscreen||el.webkitRequestFullscreen).call(el);}}catch(e){}}
function actualizar(){const f=esFull();document.body.classList.toggle("en-full",f);txt.textContent=f?"Salir de pantalla completa":"Pantalla completa";bf.setAttribute("aria-label",txt.textContent);mover();}
function mover(){document.body.classList.remove("quieto");clearTimeout(quieto);quieto=setTimeout(()=>document.body.classList.add("quieto"),esFull()?2500:6000);}
bf.onclick=alternar;document.addEventListener("fullscreenchange",actualizar);document.addEventListener("webkitfullscreenchange",actualizar);
addEventListener("keydown",e=>{if((e.key==="f"||e.key==="F")&&!e.ctrlKey&&!e.metaKey&&!e.altKey)alternar();});
["mousemove","pointerdown","keydown"].forEach(ev=>addEventListener(ev,mover));mover();
let lock=null;async function despierta(){try{if("wakeLock" in navigator&&!lock){lock=await navigator.wakeLock.request("screen");lock.addEventListener("release",()=>lock=null);}}catch(e){}}
despierta();document.addEventListener("visibilitychange",()=>{if(!document.hidden)despierta();});})();
</script></body></html>
