<?php
declare(strict_types=1);
require __DIR__ . '/../api/lib.php';
$c = cfg();
session_name($c['session_name']); session_start();
$err = '';
if (isset($_POST['salir'])) { session_destroy(); header('Location: ./'); exit; }
if (isset($_POST['clave'])) {
  if (password_verify((string)$_POST['clave'], $c['admin_pass_hash'])) { $_SESSION['admin'] = true; session_regenerate_id(true); header('Location: ./'); exit; }
  $err = 'Contraseña incorrecta.'; usleep(600000);
}
$logged = !empty($_SESSION['admin']);
if ($logged && isset($_POST['asistio'], $_POST['id'])) {
  db()->prepare('UPDATE registros SET asistio=? WHERE id=?')->execute([(int)$_POST['asistio'], (int)$_POST['id']]);
  header('Location: ./?f=' . urlencode((string)($_POST['f'] ?? ''))); exit;
}
if ($logged && isset($_POST['borrar_compromiso'])) {
  db()->prepare('UPDATE compromisos SET aprobado=0 WHERE id=?')->execute([(int)$_POST['borrar_compromiso']]);
  header('Location: ./#muro'); exit;
}
if ($logged && isset($_GET['csv'])) {
  $rows = db()->query('SELECT * FROM registros ORDER BY id')->fetchAll();
  header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="inscripciones-5-encuentro-' . date('Ymd-Hi') . '.csv"');
  $o = fopen('php://output', 'w'); fprintf($o, "\xEF\xBB\xBF");
  fputcsv($o, ['Código','Nombres y apellidos','Documento','Organización','Correo','Pronombre','Grupos (pregunta 5)','Requiere ajuste','Ajustes','Pliegue','Compromiso','Asistió','Correo enviado','Fecha registro'], ';');
  foreach ($rows as $r) {
    fputcsv($o, [$r['codigo'], $r['nombres'], decrypt_str($r['documento_enc']), $r['organizacion'], $r['correo'], $r['pronombre'],
      implode(' | ', json_decode(decrypt_str($r['grupos_enc']) ?: '[]', true) ?: []), $r['requiere_ajuste'] ? 'Sí' : 'No',
      implode(' | ', json_decode($r['ajustes'] ?: '[]', true) ?: []), $r['pliegue'], $r['compromiso'], $r['asistio'] ? 'Sí' : 'No', $r['correo_enviado'] ? 'Sí' : 'No', $r['creado']], ';');
  }
  fclose($o); exit;
}
function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?><!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css"><title>Panel · 5° Encuentro</title>
<style>
:root{--ink:#0C1646;--bg:#F4F3FA;--line:#DDE0F0;--sun:#FFC20E;--flame:#F26A21}
*{box-sizing:border-box}body{margin:0;font:15px/1.5 "elza","Segoe UI",system-ui,sans-serif;background:var(--bg);color:var(--ink)}
.wrap{max-width:1240px;margin:0 auto;padding:24px 16px}
h1{font-size:1.6rem;margin:0 0 4px}h2{font-size:1.15rem;margin:28px 0 10px}
.card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px}
.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin:16px 0}
.kpi{background:#fff;border:1px solid var(--line);border-radius:12px;padding:14px}.kpi b{display:block;font-size:1.9rem;line-height:1.1}.kpi span{font-size:.85rem;color:#4B537F}
table{width:100%;border-collapse:collapse;font-size:.9rem}th,td{padding:8px 10px;border-bottom:1px solid var(--line);text-align:left;vertical-align:top}th{background:#F8F8FD;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#4B537F}
.btn{display:inline-block;background:var(--ink);color:#fff;border:0;border-radius:999px;padding:9px 16px;font-weight:700;cursor:pointer;text-decoration:none;font-size:.9rem}
.btn.warn{background:var(--flame)}.btn.ghost{background:#fff;color:var(--ink);border:1px solid var(--line)}
input[type=text],input[type=password],select{padding:9px 12px;border:1px solid var(--line);border-radius:10px;font:inherit}
.tag{display:inline-block;background:#EEF0FA;border-radius:999px;padding:2px 9px;font-size:.78rem;margin:1px}
.tag.aj{background:#FFF4D6}
.bar{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin:12px 0}
.ok{color:#1BA160;font-weight:700}.no{color:#B33}
.login{max-width:380px;margin:12vh auto}
.muted{color:#4B537F;font-size:.85rem}
.tbl{overflow-x:auto}
</style></head><body><div class="wrap">
<?php if (!$logged): ?>
  <div class="login card"><h1>Panel privado</h1><p class="muted">5° Encuentro Diversidades e Inclusión</p>
  <form method="post"><label>Contraseña<br><input type="password" name="clave" required style="width:100%;margin:6px 0 12px"></label><button class="btn">Entrar</button><?php if ($err) echo '<p class="no">' . h($err) . '</p>'; ?></form></div>
<?php else:
  $pdo = db();
  $f = clean((string)($_GET['f'] ?? ''), 80);
  $rows = $f ? (function() use ($pdo, $f) { $q = $pdo->prepare('SELECT * FROM registros WHERE nombres LIKE ? OR organizacion LIKE ? OR correo LIKE ? OR codigo LIKE ? ORDER BY id DESC'); $like = "%$f%"; $q->execute([$like,$like,$like,$like]); return $q->fetchAll(); })()
             : $pdo->query('SELECT * FROM registros ORDER BY id DESC')->fetchAll();
  $all = $pdo->query('SELECT requiere_ajuste, ajustes, grupos_enc, asistio, organizacion FROM registros')->fetchAll();
  $total = count($all); $asis = 0; $ajN = 0; $ajCount = []; $grCount = []; $orgs = [];
  foreach ($all as $r) {
    $asis += (int)$r['asistio']; $orgs[$r['organizacion']] = true;
    if ($r['requiere_ajuste']) { $ajN++; foreach (json_decode($r['ajustes'] ?: '[]', true) ?: [] as $a) $ajCount[$a] = ($ajCount[$a] ?? 0) + 1; }
    foreach (json_decode(decrypt_str($r['grupos_enc']) ?: '[]', true) ?: [] as $g) { $g = preg_replace('/^Otras:.*/', 'Otras', $g); $grCount[$g] = ($grCount[$g] ?? 0) + 1; }
  }
  arsort($ajCount); arsort($grCount);
  $comp = $pdo->query('SELECT * FROM compromisos WHERE aprobado=1 ORDER BY id DESC LIMIT 100')->fetchAll();
  $uso = $pdo->query('SELECT dia, SUM(n) n FROM agente_uso GROUP BY dia ORDER BY dia DESC LIMIT 7')->fetchAll();
?>
  <div class="bar" style="justify-content:space-between"><div><h1>Inscripciones · 5° Encuentro</h1><p class="muted" style="margin:0">Datos personales protegidos (Ley 1581 de 2012). Úsalos solo para la logística del evento.</p></div>
  <form method="post"><button class="btn ghost" name="salir" value="1">Salir</button></form></div>
  <div class="kpis">
    <div class="kpi"><b><?= $total ?></b><span>Personas inscritas</span></div>
    <div class="kpi"><b><?= count($orgs) ?></b><span>Organizaciones</span></div>
    <div class="kpi"><b><?= $ajN ?></b><span>Requieren ajustes razonables</span></div>
    <div class="kpi"><b><?= $asis ?></b><span>Asistencia marcada</span></div>
    <div class="kpi"><b><?= count($comp) ?></b><span>Compromisos en el muro</span></div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px">
    <div class="card"><h2 style="margin-top:0">Ajustes razonables solicitados</h2><?php if (!$ajCount) echo '<p class="muted">Ninguno todavía.</p>'; foreach ($ajCount as $k => $v) echo '<div>' . h($k) . ' <b>' . $v . '</b></div>'; ?></div>
    <div class="card"><h2 style="margin-top:0">Autorreconocimiento (agregado)</h2><p class="muted" style="margin-top:0">Solo cifras agregadas: no se muestra por persona.</p><?php foreach ($grCount as $k => $v) echo '<div>' . h($k) . ' <b>' . $v . '</b></div>'; ?></div>
    <div class="card"><h2 style="margin-top:0">Pliego · mensajes por día</h2><?php if (!$uso) echo '<p class="muted">Sin uso todavía.</p>'; foreach ($uso as $u) echo '<div>' . h($u['dia']) . ' <b>' . (int)$u['n'] . '</b></div>'; ?></div>
  </div>
  <h2>Listado</h2>
  <div class="bar"><form method="get" style="display:flex;gap:8px"><input type="text" name="f" value="<?= h($f) ?>" placeholder="Buscar nombre, organización, correo o código"><button class="btn">Buscar</button><?php if ($f) echo '<a class="btn ghost" href="./">Limpiar</a>'; ?></form><a class="btn warn" href="?csv=1">Exportar CSV (incluye documento)</a></div>
  <div class="card tbl"><table><thead><tr><th>Código</th><th>Nombre</th><th>Organización</th><th>Correo</th><th>Documento</th><th>Ajustes</th><th>Pliegue</th><th>Registro</th><th>Asistió</th></tr></thead><tbody>
  <?php foreach ($rows as $r): $doc = decrypt_str($r['documento_enc']); $aj = json_decode($r['ajustes'] ?: '[]', true) ?: []; ?>
    <tr><td><b><?= h($r['codigo']) ?></b></td><td><?= h($r['nombres']) ?><?= $r['pronombre'] ? '<br><span class="muted">' . h($r['pronombre']) . '</span>' : '' ?></td><td><?= h($r['organizacion']) ?></td><td><?= h($r['correo']) ?><?= $r['correo_enviado'] ? '' : ' <span class="no" title="El correo de confirmación no se pudo enviar">✕</span>' ?></td>
    <td>••••<?= h(substr($doc, -4)) ?></td><td><?php foreach ($aj as $a) echo '<span class="tag aj">' . h($a) . '</span>'; ?></td><td><?= h($r['pliegue']) ?></td><td class="muted"><?= h($r['creado']) ?></td>
    <td><form method="post"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="f" value="<?= h($f) ?>"><input type="hidden" name="asistio" value="<?= $r['asistio'] ? 0 : 1 ?>"><button class="btn <?= $r['asistio'] ? '' : 'ghost' ?>" style="padding:5px 12px"><?= $r['asistio'] ? '✓ Sí' : 'Marcar' ?></button></form></td></tr>
  <?php endforeach; if (!$rows) echo '<tr><td colspan="9" class="muted">Sin inscripciones todavía.</td></tr>'; ?>
  </tbody></table></div>
  <h2 id="muro">Muro de compromisos</h2>
  <div class="card tbl"><table><thead><tr><th>Compromiso</th><th>Organización</th><th>Pliegue</th><th>Fecha</th><th></th></tr></thead><tbody>
  <?php foreach ($comp as $m): ?><tr><td><?= h($m['texto']) ?></td><td><?= h($m['organizacion']) ?></td><td><?= h($m['pliegue']) ?></td><td class="muted"><?= h($m['creado']) ?></td><td><form method="post"><button class="btn ghost" name="borrar_compromiso" value="<?= (int)$m['id'] ?>" style="padding:5px 12px">Ocultar</button></form></td></tr><?php endforeach; ?>
  </tbody></table></div>
<?php endif; ?>
<p class="muted" style="text-align:center;margin-top:28px"><a href="https://startinlab.org" target="_blank" rel="noopener" style="color:#4B537F;text-decoration:none">by Startin<span style="color:#E01CC0">*</span>Lab</a></p></div></body></html>
