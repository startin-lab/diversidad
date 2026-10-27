<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
// Recupera la escarapela de una inscripción existente solo con el documento (no crea registros).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Método no permitido'], 405);
$in = body_json();
$doc = preg_replace('/[^0-9A-Za-z]/', '', (string)($in['documento'] ?? ''));
if (strlen($doc) < 5 || strlen($doc) > 20) json_out(['ok' => false, 'error' => 'Escribe tu número de documento completo.'], 422);
try {
  $pdo = db();
  $pdo->exec("CREATE TABLE IF NOT EXISTS recupera_uso (dia DATE NOT NULL, ip_hash CHAR(64) NOT NULL, n INT NOT NULL DEFAULT 0, PRIMARY KEY (dia, ip_hash)) ENGINE=InnoDB");
  $ih = ip_hash();
  $pdo->prepare('INSERT INTO recupera_uso (dia, ip_hash, n) VALUES (CURDATE(), ?, 1) ON DUPLICATE KEY UPDATE n = n + 1')->execute([$ih]);
  $q = $pdo->prepare('SELECT n FROM recupera_uso WHERE dia = CURDATE() AND ip_hash = ?'); $q->execute([$ih]);
  if ((int)$q->fetchColumn() > 30) json_out(['ok' => false, 'error' => 'Hiciste demasiadas consultas hoy. Intenta mañana o escríbenos a hola@startin.org.co.'], 429);
  usleep(300000);
  $dh = hash('sha256', $doc . '|' . cfg()['enc_key_hex']);
  $q = $pdo->prepare('SELECT codigo, nombres, organizacion, pronombre, pliegue, compromiso FROM registros WHERE documento_hash = ? LIMIT 1');
  $q->execute([$dh]);
  $r = $q->fetch();
  if (!$r) json_out(['ok' => false, 'error' => 'No encontramos una inscripción con ese documento. Revisa el número o inscríbete con el formulario.'], 404);
  json_out(['ok' => true] + $r);
} catch (Throwable $e) {
  error_log('recuperar: ' . $e->getMessage());
  json_out(['ok' => false, 'error' => 'No pudimos consultar tu inscripción. Intenta de nuevo en un momento.'], 500);
}
