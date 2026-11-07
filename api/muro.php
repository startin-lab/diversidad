<?php
declare(strict_types=1);
// Muro de compromisos. Desde el portal se publica con la organización escrita a mano; desde «Mi taller»
// (con el código de la escarapela) se toma la organización de la inscripción y el nombre solo si la persona lo autoriza.
require __DIR__ . '/lib.php';
try {
  $pdo = db();
  // Columnas nuevas (nombre opcional y persona que lo escribió): se crean una sola vez.
  try { $pdo->query('SELECT nombre, registro_id, mostrar_foto FROM compromisos LIMIT 0'); }
  catch (Throwable $e) { $pdo->exec("ALTER TABLE compromisos ADD COLUMN IF NOT EXISTS nombre VARCHAR(120) NOT NULL DEFAULT '', ADD COLUMN IF NOT EXISTS registro_id INT NULL, ADD COLUMN IF NOT EXISTS mostrar_foto TINYINT(1) NOT NULL DEFAULT 0, ADD INDEX IF NOT EXISTS (registro_id)"); }
  $mio = null;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = body_json();
    $texto = clean((string)($in['texto'] ?? ''), 200);
    if (mb_strlen($texto) < 8) json_out(['ok' => false, 'error' => 'Escribe un compromiso un poco más largo.'], 422);
    $code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($in['c'] ?? '')));
    if ($code !== '') {
      $q = $pdo->prepare('SELECT id, nombres, organizacion, pliegue FROM registros WHERE codigo=?'); $q->execute([$code]);
      $r = $q->fetch();
      if (!$r) json_out(['ok' => false, 'error' => 'No encontramos tu código.'], 404);
      // Nombre como aparece en la escarapela y foto: solo si la persona lo autoriza al escribir el compromiso.
      $autoriza = !empty($in['mostrar_nombre']);
      $nombre = $autoriza ? clean((string)$r['nombres'], 120) : '';
      // Un compromiso por persona: si ya tenía uno, se actualiza.
      $q = $pdo->prepare('SELECT id FROM compromisos WHERE registro_id=?'); $q->execute([(int)$r['id']]);
      if ($id = (int)$q->fetchColumn()) $pdo->prepare('UPDATE compromisos SET texto=?, nombre=?, mostrar_foto=?, organizacion=?, aprobado=1, creado=NOW() WHERE id=?')->execute([$texto, $nombre, $autoriza ? 1 : 0, $r['organizacion'] ?: 'Una organización', $id]);
      else $pdo->prepare('INSERT INTO compromisos (texto, organizacion, pliegue, nombre, registro_id, mostrar_foto) VALUES (?,?,?,?,?,?)')->execute([$texto, $r['organizacion'] ?: 'Una organización', $r['pliegue'] ?? '', $nombre, (int)$r['id'], $autoriza ? 1 : 0]);
      $mio = ['texto' => $texto, 'nombre' => $nombre, 'organizacion' => $r['organizacion']];
    } else {
      $org = clean((string)($in['organizacion'] ?? ''), 120);
      $pliegue = clean((string)($in['pliegue'] ?? ''), 40);
      $q = $pdo->prepare('SELECT COUNT(*) c FROM compromisos WHERE organizacion=? AND creado > NOW() - INTERVAL 1 DAY'); $q->execute([$org ?: 'Anónimo']);
      if ((int)$q->fetch()['c'] > 30) json_out(['ok' => false, 'error' => 'Demasiados compromisos hoy.'], 429);
      $pdo->prepare('INSERT INTO compromisos (texto, organizacion, pliegue) VALUES (?,?,?)')->execute([$texto, $org ?: 'Una organización', $pliegue]);
    }
  } elseif (!empty($_GET['c'])) {
    // «Mi taller» pregunta si la persona ya dejó su compromiso.
    $code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)$_GET['c']));
    $q = $pdo->prepare('SELECT k.texto, k.nombre, k.organizacion FROM compromisos k JOIN registros r ON r.id=k.registro_id WHERE r.codigo=? AND k.aprobado=1'); $q->execute([$code]);
    $mio = $q->fetch() ?: null;
  }
  $lim = min(80, max(10, (int)($_GET['n'] ?? 40)));
  // La foto no viaja aquí: el muro la pide a muro_foto.php, que solo la entrega si la persona lo autorizó.
  $rows = $pdo->query("SELECT k.id, k.texto, k.organizacion, k.pliegue, k.nombre, k.creado, (k.mostrar_foto=1 AND r.foto IS NOT NULL) AS foto FROM compromisos k LEFT JOIN registros r ON r.id=k.registro_id WHERE k.aprobado=1 ORDER BY k.id DESC LIMIT $lim")->fetchAll();
  foreach ($rows as &$x) $x['foto'] = (bool)$x['foto']; unset($x);
  $t = $pdo->query('SELECT COUNT(*) n, COUNT(DISTINCT organizacion) o FROM compromisos WHERE aprobado=1')->fetch();
  json_out(['ok' => true, 'items' => $rows, 'total' => (int)$t['n'], 'organizaciones' => (int)$t['o'], 'mio' => $mio]);
} catch (Throwable $e) {
  error_log('muro: ' . $e->getMessage());
  json_out(['ok' => false, 'error' => 'Servicio no disponible.'], 500);
}
