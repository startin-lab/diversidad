<?php
declare(strict_types=1);
// «Mi taller»: cada persona consulta con el código de su escarapela su grupo, su célula, la fase en curso
// y la ficha de su célula. No devuelve datos de otras personas.
require __DIR__ . '/taller_lib.php';
try {
  $pdo = taller_db();
  $in = $_SERVER['REQUEST_METHOD'] === 'POST' ? body_json() : $_GET;
  $estado = taller_estado($pdo);
  if (($in['solo'] ?? '') === 'estado') json_out(['ok' => true, 'estado' => $estado]);
  if (($in['solo'] ?? '') === 'pantalla') {
    // Pantalla del auditorio: solo cifras agregadas y las soluciones destacadas (sin nombres).
    $grupos = [];
    foreach (TALLER_GRUPOS as $g => $d) {
      $q = $pdo->prepare('SELECT taller_celula c, COUNT(*) n FROM registros WHERE asistio=1 AND taller_grupo=? GROUP BY taller_celula ORDER BY taller_celula'); $q->execute([$g]);
      $cel = []; $n = 0; foreach ($q->fetchAll() as $x) { $n += (int)$x['n']; if ($x['c']) $cel[(int)$x['c']] = (int)$x['n']; }
      $dest = (int)taller_kv($pdo, "destacada_$g", '0');
      $grupos[] = ['grupo' => $g, 'eje' => $d['eje'], 'personas' => $n, 'celulas' => $cel, 'votos' => taller_votos($pdo, $g),
        'destacada' => $dest ? ['celula' => $dest, 'reto' => $d['retos'][($dest - 1) % 5], 'landing' => (($lp = taller_landing($pdo, $g, $dest)) ? ['titulo' => $lp['titulo'], 'lema' => $lp['lema']] : null)] + taller_ficha($pdo, $g, $dest) : null];
    }
    json_out(['ok' => true, 'estado' => $estado, 'grupos' => $grupos]);
  }
  // Entrada con el código de la escarapela o con la cédula. La cédula solo llega por POST (nunca en la URL)
  // y la respuesta devuelve el código, que es lo único que el celular guarda.
  $doc = $_SERVER['REQUEST_METHOD'] === 'POST' ? preg_replace('/[^0-9A-Za-z]/', '', (string)($in['doc'] ?? '')) : '';
  if ($doc !== '') {
    if (strlen($doc) < 5) json_out(['ok' => false, 'error' => 'Revisa el número de documento.'], 422);
    $q = $pdo->prepare('SELECT id, codigo, nombres, asistio, taller_grupo, taller_celula FROM registros WHERE documento_hash=?');
    $q->execute([hash('sha256', $doc . '|' . cfg()['enc_key_hex'])]);
    $r = $q->fetch();
    if (!$r) { usleep(600000); json_out(['ok' => false, 'error' => 'No encontramos ese documento. Prueba con el código de tu escarapela.'], 404); }
  } else {
    $code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($in['c'] ?? '')));
    if (!preg_match('/^5E-?([A-Z0-9]{6})$/', $code, $m)) json_out(['ok' => false, 'error' => 'Escribe el código de tu escarapela (5E-XXXXXX) o tu cédula.'], 422);
    $code = '5E-' . $m[1];
    $q = $pdo->prepare('SELECT id, codigo, nombres, asistio, taller_grupo, taller_celula FROM registros WHERE codigo=?'); $q->execute([$code]);
    $r = $q->fetch();
    if (!$r) { usleep(300000); json_out(['ok' => false, 'error' => 'No encontramos ese código. Revísalo en tu escarapela.'], 404); }
  }
  $u = taller_ubicacion((int)$r['taller_grupo'], (int)$r['taller_celula']);
  $g = (int)$r['taller_grupo']; $c = (int)$r['taller_celula'];

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $doc === '' && ($in['accion'] ?? '') === 'votar') {
    $f = taller_kv($pdo, 'fase', '');
    if (!$c || $f !== 'plenaria') json_out(['ok' => false, 'error' => 'La votación se abre en la puesta en común.'], 409);
    if ((int)taller_kv($pdo, "destacada_$g", '0')) json_out(['ok' => false, 'error' => 'Tu grupo ya eligió la idea que presenta.'], 409);
    $cel = (int)($in['celula'] ?? 0);
    if ($cel < 1 || $cel > taller_celulas($pdo, $g)) json_out(['ok' => false, 'error' => 'Esa célula no existe en tu grupo.'], 422);
    if ($cel === $c) json_out(['ok' => false, 'error' => 'No puedes votar por tu propia célula: elige la idea de otra.'], 422);
    $pdo->prepare('INSERT INTO taller_votos (registro_id, grupo, celula) VALUES (?,?,?) ON DUPLICATE KEY UPDATE grupo=VALUES(grupo), celula=VALUES(celula), creado=NOW()')->execute([(int)$r['id'], $g, $cel]);
    taller_registrar($pdo, $g, $c, 'voto', 'celula ' . $cel, (string)$cel, (int)$r['id']);
  } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $doc === '') {
    if (!$c) json_out(['ok' => false, 'error' => 'La ficha se abre cuando se formen las células.'], 409);
    $campo = (string)($in['campo'] ?? '');
    if (!isset(TALLER_CAMPOS[$campo])) json_out(['ok' => false, 'error' => 'Campo desconocido.'], 422);
    $valor = clean((string)($in['valor'] ?? ''), 1500);
    $antes = taller_ficha($pdo, $g, $c)[$campo];
    $pdo->prepare("INSERT INTO taller_fichas (grupo, celula, $campo, editado) VALUES (?,?,?,NOW()) ON DUPLICATE KEY UPDATE $campo=VALUES($campo), editado=NOW()")->execute([$g, $c, $valor]);
    if (trim($valor) !== trim($antes)) taller_registrar($pdo, $g, $c, 'ficha', $campo, $valor, (int)$r['id']);
  }

  $out = ['ok' => true, 'codigo' => $r['codigo'], 'nombre' => explode(' ', trim((string)$r['nombres']))[0], 'ingreso' => (bool)$r['asistio'], 'ubicacion' => $u ?: null, 'estado' => $estado];
  if ($c) {
    $ficha = taller_ficha($pdo, $g, $c);
    $out['ficha'] = $ficha;
    $out['campos'] = array_map(fn($k, $v) => ['id' => $k, 'titulo' => $v[0], 'ayuda' => $v[1]], array_keys(TALLER_CAMPOS), TALLER_CAMPOS);
    $out['prompts'] = [];
    foreach (['diagnostico', 'analisis', 'experimentacion'] as $f) $out['prompts'][$f] = taller_prompt($f, $u, $ficha);
    $q = $pdo->prepare('SELECT agente_n FROM taller_fichas WHERE grupo=? AND celula=?'); $q->execute([$g, $c]);
    $out['agente_restante'] = max(0, TALLER_TOPE_AGENTE - (int)$q->fetchColumn());
    $lp = taller_landing($pdo, $g, $c);
    $out['landing'] = $lp ? ['titulo' => $lp['titulo'], 'lema' => $lp['lema'], 'url' => "landing.php?g=$g&c=$c", 'editado' => $lp['_editado']] : null;
    $fase = $estado['fase']['id'] ?? '';
    if ($fase === 'plenaria' || $fase === 'cierre') {
      $q = $pdo->prepare('SELECT celula FROM taller_votos WHERE registro_id=?'); $q->execute([(int)$r['id']]);
      $out['plenaria'] = ['opciones' => taller_opciones($pdo, $g), 'mi_voto' => (int)$q->fetchColumn() ?: null, 'destacada' => (int)taller_kv($pdo, "destacada_$g", '0') ?: null];
    }
  }
  json_out($out);
} catch (Throwable $e) {
  error_log('taller: ' . $e->getMessage());
  json_out(['ok' => false, 'error' => 'Servicio no disponible. Intenta de nuevo.'], 500);
}
