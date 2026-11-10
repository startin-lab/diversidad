<?php
declare(strict_types=1);
header('Cache-Control: no-store');
$c = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($_GET['c'] ?? '')));
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover,interactive-widget=resizes-content"><meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#0C1646">
<title>Mi taller · Laboratorio de IA · 5° Encuentro</title><link rel="icon" href="../favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<style>
:root{--ink:#0C1646;--ink2:#14215E;--ink3:#1D2C74;--sun:#FFC20E;--flame:#F26A21;--teal:#13A79A;--pink:#EE7DB9;--violet:#6A47B8;--paper:#F7F4EE;--muted:#C9CDEA;--g:var(--sun);--g2:#D9A200;--f:"elza","Avenir Next","Segoe UI",system-ui,sans-serif}
body[data-g="2"]{--g:var(--teal);--g2:#0E8479}body[data-g="3"]{--g:var(--pink);--g2:#D2589C}
*{box-sizing:border-box}[hidden]{display:none!important}html,body{height:100%;margin:0}
html{background:var(--ink)}
body{font:16px/1.45 var(--f);background:var(--ink);color:#fff;height:var(--vp,100dvh);overflow:hidden;display:flex;flex-direction:column;position:relative}
button{font:inherit;color:inherit}
.btn{border:0;border-radius:999px;min-height:48px;padding:12px 22px;font-weight:800;font-size:1.17rem;cursor:pointer;background:var(--flame);color:#fff;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:transform .15s}
.btn:active{transform:scale(.97)}.btn.ink{background:var(--ink3)}.btn.line{background:transparent;box-shadow:inset 0 0 0 2px currentColor}
:focus-visible{outline:3px solid var(--sun);outline-offset:3px}
.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}

/* ---------- Entrada y espera (papel doblado a pantalla completa) ---------- */
.top{display:flex;align-items:center;gap:10px;padding:calc(10px + env(safe-area-inset-top,0px)) 16px 10px;flex:none;position:relative;z-index:3}
.top .n{font:900 1.65rem/1 var(--f);color:var(--sun)}.top .t{font-weight:800;font-size:.86rem;line-height:1.1}.top .t small{display:block;font-weight:600;opacity:.75}
.icon{width:44px;height:44px;border-radius:50%;border:0;background:rgba(255,255,255,.1);display:grid;place-items:center;cursor:pointer;margin-left:auto}.icon svg{width:20px;height:20px}
body.espera-on .top{color:var(--ink)}body.espera-on .top .n{color:var(--ink)}body.espera-on .icon{background:rgba(12,22,70,.12)}
.papel{position:absolute;inset:0;background:var(--g);z-index:0;overflow:hidden}
.papel::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.22) 0 38%,transparent 38%),linear-gradient(225deg,rgba(0,0,0,.08) 0 30%,transparent 30%)}
.entrada{flex:1;display:flex;flex-direction:column;justify-content:center;gap:14px;padding:0 20px 90px;position:relative;z-index:2}
.entrada h1{font:900 clamp(2.6rem,13vw,3.6rem)/.92 var(--f);margin:0;letter-spacing:-.03em}.entrada h1 span{color:var(--sun)}
.entrada p{margin:0;color:var(--muted)}.entrada input{font-size:1.25rem;text-align:center;text-transform:uppercase;letter-spacing:.05em;min-height:56px}
.entrada .err{color:#FFB4A8;font-weight:800;min-height:1.3em}
.gente{position:fixed;left:12px;right:12px;bottom:0;height:20dvh;display:flex;align-items:flex-end;justify-content:space-evenly;gap:6px;pointer-events:none;z-index:1}
.gente img{height:100%;width:auto;max-width:18%;object-fit:contain;object-position:bottom;filter:drop-shadow(0 6px 10px rgba(0,0,0,.35));transform-origin:bottom center;animation:sube .8s cubic-bezier(.2,.9,.3,1.2) both}
@keyframes sube{from{transform:translateY(60%) scale(.9);opacity:0}}
.espera{flex:1;position:relative;z-index:2;display:flex;flex-direction:column;padding:4px 16px 0;color:var(--ink)}
.espera .grande{font:900 clamp(9rem,58vw,15rem)/.8 var(--f);letter-spacing:-.06em;margin:4px 0 0}
.espera .eje{font-weight:900;font-size:1.25rem;line-height:1.1}.espera .chip{display:inline-block;margin-top:10px;background:var(--ink);color:#fff;border-radius:999px;padding:8px 16px;font-weight:900}
.espera .nota{margin-top:auto;margin-bottom:26dvh;background:var(--ink);color:#fff;border-radius:20px;padding:14px 16px;font-size:.95rem}.espera .nota b{color:var(--sun)}
.pulso{display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--sun);margin-right:8px;animation:pulso 1.4s ease-in-out infinite}@keyframes pulso{50%{opacity:.25}}
textarea,input[type=text]{width:100%;border-radius:14px;border:2px solid rgba(255,255,255,.2);background:#0A1240;color:#fff;font:16px/1.4 var(--f);padding:12px}

/* ---------- Vista de trabajo: barra de fase + 4 pestañas a pantalla completa ---------- */
.barra{flex:none;background:var(--g);color:var(--ink);padding:calc(8px + env(safe-area-inset-top,0px)) 12px 8px 16px;display:flex;align-items:center;gap:12px;position:relative;overflow:hidden;z-index:3}
.barra::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.22) 0 46%,transparent 46%);pointer-events:none}
.barra .info{position:relative;min-width:0;flex:1}
.barra .k{font-weight:900;font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;opacity:.8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.barra .nom{font:900 1.25rem/1.1 var(--f);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pliegues{display:flex;gap:3px;margin-top:5px}.pliegues i{width:16px;height:6px;background:rgba(12,22,70,.18);clip-path:polygon(0 0,100% 0,82% 100%,0 100%)}.pliegues i.ok{background:rgba(12,22,70,.55)}.pliegues i.ya{background:var(--ink)}
.anillo{width:54px;height:54px;flex:none;position:relative}.anillo svg{width:100%;height:100%;transform:rotate(-90deg)}
.anillo circle{fill:none;stroke-width:6}.anillo .bg{stroke:rgba(12,22,70,.15)}.anillo .fg{stroke:var(--ink);stroke-linecap:round;transition:stroke-dashoffset 1s linear,stroke .3s}
.anillo span{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;font-size:.82rem;font-variant-numeric:tabular-nums}
.barra.fin .anillo .fg{stroke:#B3261E}.barra.fin .anillo span{color:#8E1B14}.barra.fin .anillo{animation:late 1s ease-in-out infinite}@keyframes late{50%{transform:scale(1.08)}}
.tablero{flex:1;min-height:0;position:relative}
.panel{position:absolute;inset:0;overflow:auto;-webkit-overflow-scrolling:touch;padding:16px 16px 28px;overscroll-behavior:contain}
.panel h2{font:900 1.35rem/1.15 var(--f);margin:0 0 10px}
.reto-mini{background:var(--ink2);border-left:6px solid var(--g);border-radius:14px;padding:10px 12px;margin-bottom:14px;font-size:.92rem}.reto-mini b{display:block;font-size:.7rem;letter-spacing:.08em;text-transform:uppercase;color:var(--g)}
.preg{background:var(--g);color:var(--ink);border-radius:18px 18px 18px 4px;padding:14px 16px;font-weight:800;font-size:1.06rem;line-height:1.3;margin:0 0 14px}
.pasos{list-style:none;margin:0 0 14px;padding:0;display:grid;gap:8px;counter-reset:p}
.pasos li{counter-increment:p;display:grid;grid-template-columns:30px 1fr;gap:10px;align-items:start}
.pasos li::before{content:counter(p);width:30px;height:30px;border-radius:50%;background:var(--ink3);display:grid;place-items:center;font-weight:900;font-size:.9rem;color:var(--sun)}
.prompt{background:var(--paper);color:var(--ink);border-radius:18px;padding:14px}.prompt .k{font-size:.72rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#5A5F7D}.prompt p{margin:6px 0 12px;font-size:.96rem}
.acc{display:flex;gap:8px;flex-wrap:wrap}.acc .btn{flex:1}
.muted{color:var(--muted);font-size:.92rem}
.campo{background:var(--ink2);border-radius:18px;padding:14px;margin-bottom:12px;border-left:6px solid transparent;position:relative}
.campo.now{border-left-color:var(--g);background:var(--ink3)}
.campo.now::after{content:"Ahora";position:absolute;top:12px;right:12px;background:var(--g);color:var(--ink);font-size:.7rem;font-weight:900;padding:3px 8px;border-radius:999px}
.campo label{font-weight:900;display:block;font-size:1.02rem;padding-right:56px}.campo small{display:block;color:var(--muted);margin:2px 0 8px}.campo.lleno label::before{content:"✓ ";color:var(--g)}
.campo textarea{min-height:96px;resize:vertical}.guardado{font-size:.8rem;color:var(--muted);min-height:1.1em;margin-top:4px}
.avance{display:flex;align-items:center;gap:10px;margin-bottom:8px}.avance i{flex:1;height:8px;border-radius:9px;background:rgba(255,255,255,.14);overflow:hidden}.avance i b{display:block;height:100%;background:var(--g);transition:width .4s}

/* Pliego: conversación a pantalla completa, como un chat. */
.panel.chat-panel{padding:0;display:flex;flex-direction:column;overflow:hidden}
.chat-top{flex:none;display:flex;align-items:center;gap:8px;padding:8px 12px 8px 16px;border-bottom:1px solid rgba(255,255,255,.08);font-size:.82rem;color:var(--muted)}.chat-top b{color:#fff}
.chat-top button{margin-left:auto;display:inline-flex;align-items:center;gap:6px;background:var(--ink3);border:0;border-radius:999px;padding:8px 14px;font-weight:800;font-size:.85rem;cursor:pointer;min-height:38px}
.chat-top button svg{width:16px;height:16px}
/* Landing de la célula */
.landing{margin-top:16px;background:var(--paper);color:var(--ink);border-radius:22px;padding:16px;position:relative;overflow:hidden}
.landing::before{content:"";position:absolute;right:0;top:0;width:46px;height:46px;background:linear-gradient(225deg,var(--ink) 0 50%,var(--g) 50%)}
.landing>*{position:relative}.landing .k{font-weight:900;font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:#4B537F}
.landing h3{font:900 1.35rem/1.1 var(--f);margin:6px 0 6px}.landing p{margin:0 0 12px;font-size:.95rem}
.landing .btn{width:100%;margin-top:8px}.landing .btn.ver{background:var(--ink)}
.landing textarea{background:#fff;color:var(--ink);border-color:#D8DBEA;min-height:70px}
.landing .err{color:#B3261E;font-weight:800;font-size:.9rem;margin-top:8px}
.armando{display:flex;align-items:center;gap:12px}.armando i{flex:none;width:44px;height:44px;background:var(--g);clip-path:polygon(0 0,100% 0,0 100%);animation:dobla 1.2s ease-in-out infinite}
@keyframes dobla{50%{clip-path:polygon(0 0,100% 100%,0 100%)}}
.verlanding{display:inline-flex;margin-top:10px;font-weight:800;color:var(--g);text-decoration:underline}
.mensajes{flex:1;min-height:0;overflow:auto;padding:16px 14px 8px;display:flex;flex-direction:column;gap:12px;overscroll-behavior:contain}
.burbuja{max-width:88%;padding:11px 14px;border-radius:20px;font-size:.98rem;line-height:1.45;word-wrap:break-word}
.burbuja.u{align-self:flex-end;background:var(--g);color:var(--ink);border-bottom-right-radius:6px;font-weight:600;white-space:pre-wrap}
.burbuja.a{align-self:flex-start;background:var(--ink2);border-bottom-left-radius:6px}
.burbuja.a p{margin:0 0 8px}.burbuja.a p:last-child{margin-bottom:0}.burbuja.a ul,.burbuja.a ol{margin:4px 0 8px;padding-left:20px}.burbuja.a li{margin:3px 0}.burbuja.a b{color:#fff}
.burbuja.err{background:#3A1630;color:#FFD3DC}
.quien{display:flex;align-items:center;gap:8px;font-size:.75rem;font-weight:800;color:var(--muted);margin-bottom:4px}.quien i{width:22px;height:22px;border-radius:50%;background:var(--g);display:grid;place-items:center;color:var(--ink);font-style:normal;font-weight:900;font-size:.72rem}
.copiar{margin-top:8px;background:none;border:1px solid rgba(255,255,255,.25);border-radius:999px;padding:5px 12px;font-size:.78rem;font-weight:800;color:var(--muted);cursor:pointer}
.escribe{display:inline-flex;gap:4px;padding:4px 0}.escribe i{width:8px;height:8px;border-radius:50%;background:var(--muted);animation:punto 1.2s infinite}.escribe i:nth-child(2){animation-delay:.2s}.escribe i:nth-child(3){animation-delay:.4s}
@keyframes punto{0%,80%,100%{opacity:.25;transform:translateY(0)}40%{opacity:1;transform:translateY(-4px)}}
.bienvenida-chat{margin:auto 0;text-align:center;padding:10px 6px}.bienvenida-chat .av{width:64px;height:64px;margin:0 auto 10px;border-radius:20px;background:var(--g);display:grid;place-items:center;color:var(--ink);font:900 1.8rem/1 var(--f);transform:rotate(-6deg)}
.bienvenida-chat h3{font:900 1.3rem/1.15 var(--f);margin:0 0 6px}
.chips{display:grid;gap:8px;margin-top:14px;text-align:left}.chips button{background:var(--ink2);border:1px solid rgba(255,255,255,.14);border-radius:16px;padding:12px 14px;font-weight:700;font-size:.92rem;cursor:pointer;text-align:left}.chips button b{display:block;font-size:.7rem;letter-spacing:.08em;text-transform:uppercase;color:var(--g);margin-bottom:2px}
.compositor{flex:none;padding:8px 12px calc(10px);background:var(--ink);border-top:1px solid rgba(255,255,255,.08)}
.compositor .fila{display:flex;align-items:flex-end;gap:8px;background:#0A1240;border:2px solid rgba(255,255,255,.18);border-radius:24px;padding:6px 6px 6px 14px}
.compositor .fila:focus-within{border-color:var(--g)}
.compositor textarea{border:0;background:none;padding:8px 0;min-height:24px;max-height:132px;resize:none;outline:none;line-height:1.4}
.enviar{flex:none;width:42px;height:42px;border-radius:50%;border:0;background:var(--g);color:var(--ink);display:grid;place-items:center;cursor:pointer}.enviar:disabled{opacity:.4;cursor:default}.enviar svg{width:20px;height:20px}
.compositor .pie{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-top:6px;font-size:.74rem;color:var(--muted)}
.credito-in{font:600 .66rem/1.2 var(--f);color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.9);border-radius:999px;padding:3px 9px;white-space:nowrap}.credito-in b{color:#16162B;font-weight:800}

/* Puesta en común: votación de la idea que presenta el grupo */
.opcion{background:var(--ink2);border-radius:20px;padding:14px;margin-bottom:12px;border:2px solid transparent;position:relative}
.opcion.mia{border-color:rgba(255,255,255,.18)}.opcion.voto{border-color:var(--g)}
.opcion .cab{display:flex;align-items:center;gap:8px;flex-wrap:wrap}.opcion .cab b{font:900 1.15rem/1 var(--f)}
.tag{font-size:.68rem;font-weight:900;letter-spacing:.06em;text-transform:uppercase;background:var(--ink3);color:var(--muted);border-radius:999px;padding:3px 8px}.tag.g{background:var(--g);color:var(--ink)}
.opcion .r{font-size:.82rem;color:var(--muted);margin:6px 0 8px}.opcion .s{font-weight:700;font-size:1rem;line-height:1.35}.opcion .pp{font-size:.88rem;margin-top:6px;color:#E3E6F7}
.barra-voto{display:flex;align-items:center;gap:10px;margin:12px 0 10px}.barra-voto i{flex:1;height:10px;border-radius:9px;background:rgba(255,255,255,.12);overflow:hidden}.barra-voto i b{display:block;height:100%;background:var(--g);transition:width .5s}.barra-voto span{font-weight:900;font-size:.9rem;min-width:62px;text-align:right}
.opcion .btn{width:100%;min-height:46px;font-size:1.17rem}.opcion.voto .btn{background:var(--g);color:var(--ink)}
.presenta{background:var(--g);color:var(--ink);border-radius:24px;padding:18px;position:relative;overflow:hidden;margin-bottom:14px}
.presenta::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.24) 0 42%,transparent 42%);pointer-events:none}.presenta>*{position:relative}
.presenta .k{font-weight:900;font-size:.74rem;letter-spacing:.1em;text-transform:uppercase}.presenta .cel{font:900 clamp(3.4rem,20vw,5rem)/.85 var(--f);letter-spacing:-.04em;margin:6px 0 8px}.presenta .s{font-weight:800;font-size:1.05rem;line-height:1.35}.presenta .pp{margin-top:8px;font-size:.95rem}
.presenta .es-tuya{margin-top:12px;background:var(--ink);color:#fff;border-radius:14px;padding:10px 12px;font-weight:800}

/* Compromiso para el muro final */
.comp{margin-top:16px;background:var(--g);color:var(--ink);border-radius:22px;padding:16px;position:relative;overflow:hidden}
.comp::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.25) 0 44%,transparent 44%);pointer-events:none}.comp>*:not(.sr){position:relative}
.comp h3{font:900 1.35rem/1.1 var(--f);margin:0 0 6px}.comp p{margin:0 0 10px;font-size:.95rem}
.comp textarea{background:#fff;color:var(--ink);border-color:rgba(12,22,70,.2);min-height:90px}
.comp .chk{display:flex;gap:10px;align-items:center;margin:10px 0 2px;font-weight:700;font-size:.95rem}.comp .chk input{width:22px;height:22px;accent-color:var(--ink)}
.comp .btn{width:100%;margin-top:10px;background:var(--ink)}.comp .nota-muro{background:#fff;border-radius:16px;padding:14px;font-weight:800;font-size:1.05rem;line-height:1.35}
.comp .nota-muro small{display:block;font-weight:700;color:#4B537F;margin-top:6px;font-size:.85rem}

/* Mi célula */
.yo{background:var(--g);color:var(--ink);border-radius:24px;padding:18px;position:relative;overflow:hidden}
.yo::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.24) 0 42%,transparent 42%);pointer-events:none}
.yo::after{content:"";position:absolute;right:0;top:0;width:54px;height:54px;background:linear-gradient(225deg,var(--ink) 0 50%,var(--g2) 50%)}
.yo>*{position:relative}.yo .k{font-weight:900;font-size:.74rem;letter-spacing:.1em;text-transform:uppercase}
.yo .cel{font:900 clamp(4rem,24vw,6rem)/.85 var(--f);letter-spacing:-.04em;margin:6px 0 4px}
.yo .reto{font-weight:800;font-size:1.06rem;line-height:1.3;margin-top:10px}
.datos{display:grid;gap:10px;margin-top:14px}.datos div{background:var(--ink2);border-radius:16px;padding:12px 14px}.datos b{display:block;font-size:.7rem;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}.datos span{font-weight:800;font-size:1.02rem}

nav{flex:none;display:grid;grid-template-columns:repeat(4,1fr);gap:4px;background:var(--ink);border-top:1px solid rgba(255,255,255,.1);padding:6px 6px calc(6px + env(safe-area-inset-bottom,0px));position:relative;z-index:3}
nav button{background:none;border:0;display:grid;justify-items:center;gap:2px;padding:6px 0;font-weight:800;font-size:.74rem;color:var(--muted);cursor:pointer;border-radius:14px;min-height:52px}
nav button svg{width:23px;height:23px}nav button[aria-selected=true]{color:var(--ink);background:var(--g)}
body.teclado nav{display:none}

/* ---------- Avisos a pantalla completa ---------- */
.capa{position:fixed;inset:0;z-index:50;background:var(--g);color:var(--ink);display:flex;flex-direction:column;justify-content:center;padding:24px;perspective:900px;overflow:hidden}
.capa .hoja{transform-origin:top center;animation:despliega .9s cubic-bezier(.2,.8,.2,1) both;max-width:100%}
@keyframes despliega{from{transform:rotateX(-88deg);opacity:0}}
.capa .k{font-weight:900;letter-spacing:.1em;text-transform:uppercase;font-size:.85rem}
.capa .big{font-weight:900;line-height:.9;letter-spacing:-.04em;margin:6px 0 12px;font-family:var(--f);white-space:nowrap}
.capa .txt{font-weight:800;font-size:1.12rem;line-height:1.3;max-width:30ch}
.capa .btn{margin-top:24px;align-self:flex-start;background:var(--ink)}
.capa::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.25) 0 40%,transparent 40%),linear-gradient(225deg,rgba(0,0,0,.08) 0 28%,transparent 28%);pointer-events:none}
.confeti{position:absolute;inset:0;pointer-events:none;overflow:hidden}.confeti i{position:absolute;top:-20px;width:14px;height:14px;clip-path:polygon(50% 0,100% 100%,0 100%);animation:cae linear forwards}
@keyframes cae{to{transform:translateY(110dvh) rotate(540deg)}}
.capa.fase-aviso{background:var(--ink);color:#fff}.capa.fase-aviso .big{color:var(--sun)}.capa.fase-aviso::before{opacity:.12}
.toast{position:fixed;left:12px;right:12px;top:calc(76px + env(safe-area-inset-top,0px));z-index:40;background:var(--sun);color:var(--ink);border-radius:16px;padding:12px 14px;font-weight:800;box-shadow:0 10px 24px rgba(0,0,0,.3);animation:baja .4s ease-out both}
@keyframes baja{from{transform:translateY(-20px);opacity:0}}

.startin-credit{position:fixed;z-index:60;right:12px;bottom:calc(12px + env(safe-area-inset-bottom,0px));font:600 .66rem/1.2 var(--f);color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.9);border:1px solid #ECE7E4;border-radius:999px;padding:4px 10px;opacity:.9}.startin-credit b{color:#16162B;font-weight:800}
body.trabajo .startin-credit{bottom:calc(74px + env(safe-area-inset-bottom,0px))}
body.trabajo.en-pliego .startin-credit{display:none}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
@media (min-width:700px){.panel>*,.mensajes>*{max-width:620px;margin-left:auto;margin-right:auto;width:100%}.burbuja.u{margin-right:max(0px,calc((100% - 620px)/2))}.compositor>*{max-width:620px;margin-left:auto;margin-right:auto}.entrada,.espera{max-width:620px;margin-left:auto;margin-right:auto;width:100%}}
</style></head><body>
<div class="papel" id="papel" aria-hidden="true" hidden></div>
<header class="top" id="top"><span class="n">5°</span><span class="t">Laboratorio de IA<small>Mi taller</small></span><button class="icon" id="salir" type="button" hidden aria-label="Salir"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3M10 17l5-5-5-5M15 12H4"/></svg></button></header>

<form class="entrada" id="login" hidden>
  <h1>Tu célula<br>te <span>espera.</span></h1>
  <p>Escribe el código de tu escarapela (empieza por 5E) o tu número de cédula.</p>
  <label for="cIn" class="sr">Código de tu escarapela o número de cédula</label><input type="text" id="cIn" placeholder="5E-XXXXXX o cédula" autocomplete="off" maxlength="20" required>
  <button class="btn">Entrar a mi taller</button><p class="err" id="loginErr" role="alert"></p>
</form>
<div class="gente" id="gente" aria-hidden="true" hidden></div>
<section class="espera" id="espera" hidden aria-live="polite"></section>

<header class="barra" id="barra" hidden><div class="info"><div class="k" id="faseK"></div><div class="nom" id="faseNom"></div><div class="pliegues" id="pliegues" aria-hidden="true"></div></div>
  <div class="anillo" role="timer" aria-label="Tiempo restante de la fase"><svg viewBox="0 0 64 64"><circle class="bg" cx="32" cy="32" r="27"/><circle class="fg" id="arco" cx="32" cy="32" r="27" stroke-dasharray="169.6" stroke-dashoffset="169.6"/></svg><span id="reloj">–</span></div></header>
<div class="tablero" id="tablero" hidden>
  <section class="panel" id="p-fase" role="tabpanel" aria-labelledby="t-fase"></section>
  <section class="panel" id="p-ficha" role="tabpanel" aria-labelledby="t-ficha" hidden><h2>Ficha de la célula</h2><div class="avance"><i><b id="avBar" style="width:0"></b></i><span class="muted" id="avTxt"></span></div><p class="muted" style="margin:0 0 12px">Es de toda la célula: lo que escriban aquí lo ven todos y se guarda solo.</p><div id="campos"></div></section>
  <section class="panel chat-panel" id="p-pliego" role="tabpanel" aria-labelledby="t-pliego" hidden>
    <div class="chat-top"><span><b>Pliego</b> · ya conoce su reto y su ficha</span><button type="button" id="nuevoChat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>Nuevo chat</button></div>
    <div class="mensajes" id="mensajes" aria-live="polite"></div>
    <form class="compositor" id="ask"><div class="fila"><label for="askIn" class="sr">Mensaje para Pliego</label><textarea id="askIn" rows="1" placeholder="Escríbele a Pliego…" maxlength="1200" enterkeyhint="send"></textarea><button class="enviar" id="enviar" aria-label="Enviar" disabled><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button></div>
      <div class="pie"><span id="quedan">Pliego ayuda a pensar; las decisiones son de ustedes.</span><a class="credito-in" href="https://startinlab.org/" target="_blank" rel="noopener">by <b>Startin*Lab</b></a></div></form>
  </section>
  <section class="panel" id="p-yo" role="tabpanel" aria-labelledby="t-yo" hidden></section>
</div>
<nav id="tabs" role="tablist" hidden>
  <button role="tab" id="t-fase" aria-controls="p-fase" aria-selected="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M4 4h16L4 20z"/><path d="M20 4v16H4"/></svg>Fase</button>
  <button role="tab" id="t-ficha" aria-controls="p-ficha" aria-selected="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6z"/><path d="M14 3v5h5M9 13h7M9 17h5"/></svg>Ficha</button>
  <button role="tab" id="t-pliego" aria-controls="p-pliego" aria-selected="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"/><path d="M9 10h.01M13 10h.01M17 10h.01"/></svg>Pliego</button>
  <button role="tab" id="t-yo" aria-controls="p-yo" aria-selected="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>Mi célula</button>
</nav>
<div id="capa"></div><div id="toast"></div>
<a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener" aria-label="Hecho por Startin Lab">by <b>Startin*Lab</b></a>
<script>
(function(){
const $=s=>document.querySelector(s);const esc=s=>String(s??"").replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]));
const reducido=matchMedia("(prefers-reduced-motion: reduce)").matches,tactil=matchMedia("(pointer: coarse)").matches;
const FIG=["afro","ciego","indigena","lgbt","mayor","migrante","mujer","nina","silla"];
const KEY="dvs_taller_c";let code=<?= json_encode($c) ?>||(()=>{try{return localStorage.getItem(KEY)||""}catch(e){return""}})();
let data=null,off=0,tab="fase",faseVista,firmaFase="",firmaYo="",hist=[],esperando=false,modo="";
try{hist=JSON.parse(sessionStorage.getItem("dvs_taller_chat")||"[]")}catch(e){}
const ss=(k,v)=>{try{if(v===undefined)return sessionStorage.getItem(k);sessionStorage.setItem(k,v)}catch(e){}};
const api=(body)=>fetch("../api/taller.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(body)}).then(r=>r.json());
function guardarCodigo(c){code=c;try{localStorage.setItem(KEY,c)}catch(e){}}

// Alto real visible (con teclado abierto el compositor queda siempre a la vista).
function alto(){const v=window.visualViewport;document.body.style.setProperty("--vp",(v?v.height:innerHeight)+"px");if(v)window.scrollTo(0,0);}
if(window.visualViewport){visualViewport.addEventListener("resize",alto);visualViewport.addEventListener("scroll",alto);}alto();

function vista(v){if(modo===v)return;modo=v;// login | espera | trabajo
  $("#top").hidden=v==="trabajo";$("#login").hidden=v!=="login";$("#gente").hidden=v==="trabajo";$("#espera").hidden=v!=="espera";$("#papel").hidden=v!=="espera";
  ["#barra","#tablero","#tabs"].forEach(s=>$(s).hidden=v!=="trabajo");$("#salir").hidden=v==="login";
  document.body.classList.toggle("espera-on",v==="espera");document.body.classList.toggle("trabajo",v==="trabajo");}
function gente(n){const g=$("#gente");if(g.dataset.n==n)return;g.dataset.n=n;let h="";for(let i=0;i<n;i++){const f=FIG[(i*2+(data&&data.ubicacion?data.ubicacion.grupo*3:0))%FIG.length];h+=`<img src="../assets/fig/${f}.webp?v=x" alt="" style="animation-delay:${i*90}ms">`;}g.innerHTML=h;}
function mostrarLogin(msg){vista("login");gente(5);$("#loginErr").textContent=msg||"";$("#cIn").value=/^5E-/.test(code||"")?code:"";}
$("#login").addEventListener("submit",async e=>{e.preventDefault();const v=$("#cIn").value.trim().toUpperCase();const m=v.match(/^5E-?([A-Z0-9]{6})$/);
  if(m){guardarCodigo("5E-"+m[1]);cargar();return;}
  const doc=v.replace(/[^0-9A-Z]/g,"");if(doc.length<5){$("#loginErr").textContent="Escribe el código 5E-XXXXXX o tu número de cédula.";return;}
  $("#loginErr").textContent="Buscando…";
  try{const j=await api({doc});if(!j.ok){$("#loginErr").textContent=j.error||"No encontramos ese documento.";return;}
    $("#cIn").value="";guardarCodigo(j.codigo);recibir(j);}catch(err){$("#loginErr").textContent="Sin conexión. Intenta de nuevo.";}});
function salir(){try{localStorage.removeItem(KEY);sessionStorage.removeItem("dvs_taller_chat")}catch(e){}code="";hist=[];data=null;faseVista=undefined;firmaFase=firmaYo="";modo="";mostrarLogin();}
$("#salir").onclick=salir;
async function cargar(){if(!code){mostrarLogin();return;}
  try{const r=await fetch("../api/taller.php?c="+encodeURIComponent(code),{cache:"no-store"});const j=await r.json();
    if(!j.ok){if(r.status===404||r.status===422)mostrarLogin(j.error);return;}recibir(j);}catch(e){}}
function recibir(j){data=j;off=j.estado.ahora-Date.now()/1000;pintar();}

// Cada actualización solo toca lo que cambió: nunca la conversación ni el campo que alguien está escribiendo.
function pintar(){const j=data,u=j.ubicacion,e=j.estado;document.body.dataset.g=u?u.grupo:"";
  if(j.facilitador){vista("espera");gente(5);document.body.dataset.g="";const h=`<div class="eje">Hola, ${esc(j.nombre)}.</div><div class="grande" style="font-size:clamp(2.6rem,13.5vw,5rem);line-height:.92;margin:10px 0;letter-spacing:-.03em">Facilitas el laboratorio</div><div class="eje">No entras al reparto de grupos ni a la votación.</div><div class="nota" style="margin-bottom:24dvh"><b>Para guiar el taller:</b> las fases, las células y la votación se manejan desde el <a href="../admin/taller.php" style="color:#FFC20E">panel del taller</a>, y la <a href="pantalla.php" style="color:#FFC20E">pantalla del auditorio</a> muestra todo en vivo.</div>`;if($("#espera").dataset.h!==h){$("#espera").innerHTML=h;$("#espera").dataset.h=h;}return;}
  if(!u||!u.celula){vista("espera");gente(3);
    const h=u?`<div class="eje">Hola, ${esc(j.nombre)}. Tu grupo es</div><div class="grande" aria-label="Grupo ${+u.grupo}">${+u.grupo}</div><div class="eje">${esc(u.eje)}</div><div><span class="chip">1 de los 3 grandes grupos</span></div><div class="nota"><span class="pulso" aria-hidden="true"></span>A las <b>9:30</b> se forma tu célula y aquí aparece con su reto. No tienes que recargar.</div>`
      :`<div class="eje">Hola, ${esc(j.nombre)}.</div><div class="grande" style="font-size:clamp(5rem,30vw,8rem)">¿Ya llegaste?</div><div class="nota">Tu grupo aparece cuando registras tu ingreso en la mesa de entrada con tu escarapela.</div>`;
    if($("#espera").dataset.h!==h){$("#espera").innerHTML=h;$("#espera").dataset.h=h;}return;}
  const nueva=modo!=="trabajo";vista("trabajo");
  const f=e.fase;
  $("#faseK").textContent=!f?`Grupo ${u.grupo} · Célula ${u.celula}`:(f.n>=1&&f.n<=3?`Fase ${f.n} de 3 · Grupo ${u.grupo} · Célula ${u.celula}`:`Grupo ${u.grupo} · Célula ${u.celula}`);
  $("#faseNom").textContent=f?f.nombre:"Aún no empieza";
  $("#pliegues").innerHTML=e.fases.map((x,i)=>`<i class="${f&&i<f.n?"ok":""}${f&&i===f.n?" ya":""}"></i>`).join("");
  const ff=(f?f.id:"-")+"|"+JSON.stringify(f&&j.prompts?j.prompts[f.id]:"")+"|"+JSON.stringify(j.plenaria||"")+"|"+JSON.stringify(j.landing||"")+armando+landErr+"|"+JSON.stringify(compromiso===undefined?"?":compromiso)+editandoComp+compErr;
  const editandoFase=document.activeElement&&document.activeElement.closest&&document.activeElement.closest("#p-fase")&&document.activeElement.tagName==="TEXTAREA";
  if(ff!==firmaFase&&!editandoFase){const sc=$("#p-fase").scrollTop;firmaFase=ff;pintarFase();$("#p-fase").scrollTop=f&&faseVista!==f.id?0:sc;}
  const fy=JSON.stringify(u)+j.nombre;if(fy!==firmaYo){firmaYo=fy;pintarYo();}
  pintarFicha();pintarQuedan();tick();
  if(nueva){verTab(tab);if(tab==="pliego")pintarChat(true);}
  const clave="dvs_cel_"+j.codigo;
  if(!ss(clave)){ss(clave,"1");revelar(u);}
  else if(f&&faseVista!==undefined&&faseVista!==f.id)avisoFase(f);
  faseVista=f?f.id:null;}

function confeti(){if(reducido)return"";let h="";const cs=["#FFC20E","#F26A21","#13A79A","#EE7DB9","#6A47B8","#fff"];for(let i=0;i<34;i++)h+=`<i style="left:${Math.random()*100}%;background:${cs[i%cs.length]};animation-duration:${2.2+Math.random()*1.8}s;animation-delay:${Math.random()*.6}s"></i>`;return`<div class="confeti">${h}</div>`;}
// El título grande se ajusta al ancho de la pantalla, sin cortarse («Experimentación» incluido).
function ajustar(el){if(!el)return;el.style.fontSize="";const max=Math.min(144,innerWidth*.34);let s=max;el.style.fontSize=s+"px";const ancho=el.parentElement.clientWidth;while(el.scrollWidth>ancho&&s>28){s-=2;el.style.fontSize=s+"px";}}
function revelar(u){const c=$("#capa");c.innerHTML=`<div class="capa" role="dialog" aria-modal="true" aria-labelledby="revT">${confeti()}<div class="hoja"><div class="k">Grupo ${+u.grupo} · ${esc(u.eje)}</div><div class="big" id="revT">Célula ${+u.celula}</div><div class="txt">Tu reto: ${esc(u.reto)}</div></div><button class="btn" type="button" id="revOk">¡Vamos!</button></div>`;
  ajustar($("#revT"));$("#revOk").onclick=()=>{c.innerHTML="";};$("#revOk").focus();}
function avisoFase(f){const enUso=document.activeElement&&/TEXTAREA|INPUT/.test(document.activeElement.tagName);
  if(enUso||esperando){const t=$("#toast");t.innerHTML=`<div class="toast" role="status">Empezó ${esc(f.nombre)}${f.min?" · "+f.min+" min":""}. Mira la pestaña Fase cuando termines.</div>`;setTimeout(()=>t.innerHTML="",5000);return;}
  const c=$("#capa");c.innerHTML=`<div class="capa fase-aviso" role="alert"><div class="hoja"><div class="k">${f.n>=1&&f.n<=3?"Fase "+f.n+" de 3":"Ahora"}</div><div class="big" id="avT">${esc(f.nombre)}</div><div class="txt">${esc(f.pregunta||f.texto||"")}</div></div><button class="btn" type="button" id="avOk" style="background:var(--flame)">Empezar${f.min?" · "+f.min+" min":""}</button></div>`;
  ajustar($("#avT"));const cerrar=()=>{c.innerHTML="";verTab("fase");};$("#avOk").onclick=cerrar;setTimeout(()=>{if($("#avOk"))c.innerHTML="";},7000);}

function pintarFase(){const f=data.estado.fase,u=data.ubicacion;let h=`<div class="reto-mini"><b>Reto de su célula</b>${esc(u.reto)}</div>`;
  if(!f){h+=`<h2>Prepárense</h2><p>La persona facilitadora dará inicio en un momento.</p><ol class="pasos"><li>Reúnanse con su célula: la persona facilitadora del grupo les indica dónde.</li><li>Lean juntos el reto.</li><li>Escojan quién lleva el primer post-it al backing.</li></ol>`;}
  else{h+=`<h2>${esc(f.nombre)}</h2>`;if(f.pregunta||f.texto)h+=`<p class="preg">${esc(f.pregunta||f.texto)}</p>`;
    if(f.pasos&&f.pasos.length)h+=`<ol class="pasos">${f.pasos.map(p=>`<li>${esc(p)}</li>`).join("")}</ol>`;
    const p=data.prompts&&data.prompts[f.id];
    if(p)h+=`<div class="prompt"><div class="k">Prompt sugerido para Pliego</div><p>${esc(p)}</p><div class="acc"><button class="btn" type="button" id="usar">Preguntar a Pliego</button><button class="btn line" type="button" id="copiar" style="color:var(--ink)">Copiar</button></div></div>`;
    if(f.campo)h+=`<div class="acc" style="margin-top:12px"><button class="btn ink" type="button" id="irFicha">Escribir en la ficha</button></div>`;
    if(f.id==="experimentacion")h+=tarjetaLanding(true);else if(data.landing&&(f.id==="plenaria"||f.id==="cierre"))h+=tarjetaLanding(false);
    if(data.plenaria)h+=votacion(f);
    if(f.id==="cierre")h+=tarjetaCompromiso();}
  $("#p-fase").innerHTML=h;
  $("#p-fase").querySelectorAll("[data-votar]").forEach(b=>b.onclick=()=>votar(+b.dataset.votar,b));
  if($("#crearLanding"))$("#crearLanding").onclick=()=>crearLanding("");
  if($("#pegarComp"))$("#pegarComp").onclick=pegarCompromiso;if($("#editarComp"))$("#editarComp").onclick=()=>{editandoComp=true;firmaFase="";pintar();};
  if(f&&f.id==="cierre"&&compromiso===undefined)cargarCompromiso();
  if($("#ajustarLanding"))$("#ajustarLanding").onclick=()=>{const v=$("#instrLanding").value.trim();if(!v){$("#instrLanding").focus();return;}crearLanding(v);};
  const p=data.prompts&&f&&data.prompts[f.id];
  if($("#usar"))$("#usar").onclick=()=>{verTab("pliego");preguntar(p);};
  if($("#copiar"))$("#copiar").onclick=async()=>{try{await navigator.clipboard.writeText(p);$("#copiar").textContent="¡Copiado!";}catch(e){}};
  if($("#irFicha"))$("#irFicha").onclick=()=>{verTab("ficha");const c=$("#cp-"+f.campo);if(c)c.scrollIntoView({block:"start",behavior:reducido?"auto":"smooth"});};}

// Puesta en común: cada persona vota por la idea que presenta su grupo; quien facilita la confirma.
function votacion(f){const pl=data.plenaria,u=data.ubicacion;
  if(pl.destacada){const o=pl.opciones.find(x=>x.celula===pl.destacada)||{};
    return`<div class="presenta" role="status"><div class="k">Presenta el Grupo ${+u.grupo}</div><div class="cel">Célula ${+pl.destacada}</div><div class="s">${esc(o.solucion||o.barrera||o.reto||"")}</div>${o.primer_paso?`<div class="pp"><b>Primer paso:</b> ${esc(o.primer_paso)}</div>`:""}${o.landing?`<a class="btn ver" style="margin-top:12px;background:var(--ink);width:100%" href="${esc(o.landing.url)}" target="_blank" rel="noopener">Ver la landing</a>`:""}${pl.destacada===u.celula?`<div class="es-tuya">¡Es la idea de tu célula! Prepárense para contarla en un minuto.</div>`:""}</div>`;}
  if(f.id!=="plenaria")return"";
  const total=pl.opciones.reduce((a,o)=>a+o.votos,0)||0;
  if(pl.opciones.length<2)return`<div class="presenta"><div class="k">Grupo ${+u.grupo}</div><div class="cel">Célula ${+u.celula}</div><div class="s">Su grupo tiene una sola célula: ustedes presentan la idea del grupo.</div></div>`;
  return`<h2 style="margin-top:18px">Elijan la idea que presenta su grupo</h2><p class="muted" style="margin:-4px 0 12px">Vota por la idea de otra célula del Grupo ${+u.grupo}: por la tuya no se puede. Puedes cambiar tu voto hasta que la persona facilitadora confirme.</p>`+
    pl.opciones.map(o=>{const mia=o.celula===u.celula,voto=pl.mi_voto===o.celula,pct=total?Math.round(o.votos/total*100):0;
      return`<div class="opcion${mia?" mia":""}${voto?" voto":""}"><div class="cab"><b>Célula ${o.celula}</b>${mia?`<span class="tag g">Tu célula</span>`:""}${voto?`<span class="tag">Tu voto</span>`:""}</div><div class="r">${esc(o.reto)}</div><div class="s">${o.solucion?esc(o.solucion):`<span class="muted">Aún no escriben su solución en la ficha.</span>`}</div>${o.primer_paso?`<div class="pp"><b>Primer paso:</b> ${esc(o.primer_paso)}</div>`:""}${o.landing?`<a class="verlanding" href="${esc(o.landing.url)}" target="_blank" rel="noopener">Ver su landing: ${esc(o.landing.titulo)}</a>`:""}
        <div class="barra-voto" aria-label="${o.votos} votos"><i><b style="width:${pct}%"></b></i><span>${o.votos} ${o.votos===1?"voto":"votos"}</span></div>${mia?`<p class="muted" style="margin:0;text-align:center">Es tu célula: no puedes votar por ella.</p>`:`<button class="btn${voto?"":" ink"}" type="button" data-votar="${o.celula}" ${voto?"aria-pressed=\"true\"":""}>${voto?"✓ Votaste por esta":"Votar por esta idea"}</button>`}</div>`;}).join("");}
async function votar(cel,b){b.disabled=true;b.textContent="Votando…";try{const j=await api({c:code,accion:"votar",celula:cel});if(j.ok){firmaFase="";recibir(j);}else{b.textContent=j.error||"No se pudo votar";}}catch(e){b.textContent="Sin conexión. Intenta de nuevo.";}finally{b.disabled=false;}}

// ---- Landing de la solución (Experimentación) ----
let armando=false,landErr="";
function tarjetaLanding(editable){const L=data.landing;
  if(armando)return`<div class="landing" role="status"><div class="armando"><i aria-hidden="true"></i><div><b>Pliego está armando su landing…</b><br><span style="font-size:.9rem">Toma su reto, la ficha y lo que han conversado. Puede tardar hasta un minuto.</span></div></div></div>`;
  if(!L)return editable?`<div class="landing"><div class="k">Su solución, en una landing</div><h3>Que se vea lo que imaginan</h3><p>Pliego toma su reto, la ficha y lo que han conversado, y arma una página con el problema, la solución, cómo funciona y un mockup de lo que imaginan. Luego la ajustan con sus palabras.</p><button class="btn" type="button" id="crearLanding">Crear nuestra landing</button>${landErr?`<div class="err" role="alert">${esc(landErr)}</div>`:""}</div>`:"";
  return`<div class="landing"><div class="k">Landing de su célula</div><h3>${esc(L.titulo)}</h3><p>${esc(L.lema)}</p><a class="btn ver" href="${esc(L.url)}" target="_blank" rel="noopener">Ver nuestra landing</a>${editable?`<label for="instrLanding" style="display:block;font-weight:800;margin-top:14px">¿Qué le cambiamos?</label><textarea id="instrLanding" maxlength="600" placeholder="Ej.: hazlo un chatbot, más sencillo, pensado para personas mayores…"></textarea><button class="btn" type="button" id="ajustarLanding">Ajustar la landing</button>`:""}${landErr?`<div class="err" role="alert">${esc(landErr)}</div>`:""}</div>`;}
async function crearLanding(instr){if(armando)return;armando=true;landErr="";firmaFase="";pintar();
  try{const r=await fetch("../api/landing.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({c:code,instruccion:instr,contexto:hist.filter(m=>!m.p&&!m.err).slice(-8).map(m=>({role:m.role,content:m.content}))})});
    const raw=await r.text();const j=JSON.parse(raw.slice(Math.max(0,raw.indexOf("{"))));
    if(j.ok){data.landing={titulo:j.landing.titulo,lema:j.landing.lema,url:j.url};const t=$("#toast");t.innerHTML=`<div class="toast" role="status">¡Listo! Su landing «${esc(j.landing.titulo)}» está lista.</div>`;setTimeout(()=>t.innerHTML="",5000);}
    else landErr=j.error||"No se pudo crear la landing.";}
  catch(e){landErr="Sin conexión o Pliego tardó demasiado. Intenta de nuevo.";}
  armando=false;firmaFase="";pintar();cargar();}

// ---- Compromiso para el muro final (Cierre) ----
let compromiso,editandoComp=false,compErr="";
async function cargarCompromiso(){compromiso=null;try{const r=await fetch("../api/muro.php?n=10&c="+encodeURIComponent(code),{cache:"no-store"});const j=await r.json();if(j.ok)compromiso=j.mio||null;}catch(e){}firmaFase="";pintar();}
function tarjetaCompromiso(){const n=esc(data.nombre);
  if(compromiso&&!editandoComp)return`<div class="comp"><h3>Tu compromiso está en el muro</h3><div class="nota-muro">${esc(compromiso.texto)}<small>— ${compromiso.nombre?esc(compromiso.nombre)+" · ":""}${esc(compromiso.organizacion||"tu organización")}${compromiso.nombre?" · con tu foto":""}</small></div><button class="btn" type="button" id="editarComp">Editar mi compromiso</button></div>`;
  return`<div class="comp"><h3>Dobla tu compromiso</h3><p>Aparecerá en el muro final, en la pantalla del auditorio.</p><label for="compIn" class="sr">Tu compromiso</label><textarea id="compIn" maxlength="200" placeholder="Mi organización se compromete a…">${compromiso?esc(compromiso.texto):""}</textarea>
    <label class="chk"><input type="checkbox" id="compNom" ${compromiso&&compromiso.nombre?"checked":""}> Mostrar mi nombre y la foto de mi escarapela en el muro del auditorio</label><p style="font-size:.82rem;margin:2px 0 0">Es opcional. Si no lo marcas, solo aparece tu organización. Puedes cambiarlo editando tu compromiso.</p>
    <button class="btn" type="button" id="pegarComp">Pegar en el muro</button>${compErr?`<p role="alert" style="color:#8E1B14;font-weight:800;margin-top:8px">${esc(compErr)}</p>`:""}</div>`;}
async function pegarCompromiso(){const t=$("#compIn").value.trim();if(t.length<8){compErr="Escribe un compromiso un poco más largo.";firmaFase="";pintar();return;}
  const b=$("#pegarComp");b.disabled=true;b.textContent="Pegando…";
  try{const r=await fetch("../api/muro.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({c:code,texto:t,mostrar_nombre:$("#compNom").checked})});const j=await r.json();
    if(j.ok){compromiso=j.mio;editandoComp=false;compErr="";const ts=$("#toast");ts.innerHTML=`<div class="toast" role="status">¡Listo! Tu compromiso ya está en el muro.</div>`;setTimeout(()=>ts.innerHTML="",4000);}else compErr=j.error||"No se pudo pegar.";}
  catch(e){compErr="Sin conexión. Intenta de nuevo.";}
  document.activeElement&&document.activeElement.blur&&document.activeElement.blur();firmaFase="";pintar();}

function pintarYo(){const j=data,u=j.ubicacion;
  $("#p-yo").innerHTML=`<div class="yo"><div class="k">Hola, ${esc(j.nombre)}</div><div class="cel">Célula ${+u.celula}</div><div style="font-weight:900">Grupo ${+u.grupo} · ${esc(u.eje)}</div><div class="reto">Reto: ${esc(u.reto)}</div></div>
    <div class="datos"><div><b>Pregunta de tu grupo</b><span>${esc(u.pregunta_eje)}</span></div><div><b>Cómo funciona</b><span>Participantes → 3 grandes grupos → células. Cada grupo se divide en 5 células, una por reto. Tu célula trabaja este reto en tres fases: Diagnóstico, Análisis y Experimentación.</span></div><div><b>Código de tu escarapela</b><span>${esc(j.codigo)}</span></div></div>
    <div class="acc" style="margin-top:16px"><button class="btn line" type="button" id="salir2">Salir de Mi taller</button></div>`;
  $("#salir2").onclick=salir;}

const timers={};
function pintarFicha(){const box=$("#campos"),f=data.estado.fase;
  if(!box.dataset.listo){box.innerHTML=data.campos.map(c=>`<div class="campo" id="cp-${c.id}"><label for="f-${c.id}">${esc(c.titulo)}</label><small>${esc(c.ayuda)}</small><textarea id="f-${c.id}" maxlength="1500"></textarea><div class="guardado" id="g-${c.id}" aria-live="polite"></div></div>`).join("");box.dataset.listo=1;
    data.campos.forEach(c=>{const t=$("#f-"+c.id);t.addEventListener("input",()=>{clearTimeout(timers[c.id]);$("#g-"+c.id).textContent="Escribiendo…";timers[c.id]=setTimeout(()=>guardar(c.id,t.value),1200);});t.addEventListener("blur",()=>{if(timers[c.id]){clearTimeout(timers[c.id]);guardar(c.id,t.value);}});});}
  let n=0;data.campos.forEach(c=>{const t=$("#f-"+c.id),v=data.ficha[c.id]||"";if(document.activeElement!==t&&!timers[c.id]&&t.value!==v)t.value=v;if(v.trim())n++;
    $("#cp-"+c.id).classList.toggle("now",!!(f&&f.campo===c.id));$("#cp-"+c.id).classList.toggle("lleno",!!v.trim());});
  $("#avBar").style.width=(n/data.campos.length*100)+"%";$("#avTxt").textContent=`${n} de ${data.campos.length}`;}
async function guardar(id,v){delete timers[id];try{const j=await api({c:code,campo:id,valor:v});$("#g-"+id).textContent=j.ok?"Guardado ✓":(j.error||"No se pudo guardar");if(j.ok){data.ficha=j.ficha;data.prompts=j.prompts;pintarFicha();}}catch(e){$("#g-"+id).textContent="Sin conexión: se guardará cuando vuelvas a escribir.";}}
function pintarQuedan(){if(data.agente_restante!=null)$("#quedan").textContent=`A tu célula le quedan ${data.agente_restante} consultas.`;}

// ---- Pliego como chat ----
function md(t){// Formato sencillo: negritas, listas y párrafos. Todo se escapa antes.
  const lineas=esc(t).split(/\n/);let h="",lista="";
  const cierra=()=>{if(lista){h+=`</${lista}>`;lista="";}};
  for(let l of lineas){l=l.replace(/\*\*(.+?)\*\*/g,"<b>$1</b>");let m;
    if(m=l.match(/^\s*[-•*]\s+(.*)/)){if(lista!=="ul"){cierra();h+="<ul>";lista="ul";}h+=`<li>${m[1]}</li>`;}
    else if(m=l.match(/^\s*\d+[.)]\s+(.*)/)){if(lista!=="ol"){cierra();h+="<ol>";lista="ol";}h+=`<li>${m[1]}</li>`;}
    else if(!l.trim()){cierra();}
    else{cierra();h+=`<p>${l.replace(/^#+\s*/,"")}</p>`;}}
  cierra();return h;}
function cercaDelFinal(){const m=$("#mensajes");return m.scrollHeight-m.scrollTop-m.clientHeight<120;}
function alFinal(){const m=$("#mensajes");m.scrollTop=m.scrollHeight;}
function burbuja(m,i){if(m.role==="user")return`<div class="burbuja u">${esc(m.content)}</div>`;
  if(m.p)return`<div class="burbuja a"><div class="quien"><i>P</i>Pliego</div><span class="escribe" aria-label="Pliego está escribiendo"><i></i><i></i><i></i></span></div>`;
  return`<div class="burbuja a${m.err?" err":""}"><div class="quien"><i>P</i>Pliego</div>${md(m.content)}${m.err?"":`<button class="copiar" type="button" data-i="${i}">Copiar</button>`}</div>`;}
function pintarChat(abajo){const box=$("#mensajes");const f=data&&data.estado.fase;
  if(!hist.length){const p=data&&f&&data.prompts&&data.prompts[f.id];
    box.innerHTML=`<div class="bienvenida-chat"><div class="av">P</div><h3>Hola, soy Pliego</h3><p class="muted">Conozco el reto de tu célula y lo que ya escribieron en la ficha: no tienen que copiar nada. Pregúntame lo que necesiten para avanzar.</p><div class="chips">${p?`<button type="button" data-q="fase"><b>Prompt de ${esc(f.nombre)}</b>Usar el prompt sugerido de la fase</button>`:""}<button type="button" data-q="¿Qué barreras de inclusión podríamos no estar viendo en nuestro reto?"><b>Explorar</b>¿Qué barreras podríamos no estar viendo?</button><button type="button" data-q="Ayúdanos a escribir un prompt reutilizable para que una empresa trabaje nuestro reto."><b>Llevar a la empresa</b>Escribir un prompt reutilizable</button>${f&&f.id==="experimentacion"?`<button type="button" data-q="landing"><b>Hacerlo visual</b>Crear la landing de nuestra solución</button>`:""}</div></div>`;
    box.querySelectorAll("[data-q]").forEach(b=>b.onclick=()=>{if(b.dataset.q==="landing"){verTab("fase");crearLanding("");return;}preguntar(b.dataset.q==="fase"?data.prompts[data.estado.fase.id]:b.dataset.q);});return;}
  box.innerHTML=hist.map(burbuja).join("");
  box.querySelectorAll(".copiar").forEach(b=>b.onclick=async()=>{try{await navigator.clipboard.writeText(hist[+b.dataset.i].content);b.textContent="¡Copiado!";}catch(e){}});
  if(abajo)alFinal();}
async function preguntar(t){t=(t||"").trim();if(!t||esperando)return;esperando=true;$("#enviar").disabled=true;
  hist.push({role:"user",content:t});hist.push({role:"assistant",content:"",p:1});pintarChat(true);
  let txt,err=false;
  try{const r=await fetch("../api/agente.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({modo:"taller",c:code,messages:hist.filter(m=>!m.p&&!m.err).slice(-12).map(m=>({role:m.role,content:m.content}))})});
    const j=await r.json();if(j.ok){txt=j.answer;if(data&&data.agente_restante)data.agente_restante--;}else{txt=j.error||"Pliego no pudo responder. Intenta de nuevo.";err=true;}}
  catch(e){txt="Sin conexión. Revisa el internet e intenta de nuevo.";err=true;}
  const seguir=cercaDelFinal();hist[hist.length-1]={role:"assistant",content:txt,err};

  hist=hist.slice(-24);ss("dvs_taller_chat",JSON.stringify(hist));esperando=false;pintarChat(seguir);pintarQuedan();estadoEnviar();}
const caja=$("#askIn");
function crecer(){caja.style.height="auto";caja.style.height=Math.min(caja.scrollHeight,132)+"px";}
function estadoEnviar(){$("#enviar").disabled=esperando||!caja.value.trim();}
caja.addEventListener("input",()=>{crecer();estadoEnviar();});
caja.addEventListener("keydown",e=>{if(e.key==="Enter"&&!e.shiftKey&&!tactil){e.preventDefault();$("#ask").requestSubmit();}});
caja.addEventListener("focus",()=>{document.body.classList.add("teclado");setTimeout(()=>{alto();if(cercaDelFinal())alFinal();},250);});
caja.addEventListener("blur",()=>setTimeout(()=>{document.body.classList.remove("teclado");alto();},120));
$("#ask").addEventListener("submit",e=>{e.preventDefault();const v=caja.value;if(!v.trim()||esperando)return;caja.value="";crecer();estadoEnviar();preguntar(v);});

function verTab(t){tab=t;["fase","ficha","pliego","yo"].forEach(x=>{$("#t-"+x).setAttribute("aria-selected",x===t);$("#p-"+x).hidden=x!==t;});
  document.body.classList.toggle("en-pliego",t==="pliego");if(t==="pliego")pintarChat(true);}
["fase","ficha","pliego","yo"].forEach(x=>$("#t-"+x).onclick=()=>verTab(x));
$("#nuevoChat").onclick=()=>{if(esperando)return;if(hist.length&&!confirm("¿Empezar un chat nuevo? Esta conversación se borra de este celular. La ficha no cambia."))return;hist=[];ss("dvs_taller_chat","[]");pintarChat(true);caja.focus();};
function tick(){if(!data||!data.ubicacion||!data.ubicacion.celula)return;const f=data.estado.fase,el=$("#reloj"),arco=$("#arco");
  if(!f){el.textContent="–";arco.style.strokeDashoffset=169.6;$("#barra").classList.remove("fin");return;}
  const tot=f.min*60,s=Math.max(0,Math.round(f.fin-(Date.now()/1000+off)));
  el.textContent=s?`${Math.floor(s/60)}:${String(s%60).padStart(2,"0")}`:"¡Ya!";arco.style.strokeDashoffset=169.6*(1-s/tot);$("#barra").classList.toggle("fin",s>0&&s<60);}
setInterval(tick,1000);setInterval(()=>{if(code&&!document.hidden)cargar();},8000);
cargar();
})();
</script></body></html>
