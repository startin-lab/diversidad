<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Método no permitido'], 405);
$in = body_json();
$codigo = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($in['codigo'] ?? '')));
$correo = mb_strtolower(clean((string)($in['correo'] ?? ''), 190));
$badge = (string)($in['escarapela_png'] ?? '');
try {
  $pdo = db();
  $q = $pdo->prepare('SELECT * FROM registros WHERE codigo=? AND correo=?'); $q->execute([$codigo, $correo]);
  $r = $q->fetch();
  if (!$r) json_out(['ok' => false, 'error' => 'Registro no encontrado.'], 404);
  if ($r['correo_enviado']) json_out(['ok' => true, 'ya' => true]);
  $html = correo_confirmacion_html($r, true);
  $png = null;
  if ($badge && preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', $badge, $m) && strlen($m[1]) < 2000000) $png = $m[1];
  $sent = send_mail($correo, "Tu inscripción al 5° Encuentro · código $codigo", $html, $png, "escarapela-$codigo.png");
  $pdo->prepare('UPDATE registros SET correo_enviado=? WHERE id=?')->execute([$sent ? 1 : 0, (int)$r['id']]);
  json_out(['ok' => true, 'enviado' => $sent]);
} catch (Throwable $e) { error_log('correo: ' . $e->getMessage()); json_out(['ok' => false, 'error' => 'No se pudo enviar el correo.'], 500); }
