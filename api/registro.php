<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Método no permitido'], 405);

$in = body_json();
$nombres = clean((string)($in['nombres'] ?? ''), 120);
$documento = preg_replace('/[^0-9A-Za-z]/', '', (string)($in['documento'] ?? ''));
$organizacion = clean((string)($in['organizacion'] ?? ''), 120);
$correo = mb_strtolower(clean((string)($in['correo'] ?? ''), 190));
$pronombre = clean((string)($in['pronombre'] ?? ''), 40);
$grupos = $in['grupos'] ?? [];
$grupoOtro = clean((string)($in['grupo_otro'] ?? ''), 60);
$requiere = !empty($in['requiere_ajuste']);
$ajustes = $in['ajustes'] ?? [];
$ajusteOtro = clean((string)($in['ajuste_otro'] ?? ''), 120);
$pliegue = clean((string)($in['pliegue'] ?? ''), 40);
$compromiso = clean((string)($in['compromiso'] ?? ''), 200);
$consent = !empty($in['consentimiento']);
$badge = (string)($in['escarapela_png'] ?? '');

$errores = [];
if (mb_strlen($nombres) < 3) $errores[] = 'nombres y apellidos';
if (strlen($documento) < 5 || strlen($documento) > 20) $errores[] = 'documento de identidad';
if (mb_strlen($organizacion) < 2) $errores[] = 'institución u organización';
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = 'correo electrónico válido';
if (!is_array($grupos) || !count($grupos)) $errores[] = 'pregunta 5';
if (!isset($in['requiere_ajuste'])) $errores[] = 'pregunta 6';
if (!$consent) $errores[] = 'autorización de tratamiento de datos';
if ($errores) json_out(['ok' => false, 'error' => 'Falta: ' . implode(', ', $errores) . '.'], 422);

$gruposOk = ['Persona con discapacidad','Pueblos indígenas','Afrodescendiente, negra, raizal o palenquera','Pueblo Rrom','Migrante o refugiada','LGBTIQ+','Víctima del conflicto armado','Ninguno de los anteriores','Prefiero no responder','Otras'];
$grupos = array_values(array_intersect(array_map('strval', $grupos), $gruposOk));
if (!$grupos) json_out(['ok' => false, 'error' => 'Opción de grupo no válida.'], 422);
if (in_array('Otras', $grupos, true) && $grupoOtro !== '') $grupos[] = 'Otras: ' . $grupoOtro;
$ajustes = $requiere ? array_values(array_filter(array_map(fn($a) => clean((string)$a, 60), is_array($ajustes) ? $ajustes : []))) : [];
if ($requiere && $ajusteOtro !== '') $ajustes[] = $ajusteOtro;

try {
  $pdo = db();
  // Evita duplicados: mismo documento o mismo correo → devuelve el registro existente
  $dh = hash('sha256', $documento . '|' . cfg()['enc_key_hex']);
  $q = $pdo->prepare('SELECT id, codigo, nombres, correo, documento_hash, compromiso FROM registros WHERE documento_hash=? OR correo=? ORDER BY (documento_hash=?) DESC LIMIT 1');
  $q->execute([$dh, $correo, $dh]);
  if ($r = $q->fetch()) {
    if (hash_equals($r['documento_hash'], $dh) && $r['correo'] === $correo) {
      // Mismo documento y mismo correo: actualiza los datos y vuelve a enviar la escarapela
      $pdo->prepare('UPDATE registros SET nombres=?, organizacion=?, pronombre=?, grupos_enc=?, requiere_ajuste=?, ajustes=?, pliegue=?, compromiso=?, correo_enviado=0 WHERE id=?')
          ->execute([$nombres, $organizacion, $pronombre, encrypt_str(json_encode($grupos, JSON_UNESCAPED_UNICODE)), $requiere ? 1 : 0, json_encode($ajustes, JSON_UNESCAPED_UNICODE), $pliegue, $compromiso, (int)$r['id']]);
      if ($compromiso !== '' && $compromiso !== (string)$r['compromiso']) {
        $pdo->prepare('INSERT INTO compromisos (texto, organizacion, pliegue) VALUES (?,?,?)')->execute([$compromiso, $organizacion, $pliegue]);
      }
      json_out(['ok' => true, 'codigo' => $r['codigo'], 'repetido' => true, 'actualizado' => true, 'mensaje' => 'Actualizamos tu inscripción.']);
    }
    // Coincide solo el documento o solo el correo: no se modifica nada
    json_out(['ok' => true, 'codigo' => $r['codigo'], 'repetido' => true, 'mensaje' => 'Ya tenías una inscripción. Para cambiar tus datos usa el mismo documento y el mismo correo.']);
  }
  $codigo = gen_code($pdo);
  $st = $pdo->prepare('INSERT INTO registros (codigo,nombres,documento_enc,documento_hash,organizacion,correo,pronombre,grupos_enc,requiere_ajuste,ajustes,pliegue,compromiso,consentimiento,ip_hash) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1,?)');
  $st->execute([$codigo, $nombres, encrypt_str($documento), $dh, $organizacion, $correo, $pronombre,
    encrypt_str(json_encode($grupos, JSON_UNESCAPED_UNICODE)), $requiere ? 1 : 0, json_encode($ajustes, JSON_UNESCAPED_UNICODE), $pliegue, $compromiso, ip_hash()]);
  if ($compromiso !== '') {
    $pdo->prepare('INSERT INTO compromisos (texto, organizacion, pliegue) VALUES (?,?,?)')->execute([$compromiso, $organizacion, $pliegue]);
  }

  json_out(['ok' => true, 'codigo' => $codigo]);
} catch (Throwable $e) {
  error_log('registro: ' . $e->getMessage());
  json_out(['ok' => false, 'error' => 'No pudimos guardar tu inscripción. Intenta de nuevo en un momento.'], 500);
}
