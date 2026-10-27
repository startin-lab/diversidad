<?php
declare(strict_types=1);
require __DIR__ . '/../api/lib.php';
$c = cfg();
session_name($c['session_name']); session_start();
$err = '';
if (isset($_POST['clave'])) {
  if (password_verify((string)$_POST['clave'], $c['admin_pass_hash'])) { $_SESSION['admin'] = true; session_regenerate_id(true); header('Location: ./'); exit; }
  $err = 'Contraseña incorrecta.'; usleep(600000);
}
$logged = !empty($_SESSION['admin']);
if ($logged && empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'] ?? '';
if ($logged && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['clave']) && !hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) { http_response_code(400); exit('Sesión vencida. Vuelve a cargar el panel.'); }
if (isset($_POST['salir'])) { session_destroy(); header('Location: ./'); exit; }
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
$PLIEGUES = ['Personas mayores','Género y cuidado','Educación inclusiva','Discapacidad y trabajo','La ley ya cambió','Población afrodescendiente','Pueblos indígenas y Rrom','Personas LGBTIQ+','Migración y conflicto'];
if ($logged && isset($_POST['guardar'], $_POST['id'])) {
  $id = (int)$_POST['id'];
  $nom = clean((string)($_POST['nombres'] ?? ''), 120); $org = clean((string)($_POST['organizacion'] ?? ''), 120);
  $mail = mb_strtolower(clean((string)($_POST['correo'] ?? ''), 190)); $pron = clean((string)($_POST['pronombre'] ?? ''), 40);
  $pl = clean((string)($_POST['pliegue'] ?? ''), 40); $comp = clean((string)($_POST['compromiso'] ?? ''), 200);
  $req = !empty($_POST['requiere_ajuste']);
  $aj = $req ? array_values(array_filter(array_map(fn($a) => clean($a, 60), explode(',', (string)($_POST['ajustes'] ?? ''))))) : [];
  $errs = [];
  if (mb_strlen($nom) < 3) $errs[] = 'nombre'; if (mb_strlen($org) < 2) $errs[] = 'organización'; if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) $errs[] = 'correo';
  if (!$errs) { $q = db()->prepare('SELECT id FROM registros WHERE correo=? AND id<>?'); $q->execute([$mail, $id]); if ($q->fetch()) $errs[] = 'el correo ya lo usa otro registro'; }
  if ($errs) { $_SESSION['flash'] = 'No se guardó: revisa ' . implode(', ', $errs) . '.'; header('Location: ./?edit=' . $id); exit; }
  if (!empty($_POST['quitar_foto'])) db()->prepare('UPDATE registros SET foto=NULL WHERE id=?')->execute([$id]);
  db()->prepare('UPDATE registros SET nombres=?, organizacion=?, correo=?, pronombre=?, pliegue=?, compromiso=?, requiere_ajuste=?, ajustes=? WHERE id=?')
     ->execute([$nom, $org, $mail, $pron, $pl, $comp, $req ? 1 : 0, json_encode($aj, JSON_UNESCAPED_UNICODE), $id]);
  $_SESSION['flash'] = 'Registro actualizado.'; header('Location: ./?edit=' . $id); exit;
}
if ($logged && isset($_POST['eliminar'], $_POST['id'])) {
  $id = (int)$_POST['id'];
  $q = db()->prepare('SELECT codigo, compromiso, organizacion FROM registros WHERE id=?'); $q->execute([$id]); $r = $q->fetch();
  if (!$r || strtoupper(trim((string)($_POST['confirma'] ?? ''))) !== $r['codigo']) { $_SESSION['flash'] = 'No se eliminó: escribe el código exacto del registro para confirmar.'; header('Location: ./?edit=' . $id); exit; }
  db()->prepare('DELETE FROM registros WHERE id=?')->execute([$id]);
  if ($r['compromiso'] !== '') db()->prepare('UPDATE compromisos SET aprobado=0 WHERE texto=?')->execute([$r['compromiso']]);
  $_SESSION['flash'] = 'Registro ' . $r['codigo'] . ' eliminado.'; header('Location: ./'); exit;
}
if ($logged && isset($_POST['reenviar'], $_POST['id'])) {
  $id = (int)$_POST['id']; $q = db()->prepare('SELECT * FROM registros WHERE id=?'); $q->execute([$id]); $r = $q->fetch();
  if ($r) { $sent = send_mail($r['correo'], 'Tu inscripción al 5° Encuentro · código ' . $r['codigo'], correo_confirmacion_html($r, false));
    db()->prepare('UPDATE registros SET correo_enviado=? WHERE id=?')->execute([$sent ? 1 : 0, $id]);
    $_SESSION['flash'] = $sent ? 'Correo reenviado a ' . $r['correo'] . '.' : 'El servidor no pudo enviar el correo.'; }
  header('Location: ./?edit=' . $id); exit;
}
if ($logged && isset($_POST['asistio'], $_POST['id'])) {
  db()->prepare('UPDATE registros SET asistio=? WHERE id=?')->execute([(int)$_POST['asistio'], (int)$_POST['id']]);
  header('Location: ./?f=' . urlencode((string)($_POST['f'] ?? ''))); exit;
}
if ($logged && isset($_POST['borrar_fotos'])) {
  if (strtoupper(trim((string)($_POST['confirma'] ?? ''))) !== 'BORRAR FOTOS') { $_SESSION['flash'] = 'No se borraron las fotos: escribe BORRAR FOTOS para confirmar.'; header('Location: ./#datos'); exit; }
  $n = db()->exec('UPDATE registros SET foto=NULL WHERE foto IS NOT NULL');
  $_SESSION['flash'] = 'Fotos eliminadas: ' . (int)$n . '. Las escarapelas ya descargadas no cambian.'; header('Location: ./#datos'); exit;
}
if ($logged && isset($_POST['borrar_compromiso'])) {
  db()->prepare('UPDATE compromisos SET aprobado=0 WHERE id=?')->execute([(int)$_POST['borrar_compromiso']]);
  header('Location: ./#muro'); exit;
}
if ($logged && isset($_GET['csv'])) {
  $rows = db()->query('SELECT id,codigo,nombres,documento_enc,documento_hash,organizacion,correo,pronombre,grupos_enc,requiere_ajuste,ajustes,pliegue,compromiso,consentimiento,asistio,correo_enviado,creado FROM registros ORDER BY id')->fetchAll();
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
.ed-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px 16px}.ed-form label{display:flex;flex-direction:column;gap:4px;font-weight:700;font-size:.85rem}.ed-form label.chk{flex-direction:row;align-items:center;gap:8px}.ed-form input[type=text],.ed-form select{font-weight:400}.ed-form .bar{grid-column:1/-1}
.foto-adm{grid-column:1/-1;display:flex;align-items:center;gap:12px}.foto-adm img{border-radius:50%;object-fit:cover}
.ed-actions{border-top:1px solid var(--line);margin-top:14px;padding-top:14px;display:grid;gap:12px}
</style></head><body><div class="wrap">
<?php if (!$logged): ?>
  <div class="login card"><h1>Panel privado</h1><p class="muted">5° Encuentro Diversidades e Inclusión</p>
  <form method="post"><label>Contraseña<br><input type="password" name="clave" required style="width:100%;margin:6px 0 12px"></label><button class="btn">Entrar</button><?php if ($err) echo '<p class="no">' . h($err) . '</p>'; ?></form></div>
<?php else:
  $pdo = db();
  $f = clean((string)($_GET['f'] ?? ''), 80);
  $rows = $f ? (function() use ($pdo, $f) { $q = $pdo->prepare('SELECT id,codigo,nombres,documento_enc,documento_hash,organizacion,correo,pronombre,grupos_enc,requiere_ajuste,ajustes,pliegue,compromiso,consentimiento,asistio,correo_enviado,creado FROM registros WHERE nombres LIKE ? OR organizacion LIKE ? OR correo LIKE ? OR codigo LIKE ? ORDER BY id DESC'); $like = "%$f%"; $q->execute([$like,$like,$like,$like]); return $q->fetchAll(); })()
             : $pdo->query('SELECT id,codigo,nombres,documento_enc,documento_hash,organizacion,correo,pronombre,grupos_enc,requiere_ajuste,ajustes,pliegue,compromiso,consentimiento,asistio,correo_enviado,creado FROM registros ORDER BY id DESC')->fetchAll();
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
  <form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><button class="btn ghost" name="salir" value="1">Salir</button></form></div>
  <?php if ($flash) echo '<p class="card" style="padding:12px 16px;border-color:#FFC20E;background:#FFF8E1">' . h($flash) . '</p>'; ?>
  <?php if (isset($_GET['edit'])): $eq = $pdo->prepare('SELECT * FROM registros WHERE id=?'); $eq->execute([(int)$_GET['edit']]); $e = $eq->fetch(); ?>
  <?php if (!$e): ?><p class="card">Ese registro ya no existe. <a href="./">Volver</a></p><?php else: $eaj = json_decode($e['ajustes'] ?: '[]', true) ?: []; ?>
  <div class="card" id="editar" style="margin:16px 0;border:2px solid var(--ink)">
    <div class="bar" style="justify-content:space-between;margin-top:0"><h2 style="margin:0">Editar registro <?= h($e['codigo']) ?></h2><a class="btn ghost" href="./">Cerrar</a></div>
    <p class="muted" style="margin-top:0">Documento ••••<?= h(substr(decrypt_str($e['documento_enc']), -4)) ?> · inscrito el <?= h($e['creado']) ?>. El código y el documento no se pueden cambiar. La pregunta 5 (autorreconocimiento) no se edita desde aquí.</p>
    <form method="post" class="ed-form"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
      <label>Nombres y apellidos<input type="text" name="nombres" value="<?= h($e['nombres']) ?>" maxlength="120" required></label>
      <label>Organización<input type="text" name="organizacion" value="<?= h($e['organizacion']) ?>" maxlength="120" required></label>
      <label>Correo<input type="text" name="correo" value="<?= h($e['correo']) ?>" maxlength="190" required></label>
      <label>Pronombre<input type="text" name="pronombre" value="<?= h($e['pronombre']) ?>" maxlength="40"></label>
      <label>Pliegue<select name="pliegue"><option value=""<?= $e['pliegue'] === '' ? ' selected' : '' ?>>(sin pliegue)</option><?php foreach ($PLIEGUES as $pl) echo '<option' . ($pl === $e['pliegue'] ? ' selected' : '') . '>' . h($pl) . '</option>'; ?></select></label>
      <label>Compromiso<input type="text" name="compromiso" value="<?= h($e['compromiso']) ?>" maxlength="200"></label>
      <label class="chk"><input type="checkbox" name="requiere_ajuste" value="1" <?= $e['requiere_ajuste'] ? 'checked' : '' ?>> Requiere ajustes razonables</label>
      <label>Ajustes (separados por coma)<input type="text" name="ajustes" value="<?= h(implode(', ', $eaj)) ?>" maxlength="400"></label>
      <div class="foto-adm"><?php if (!empty($e['foto'])): ?><img src="<?= h(decrypt_str($e['foto'])) ?>" alt="Foto de <?= h($e['nombres']) ?>" width="72" height="72"><label class="chk"><input type="checkbox" name="quitar_foto" value="1"> Quitar foto</label><?php else: ?><span class="muted">Sin foto guardada.</span><?php endif; ?></div>
      <div class="bar"><button class="btn" name="guardar" value="1">Guardar cambios</button></div>
    </form>
    <div class="ed-actions">
      <form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$e['id'] ?>"><button class="btn ghost" name="reenviar" value="1">Reenviar correo de confirmación</button><span class="muted"> Va sin escarapela adjunta: le indica que puede volver a generarla en el portal con su mismo documento y correo.</span></form>
      <form method="post" onsubmit="return confirm('¿Eliminar definitivamente <?= h($e['codigo']) ?>? No se puede deshacer.')"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
        <label class="muted">Para eliminar, escribe el código <b><?= h($e['codigo']) ?></b>: <input type="text" name="confirma" autocomplete="off" style="width:140px"></label> <button class="btn warn" name="eliminar" value="1">Eliminar registro</button></form>
    </div>
  </div>
  <?php endif; endif; ?>
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
  <div class="card tbl"><table><thead><tr><th>Código</th><th>Nombre</th><th>Organización</th><th>Correo</th><th>Documento</th><th>Ajustes</th><th>Pliegue</th><th>Registro</th><th>Asistió</th><th></th></tr></thead><tbody>
  <?php foreach ($rows as $r): $doc = decrypt_str($r['documento_enc']); $aj = json_decode($r['ajustes'] ?: '[]', true) ?: []; ?>
    <tr><td><b><?= h($r['codigo']) ?></b></td><td><?= h($r['nombres']) ?><?= $r['pronombre'] ? '<br><span class="muted">' . h($r['pronombre']) . '</span>' : '' ?></td><td><?= h($r['organizacion']) ?></td><td><?= h($r['correo']) ?><?= $r['correo_enviado'] ? '' : ' <span class="no" title="El correo de confirmación no se pudo enviar">✕</span>' ?></td>
    <td>••••<?= h(substr($doc, -4)) ?></td><td><?php foreach ($aj as $a) echo '<span class="tag aj">' . h($a) . '</span>'; ?></td><td><?= h($r['pliegue']) ?></td><td class="muted"><?= h($r['creado']) ?></td>
    <td><form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="f" value="<?= h($f) ?>"><input type="hidden" name="asistio" value="<?= $r['asistio'] ? 0 : 1 ?>"><button class="btn <?= $r['asistio'] ? '' : 'ghost' ?>" style="padding:5px 12px"><?= $r['asistio'] ? '✓ Sí' : 'Marcar' ?></button></form></td><td><a class="btn ghost" style="padding:5px 12px" href="?edit=<?= (int)$r['id'] ?>">Editar</a></td></tr>
  <?php endforeach; if (!$rows) echo '<tr><td colspan="10" class="muted">Sin inscripciones todavía.</td></tr>'; ?>
  </tbody></table></div>
  <h2 id="muro">Muro de compromisos</h2>
  <div class="card tbl"><table><thead><tr><th>Compromiso</th><th>Organización</th><th>Pliegue</th><th>Fecha</th><th></th></tr></thead><tbody>
  <?php foreach ($comp as $m): ?><tr><td><?= h($m['texto']) ?></td><td><?= h($m['organizacion']) ?></td><td><?= h($m['pliegue']) ?></td><td class="muted"><?= h($m['creado']) ?></td><td><form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><button class="btn ghost" name="borrar_compromiso" value="<?= (int)$m['id'] ?>" style="padding:5px 12px">Ocultar</button></form></td></tr><?php endforeach; ?>
  </tbody></table></div>
  <h2 id="datos">Datos después del evento</h2>
  <div class="card"><p style="margin-top:0">Fotos guardadas (cifradas): <b><?= (int)$pdo->query('SELECT COUNT(*) FROM registros WHERE foto IS NOT NULL')->fetchColumn() ?></b>. Se usan solo para regenerar la escarapela y mostrarla en la puerta de entrada. Recomendado: borrarlas cuando termine el evento.</p>
  <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="text" name="confirma" placeholder="Escribe BORRAR FOTOS" autocomplete="off" style="max-width:220px"><button class="btn warn" name="borrar_fotos" value="1">Borrar todas las fotos</button></form></div>
<?php endif; ?>
</div><style>.startin-credit{position:fixed;z-index:60;right:12px;bottom:calc(var(--dock,0px) + 10px + env(safe-area-inset-bottom,0px));font:600 .68rem/1.2 "elza","Segoe UI",system-ui,sans-serif;color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.88);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);border:1px solid #ECE7E4;border-radius:999px;padding:4px 10px;opacity:.85;transition:opacity .2s,color .2s}.startin-credit b{color:#16162B;font-weight:800;letter-spacing:-.01em}.startin-credit:hover,.startin-credit:focus-visible{opacity:1;color:#16162B}@media (min-width:901px){.startin-credit{bottom:16px;right:16px}}</style><a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener" aria-label="Hecho por Startin Lab">by <b>Startin*Lab</b></a></body></html>
