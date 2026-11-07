<?php
declare(strict_types=1);
// Landing de la solución de una célula, generada con Pliego a partir de su reto y su ficha. Sin datos personales.
require __DIR__ . '/../api/taller_lib.php';
header('Cache-Control: no-store');
$g = (int)($_GET['g'] ?? 0); $c = (int)($_GET['c'] ?? 0);
$l = null;
if (isset(TALLER_GRUPOS[$g]) && $c >= 1 && $c <= TALLER_MAX_CELULAS) { try { $l = taller_landing(taller_db(), $g, $c); } catch (Throwable $e) { error_log('landing ver: ' . $e->getMessage()); } }
function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$color = [1 => ['#FFC20E', '#D9A200'], 2 => ['#13A79A', '#0E8479'], 3 => ['#EE7DB9', '#D2589C']][$g] ?? ['#FFC20E', '#D9A200'];
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="robots" content="noindex,nofollow">
<title><?= $l ? h($l['titulo']) . ' · ' : '' ?>Laboratorio de IA · 5° Encuentro</title><link rel="icon" href="../favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<style>
:root{--ink:#0C1646;--ink2:#14215E;--ink3:#1D2C74;--paper:#F7F4EE;--muted:#4B537F;--g:<?= $color[0] ?>;--g2:<?= $color[1] ?>;--f:"elza","Avenir Next","Segoe UI",system-ui,sans-serif}
*{box-sizing:border-box}html,body{margin:0}body{font:17px/1.55 var(--f);background:var(--paper);color:var(--ink)}
:focus-visible{outline:3px solid var(--ink);outline-offset:3px}
.hero{background:var(--g);position:relative;overflow:hidden;padding:28px 20px 56px}
.hero::before{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.26) 0 44%,transparent 44%),linear-gradient(225deg,rgba(0,0,0,.07) 0 30%,transparent 30%);pointer-events:none}
.hero::after{content:"";position:absolute;right:0;bottom:0;width:90px;height:90px;background:linear-gradient(315deg,var(--paper) 0 50%,var(--g2) 50%)}
.in{max-width:1120px;margin:0 auto;position:relative}
.top{display:flex;align-items:center;gap:10px;font-weight:800;font-size:.85rem}.top .n{font:900 1.8rem/1 var(--f)}
.k{font-weight:900;font-size:.78rem;letter-spacing:.12em;text-transform:uppercase}
h1{font:900 clamp(2.4rem,7vw,4.6rem)/.95 var(--f);letter-spacing:-.03em;margin:22px 0 12px;max-width:16ch}
.lema{font-weight:800;font-size:clamp(1.1rem,2.6vw,1.45rem);line-height:1.3;max-width:34ch;margin:0}
.chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:18px}.chips span{background:var(--ink);color:#fff;border-radius:999px;padding:6px 14px;font-weight:800;font-size:.85rem}
main .in{display:grid;grid-template-columns:1fr;gap:28px;padding:28px 20px 60px}
@media (min-width:900px){main .in{grid-template-columns:1.25fr .75fr;align-items:start}.lado{position:sticky;top:20px}}
@media (max-width:899px){.lado{order:-1}.pant{min-height:420px}}
section{margin-bottom:26px}h2{font:900 1.5rem/1.15 var(--f);margin:0 0 10px}h2 small{display:block;font-size:.75rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:4px}
.caja{background:#fff;border-radius:22px;padding:20px;box-shadow:0 1px 0 rgba(12,22,70,.06)}
.reto{border-left:8px solid var(--g)}
ul.lst{margin:0;padding:0;list-style:none;display:grid;gap:10px}ul.lst li{background:#fff;border-radius:16px;padding:12px 14px 12px 44px;position:relative}ul.lst li::before{content:"";position:absolute;left:14px;top:16px;width:16px;height:16px;background:var(--g);clip-path:polygon(0 0,100% 0,0 100%)}
.pasos{display:grid;gap:12px;counter-reset:p}.paso{background:var(--ink);color:#fff;border-radius:20px;padding:16px 18px 16px 70px;position:relative;counter-increment:p}.paso::before{content:counter(p);position:absolute;left:16px;top:14px;width:40px;height:40px;border-radius:12px;background:var(--g);color:var(--ink);display:grid;place-items:center;font:900 1.2rem/1 var(--f)}.paso b{display:block;font-size:1.05rem}
.dos{display:grid;gap:12px}@media (min-width:640px){.dos{grid-template-columns:1fr 1fr}}
.dato{background:var(--g);border-radius:20px;padding:16px 18px}.dato b{display:block;font-size:.75rem;letter-spacing:.1em;text-transform:uppercase}.dato span{font-weight:800;font-size:1.05rem;line-height:1.35}
.prompt{background:var(--ink);color:#fff;border-radius:20px;padding:18px}.prompt p{margin:6px 0 12px;white-space:pre-wrap;font-size:.98rem}.prompt button{border:0;border-radius:999px;padding:10px 18px;font:800 1rem var(--f);background:var(--g);color:var(--ink);cursor:pointer}
/* Mockup de celular */
.cel{width:300px;max-width:100%;margin:0 auto;background:var(--ink);border-radius:42px;padding:12px;box-shadow:0 24px 50px rgba(12,22,70,.28)}
.pant{background:#fff;border-radius:32px;overflow:hidden;min-height:540px;display:flex;flex-direction:column}
.barra{background:var(--g);padding:22px 16px 14px;font-weight:900;display:flex;align-items:center;gap:10px}.barra i{width:30px;height:30px;border-radius:10px;background:var(--ink);display:block}
.cuerpo{padding:14px;display:grid;gap:10px;align-content:start;flex:1;font-size:.9rem}
.b{max-width:85%;padding:9px 12px;border-radius:16px;line-height:1.35}.b.p{justify-self:end;background:var(--g);border-bottom-right-radius:4px;font-weight:700}.b.a{background:#EEF0F8;border-bottom-left-radius:4px}
.chk{display:flex;gap:10px;align-items:flex-start;background:#F4F5FA;border-radius:12px;padding:10px}.chk i{flex:none;width:20px;height:20px;border-radius:6px;border:2px solid var(--ink)}.chk:nth-child(-n+2) i{background:var(--g)}
.cmp{display:grid;gap:4px}.cmp b{font-size:.78rem;color:var(--muted)}.cmp span{display:block;border:2px solid #DCDFEC;border-radius:10px;padding:9px;color:#9AA0BF}
.bt{background:var(--ink);color:#fff;border-radius:999px;padding:11px;text-align:center;font-weight:800;margin-top:4px}
.tiles{display:grid;grid-template-columns:1fr 1fr;gap:8px}.tile{background:#F4F5FA;border-radius:14px;padding:10px;min-height:72px;font-weight:700}.tile:first-child{background:var(--g);grid-column:1/-1}.tile::after{content:"";display:block;height:6px;border-radius:6px;background:var(--ink);opacity:.18;margin-top:10px;width:70%}.tile:nth-child(2n)::after{width:45%}.tile:nth-child(3n)::after{width:85%}
.gpaso{display:grid;grid-template-columns:28px 1fr;gap:8px;align-items:start}.gpaso i{width:28px;height:28px;border-radius:50%;background:var(--g);display:grid;place-items:center;font-style:normal;font-weight:900}
.pie-cel{text-align:center;font-size:.82rem;color:var(--muted);margin-top:12px}
footer{background:var(--ink);color:#fff;padding:22px 20px;font-size:.9rem}footer .in{display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;align-items:center}
.vacio{max-width:560px;margin:15vh auto;padding:20px;text-align:center}
.startin-credit{position:fixed;z-index:60;right:12px;bottom:calc(12px + env(safe-area-inset-bottom,0px));font:600 .68rem/1.2 var(--f);color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.92);border:1px solid #ECE7E4;border-radius:999px;padding:4px 10px}.startin-credit b{color:#16162B;font-weight:800}
@media print{.startin-credit,.prompt button{display:none}.lado{position:static}}
</style></head><body>
<?php if (!$l): ?>
  <div class="vacio"><div class="k">Laboratorio de IA · 5° Encuentro</div><h1 style="margin:10px auto">Aún no hay landing</h1><p>Esta célula todavía no ha creado la landing de su solución. Se arma desde «Mi taller», en la fase de Experimentación.</p><p><a href="./">Ir a Mi taller</a></p></div>
<?php else: $mk = $l['mockup'] ?? []; ?>
<header class="hero"><div class="in">
  <div class="top"><span class="n">5°</span><span>Laboratorio de IA aplicada a procesos de inclusión</span></div>
  <h1><?= h($l['titulo']) ?></h1>
  <p class="lema"><?= h($l['lema']) ?></p>
  <div class="chips"><span>Grupo <?= (int)$g ?> · <?= h($l['eje'] ?? TALLER_GRUPOS[$g]['eje']) ?></span><span>Célula <?= (int)$c ?></span></div>
</div></header>
<main><div class="in">
  <div>
    <section class="caja reto"><h2><small>El reto</small><?= h($l['reto'] ?? '') ?></h2><p style="margin:0"><?= h($l['problema']) ?></p></section>
    <?php if ($l['para_quien']): ?><section><h2><small>A quiénes ayuda</small></h2><div class="caja"><?= h($l['para_quien']) ?></div></section><?php endif; ?>
    <?php if ($l['hallazgos']): ?><section><h2><small>Lo que encontramos</small>Las causas detrás de la barrera</h2><ul class="lst"><?php foreach ($l['hallazgos'] as $x) echo '<li>' . h($x) . '</li>'; ?></ul></section><?php endif; ?>
    <section><h2><small>La solución</small>Qué proponemos</h2><div class="caja"><?= h($l['solucion']) ?></div></section>
    <?php if ($l['como_funciona']): ?><section><h2><small>Cómo funciona</small>Paso a paso</h2><div class="pasos"><?php foreach ($l['como_funciona'] as $p) echo '<div class="paso"><b>' . h($p['paso']) . '</b>' . h($p['detalle']) . '</div>'; ?></div></section><?php endif; ?>
    <?php if ($l['riesgos']): ?><section><h2><small>Para hacerlo bien</small>Riesgos y sesgos a vigilar</h2><ul class="lst"><?php foreach ($l['riesgos'] as $x) echo '<li>' . h($x) . '</li>'; ?></ul></section><?php endif; ?>
    <section class="dos"><div class="dato"><b>Primer paso · 30 días</b><span><?= h($l['primer_paso']) ?></span></div><div class="dato" style="background:#fff"><b>Cómo sabremos que funciona</b><span><?= h($l['indicador']) ?></span></div></section>
    <?php if ($l['prompt']): ?><section class="prompt"><div class="k" style="color:var(--g)">Prompt para seguir trabajándola</div><p id="pr"><?= h($l['prompt']) ?></p><button type="button" id="cp">Copiar prompt</button></section><?php endif; ?>
  </div>
  <aside class="lado" aria-label="Mockup de la solución">
    <div class="cel"><div class="pant"><div class="barra"><i aria-hidden="true"></i><?= h($mk['app'] ?? '') ?></div><div class="cuerpo">
    <?php $el = $mk['elementos'] ?? []; switch ($mk['tipo'] ?? 'guia'):
      case 'chat': foreach (($mk['mensajes'] ?? []) as $m) echo '<div class="b ' . ($m['de'] === 'asistente' ? 'a' : 'p') . '">' . h($m['texto']) . '</div>'; if (!($mk['mensajes'] ?? [])) foreach ($el as $i => $x) echo '<div class="b ' . ($i % 2 ? 'p' : 'a') . '">' . h($x) . '</div>'; break;
      case 'checklist': foreach ($el as $x) echo '<div class="chk"><i aria-hidden="true"></i><span>' . h($x) . '</span></div>'; break;
      case 'formulario': foreach (array_slice($el, 0, 5) as $x) echo '<div class="cmp"><b>' . h($x) . '</b><span>Escribe aquí…</span></div>'; echo '<div class="bt">Enviar</div>'; break;
      case 'tablero': echo '<div class="tiles">'; foreach ($el as $x) echo '<div class="tile">' . h($x) . '</div>'; echo '</div>'; break;
      default: foreach ($el as $i => $x) echo '<div class="gpaso"><i>' . ($i + 1) . '</i><span>' . h($x) . '</span></div>';
    endswitch; ?>
    </div></div></div>
    <p class="pie-cel">Mockup de lo que imaginamos · <?= h(ucfirst((string)($mk['tipo'] ?? ''))) ?></p>
  </aside>
</div></main>
<footer><div class="in"><span>5° Encuentro Diversidades e Inclusión · 11 de noviembre de 2026 · Universidad El Bosque</span><span>Creada por la célula <?= (int)$c ?> del grupo <?= (int)$g ?> con Pliego, el copiloto de IA. Es un prototipo para validar con las personas involucradas.</span></div></footer>
<script>(function(){const b=document.getElementById("cp");if(b)b.onclick=async()=>{try{await navigator.clipboard.writeText(document.getElementById("pr").textContent);b.textContent="¡Copiado!";}catch(e){}};})();</script>
<?php endif; ?>
<a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener" aria-label="Hecho por Startin Lab">by <b>Startin*Lab</b></a>
</body></html>
