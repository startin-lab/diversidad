<?php
declare(strict_types=1);
// Panel del Laboratorio de IA: reparto por grupos, formación de células, fases, fichas y lista imprimible.
require __DIR__ . '/../api/taller_lib.php';
$c = cfg();
session_name($c['session_name']); session_start();
if (empty($_SESSION['admin'])) { header('Location: ./'); exit; }
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];
$pdo = taller_db();
function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function volver(string $msg): void { $_SESSION['flash_t'] = $msg; header('Location: taller.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) { http_response_code(400); exit('Sesión vencida. Vuelve a cargar el panel.'); }
  $a = (string)($_POST['accion'] ?? '');
  if ($a === 'formar') {
    $res = taller_formar($pdo);
    $txt = []; foreach ($res as $g => $cel) $txt[] = "Grupo $g: " . ($cel ? count($cel) . ' células (' . implode(', ', $cel) . ')' : 'sin personas');
    volver('Células formadas. ' . implode(' · ', $txt) . '.');
  }
  if ($a === 'fase') {
    $f = (string)($_POST['fase'] ?? '');
    if ($f === 'detener') { taller_kv_set($pdo, 'fase', null); volver('Taller en pausa: no hay fase en curso.'); }
    if (!isset(TALLER_FASES[$f])) volver('Fase desconocida.');
    taller_kv_set($pdo, 'fase', $f); taller_kv_set($pdo, 'fase_inicio', (string)time());
    volver('Fase en curso: ' . TALLER_FASES[$f]['nombre'] . ' (' . TALLER_FASES[$f]['min'] . ' min).');
  }
  if ($a === 'mover') {
    $code = strtoupper(clean((string)($_POST['codigo'] ?? ''), 12)); $g = (int)($_POST['grupo'] ?? 0); $cel = (int)($_POST['celula'] ?? 0);
    if (!isset(TALLER_GRUPOS[$g]) || $cel < 0 || $cel > TALLER_MAX_CELULAS) volver('Grupo o célula no válidos.');
    $q = $pdo->prepare('UPDATE registros SET taller_grupo=?, taller_celula=? WHERE codigo=? AND asistio=1'); $q->execute([$g, $cel ?: null, $code]);
    if (!$q->rowCount()) volver("No se movió: $code no existe o no ha registrado ingreso.");
    if ($cel > taller_celulas($pdo, $g)) taller_kv_set($pdo, "celulas_$g", (string)$cel);
    volver("$code quedó en el grupo $g" . ($cel ? ", célula $cel" : '') . '.');
  }
  if ($a === 'destacada') {
    $g = (int)($_POST['grupo'] ?? 0); $cel = (int)($_POST['celula'] ?? 0);
    if (!isset(TALLER_GRUPOS[$g])) volver('Grupo no válido.');
    $actual = (int)taller_kv($pdo, "destacada_$g", '0');
    taller_kv_set($pdo, "destacada_$g", $actual === $cel ? null : (string)$cel);
    volver($actual === $cel ? "Se quitó la destacada del grupo $g." : "Célula $cel es la destacada del grupo $g.");
  }
  if ($a === 'facilitar') {
    $id = (int)($_POST['id'] ?? 0); $si = !empty($_POST['si']);
    $q = $pdo->prepare('SELECT nombres FROM registros WHERE id=?'); $q->execute([$id]); $nom = $q->fetchColumn();
    if ($nom === false) volver('Esa persona no existe.');
    taller_facilitar($pdo, $id, $si);
    volver($si ? "$nom quedó en el equipo que facilita: no entra al reparto ni a la votación." : "$nom volvió a participar en el reparto.");
  }
  if ($a === 'reiniciar') {
    if (trim((string)($_POST['confirma'] ?? '')) !== 'REINICIAR') volver('No se reinició: escribe REINICIAR para confirmar.');
    taller_reiniciar($pdo);
    volver('Taller reiniciado: grupos repartidos de nuevo, sin células, fases ni fichas.');
  }
  if ($a === 'prueba_crear') {
    if (taller_prueba_n($pdo)) volver('Ya hay personas de prueba.');
    taller_prueba_crear($pdo);
    volver('Listo: 50 personas de prueba. 44 ya ingresaron y quedaron repartidas en los 3 grupos; 6 esperan en la mesa de entrada.');
  }
  if ($a === 'prueba_borrar') {
    $n = taller_prueba_borrar($pdo);
    volver("Se borraron $n personas de prueba y el taller quedó en cero.");
  }
  volver('Acción desconocida.');
}

if (isset($_GET['csv'])) {
  header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="fichas-laboratorio-ia-ronda-' . taller_ronda($pdo) . '.csv"');
  $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
  fputcsv($o, array_merge(['Grupo', 'Eje', 'Célula', 'Reto'], array_column(TALLER_CAMPOS, 0), ['Landing', 'Lema', 'Enlace de la landing', 'Votos', 'Presenta']), ';');
  $base = rtrim((string)(cfg()['site_url'] ?? 'https://diversidad.startinlab.org'), '/');
  foreach (TALLER_GRUPOS as $g => $d) { $vt = taller_votos($pdo, $g); for ($i = 1; $i <= taller_celulas($pdo, $g); $i++) {
    $f = taller_ficha($pdo, $g, $i); $l = taller_landing($pdo, $g, $i);
    fputcsv($o, array_merge([$g, $d['eje'], $i, $d['retos'][($i - 1) % 5]], array_values($f), [$l['titulo'] ?? '', $l['lema'] ?? '', $l ? "$base/taller/landing.php?g=$g&c=$i" : '', $vt[$i] ?? 0, (int)taller_kv($pdo, "destacada_$g", '0') === $i ? 'Sí' : '']), ';');
  } }
  exit;
}
// Todo el laboratorio (ronda en curso o una archivada): fichas, landings, votos e historial completo, sin nombres.
if (isset($_GET['exportar'])) {
  $id = (int)$_GET['exportar'];
  if ($id > 0) { $q = $pdo->prepare('SELECT ronda, datos FROM taller_archivo WHERE id=?'); $q->execute([$id]); $a = $q->fetch(); if (!$a) volver('Esa copia no existe.'); $json = $a['datos']; $nom = 'ronda-' . $a['ronda'] . '-archivada'; }
  else { $json = json_encode(taller_exportar($pdo), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); $nom = 'ronda-' . taller_ronda($pdo); }
  header('Content-Type: application/json; charset=utf-8'); header('Content-Disposition: attachment; filename="laboratorio-ia-' . $nom . '.json"');
  echo $json; exit;
}

$flash = $_SESSION['flash_t'] ?? ''; unset($_SESSION['flash_t']);
$gente = $pdo->query("SELECT codigo, nombres, organizacion, ajustes, taller_grupo g, taller_celula c FROM registros WHERE asistio=1 AND taller_rol='' ORDER BY taller_grupo, taller_celula, nombres")->fetchAll();
$equipo = $pdo->query("SELECT id, codigo, nombres, organizacion, asistio FROM registros WHERE taller_rol='facilitador' ORDER BY nombres")->fetchAll();
$buscar = clean((string)($_GET['buscar'] ?? ''), 80); $hallados = [];
if (mb_strlen($buscar) >= 3) { $q = $pdo->prepare("SELECT id, codigo, nombres, organizacion, asistio, taller_rol FROM registros WHERE nombres LIKE ? OR codigo=? ORDER BY nombres LIMIT 10"); $q->execute(['%' . $buscar . '%', strtoupper($buscar)]); $hallados = $q->fetchAll(); }
$porG = []; foreach ($gente as $p) $porG[(int)$p['g']][(int)$p['c']][] = $p;
$est = taller_estado($pdo); $formado = $est['formado']; $imprimir = isset($_GET['imprimir']);
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Taller · Panel · 5° Encuentro</title>
<link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<style>
body{margin:0;font:15px/1.45 "elza","Segoe UI",system-ui,sans-serif;background:#F7F4EE;color:#16162B}.wrap{max-width:1200px;margin:0 auto;padding:20px 16px 60px}
h1{margin:0;font-size:1.5rem}h2{margin:26px 0 10px;font-size:1.15rem}.muted{color:#5A5F7D}
.card{background:#fff;border:1px solid #ECE7E4;border-radius:14px;padding:14px 16px}
.grid{display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr))}
.btn{border:0;border-radius:999px;padding:9px 16px;font:700 .92rem "elza","Segoe UI",system-ui,sans-serif;cursor:pointer;background:#F26A21;color:#fff;text-decoration:none;display:inline-block}.btn.ghost{background:#fff;color:#0C1646;box-shadow:inset 0 0 0 2px #0C1646}.btn.on{background:#0C1646;color:#FFC20E}
.bar{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.flash{border-color:#FFC20E;background:#FFF8E1;margin-top:12px}
.g1{border-top:6px solid #FFC20E}.g2{border-top:6px solid #13A79A}.g3{border-top:6px solid #EE7DB9}
.cel{border-top:1px solid #ECE7E4;padding:8px 0}.cel summary{cursor:pointer;font-weight:700}.cel ul{margin:6px 0;padding-left:18px}
.lsc{background:#E6F6F4;color:#0B6B62;border-radius:6px;padding:0 6px;font-size:.8rem;font-weight:700}
.ficha{font-size:.88rem;background:#FAF8F4;border-radius:10px;padding:8px 10px;margin-top:6px}.ficha b{display:block;font-size:.8rem;color:#5A5F7D}
.reloj{font-size:1.6rem;font-weight:900;font-variant-numeric:tabular-nums;color:#F26A21}
input,select{font:inherit;padding:8px;border:1px solid #CFCBD6;border-radius:10px}
.btn:focus-visible,input:focus-visible,select:focus-visible,summary:focus-visible{outline:3px solid #FFC20E;outline-offset:2px}
table.print{border-collapse:collapse;width:100%;font-size:12pt}table.print td,table.print th{border:1px solid #999;padding:4px 8px;text-align:left}
@media print{.noprint{display:none!important}body{background:#fff}.pg{page-break-after:always}.card{border:0;padding:0}}
.startin-credit{position:fixed;z-index:60;right:16px;bottom:16px;font:600 .68rem/1.2 "elza","Segoe UI",system-ui,sans-serif;color:#6D6D7C;text-decoration:none;background:rgba(255,255,255,.88);border:1px solid #ECE7E4;border-radius:999px;padding:4px 10px;opacity:.85}.startin-credit b{color:#16162B;font-weight:800}
</style></head><body><div class="wrap">
<?php if ($imprimir): ?>
  <p class="noprint bar"><button class="btn" onclick="print()">Imprimir</button><a class="btn ghost" href="taller.php">Volver</a></p>
  <?php foreach (TALLER_GRUPOS as $g => $d): ?><div class="pg"><h1>Grupo <?= $g ?> · <?= h($d['eje']) ?></h1><p><?= h($d['pregunta']) ?></p>
    <?php foreach ($porG[$g] ?? [] as $cel => $ps): ?><h2><?= $cel ? 'Célula ' . $cel . ' · ' . h($d['retos'][($cel - 1) % 5]) : 'Sin célula todavía' ?></h2>
      <table class="print"><tr><th>Nombre</th><th>Organización</th><th>Código</th></tr><?php foreach ($ps as $p): ?><tr><td><?= h($p['nombres']) ?><?= taller_es_lsc($p['ajustes']) ? ' (LSC)' : '' ?></td><td><?= h($p['organizacion']) ?></td><td><?= h($p['codigo']) ?></td></tr><?php endforeach; ?></table>
    <?php endforeach; ?></div><?php endforeach; ?>
<?php else: ?>
  <div class="bar" style="justify-content:space-between"><div><h1>Laboratorio de IA · Taller</h1><p class="muted" style="margin:0">9:30 – 10:30 · Las personas reciben grupo al registrar su ingreso. Datos personales protegidos (Ley 1581 de 2012).</p></div>
  <div class="bar"><a class="btn ghost" href="./">Volver al panel</a><a class="btn ghost" href="../taller/pantalla.php" target="_blank" rel="noopener">Pantalla del auditorio</a><a class="btn ghost" href="?imprimir=1">Lista imprimible</a><a class="btn ghost" href="?csv=1">Fichas en CSV</a><a class="btn" href="?exportar=0">Descargar todo el laboratorio</a></div></div>
  <?php if ($flash) echo '<p class="card flash">' . h($flash) . '</p>'; ?>

  <div class="grid" style="margin-top:14px">
    <div class="card"><h2 style="margin-top:0">1. Reparto</h2>
      <p><b><?= count($gente) ?></b> personas con ingreso registrado. <?php foreach (TALLER_GRUPOS as $g => $d) { $n = 0; foreach ($porG[$g] ?? [] as $ps) $n += count($ps); echo "Grupo $g: <b>$n</b>. "; } ?></p>
      <p class="muted">Quienes pidieron intérprete de LSC van al grupo <?= taller_grupo_lsc() ?> y, al formar, juntas a la célula 1. Cada grupo se divide siempre en 5 células, una por reto (con 90 personas: 30 por grupo y 6 por célula). Quien llega tarde entra a la célula más pequeña.</p>
      <form method="post" onsubmit="return confirm(<?= h(json_encode($formado ? '¿Formar las células de nuevo? Se rehace el reparto de células y las fichas ya escritas pueden quedar con otra gente.' : '¿Formar las células ahora con las personas que ya ingresaron? Quien llegue después entra a la célula más pequeña de su grupo.')) ?>)">
        <input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="formar"><button class="btn"><?= $formado ? 'Formar células de nuevo' : 'Formar células' ?></button></form>
    </div>
    <div class="card"><h2 style="margin-top:0">2. Fases</h2>
      <p>En curso: <b><?= $est['fase'] ? h($est['fase']['nombre']) : 'ninguna' ?></b> <span class="reloj" id="reloj"></span></p>
      <form method="post" class="bar"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="fase">
        <?php foreach (TALLER_FASES as $k => $f): ?><button class="btn <?= ($est['fase']['id'] ?? '') === $k ? 'on' : 'ghost' ?>" name="fase" value="<?= $k ?>"><?= h($f['nombre']) ?> · <?= $f['min'] ?>′</button><?php endforeach; ?>
        <button class="btn ghost" name="fase" value="detener">Pausar</button></form>
      <p class="muted">Al pulsar una fase arranca su cronómetro en todos los celulares y en la pantalla.</p>
    </div>
    <div class="card"><h2 style="margin-top:0">Mover a una persona</h2>
      <form method="post" class="bar"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="mover">
        <label>Código <input name="codigo" placeholder="5E-XXXXXX" size="10" required></label>
        <label>Grupo <select name="grupo"><?php foreach (TALLER_GRUPOS as $g => $d) echo "<option>$g</option>"; ?></select></label>
        <label>Célula <select name="celula"><option value="0">Sin célula</option><?php for ($i = 1; $i <= TALLER_MAX_CELULAS; $i++) echo "<option>$i</option>"; ?></select></label>
        <button class="btn">Mover</button></form>
    </div>
  </div>

  <h2>Grupos y células</h2>
  <div class="grid">
  <?php foreach (TALLER_GRUPOS as $g => $d): $dest = (int)taller_kv($pdo, "destacada_$g", '0'); $vt = taller_votos($pdo, $g); ?>
    <div class="card g<?= $g ?>"><h3 style="margin:0">Grupo <?= $g ?> · <?= h($d['eje']) ?></h3><p class="muted" style="margin:2px 0 8px"><?= taller_celulas($pdo, $g) ?> células</p>
    <?php foreach ($porG[$g] ?? [] as $cel => $ps): $f = $cel ? taller_ficha($pdo, $g, $cel) : []; ?>
      <details class="cel"><summary><?= $cel ? "Célula $cel" : 'Sin célula' ?> · <?= count($ps) ?> personas<?= $cel ? ' · ' . ($vt[$cel] ?? 0) . ' votos' : '' ?><?= $cel && taller_landing($pdo, $g, $cel) ? ' · landing ✓' : '' ?><?= $cel && $dest === $cel ? ' · ★ presenta' : '' ?></summary>
        <?php if ($cel): $lp = taller_landing($pdo, $g, $cel); ?><p style="margin:4px 0"><b>Reto:</b> <?= h($d['retos'][($cel - 1) % 5]) ?></p><?php if ($lp): ?><p style="margin:4px 0"><b>Landing:</b> <a href="../taller/landing.php?g=<?= $g ?>&amp;c=<?= $cel ?>" target="_blank" rel="noopener"><?= h($lp['titulo']) ?></a></p><?php endif; endif; ?>
        <ul><?php foreach ($ps as $p): ?><li><?= h($p['nombres']) ?> · <span class="muted"><?= h($p['organizacion']) ?> · <?= h($p['codigo']) ?></span> <?= taller_es_lsc($p['ajustes']) ? '<span class="lsc">LSC</span>' : '' ?></li><?php endforeach; ?></ul>
        <?php if ($cel): foreach (TALLER_CAMPOS as $k => [$tit]) if ($f[$k] !== '') echo '<div class="ficha"><b>' . h($tit) . '</b>' . nl2br(h($f[$k])) . '</div>'; ?>
          <form method="post" style="margin-top:6px"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="destacada"><input type="hidden" name="grupo" value="<?= $g ?>"><input type="hidden" name="celula" value="<?= $cel ?>"><button class="btn ghost"><?= $dest === $cel ? 'Quitar: no presenta' : '★ Esta célula presenta' ?></button></form>
        <?php endif; ?>
      </details>
    <?php endforeach; if (empty($porG[$g])) echo '<p class="muted">Todavía nadie.</p>'; ?>
    </div>
  <?php endforeach; ?>
  </div>

  <h2 id="equipo">Equipo que facilita</h2>
  <div class="card"><p class="muted" style="margin-top:0">Registran su ingreso como cualquier persona, pero no entran al reparto de grupos ni a las células, y no votan. En «Mi taller» ven la pantalla de facilitación.</p>
    <?php if ($equipo): ?><ul style="margin:0 0 10px;padding-left:18px"><?php foreach ($equipo as $p): ?><li><b><?= h($p['nombres']) ?></b> · <span class="muted"><?= h($p['organizacion']) ?> · <?= h($p['codigo']) ?> · <?= $p['asistio'] ? 'ya ingresó' : 'aún no ingresa' ?></span>
      <form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="facilitar"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="btn ghost" style="padding:4px 12px">Quitar</button></form></li><?php endforeach; ?></ul>
    <?php else: ?><p>Nadie marcado todavía.</p><?php endif; ?>
    <form method="get" action="#equipo" class="bar"><label>Buscar por nombre o código <input name="buscar" value="<?= h($buscar) ?>" placeholder="Nombre o 5E-XXXXXX" required minlength="3"></label><button class="btn ghost">Buscar</button></form>
    <?php if ($buscar !== ''): ?><?php if (!$hallados) echo '<p class="muted">No hay inscripciones con ese nombre o código.</p>'; foreach ($hallados as $p): ?>
      <div class="bar" style="margin-top:6px"><span><b><?= h($p['nombres']) ?></b> · <span class="muted"><?= h($p['organizacion']) ?> · <?= h($p['codigo']) ?></span></span>
      <?php if ($p['taller_rol'] === 'facilitador'): ?><span class="muted">ya facilita</span><?php else: ?><form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="facilitar"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="si" value="1"><button class="btn">Marcar como facilitación</button></form><?php endif; ?></div>
    <?php endforeach; endif; ?>
  </div>

  <h2 id="prueba">Ensayar el taller</h2>
  <?php $np = taller_prueba_n($pdo); ?>
  <div class="card" style="<?= $np ? 'border-color:#F26A21;background:#FFF3EA' : '' ?>">
  <?php if (!$np): ?>
    <p style="margin-top:0">Crea 50 personas ficticias para ensayar todo el flujo sin tocar a los inscritos reales. No reciben correos. Las 44 primeras quedan con ingreso y repartidas en grupos; las otras 6 las puedes validar en la mesa de entrada para ver cómo llega alguien tarde.</p>
    <form method="post"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="prueba_crear"><button class="btn">Crear 50 personas de prueba</button></form>
  <?php else: $pq = $pdo->query("SELECT codigo, asistio, taller_grupo g, taller_celula c FROM registros WHERE codigo LIKE '" . TALLER_PRUEBA . "%' ORDER BY codigo")->fetchAll(); ?>
    <p style="margin-top:0"><b>Modo prueba activo: <?= $np ?> personas ficticias.</b> Bórralas al terminar: también cuentan en las cifras del panel. Al borrarlas se guarda una copia de lo que produjeron.</p>
    <p><b>Sin ingreso (valídalas en <a href="../ingreso/" target="_blank" rel="noopener">la mesa de entrada</a>):</b> <?php foreach ($pq as $p) if (!$p['asistio']) echo '<code>' . h($p['codigo']) . '</code> '; ?></p>
    <p><b>Abre «Mi taller» como si fueras una de ellas:</b> <?php $vis = []; foreach ($pq as $p) if ($p['asistio'] && $p['g'] && !isset($vis[$p['g']])) { $vis[$p['g']] = 1; echo '<a class="btn ghost" target="_blank" rel="noopener" href="../taller/?c=' . h($p['codigo']) . '">Grupo ' . (int)$p['g'] . ' · ' . h($p['codigo']) . '</a> '; } ?></p>
    <form method="post" onsubmit="return confirm('¿Borrar las personas de prueba y dejar el taller en cero (sin células, fases ni fichas)? Los inscritos reales no se tocan.')"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="prueba_borrar"><button class="btn">Borrar personas de prueba y reiniciar</button></form>
  <?php endif; ?>
  </div>

  <h2 id="datos">Lo que produce el laboratorio</h2>
  <?php $ronda = taller_ronda($pdo); $hn = $pdo->prepare('SELECT tipo, COUNT(*) n FROM taller_historial WHERE ronda=? GROUP BY tipo'); $hn->execute([$ronda]); $hc = $hn->fetchAll(PDO::FETCH_KEY_PAIR);
    $arch = $pdo->query('SELECT id, ronda, motivo, creado, LENGTH(datos) b FROM taller_archivo ORDER BY id DESC LIMIT 12')->fetchAll(); ?>
  <div class="card"><p style="margin-top:0">Todo queda guardado: cada cambio de la ficha, cada landing (con lo que pidieron cambiar), cada pregunta y respuesta de Pliego y cada voto, con fecha y hora. <b>Ronda en curso: <?= $ronda ?></b> · <?= (int)($hc['ficha'] ?? 0) ?> cambios de ficha · <?= (int)($hc['landing'] ?? 0) ?> landings · <?= (int)($hc['chat'] ?? 0) ?> conversaciones con Pliego · <?= (int)($hc['voto'] ?? 0) ?> votos.</p>
    <p class="muted">El archivo no lleva nombres de personas: solo grupo, célula y organización.</p>
    <p><a class="btn" href="?exportar=0">Descargar la ronda en curso</a></p>
    <?php if ($arch): ?><p style="margin-bottom:4px"><b>Rondas guardadas antes de reiniciar o borrar pruebas:</b></p><ul style="margin:0;padding-left:18px"><?php foreach ($arch as $a) echo '<li>Ronda ' . (int)$a['ronda'] . ' · ' . h($a['motivo']) . ' · ' . h($a['creado']) . ' · <a href="?exportar=' . (int)$a['id'] . '">descargar</a></li>'; ?></ul><?php endif; ?>
  </div>

  <h2>Reiniciar el taller</h2>
  <div class="card"><p class="muted" style="margin-top:0">Úsalo solo si hiciste pruebas: deja en cero células, fases, fichas y votos, y vuelve a repartir por grupos a quienes ya ingresaron. No cambia la asistencia. Antes de limpiar se guarda una copia completa de la ronda, que puedes descargar arriba.</p>
  <form method="post" class="bar"><input type="hidden" name="csrf" value="<?= h($csrf) ?>"><input type="hidden" name="accion" value="reiniciar"><input name="confirma" placeholder="Escribe REINICIAR" autocomplete="off"><button class="btn ghost">Reiniciar</button></form></div>
  <script>
  (function(){const f=<?= json_encode($est['fase'] ? $est['fase']['fin'] : 0) ?>,off=<?= time() ?>-Date.now()/1000,el=document.getElementById("reloj");
  function t(){if(!f){el.textContent="";return;}const s=Math.max(0,Math.round(f-(Date.now()/1000+off)));el.textContent=s?Math.floor(s/60)+":"+String(s%60).padStart(2,"0"):"Tiempo";}t();setInterval(t,1000);
  setInterval(()=>{const a=document.activeElement;if(document.hidden||(a&&/INPUT|SELECT|TEXTAREA/.test(a.tagName))||document.querySelector("details[open]"))return;location.reload();},20000);})();
  </script>
<?php endif; ?>
</div><a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener" aria-label="Hecho por Startin Lab">by <b>Startin*Lab</b></a></body></html>
