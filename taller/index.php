<?php
declare(strict_types=1);
header('Cache-Control: no-store');
$c = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($_GET['c'] ?? '')));
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#0C1646">
<title>Mi taller · Laboratorio de IA · 5° Encuentro</title><link rel="icon" href="../favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<style>
:root{--ink:#0C1646;--ink2:#14215E;--ink3:#1D2C74;--sun:#FFC20E;--flame:#F26A21;--teal:#13A79A;--pink:#EE7DB9;--violet:#6A47B8;--paper:#F7F4EE;--muted:#C9CDEA;--g:var(--sun);--g2:#D9A200;--f:"elza","Avenir Next","Segoe UI",system-ui,sans-serif}
body[data-g="2"]{--g:var(--teal);--g2:#0E8479}body[data-g="3"]{--g:var(--pink);--g2:#D2589C}
*{box-sizing:border-box}[hidden]{display:none!important}html,body{height:100%;margin:0}
body{font:16px/1.45 var(--f);background:var(--ink);color:#fff;height:100dvh;overflow:hidden;display:flex;flex-direction:column}
button{font:inherit;color:inherit}
.btn{border:0;border-radius:999px;min-height:48px;padding:12px 22px;font-weight:800;font-size:1.17rem;cursor:pointer;background:var(--flame);color:#fff;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:transform .15s}
.btn:active{transform:scale(.97)}.btn.ink{background:var(--ink);color:#fff}.btn.line{background:transparent;box-shadow:inset 0 0 0 2px currentColor}
:focus-visible{outline:3px solid var(--sun);outline-offset:3px}

/* ---------- Cabecera ---------- */
.top{display:flex;align-items:center;gap:10px;padding:calc(10px + env(safe-area-inset-top,0px)) 16px 10px;flex:none;position:relative;z-index:3}
.top .n{font:900 1.65rem/1 var(--f);color:var(--sun)}.top .t{font-weight:800;font-size:.86rem;line-height:1.1}.top .t small{display:block;font-weight:600;opacity:.75}
.icon{width:44px;height:44px;border-radius:50%;border:0;background:rgba(255,255,255,.1);display:grid;place-items:center;cursor:pointer;margin-left:auto}
.icon svg{width:20px;height:20px}
body.on .top{color:var(--ink)}body.on .top .n{color:var(--ink)}body.on .icon{background:rgba(12,22,70,.12)}

/* ---------- Papel doblado: el color del grupo ocupa la parte de arriba ---------- */
.papel{position:absolute;inset:0 0 auto 0;height:var(--alto,0);background:var(--g);transition:height .7s cubic-bezier(.2,.8,.2,1);z-index:0;overflow:hidden}
.papel::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.22) 0 38%,transparent 38%),linear-gradient(225deg,rgba(0,0,0,.08) 0 30%,transparent 30%);}
.papel::after{content:"";position:absolute;right:0;bottom:0;width:72px;height:72px;background:linear-gradient(315deg,var(--ink) 0 50%,var(--g2) 50%);filter:drop-shadow(-4px -4px 6px rgba(0,0,0,.15))}

/* ---------- Héroe de la célula ---------- */
.hero{position:relative;z-index:2;padding:4px 16px 18px;color:var(--ink);flex:none}
.hero .eje{font-weight:800;font-size:.74rem;letter-spacing:.09em;text-transform:uppercase;opacity:.85}
.hero .linea{display:flex;align-items:flex-end;gap:12px;margin-top:2px}
.hero .cel{font:900 clamp(3.4rem,19vw,5.2rem)/.85 var(--f);letter-spacing:-.04em}
.hero .cel small{display:block;font-size:.95rem;letter-spacing:.02em;font-weight:800;margin-bottom:4px}
.hero .mesa{margin-left:auto;text-align:right;font-weight:800;font-size:.9rem;line-height:1.15}.hero .mesa b{display:block;font-size:1.9rem;line-height:1}
.hero .reto{margin-top:10px;font-weight:800;font-size:1.04rem;line-height:1.28;max-width:30ch}

/* ---------- Fase con cronómetro en anillo ---------- */
.fase{position:relative;z-index:2;margin:-6px 16px 0;background:var(--ink2);border-radius:20px;padding:10px 12px;display:flex;align-items:center;gap:12px;flex:none;box-shadow:0 10px 24px rgba(0,0,0,.25)}
.anillo{width:58px;height:58px;flex:none;position:relative}.anillo svg{width:100%;height:100%;transform:rotate(-90deg)}
.anillo circle{fill:none;stroke-width:6}.anillo .bg{stroke:rgba(255,255,255,.14)}.anillo .fg{stroke:var(--sun);stroke-linecap:round;transition:stroke-dashoffset 1s linear,stroke .3s}
.anillo span{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;font-size:.86rem;font-variant-numeric:tabular-nums}
.fase.fin .anillo .fg{stroke:var(--flame)}.fase.fin .anillo{animation:late 1s ease-in-out infinite}
@keyframes late{50%{transform:scale(1.07)}}
.fase .nom{font-weight:900;font-size:1.12rem;line-height:1.1}.fase .nom small{display:block;font-weight:700;font-size:.74rem;color:var(--muted);letter-spacing:.06em;text-transform:uppercase}
.pliegues{display:flex;gap:4px;margin-top:6px}.pliegues i{width:18px;height:8px;background:rgba(255,255,255,.18);clip-path:polygon(0 0,100% 0,82% 100%,0 100%)}.pliegues i.ok{background:var(--g)}.pliegues i.ya{background:var(--sun)}

/* ---------- Paneles ---------- */
main{flex:1;min-height:0;overflow:auto;padding:16px 16px 64px;-webkit-overflow-scrolling:touch;position:relative;z-index:1}
h2{font:900 1.3rem/1.15 var(--f);margin:0 0 10px}
.preg{background:var(--g);color:var(--ink);border-radius:18px 18px 18px 4px;padding:14px 16px;font-weight:800;font-size:1.06rem;line-height:1.3;margin:0 0 14px}
.pasos{list-style:none;margin:0 0 14px;padding:0;display:grid;gap:8px;counter-reset:p}
.pasos li{counter-increment:p;display:grid;grid-template-columns:30px 1fr;gap:10px;align-items:start}
.pasos li::before{content:counter(p);width:30px;height:30px;border-radius:50%;background:var(--ink3);display:grid;place-items:center;font-weight:900;font-size:.9rem;color:var(--sun)}
.prompt{background:var(--paper);color:var(--ink);border-radius:18px;padding:14px;position:relative}
.prompt .k{font-size:.72rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#5A5F7D}
.prompt p{margin:6px 0 12px;font-size:.96rem}
.acc{display:flex;gap:8px;flex-wrap:wrap}.acc .btn{flex:1}
.muted{color:var(--muted);font-size:.92rem}
.campo{background:var(--ink2);border-radius:18px;padding:14px;margin-bottom:12px;border-left:6px solid transparent;position:relative}
.campo.now{border-left-color:var(--g);background:var(--ink3)}
.campo.now::after{content:"Ahora";position:absolute;top:12px;right:12px;background:var(--g);color:var(--ink);font-size:.7rem;font-weight:900;padding:3px 8px;border-radius:999px;letter-spacing:.04em}
.campo label{font-weight:900;display:block;font-size:1.02rem}.campo small{display:block;color:var(--muted);margin:2px 0 8px}
.campo.lleno label::before{content:"✓ ";color:var(--g)}
textarea,input[type=text]{width:100%;border-radius:14px;border:2px solid rgba(255,255,255,.2);background:#0A1240;color:#fff;font:16px/1.4 var(--f);padding:12px}
textarea{min-height:88px;resize:vertical}.guardado{font-size:.8rem;color:var(--muted);min-height:1.1em;margin-top:4px}
.avance{display:flex;align-items:center;gap:10px;margin-bottom:12px}.avance i{flex:1;height:8px;border-radius:9px;background:rgba(255,255,255,.14);overflow:hidden}.avance i b{display:block;height:100%;background:var(--g);transition:width .4s}
.chat{display:grid;gap:10px}.msg{padding:12px 14px;border-radius:18px;max-width:90%;white-space:pre-wrap;font-size:.96rem}
.msg.u{background:var(--g);color:var(--ink);justify-self:end;border-bottom-right-radius:4px;font-weight:600}.msg.a{background:var(--ink2);border-bottom-left-radius:4px}
.msg.a.pensando{color:var(--muted);font-style:italic}
.ask{position:sticky;bottom:0;display:flex;gap:8px;padding-top:10px;background:linear-gradient(transparent,var(--ink) 30%)}.ask textarea{min-height:48px;max-height:120px}
.ask .btn{width:52px;padding:0;flex:none}
.vacio{text-align:center;padding:10px 0}

/* ---------- Navegación inferior ---------- */
nav{flex:none;display:grid;grid-template-columns:repeat(3,1fr);background:var(--ink);border-top:1px solid rgba(255,255,255,.1);padding:6px 8px calc(6px + env(safe-area-inset-bottom,0px));position:relative;z-index:3}
nav button{background:none;border:0;display:grid;justify-items:center;gap:2px;padding:6px 0;font-weight:800;font-size:.78rem;color:var(--muted);cursor:pointer;border-radius:14px;min-height:52px}
nav button svg{width:24px;height:24px}nav button[aria-selected=true]{color:var(--ink);background:var(--g)}

/* ---------- Pantallas de entrada y espera ---------- */
.entrada{flex:1;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:0 20px 90px;position:relative;z-index:2}
.entrada h1{font:900 clamp(2.6rem,13vw,3.6rem)/.92 var(--f);margin:0;letter-spacing:-.03em}.entrada h1 span{color:var(--sun)}
.entrada p{margin:0;color:var(--muted)}.entrada input{font-size:1.25rem;text-align:center;text-transform:uppercase;letter-spacing:.05em;min-height:56px}
.entrada .err{color:#FFB4A8;font-weight:800;min-height:1.3em}
.gente{position:fixed;left:0;right:0;bottom:0;height:24dvh;display:flex;align-items:flex-end;justify-content:center;gap:0;pointer-events:none;z-index:1;overflow:hidden}
.gente img{height:100%;width:auto;margin:0 -2.2vw;filter:drop-shadow(0 6px 10px rgba(0,0,0,.35));transform-origin:bottom center;animation:sube .8s cubic-bezier(.2,.9,.3,1.2) both}
.gente img:nth-child(odd){height:86%}
@keyframes sube{from{transform:translateY(60%) scale(.9);opacity:0}}
.espera{flex:1;position:relative;z-index:2;display:flex;flex-direction:column;padding:4px 16px 0;color:var(--ink)}
.espera .grande{font:900 clamp(9rem,58vw,15rem)/.8 var(--f);letter-spacing:-.06em;margin:4px 0 0}
.espera .eje{font-weight:900;font-size:1.25rem;line-height:1.1}.espera .mesa{display:inline-block;margin-top:10px;background:var(--ink);color:#fff;border-radius:999px;padding:8px 16px;font-weight:900}
.espera .nota{margin-top:auto;margin-bottom:26dvh;background:var(--ink);color:#fff;border-radius:20px;padding:14px 16px;font-size:.95rem}
.espera .nota b{color:var(--sun)}
.pulso{display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--sun);margin-right:8px;animation:pulso 1.4s ease-in-out infinite}@keyframes pulso{50%{opacity:.25}}

/* ---------- Revelación y avisos a pantalla completa ---------- */
.capa{position:fixed;inset:0;z-index:50;background:var(--g);color:var(--ink);display:flex;flex-direction:column;justify-content:center;padding:24px;perspective:900px}
.capa .hoja{transform-origin:top center;animation:despliega .9s cubic-bezier(.2,.8,.2,1) both}
@keyframes despliega{from{transform:rotateX(-88deg);opacity:0}}
.capa .k{font-weight:900;letter-spacing:.1em;text-transform:uppercase;font-size:.85rem}
.capa .big{font:900 clamp(5rem,32vw,9rem)/.82 var(--f);letter-spacing:-.05em;margin:6px 0 10px}
.capa .txt{font-weight:800;font-size:1.15rem;line-height:1.3;max-width:28ch}
.capa .btn{margin-top:24px;align-self:flex-start;background:var(--ink)}
.capa::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.25) 0 40%,transparent 40%),linear-gradient(225deg,rgba(0,0,0,.08) 0 28%,transparent 28%);pointer-events:none}
.confeti{position:absolute;inset:0;pointer-events:none;overflow:hidden}.confeti i{position:absolute;top:-20px;width:14px;height:14px;clip-path:polygon(50% 0,100% 100%,0 100%);animation:cae linear forwards}
@keyframes cae{to{transform:translateY(110dvh) rotate(540deg)}}
.capa.fase-aviso{background:var(--ink);color:#fff}.capa.fase-aviso .big{color:var(--sun)}.capa.fase-aviso::before{opacity:.12}

.startin-credit{position:fixed;z-index:60;right:12px;bottom:calc(78px + env(safe-area-inset-bottom,0px));font:600 .66rem/1.2 var(--f);color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.9);border:1px solid #ECE7E4;border-radius:999px;padding:4px 10px;opacity:.9}.startin-credit b{color:#16162B;font-weight:800}
body.sin-nav .startin-credit{bottom:calc(12px + env(safe-area-inset-bottom,0px))}
.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
@media (min-width:700px){main,.hero,.entrada,.espera{max-width:620px;margin-left:auto;margin-right:auto;width:100%}.fase{max-width:588px;margin-left:auto;margin-right:auto}}
</style></head><body class="sin-nav">
<div class="papel" id="papel" aria-hidden="true"></div>
<header class="top"><span class="n">5°</span><span class="t">Laboratorio de IA<small>Mi taller</small></span><button class="icon" id="salir" type="button" hidden aria-label="Salir"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3M10 17l5-5-5-5M15 12H4"/></svg></button></header>

<form class="entrada" id="login" hidden>
  <h1>Tu célula<br>te <span>espera.</span></h1>
  <p>Escribe el código de tu escarapela (empieza por 5E) o tu número de cédula.</p>
  <label for="cIn" class="sr">Código de tu escarapela o número de cédula</label><input type="text" id="cIn" placeholder="5E-XXXXXX o cédula" autocomplete="off" maxlength="20" required>
  <button class="btn">Entrar a mi taller</button><p class="err" id="loginErr" role="alert"></p>
</form>
<div class="gente" id="gente" aria-hidden="true" hidden></div>

<section class="espera" id="espera" hidden aria-live="polite"></section>

<section class="hero" id="hero" hidden aria-live="polite"></section>
<div class="fase" id="fase" hidden><div class="anillo"><svg viewBox="0 0 64 64"><circle class="bg" cx="32" cy="32" r="27"/><circle class="fg" id="arco" cx="32" cy="32" r="27" stroke-dasharray="169.6" stroke-dashoffset="0"/></svg><span id="reloj">–</span></div><div><div class="nom" id="faseNom"></div><div class="pliegues" id="pliegues" aria-hidden="true"></div></div></div>
<main id="main" hidden>
  <div id="p-fase" role="tabpanel" aria-labelledby="t-fase"></div>
  <div id="p-ficha" role="tabpanel" aria-labelledby="t-ficha" hidden><h2>La ficha de tu célula</h2><div class="avance"><i><b id="avBar" style="width:0"></b></i><span class="muted" id="avTxt"></span></div><p class="muted" style="margin-top:0">Es de toda la célula: lo que escriban aquí lo ven todos y se guarda solo.</p><div id="campos"></div></div>
  <div id="p-pliego" role="tabpanel" aria-labelledby="t-pliego" hidden><h2>Pliego</h2><p class="muted" style="margin-top:-4px">Tu copiloto de IA ya conoce el reto. Ayuda a pensar; las decisiones son de ustedes. No compartas datos personales. <span id="quedan"></span></p><div class="chat" id="chat" aria-live="polite"></div>
    <form class="ask" id="ask"><label for="askIn" class="sr">Pregunta para Pliego</label><textarea id="askIn" placeholder="Pregúntale a Pliego…" maxlength="1200" rows="1"></textarea><button class="btn" aria-label="Enviar"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button></form></div>
</main>
<nav id="tabs" role="tablist" hidden>
  <button role="tab" id="t-fase" aria-controls="p-fase" aria-selected="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M4 4h16L4 20z"/><path d="M20 4v16H4"/></svg>Fase</button>
  <button role="tab" id="t-ficha" aria-controls="p-ficha" aria-selected="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5M9 13h7M9 17h5"/></svg>Ficha</button>
  <button role="tab" id="t-pliego" aria-controls="p-pliego" aria-selected="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"/><path d="M9 10h.01M13 10h.01M17 10h.01"/></svg>Pliego</button>
</nav>
<div id="capa"></div>
<a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener" aria-label="Hecho por Startin Lab">by <b>Startin*Lab</b></a>
<script>
(function(){
const $=s=>document.querySelector(s);const esc=s=>String(s??"").replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]));
const reducido=matchMedia("(prefers-reduced-motion: reduce)").matches;
const FIG=["afro","ciego","indigena","lgbt","mayor","migrante","mujer","nina","silla"];
const KEY="dvs_taller_c";let code=<?= json_encode($c) ?>||(()=>{try{return localStorage.getItem(KEY)||""}catch(e){return""}})();
let data=null,off=0,tab="fase",faseVista=undefined,celVista=undefined,hist=[];
try{hist=JSON.parse(sessionStorage.getItem("dvs_taller_chat")||"[]")}catch(e){}
const ss=(k,v)=>{try{if(v===undefined)return sessionStorage.getItem(k);sessionStorage.setItem(k,v)}catch(e){}};
const api=(body)=>fetch("../api/taller.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(body)}).then(r=>r.json());
function guardarCodigo(c){code=c;try{localStorage.setItem(KEY,c)}catch(e){}}
function vista(v){// login | espera | activo
  $("#login").hidden=v!=="login";$("#gente").hidden=v==="activo";$("#espera").hidden=v!=="espera";
  ["#hero","#fase","#main","#tabs"].forEach(s=>$(s).hidden=v!=="activo");$("#salir").hidden=v==="login";
  document.body.classList.toggle("on",v!=="login");document.body.classList.toggle("sin-nav",v!=="activo");
  const alto=v==="login"?"0px":v==="espera"?"100dvh":($(".top").offsetHeight+$("#hero").offsetHeight+34)+"px";$("#papel").style.setProperty("--alto",alto);}
function gente(n){const g=$("#gente");if(g.dataset.n==n)return;g.dataset.n=n;let h="";for(let i=0;i<n;i++){const f=FIG[(i*4+(data&&data.ubicacion?data.ubicacion.grupo:0))%FIG.length];h+=`<img src="../assets/fig/${f}.webp?v=x" alt="" style="animation-delay:${i*90}ms">`;}g.innerHTML=h;}
function mostrarLogin(msg){vista("login");gente(9);$("#loginErr").textContent=msg||"";$("#cIn").value=/^5E-/.test(code||"")?code:"";}
$("#login").addEventListener("submit",async e=>{e.preventDefault();const v=$("#cIn").value.trim().toUpperCase();const m=v.match(/^5E-?([A-Z0-9]{6})$/);
  if(m){guardarCodigo("5E-"+m[1]);cargar(true);return;}
  const doc=v.replace(/[^0-9A-Z]/g,"");if(doc.length<5){$("#loginErr").textContent="Escribe el código 5E-XXXXXX o tu número de cédula.";return;}
  $("#loginErr").textContent="Buscando…";
  try{const j=await api({doc});if(!j.ok){$("#loginErr").textContent=j.error||"No encontramos ese documento.";return;}
    $("#cIn").value="";guardarCodigo(j.codigo);data=j;off=j.estado.ahora-Date.now()/1000;pintar();}catch(err){$("#loginErr").textContent="Sin conexión. Intenta de nuevo.";}});
$("#salir").onclick=()=>{try{localStorage.removeItem(KEY);sessionStorage.removeItem("dvs_taller_chat")}catch(e){}code="";hist=[];data=null;faseVista=celVista=undefined;mostrarLogin();};
async function cargar(){if(!code){mostrarLogin();return;}
  try{const r=await fetch("../api/taller.php?c="+encodeURIComponent(code),{cache:"no-store"});const j=await r.json();
    if(!j.ok){if(r.status===404||r.status===422)mostrarLogin(j.error);return;}
    data=j;off=j.estado.ahora-Date.now()/1000;pintar();}catch(e){}}

function pintar(){const j=data,u=j.ubicacion,e=j.estado;document.body.dataset.g=u?u.grupo:"";
  if(!u||!u.celula){vista("espera");gente(u?5:3);
    $("#espera").innerHTML=u
      ?`<div class="eje">Hola, ${esc(j.nombre)}. Tu grupo es</div><div class="grande" aria-label="Grupo ${+u.grupo}">${+u.grupo}</div><div class="eje">${esc(u.eje)}</div><div><span class="mesa">Siéntate en la Mesa ${+u.mesa}</span></div><div class="nota"><span class="pulso" aria-hidden="true"></span>A las <b>9:30</b> se forma tu célula y aquí aparece con su reto. No tienes que recargar.</div>`
      :`<div class="eje">Hola, ${esc(j.nombre)}.</div><div class="grande" style="font-size:clamp(5rem,30vw,8rem)">¿Ya llegaste?</div><div class="nota">Tu grupo aparece cuando registras tu ingreso en la mesa de entrada con tu escarapela.</div>`;
    celVista=u?0:celVista;return;}
  const f=e.fase;
  $("#hero").innerHTML=`<div class="eje">Hola, ${esc(j.nombre)} · Grupo ${+u.grupo} · ${esc(u.eje)}</div><div class="linea"><div class="cel"><small>Tu célula</small>${+u.celula}</div><div class="mesa">Mesa<b>${+u.mesa}</b></div></div><div class="reto">${esc(u.reto)}</div>`;
  vista("activo");
  const etq=!f?"Laboratorio de IA":(f.n>=1&&f.n<=3?`Fase ${f.n} de 3`:(f.n===0?"Arranca":"Cierre del taller"));
  $("#faseNom").innerHTML=`<small>${etq}</small>${f?esc(f.nombre):"Aún no empieza"}`;
  $("#pliegues").innerHTML=e.fases.map((x,i)=>`<i class="${f&&i<f.n?"ok":""}${f&&i===f.n?" ya":""}"></i>`).join("");
  pintarFase();pintarFicha();pintarQuedan();tick();verTab(tab);
  // Revelación de la célula (una vez por persona) y aviso a pantalla completa en cada cambio de fase.
  const clave="dvs_cel_"+j.codigo;
  if(!ss(clave)){ss(clave,"1");revelar(u);}
  else if(f&&faseVista!==undefined&&faseVista!==f.id){avisoFase(f);const esc2=document.activeElement&&document.activeElement.tagName==="TEXTAREA";if(tab!=="fase"&&!esc2)verTab("fase");}
  faseVista=f?f.id:null;}

function confeti(c){if(reducido)return"";let h="";const cs=["#FFC20E","#F26A21","#13A79A","#EE7DB9","#6A47B8","#fff"];for(let i=0;i<34;i++)h+=`<i style="left:${Math.random()*100}%;background:${cs[i%cs.length]};animation-duration:${2.2+Math.random()*1.8}s;animation-delay:${Math.random()*.6}s"></i>`;return`<div class="confeti">${h}</div>`;}
function revelar(u){const c=$("#capa");c.innerHTML=`<div class="capa" role="dialog" aria-modal="true" aria-labelledby="revT">${confeti()}<div class="hoja"><div class="k">Grupo ${+u.grupo} · Mesa ${+u.mesa}</div><div class="big" id="revT">Célula ${+u.celula}</div><div class="txt">Tu reto: ${esc(u.reto)}</div></div><button class="btn" type="button" id="revOk">¡Vamos!</button></div>`;
  $("#revOk").onclick=()=>{c.innerHTML="";};$("#revOk").focus();}
function avisoFase(f){const c=$("#capa");c.innerHTML=`<div class="capa fase-aviso" role="alert"><div class="hoja"><div class="k">${f.n&&f.n<4?"Fase "+f.n+" de 3":"Ahora"}</div><div class="big">${esc(f.nombre)}</div><div class="txt">${esc(f.pregunta||f.texto||"")}</div></div><button class="btn" type="button" id="avOk" style="background:var(--flame)">Empezar · ${f.min} min</button></div>`;
  const cerrar=()=>{c.innerHTML="";};$("#avOk").onclick=cerrar;setTimeout(()=>{if($("#avOk"))cerrar();},6000);}

function pintarFase(){const f=data.estado.fase;let h="";
  if(!f){h=`<h2>Prepárense</h2><p>La persona facilitadora dará inicio en un momento.</p><ol class="pasos"><li>Busquen a su célula en la Mesa ${+data.ubicacion.mesa}.</li><li>Lean juntos el reto de arriba.</li><li>Escojan quién lleva el primer post-it al backing.</li></ol>`;}
  else{h=`<h2>${esc(f.nombre)}</h2>`;if(f.pregunta)h+=`<p class="preg">${esc(f.pregunta)}</p>`;if(f.texto)h+=`<p class="preg">${esc(f.texto)}</p>`;
    if(f.pasos&&f.pasos.length)h+=`<ol class="pasos">${f.pasos.map(p=>`<li>${esc(p)}</li>`).join("")}</ol>`;
    const p=data.prompts&&data.prompts[f.id];
    if(p)h+=`<div class="prompt"><div class="k">Prompt sugerido para Pliego</div><p>${esc(p)}</p><div class="acc"><button class="btn" type="button" id="usar">Preguntar a Pliego</button><button class="btn line" type="button" id="copiar" style="color:var(--ink)">Copiar</button></div></div>`;
    if(f.campo)h+=`<div class="acc" style="margin-top:12px"><button class="btn ink" type="button" id="irFicha" style="background:var(--ink3)">Escribir en la ficha</button></div>`;
    if(f.id==="cierre")h+=`<div class="acc" style="margin-top:12px"><a class="btn" href="../#evento">Dejar mi compromiso</a></div>`;}
  $("#p-fase").innerHTML=h;
  const p=data.prompts&&f&&data.prompts[f.id];
  if($("#usar"))$("#usar").onclick=()=>{verTab("pliego");preguntar(p);};
  if($("#copiar"))$("#copiar").onclick=async()=>{try{await navigator.clipboard.writeText(p);$("#copiar").textContent="¡Copiado!";}catch(e){}};
  if($("#irFicha"))$("#irFicha").onclick=()=>{verTab("ficha");const t=$("#f-"+f.campo);if(t){t.scrollIntoView({block:"center",behavior:reducido?"auto":"smooth"});}};}
const timers={};
function pintarFicha(){const box=$("#campos"),f=data.estado.fase;
  if(!box.dataset.listo){box.innerHTML=data.campos.map(c=>`<div class="campo" id="cp-${c.id}"><label for="f-${c.id}">${esc(c.titulo)}</label><small>${esc(c.ayuda)}</small><textarea id="f-${c.id}" maxlength="1500"></textarea><div class="guardado" id="g-${c.id}" aria-live="polite"></div></div>`).join("");box.dataset.listo=1;
    data.campos.forEach(c=>{const t=$("#f-"+c.id);t.addEventListener("input",()=>{clearTimeout(timers[c.id]);$("#g-"+c.id).textContent="Escribiendo…";timers[c.id]=setTimeout(()=>guardar(c.id,t.value),1200);});t.addEventListener("blur",()=>{if(timers[c.id]){clearTimeout(timers[c.id]);guardar(c.id,t.value);}});});}
  let n=0;data.campos.forEach(c=>{const t=$("#f-"+c.id);if(document.activeElement!==t&&!timers[c.id])t.value=data.ficha[c.id]||"";const lleno=!!(data.ficha[c.id]||"").trim();if(lleno)n++;
    $("#cp-"+c.id).classList.toggle("now",!!(f&&f.campo===c.id));$("#cp-"+c.id).classList.toggle("lleno",lleno);});
  $("#avBar").style.width=(n/data.campos.length*100)+"%";$("#avTxt").textContent=`${n} de ${data.campos.length}`;}
async function guardar(id,v){delete timers[id];try{const j=await api({c:code,campo:id,valor:v});$("#g-"+id).textContent=j.ok?"Guardado ✓":(j.error||"No se pudo guardar");if(j.ok){data.ficha=j.ficha;data.prompts=j.prompts;pintarFicha();}}catch(e){$("#g-"+id).textContent="Sin conexión: se guardará cuando vuelvas a escribir.";}}
function pintarQuedan(){$("#quedan").textContent=data.agente_restante!=null?`A tu célula le quedan ${data.agente_restante} consultas.`:"";}
function pintarChat(){$("#chat").innerHTML=hist.length?hist.map(m=>`<div class="msg ${m.role==="user"?"u":"a"}${m.p?" pensando":""}">${esc(m.content)}</div>`).join(""):`<p class="muted vacio">Empieza con el prompt sugerido de la fase o escribe tu pregunta.</p>`;const mm=$("#main");mm.scrollTop=mm.scrollHeight;}
async function preguntar(t){t=(t||"").trim();if(!t)return;hist.push({role:"user",content:t});hist.push({role:"assistant",content:"Pliego está pensando…",p:1});pintarChat();
  try{const r=await fetch("../api/agente.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({modo:"taller",c:code,messages:hist.slice(0,-1).slice(-12).map(m=>({role:m.role,content:m.content}))})});const j=await r.json();
    hist[hist.length-1]={role:"assistant",content:j.ok?j.answer:(j.error||"Pliego no pudo responder.")};if(j.ok&&data.agente_restante)data.agente_restante--;}
  catch(e){hist[hist.length-1]={role:"assistant",content:"Sin conexión. Intenta de nuevo o usa el prompt impreso."};}
  hist=hist.slice(-20);ss("dvs_taller_chat",JSON.stringify(hist));pintarChat();pintarQuedan();}
$("#ask").addEventListener("submit",e=>{e.preventDefault();const v=$("#askIn").value;$("#askIn").value="";preguntar(v);});
$("#askIn").addEventListener("keydown",e=>{if(e.key==="Enter"&&!e.shiftKey){e.preventDefault();$("#ask").requestSubmit();}});
function verTab(t){tab=t;["fase","ficha","pliego"].forEach(x=>{$("#t-"+x).setAttribute("aria-selected",x===t);$("#p-"+x).hidden=x!==t;});if(t==="pliego")pintarChat();else $("#main").scrollTop=0;}
["fase","ficha","pliego"].forEach(x=>$("#t-"+x).onclick=()=>verTab(x));
function tick(){if(!data||!data.ubicacion||!data.ubicacion.celula)return;const f=data.estado.fase,el=$("#reloj"),arco=$("#arco");
  if(!f){el.textContent="–";arco.style.strokeDashoffset=169.6;$("#fase").classList.remove("fin");return;}
  const tot=f.min*60,s=Math.max(0,Math.round(f.fin-(Date.now()/1000+off)));
  el.textContent=s?`${Math.floor(s/60)}:${String(s%60).padStart(2,"0")}`:"¡Ya!";arco.style.strokeDashoffset=169.6*(1-s/tot);$("#fase").classList.toggle("fin",s<60);}
addEventListener("resize",()=>{if(data&&data.ubicacion&&data.ubicacion.celula)vista("activo");});
setInterval(tick,1000);setInterval(()=>{if(code&&!document.hidden)cargar();},8000);
cargar();
})();
</script></body></html>
