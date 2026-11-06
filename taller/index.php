<?php
declare(strict_types=1);
header('Cache-Control: no-store');
$c = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($_GET['c'] ?? '')));
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="robots" content="noindex,nofollow">
<title>Mi taller · Laboratorio de IA · 5° Encuentro</title><link rel="icon" href="../favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<style>
:root{--ink:#0C1646;--ink2:#14215E;--ink3:#1D2C74;--sun:#FFC20E;--flame:#F26A21;--teal:#13A79A;--pink:#EE7DB9;--muted:#C9CDEA;--g:var(--sun)}
*{box-sizing:border-box}[hidden]{display:none!important}html,body{height:100%}
body{margin:0;font:16px/1.45 "elza","Segoe UI",system-ui,sans-serif;background:var(--ink);color:#fff;height:100dvh;display:grid;grid-template-rows:auto auto 1fr auto;overflow:hidden;padding:env(safe-area-inset-top,0) 0 0}
body[data-g="2"]{--g:var(--teal)}body[data-g="3"]{--g:var(--pink)}
header{display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,.12)}header .n{color:var(--sun);font-size:1.7rem;font-weight:900;line-height:1}header b{font-size:.85rem;line-height:1.1}header button{margin-left:auto}
.wrap{max-width:640px;width:100%;margin:0 auto;padding:0 16px}
.ubic{padding:12px 16px 0}.ubic .card{background:var(--ink2);border-radius:18px;padding:14px 16px;border-left:8px solid var(--g);display:grid;gap:2px}
.ubic .k{font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;color:var(--g);font-weight:800}.ubic .big{font-size:1.5rem;font-weight:900;line-height:1.15}.ubic .reto{font-size:1.02rem;font-weight:700;margin-top:4px}
.fase{display:flex;align-items:center;gap:10px;margin-top:10px;background:var(--ink3);border-radius:14px;padding:8px 12px}.fase .nom{font-weight:800}.fase .t{margin-left:auto;font-variant-numeric:tabular-nums;font-size:1.4rem;font-weight:900;color:var(--sun)}.fase .t.fin{color:var(--flame)}
.dots{display:flex;gap:4px;margin-top:8px}.dots i{flex:1;height:5px;border-radius:5px;background:rgba(255,255,255,.18)}.dots i.on{background:var(--g)}.dots i.now{background:var(--sun)}
main{overflow:auto;padding:12px 0 48px;-webkit-overflow-scrolling:touch}
.panel[hidden]{display:none}
h2{font-size:1.15rem;margin:4px 0 8px}p{margin:6px 0}.muted{color:var(--muted);font-size:.92rem}
ol.pasos{margin:6px 0 10px;padding-left:22px;display:grid;gap:4px}
.preg{background:rgba(255,194,14,.12);border:1px solid rgba(255,194,14,.4);border-radius:14px;padding:10px 12px;font-weight:700}
.prompt{background:#fff;color:var(--ink);border-radius:14px;padding:12px;font-size:.95rem;white-space:pre-wrap}
.row{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
.btn{border:0;border-radius:999px;padding:12px 18px;font:800 1rem "elza","Segoe UI",system-ui,sans-serif;cursor:pointer;background:var(--flame);color:#fff;text-decoration:none;display:inline-block}.btn.ghost{background:transparent;color:#fff;box-shadow:inset 0 0 0 2px rgba(255,255,255,.35)}.btn.sm{padding:8px 14px;font-size:.88rem}
.btn:focus-visible,textarea:focus-visible,input:focus-visible,nav button:focus-visible{outline:3px solid var(--sun);outline-offset:2px}
.campo{background:var(--ink2);border-radius:16px;padding:12px;margin-bottom:10px;border:2px solid transparent}.campo.now{border-color:var(--sun)}
.campo label{font-weight:800;display:block}.campo small{display:block;color:var(--muted);margin-bottom:6px}
textarea,input[type=text]{width:100%;border-radius:12px;border:2px solid rgba(255,255,255,.25);background:#0A1240;color:#fff;font:1rem/1.4 "elza","Segoe UI",system-ui,sans-serif;padding:10px}
textarea{min-height:84px;resize:vertical}.estado-g{font-size:.8rem;color:var(--muted);min-height:1em}
.chat{display:grid;gap:8px}.msg{padding:10px 12px;border-radius:14px;max-width:92%;white-space:pre-wrap}.msg.u{background:var(--flame);justify-self:end}.msg.a{background:var(--ink2)}
.ask{display:flex;gap:8px;margin-top:10px}.ask textarea{min-height:52px}
nav{display:grid;grid-template-columns:repeat(3,1fr);border-top:1px solid rgba(255,255,255,.12);background:var(--ink);padding-bottom:env(safe-area-inset-bottom,0)}
nav button{background:none;border:0;color:var(--muted);font:700 .92rem "elza","Segoe UI",system-ui,sans-serif;padding:12px 4px;cursor:pointer}nav button[aria-selected=true]{color:#fff;box-shadow:inset 0 3px 0 var(--g)}
nav[hidden]{display:none}
.login{display:grid;gap:12px;margin-top:8vh;text-align:center}.login input{font-size:1.3rem;text-align:center;text-transform:uppercase;letter-spacing:.06em}
.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}
.startin-credit{position:fixed;z-index:60;right:12px;bottom:calc(58px + env(safe-area-inset-bottom,0px));font:600 .68rem/1.2 "elza","Segoe UI",system-ui,sans-serif;color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.88);border:1px solid #ECE7E4;border-radius:999px;padding:4px 10px;opacity:.85}.startin-credit b{color:#16162B;font-weight:800}
@media (prefers-reduced-motion:reduce){*{transition:none!important}}
</style></head><body>
<header><span class="n">5°</span><b>Laboratorio de IA aplicada<br>a procesos de inclusión</b><button class="btn ghost sm" id="salir" type="button" hidden>Cambiar código</button></header>
<section class="ubic wrap" id="ubic" hidden aria-live="polite"></section>
<main><div class="wrap">
  <form class="login" id="login" hidden>
    <h1 style="margin:0;font-size:1.5rem">Mi taller</h1>
    <p class="muted">Escribe el código de tu escarapela. Está debajo del QR y empieza por 5E.</p>
    <label for="cIn" class="sr">Código de tu escarapela</label><input type="text" id="cIn" placeholder="5E-XXXXXX" autocomplete="off" maxlength="9" required>
    <button class="btn">Ver mi grupo</button><p id="loginErr" role="alert" style="color:#FFB4A8;font-weight:700"></p>
  </form>
  <div id="espera" class="panel" hidden></div>
  <div id="p-fase" class="panel" role="tabpanel" aria-labelledby="t-fase" hidden></div>
  <div id="p-ficha" class="panel" role="tabpanel" aria-labelledby="t-ficha" hidden>
    <h2>Ficha de la célula</h2><p class="muted">Es de toda la célula: lo que escriban aquí lo ven sus compañeros y se guarda solo.</p><div id="campos"></div>
  </div>
  <div id="p-pliego" class="panel" role="tabpanel" aria-labelledby="t-pliego" hidden>
    <h2>Pliego, el copiloto de IA</h2><p class="muted">Conoce el reto de tu célula. Ayuda a pensar; las decisiones son de ustedes. No compartas datos personales. <span id="quedan"></span></p>
    <div class="chat" id="chat" aria-live="polite"></div>
    <form class="ask" id="ask"><label for="askIn" class="sr">Pregunta para Pliego</label><textarea id="askIn" placeholder="Escribe o pega un prompt…" maxlength="1200"></textarea><button class="btn">Enviar</button></form>
  </div>
</div></main>
<nav id="tabs" role="tablist" hidden><button role="tab" id="t-fase" aria-controls="p-fase" aria-selected="true">Fase</button><button role="tab" id="t-ficha" aria-controls="p-ficha" aria-selected="false">Ficha</button><button role="tab" id="t-pliego" aria-controls="p-pliego" aria-selected="false">Pliego</button></nav>
<a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener" aria-label="Hecho por Startin Lab">by <b>Startin*Lab</b></a>
<script>
(function(){
const $=s=>document.querySelector(s);const esc=s=>String(s??"").replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]));
const KEY="dvs_taller_c";let code=<?= json_encode($c) ?>||(()=>{try{return localStorage.getItem(KEY)||""}catch(e){return""}})();
let data=null,off=0,tab="fase",faseVista=null,hist=[];
try{hist=JSON.parse(sessionStorage.getItem("dvs_taller_chat")||"[]")}catch(e){}
const api=(body)=>fetch("../api/taller.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(body)}).then(r=>r.json());
function guardarCodigo(c){code=c;try{localStorage.setItem(KEY,c)}catch(e){}}
function mostrarLogin(msg){$("#login").hidden=false;$("#ubic").hidden=true;$("#tabs").hidden=true;$("#espera").hidden=true;["fase","ficha","pliego"].forEach(t=>$("#p-"+t).hidden=true);$("#salir").hidden=true;$("#loginErr").textContent=msg||"";$("#cIn").value=code||"";}
$("#login").addEventListener("submit",e=>{e.preventDefault();const v=$("#cIn").value.trim().toUpperCase();const m=v.match(/5E-?([A-Z0-9]{6})/);if(!m){$("#loginErr").textContent="El código tiene la forma 5E-XXXXXX.";return;}guardarCodigo("5E-"+m[1]);cargar();});
$("#salir").onclick=()=>{try{localStorage.removeItem(KEY);sessionStorage.removeItem("dvs_taller_chat")}catch(e){}code="";hist=[];mostrarLogin();};
async function cargar(){if(!code){mostrarLogin();return;}
  try{const r=await fetch("../api/taller.php?c="+encodeURIComponent(code),{cache:"no-store"});const j=await r.json();
    if(!j.ok){if(r.status===404||r.status===422){mostrarLogin(j.error);}return;}
    data=j;off=j.estado.ahora-Date.now()/1000;pintar();}catch(e){}}
function pintar(){const j=data,u=j.ubicacion,e=j.estado;$("#login").hidden=true;$("#salir").hidden=false;$("#ubic").hidden=false;
  document.body.dataset.g=u?u.grupo:"";
  let h="";
  if(!u){h=`<div class="card"><span class="k">Hola, ${esc(j.nombre)}</span><div class="big">Aún no tienes grupo</div><p class="muted">Tu grupo se asigna cuando registras tu ingreso en la mesa de entrada con tu escarapela.</p></div>`;}
  else{h=`<div class="card"><span class="k">Hola, ${esc(j.nombre)} · ${esc(u.eje)}</span><div class="big">Grupo ${+u.grupo}${u.celula?" · Célula "+(+u.celula):""}</div><div>Siéntate en la <b>Mesa ${+u.mesa}</b>${u.celula?" con tu célula "+(+u.celula):""}.</div>${u.reto?`<div class="reto">Reto: ${esc(u.reto)}</div>`:""}`;
    if(u.celula){const f=e.fase;h+=`<div class="fase"><span class="nom">${f?esc(f.nombre):"Aún no empieza"}</span><span class="t" id="reloj"></span></div><div class="dots" aria-hidden="true">${e.fases.map((x,i)=>`<i class="${f&&i<f.n?"on":""}${f&&i===f.n?" now":""}"></i>`).join("")}</div>`;}
    h+=`</div>`;}
  $("#ubic").innerHTML=h;
  const listo=!!(u&&u.celula);$("#tabs").hidden=!listo;$("#espera").hidden=listo;
  if(!listo){["fase","ficha","pliego"].forEach(t=>$("#p-"+t).hidden=true);
    $("#espera").innerHTML=u?`<h2>${esc(u.pregunta_eje)}</h2><p>A las 9:30, al iniciar el Laboratorio, tu grupo se divide en células de unas ocho personas. Aquí verás tu célula y tu reto: no tienes que recargar.</p><p class="muted">Cada célula trabaja un reto en tres fases (Diagnóstico, Análisis y Experimentación) con apoyo de Pliego, el agente de IA.</p>`:`<p>Cuando registres tu ingreso, aquí verás tu grupo y tu mesa.</p>`;return;}
  verTab(tab);pintarFase();pintarFicha();pintarQuedan();tick();
  const escribiendo=document.activeElement&&document.activeElement.tagName==="TEXTAREA";if(e.fase&&faseVista&&faseVista!==e.fase.id&&tab!=="fase"&&!escribiendo){verTab("fase");}faseVista=e.fase?e.fase.id:null;}
function pintarFase(){const e=data.estado,f=e.fase;let h="";
  if(!f){h=`<h2>Prepárense</h2><p>La persona facilitadora dará inicio en un momento. Mientras tanto, lean juntos su reto y escojan quién lleva el primer post-it al backing.</p>`;}
  else{h=`<h2>${esc(f.nombre)}</h2>`;if(f.pregunta)h+=`<p class="preg">${esc(f.pregunta)}</p>`;if(f.texto)h+=`<p>${esc(f.texto)}</p>`;
    if(f.pasos&&f.pasos.length)h+=`<ol class="pasos">${f.pasos.map(p=>`<li>${esc(p)}</li>`).join("")}</ol>`;
    const p=data.prompts&&data.prompts[f.id];
    if(p)h+=`<p class="muted" style="margin-top:12px">Prompt sugerido para Pliego:</p><div class="prompt" id="pr">${esc(p)}</div><div class="row"><button class="btn" type="button" id="usar">Preguntar a Pliego</button><button class="btn ghost" type="button" id="copiar">Copiar</button></div>`;
    if(f.campo)h+=`<div class="row"><button class="btn ghost" type="button" id="irFicha">Escribir en la ficha</button></div>`;
    if(f.id==="cierre")h+=`<div class="row"><a class="btn" href="../#evento">Ir al Muro de compromisos</a></div>`;}
  $("#p-fase").innerHTML=h;
  const p=data.prompts&&f&&data.prompts[f.id];
  if($("#usar"))$("#usar").onclick=()=>{verTab("pliego");preguntar(p);};
  if($("#copiar"))$("#copiar").onclick=async()=>{try{await navigator.clipboard.writeText(p);$("#copiar").textContent="Copiado";}catch(e){}};
  if($("#irFicha"))$("#irFicha").onclick=()=>verTab("ficha");}
const timers={};
function pintarFicha(){const box=$("#campos"),f=data.estado.fase;
  if(!box.dataset.listo){box.innerHTML=data.campos.map(c=>`<div class="campo" id="cp-${c.id}"><label for="f-${c.id}">${esc(c.titulo)}</label><small>${esc(c.ayuda)}</small><textarea id="f-${c.id}" maxlength="1500"></textarea><div class="estado-g" id="g-${c.id}" aria-live="polite"></div></div>`).join("");box.dataset.listo=1;
    data.campos.forEach(c=>{const t=$("#f-"+c.id);t.addEventListener("input",()=>{clearTimeout(timers[c.id]);$("#g-"+c.id).textContent="Escribiendo…";timers[c.id]=setTimeout(()=>guardar(c.id,t.value),1200);});t.addEventListener("blur",()=>{if(timers[c.id]){clearTimeout(timers[c.id]);guardar(c.id,t.value);}});});}
  data.campos.forEach(c=>{const t=$("#f-"+c.id);if(document.activeElement!==t&&!timers[c.id])t.value=data.ficha[c.id]||"";$("#cp-"+c.id).classList.toggle("now",!!(f&&f.campo===c.id));});}
async function guardar(id,v){delete timers[id];try{const j=await api({c:code,campo:id,valor:v});$("#g-"+id).textContent=j.ok?"Guardado":(j.error||"No se pudo guardar");if(j.ok){data.ficha=j.ficha;data.prompts=j.prompts;}}catch(e){$("#g-"+id).textContent="Sin conexión: se guardará cuando vuelvas a escribir.";}}
function pintarQuedan(){$("#quedan").textContent=data.agente_restante!=null?`Le quedan ${data.agente_restante} consultas a tu célula.`:"";}
function pintarChat(){$("#chat").innerHTML=hist.map(m=>`<div class="msg ${m.role==="user"?"u":"a"}">${esc(m.content)}</div>`).join("");const mm=document.querySelector("main");mm.scrollTop=mm.scrollHeight;}
async function preguntar(t){t=(t||"").trim();if(!t)return;hist.push({role:"user",content:t});hist.push({role:"assistant",content:"Pensando…"});pintarChat();
  try{const r=await fetch("../api/agente.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({modo:"taller",c:code,messages:hist.slice(0,-1).slice(-12)})});const j=await r.json();
    hist[hist.length-1].content=j.ok?j.answer:(j.error||"Pliego no pudo responder.");if(j.ok&&data.agente_restante)data.agente_restante--;}
  catch(e){hist[hist.length-1].content="Sin conexión. Intenta de nuevo o usa el prompt impreso.";}
  hist=hist.slice(-20);try{sessionStorage.setItem("dvs_taller_chat",JSON.stringify(hist))}catch(e){}pintarChat();pintarQuedan();}
$("#ask").addEventListener("submit",e=>{e.preventDefault();const v=$("#askIn").value;$("#askIn").value="";preguntar(v);});
function verTab(t){tab=t;["fase","ficha","pliego"].forEach(x=>{$("#t-"+x).setAttribute("aria-selected",x===t);$("#p-"+x).hidden=x!==t;});if(t==="pliego")pintarChat();}
["fase","ficha","pliego"].forEach(x=>$("#t-"+x).onclick=()=>verTab(x));
function tick(){const el=$("#reloj");if(!el||!data||!data.estado.fase){if(el)el.textContent="";return;}const s=Math.max(0,Math.round(data.estado.fase.fin-(Date.now()/1000+off)));el.textContent=s?`${Math.floor(s/60)}:${String(s%60).padStart(2,"0")}`:"Tiempo";el.classList.toggle("fin",s<60);}
setInterval(tick,1000);setInterval(()=>{if(code&&!document.hidden)cargar();},8000);
cargar();
})();
</script></body></html>
