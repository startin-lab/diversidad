<?php
declare(strict_types=1);
// Foto de la escarapela para una nota del muro: solo si la persona autorizó mostrarla al escribir su compromiso.
// Si después del evento se borran las fotos desde el panel, deja de existir aquí también.
require __DIR__ . '/lib.php';
$k = (int)($_GET['k'] ?? 0);
try {
  $q = db()->prepare('SELECT r.foto FROM compromisos k JOIN registros r ON r.id=k.registro_id WHERE k.id=? AND k.mostrar_foto=1 AND k.aprobado=1');
  $q->execute([$k]); $enc = (string)$q->fetchColumn();
  $data = $enc !== '' ? decrypt_str($enc) : '';
  if (!preg_match('#^data:image/(jpeg|png|webp);base64,([A-Za-z0-9+/=]+)$#', $data, $m)) { http_response_code(404); exit; }
  header('Content-Type: image/' . $m[1]);
  header('Cache-Control: private, max-age=600');
  header('X-Robots-Tag: noindex');
  echo base64_decode($m[2]);
} catch (Throwable $e) { error_log('muro_foto: ' . $e->getMessage()); http_response_code(404); }
