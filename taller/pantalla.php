<?php declare(strict_types=1); header('Cache-Control: no-store'); ?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Laboratorio de IA · Pantalla · 5° Encuentro</title><link rel="icon" href="../favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<style>
:root{--ink:#0C1646;--ink2:#14215E;--sun:#FFC20E;--flame:#F26A21;--teal:#13A79A;--pink:#EE7DB9;--muted:#C9CDEA}
*{box-sizing:border-box}html,body{height:100%;margin:0}
body{font:clamp(14px,1.4vw,26px)/1.35 "elza","Segoe UI",system-ui,sans-serif;background:var(--ink);color:#fff;display:grid;grid-template-rows:auto 1fr auto;height:100vh;overflow:hidden;padding:2.2vh 3vw}
header{display:flex;align-items:center;gap:1.2vw}header .n{color:var(--sun);font-size:3.2em;font-weight:900;line-height:1}header b{font-size:1.1em;line-height:1.15}
.reloj{margin-left:auto;text-align:right}.reloj .f{font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-size:.9em}.reloj .t{font-size:4.4em;font-weight:900;line-height:1;color:var(--sun);font-variant-numeric:tabular-nums}.reloj .t.fin{color:var(--flame)}
main{display:grid;grid-template-rows:auto 1fr;gap:2.4vh;padding-top:2.4vh;min-height:0}
.preg{font-size:2.2em;font-weight:900;line-height:1.15;max-width:30ch}.sub{color:var(--muted);margin-top:.6vh}
.grupos{display:grid;grid-template-columns:repeat(3,1fr);gap:1.6vw;min-height:0}
.g{background:var(--ink2);border-radius:1.4vw;padding:2vh 1.4vw;border-top:.8vh solid var(--c);display:flex;flex-direction:column;gap:.8vh;min-height:0;overflow:hidden}
.g:nth-child(1){--c:var(--sun)}.g:nth-child(2){--c:var(--teal)}.g:nth-child(3){--c:var(--pink)}
.g h2{margin:0;font-size:1.35em}.g .k{color:var(--c);font-weight:800;text-transform:uppercase;letter-spacing:.06em;font-size:.8em}.g .num{font-size:2.6em;font-weight:900;line-height:1}
.cels{display:flex;gap:.5vw;flex-wrap:wrap}.cels span{background:rgba(255,255,255,.1);border-radius:999px;padding:.3vh .8vw;font-weight:700;font-size:.85em}
.dest{background:#fff;color:var(--ink);border-radius:1vw;padding:1.4vh 1vw;font-size:.92em;overflow:hidden}.dest b{display:block;font-size:.8em;color:#5A5F7D;text-transform:uppercase;letter-spacing:.05em;margin-top:.6vh}
footer{display:flex;align-items:center;gap:1.4vw;color:var(--muted)}footer .qr{background:#fff;border-radius:.8vw;padding:.6vw;width:9vh;height:9vh}footer .qr svg,footer .qr img{width:100%;height:100%;display:block}footer .u{font-weight:900;color:#fff;font-size:1.3em}
.dots{display:flex;gap:.4vw;margin-left:auto}.dots i{width:4vw;height:.8vh;border-radius:9px;background:rgba(255,255,255,.18)}.dots i.on{background:var(--teal)}.dots i.now{background:var(--sun)}
.startin-credit{position:fixed;right:1vw;bottom:1vh;font:600 .6em/1.2 "elza","Segoe UI",system-ui,sans-serif;color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.88);border-radius:999px;padding:4px 10px}.startin-credit b{color:#16162B;font-weight:800}
</style></head><body>
<header><span class="n">5°</span><b>Laboratorio de IA aplicada<br>a procesos de inclusión</b><div class="reloj"><div class="f" id="fase">Preparando</div><div class="t" id="t"></div></div></header>
<main><div><div class="preg" id="preg">De identificar barreras a experimentar soluciones.</div><div class="sub" id="sub">Tres grupos, una ruta: Diagnóstico, Análisis y Experimentación.</div></div><div class="grupos" id="grupos"></div></main>
<footer><div class="qr" id="qr" aria-hidden="true"></div><div>Tu grupo, tu célula, tu reto y Pliego en el celular:<div class="u">diversidad.startinlab.org/taller</div></div><div class="dots" id="dots" aria-hidden="true"></div></footer>
<a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener">by <b>Startin*Lab</b></a>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>
<script>
(function(){const $=s=>document.querySelector(s);const esc=s=>String(s??"").replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]));
try{const q=qrcode(0,"M");q.addData("https://diversidad.startinlab.org/taller/");q.make();$("#qr").innerHTML=q.createSvgTag({cellSize:4,margin:0,scalable:true});}catch(e){}
let d=null,off=0;
async function cargar(){try{const r=await fetch("../api/taller.php?solo=pantalla",{cache:"no-store"});const j=await r.json();if(j.ok){d=j;off=j.estado.ahora-Date.now()/1000;pintar();}}catch(e){}}
function pintar(){const e=d.estado,f=e.fase;
  $("#fase").textContent=f?f.nombre:(e.formado?"Células listas":"Preparando");
  $("#preg").textContent=f?(f.pregunta||f.texto||""):"De identificar barreras a experimentar soluciones.";
  $("#sub").textContent=f&&f.pregunta?"Escriban la respuesta en su ficha y lleven el post-it al backing de "+f.nombre+".":(f?"":"Tres grupos, una ruta: Diagnóstico, Análisis y Experimentación.");
  $("#dots").innerHTML=e.fases.map((x,i)=>`<i class="${f&&i<f.n?"on":""}${f&&i===f.n?" now":""}"></i>`).join("");
  const plen=f&&(f.id==="plenaria"||f.id==="cierre");
  $("#grupos").innerHTML=d.grupos.map(g=>{let h=`<div class="g"><span class="k">Grupo ${g.grupo} · Mesa ${g.grupo}</span><h2>${esc(g.eje)}</h2>`;
    if(plen&&g.destacada){const x=g.destacada;h+=`<div class="dest"><b>Célula ${x.celula} · ${esc(x.reto)}</b>${esc(x.solucion||x.barrera||"")}${x.primer_paso?`<b>Primer paso</b>${esc(x.primer_paso)}`:""}</div>`;}
    else{h+=`<div class="num">${g.personas}</div><div>personas</div>`;const cs=Object.entries(g.celulas);if(cs.length)h+=`<div class="cels">${cs.map(([c,n])=>`<span>Célula ${c} · ${n}</span>`).join("")}</div>`;}
    return h+`</div>`;}).join("");tick();}
function tick(){const el=$("#t");if(!d||!d.estado.fase){el.textContent="";return;}const s=Math.max(0,Math.round(d.estado.fase.fin-(Date.now()/1000+off)));el.textContent=s?`${Math.floor(s/60)}:${String(s%60).padStart(2,"0")}`:"Tiempo";el.classList.toggle("fin",s<60);}
setInterval(tick,1000);setInterval(cargar,5000);cargar();})();
</script></body></html>
