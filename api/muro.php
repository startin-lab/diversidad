<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
try {
  $pdo = db();
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = body_json();
    $texto = clean((string)($in['texto'] ?? ''), 200);
    $org = clean((string)($in['organizacion'] ?? ''), 120);
    $pliegue = clean((string)($in['pliegue'] ?? ''), 40);
    if (mb_strlen($texto) < 8) json_out(['ok' => false, 'error' => 'Escribe un compromiso un poco más largo.'], 422);
    // límite simple: 5 por IP por día
    $q = $pdo->prepare('SELECT COUNT(*) c FROM compromisos WHERE organizacion=? AND creado > NOW() - INTERVAL 1 DAY'); $q->execute([$org ?: 'Anónimo']);
    if ((int)$q->fetch()['c'] > 30) json_out(['ok' => false, 'error' => 'Demasiados compromisos hoy.'], 429);
    $pdo->prepare('INSERT INTO compromisos (texto, organizacion, pliegue) VALUES (?,?,?)')->execute([$texto, $org ?: 'Una organización', $pliegue]);
  }
  $rows = $pdo->query('SELECT texto, organizacion, pliegue, creado FROM compromisos WHERE aprobado=1 ORDER BY id DESC LIMIT 40')->fetchAll();
  json_out(['ok' => true, 'items' => $rows]);
} catch (Throwable $e) {
  error_log('muro: ' . $e->getMessage());
  json_out(['ok' => false, 'error' => 'Servicio no disponible.'], 500);
}
