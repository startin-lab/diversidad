<?php
declare(strict_types=1);
require __DIR__ . '/../api/lib.php';
$c = cfg(); session_name($c['session_name']); session_start();
$err = '';
if (isset($_POST['salir'])) { unset($_SESSION['puerta']); header('Location: ./'); exit; }
if (isset($_POST['clave'])) {
  $k = (string)$_POST['clave'];
  if (password_verify($k, $c['puerta_pass_hash'] ?? '') || password_verify($k, $c['admin_pass_hash'])) { $_SESSION['puerta'] = true; session_regenerate_id(true); header('Location: ./'); exit; }
  $err = 'Clave incorrecta.'; usleep(600000);
}
$logged = !empty($_SESSION['puerta']) || !empty($_SESSION['admin']);
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="robots" content="noindex,nofollow"><link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css"><title>Entrada · 5° Encuentro</title>
<link rel="icon" href="../favicon.svg" type="image/svg+xml">
<style>
:root{--ink:#0C1646;--sun:#FFC20E;--flame:#F26A21;--ok:#1BA160;--no:#D63A2F;--warn:#E09B00}
*{box-sizing:border-box}body{margin:0;font:17px/1.45 "elza","Segoe UI",system-ui,sans-serif;background:var(--ink);color:#fff;min-height:100dvh;display:grid;grid-template-rows:auto 1fr;padding:env(safe-area-inset-top,0) 0 env(safe-area-inset-bottom,0)}
header{display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.15)}header .n{color:var(--sun);font-size:1.8rem;font-weight:900}header b{line-height:1.05;font-size:.9rem}header form{margin-left:auto}
main{padding:16px;display:grid;gap:14px;align-content:start;max-width:560px;width:100%;margin:0 auto}
.btn{border:0;border-radius:999px;padding:14px 20px;font-weight:800;font-size:1.05rem;cursor:pointer;background:var(--flame);color:#fff}.btn.ghost{background:transparent;color:#fff;box-shadow:inset 0 0 0 2px rgba(255,255,255,.35)}.btn.sm{padding:9px 14px;font-size:.9rem}
input[type=text],input[type=password]{width:100%;padding:16px;border:2px solid rgba(255,255,255,.3);border-radius:14px;font:1.3rem "elza","Segoe UI",system-ui;background:#0A1240;color:#fff;text-align:center;letter-spacing:.05em;text-transform:uppercase}
.scan{position:relative;border-radius:18px;overflow:hidden;background:#000;aspect-ratio:1;max-height:46dvh}.scan video{width:100%;height:100%;object-fit:cover}.scan .frame{position:absolute;inset:14%;border:3px solid var(--sun);border-radius:18px;box-shadow:0 0 0 999px rgba(0,0,0,.35)}.scan[hidden]{display:none}
.result{border-radius:18px;padding:22px;display:grid;gap:6px;text-align:center}.result.ok{background:var(--ok)}.result.no{background:var(--no)}.result.warn{background:var(--warn);color:#000}
.result .big{font-size:2rem;font-weight:900;line-height:1.1}.result .code{font-weight:800;letter-spacing:.06em;opacity:.9}.result .aj{background:rgba(255,255,255,.2);border-radius:12px;padding:10px;font-size:.95rem;margin-top:6px}
.result[hidden]{display:none}.muted{opacity:.75;font-size:.9rem}.login{margin-top:12vh;display:grid;gap:12px;text-align:center}
.hist{display:grid;gap:6px;font-size:.9rem}.hist div{display:flex;justify-content:space-between;gap:10px;padding:8px 12px;background:rgba(255,255,255,.08);border-radius:10px}
</style></head><body>
<header><span class="n">5°</span><b>Mesa de entrada<br>Encuentro Diversidades e Inclusión</b><?php if ($logged): ?><form method="post"><button class="btn ghost sm" name="salir" value="1">Salir</button></form><?php endif; ?></header>
<main>
<?php if (!$logged): ?>
  <form method="post" class="login"><p>Escribe la clave de la mesa de entrada.</p><input type="password" name="clave" inputmode="numeric" autofocus required><button class="btn">Entrar</button><?php if ($err) echo '<p style="color:#FFB4A8;font-weight:700">' . htmlspecialchars($err) . '</p>'; ?></form>
<?php else: ?>
  <div class="result" id="res" hidden></div>
  <div class="scan" id="scan" hidden><video id="video" playsinline muted></video><div class="frame"></div></div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center"><button class="btn" id="btnScan" type="button">Escanear QR</button><button class="btn ghost" id="btnStop" type="button" hidden>Detener cámara</button></div>
  <form id="f" autocomplete="off"><label for="q" class="muted">O escribe el código (5E-XXXXXX) o la cédula</label><input type="text" id="q" placeholder="5E-ABC123 ó 1020304050" autofocus><button class="btn" style="width:100%;margin-top:8px">Validar y registrar ingreso</button></form>
  <p class="muted" style="text-align:center">Cada validación marca la asistencia. Si la persona ya había ingresado, lo verás en amarillo.</p>
  <div class="hist" id="hist"></div>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
(function(){
const $=s=>document.querySelector(s);const res=$("#res"),q=$("#q"),hist=$("#hist");let stream=null,raf=null,last="",lastT=0;
const esc=s=>String(s).replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]));
function beep(ok){try{const a=new (window.AudioContext||window.webkitAudioContext)();const o=a.createOscillator(),g=a.createGain();o.connect(g);g.connect(a.destination);o.frequency.value=ok?880:220;g.gain.value=.15;o.start();o.stop(a.currentTime+(ok?.15:.4));}catch(e){}}
async function validar(v){v=v.trim();if(!v)return;if(v===last&&Date.now()-lastT<4000)return;last=v;lastT=Date.now();
  res.hidden=false;res.className="result";res.innerHTML="<div class='big'>Validando…</div>";
  try{const r=await fetch("../api/ingreso.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({q:v,marcar:true})});const j=await r.json();
    if(!j.ok){res.className="result no";res.innerHTML=`<div class='big'>Error</div><div>${esc(j.error||"")}</div>`;beep(false);return;}
    if(!j.encontrado){res.className="result no";res.innerHTML=`<div class='big'>No está inscrito/a</div><div class='code'>${esc(v)}</div><div class='muted'>Invítale a inscribirse en el portal o verifica el documento.</div>`;beep(false);return;}
    const aj=j.ajustes&&j.ajustes.length?`<div class='aj'><b>Ajustes razonables:</b> ${esc(j.ajustes.join(", "))}</div>`:"";
    if(j.ya_ingreso){res.className="result warn";res.innerHTML=`<div class='big'>Ya había ingresado</div><div style='font-size:1.3rem;font-weight:800'>${esc(j.nombres)}</div><div>${esc(j.organizacion)}</div><div class='code'>${esc(j.codigo)}</div>${aj}`;beep(false);}
    else{res.className="result ok";res.innerHTML=`<div class='big'>¡Bienvenido/a!</div><div style='font-size:1.4rem;font-weight:800'>${esc(j.nombres)}${j.pronombre?" · "+esc(j.pronombre):""}</div><div>${esc(j.organizacion)}</div><div class='code'>${esc(j.codigo)}</div>${aj}`;beep(true);}
    const d=document.createElement("div");d.innerHTML=`<span>${esc(j.nombres)}</span><span>${esc(j.codigo)} · ${new Date().toLocaleTimeString("es-CO",{hour:"2-digit",minute:"2-digit"})}</span>`;hist.prepend(d);while(hist.children.length>8)hist.lastChild.remove();
  }catch(e){res.className="result no";res.innerHTML="<div class='big'>Sin conexión</div>";}
}
$("#f").addEventListener("submit",e=>{e.preventDefault();validar(q.value);q.value="";q.focus();});
async function startScan(){try{stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:"environment"}},audio:false});const v=$("#video");v.srcObject=stream;await v.play();$("#scan").hidden=false;$("#btnScan").hidden=true;$("#btnStop").hidden=false;
  const cv=document.createElement("canvas"),x=cv.getContext("2d",{willReadFrequently:true});const det=("BarcodeDetector" in window)?new BarcodeDetector({formats:["qr_code"]}):null;
  const loop=async()=>{if(!stream)return;if(v.readyState>=2){let text=null;
      if(det){try{const b=await det.detect(v);if(b.length)text=b[0].rawValue;}catch(e){}}
      else if(window.jsQR){cv.width=v.videoWidth/2;cv.height=v.videoHeight/2;x.drawImage(v,0,0,cv.width,cv.height);const im=x.getImageData(0,0,cv.width,cv.height);const r=jsQR(im.data,im.width,im.height);if(r)text=r.data;}
      if(text){const m=text.match(/5E-?[A-Z0-9]{6}/i);validar(m?m[0]:text);}}
    raf=requestAnimationFrame(loop);};loop();}catch(e){res.hidden=false;res.className="result no";res.innerHTML="<div class='big'>Sin cámara</div><div class='muted'>Permite el acceso a la cámara o escribe el código.</div>";}}
function stopScan(){if(stream){stream.getTracks().forEach(t=>t.stop());stream=null;}if(raf)cancelAnimationFrame(raf);$("#scan").hidden=true;$("#btnScan").hidden=false;$("#btnStop").hidden=true;}
$("#btnScan").onclick=startScan;$("#btnStop").onclick=stopScan;
})();
</script>
<?php endif; ?>
<p style="text-align:center;font-size:.8rem;margin:24px 0 8px"><a href="https://startinlab.org" target="_blank" rel="noopener" style="color:#C9CDEA;text-decoration:none">by Startin<span style="color:#FF2BD6">*</span>Lab</a></p></main></body></html>
