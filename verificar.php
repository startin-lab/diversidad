<?php
declare(strict_types=1);
require __DIR__ . '/api/lib.php';
$code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($_GET['c'] ?? '')));
$r = null; $logged = admin_logged();
if ($code) { try { $q = db()->prepare('SELECT id,codigo,nombres,organizacion,asistio,requiere_ajuste,ajustes FROM registros WHERE codigo=?'); $q->execute([$code]); $r = $q->fetch() ?: null; } catch (Throwable $e) {} }
if ($logged && $r && isset($_POST['marcar'])) { db()->prepare('UPDATE registros SET asistio=1 WHERE id=?')->execute([(int)$r['id']]); $r['asistio'] = 1; }
function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Verificación · 5° Encuentro</title>
<style>body{margin:0;font:17px/1.5 "Segoe UI",system-ui,sans-serif;background:#0C1646;color:#fff;display:grid;place-items:center;min-height:100vh;padding:16px;box-sizing:border-box}.c{background:#fff;color:#0C1646;border-radius:18px;padding:28px;max-width:420px;width:100%;text-align:center}.n{font-size:3rem;font-weight:900;color:#FFC20E;line-height:1}.code{font-size:1.6rem;font-weight:900;letter-spacing:.06em;margin:8px 0}.ok{color:#1BA160;font-weight:800}.no{color:#B33;font-weight:800}.btn{background:#F26A21;color:#fff;border:0;border-radius:999px;padding:12px 20px;font-weight:800;font-size:1rem;cursor:pointer}.aj{background:#FFF4D6;border-radius:10px;padding:10px;margin-top:12px;font-size:.95rem;text-align:left}a{color:#4B537F}</style></head><body>
<div class="c"><div class="n">5°</div><div style="font-weight:800">Encuentro Diversidades e Inclusión</div>
<?php if (!$code): ?><p>Escanea el QR de una escarapela o escribe el código.</p><form method="get"><input name="c" placeholder="5E-XXXXXX" style="padding:10px;border:1px solid #ccc;border-radius:10px;font-size:1rem"> <button class="btn">Ver</button></form>
<?php elseif (!$r): ?><div class="code"><?= h($code) ?></div><p class="no">Código no encontrado.</p><p><a href="./">Ir al portal</a></p>
<?php else: ?><div class="code"><?= h($r['codigo']) ?></div><p class="ok">Inscripción válida</p><p style="font-size:1.3rem;font-weight:800;margin:4px 0"><?= h($r['nombres']) ?></p><p style="margin:0"><?= h($r['organizacion']) ?></p>
  <?php if ($r['asistio']) echo '<p class="ok">✓ Asistencia registrada</p>'; ?>
  <?php if ($logged): if ($r['requiere_ajuste']) echo '<div class="aj"><b>Ajustes razonables:</b> ' . h(implode(', ', json_decode($r['ajustes'] ?: '[]', true) ?: [])) . '</div>'; if (!$r['asistio']) echo '<form method="post" style="margin-top:14px"><button class="btn" name="marcar" value="1">Marcar asistencia</button></form>'; else echo ''; else: ?><p style="font-size:.85rem;color:#4B537F">El equipo de la mesa de entrada valida el ingreso en <a href="ingreso/">/ingreso</a>.</p><?php endif; ?>
<?php endif; ?></div></body></html>
