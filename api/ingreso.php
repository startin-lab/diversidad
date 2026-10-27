<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
if (!puerta_logged()) json_out(['ok' => false, 'error' => 'Sesión de entrada no iniciada.'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Método no permitido'], 405);
$in = body_json();
$q = trim((string)($in['q'] ?? ''));
$marcar = !empty($in['marcar']);
if ($q === '') json_out(['ok' => false, 'error' => 'Escribe o escanea un código.'], 422);
try {
  $pdo = db();
  // 1) URL del QR o código 5E-XXXXXX
  if (preg_match('/5E-?([A-Z0-9]{6})/i', $q, $m)) {
    $code = '5E-' . strtoupper($m[1]);
    $st = $pdo->prepare('SELECT * FROM registros WHERE codigo=?'); $st->execute([$code]);
  } else {
    // 2) cédula / documento
    $doc = preg_replace('/[^0-9A-Za-z]/', '', $q);
    if (strlen($doc) < 5) json_out(['ok' => false, 'error' => 'No parece un código ni un documento.'], 422);
    $st = $pdo->prepare('SELECT * FROM registros WHERE documento_hash=?'); $st->execute([hash('sha256', $doc . '|' . cfg()['enc_key_hex'])]);
  }
  $r = $st->fetch();
  if (!$r) json_out(['ok' => true, 'encontrado' => false]);
  $ya = (bool)$r['asistio'];
  if ($marcar && !$ya) { $pdo->prepare('UPDATE registros SET asistio=1 WHERE id=?')->execute([(int)$r['id']]); }
  json_out(['ok' => true, 'encontrado' => true, 'codigo' => $r['codigo'], 'nombres' => $r['nombres'], 'pronombre' => $r['pronombre'], 'organizacion' => $r['organizacion'],
    'pliegue' => $r['pliegue'], 'ajustes' => json_decode($r['ajustes'] ?: '[]', true) ?: [], 'ya_ingreso' => $ya, 'marcado' => $marcar && !$ya,
    'foto' => !empty($r['foto']) ? (decrypt_str($r['foto']) ?: '') : '']);
} catch (Throwable $e) { error_log('ingreso: ' . $e->getMessage()); json_out(['ok' => false, 'error' => 'Servicio no disponible.'], 500); }
