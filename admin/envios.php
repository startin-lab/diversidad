<?php
declare(strict_types=1);
// Envío en lotes desde el panel: el navegador pide un correo a la vez y el servidor impone el ritmo.
// Graph admite 30 mensajes por minuto por buzón; aquí se deja uno cada ENVIO_PAUSA segundos.
require __DIR__ . '/../api/lib.php';
const ENVIO_PAUSA = 2.5;
const CAMPANAS = [
  'confirmacion' => 'Inscritos a quienes no les llegó la confirmación',
  'recordatorio' => 'Recordatorio previo al evento (todos los inscritos)',
];
if (!admin_logged()) json_out(['ok' => false, 'error' => 'Sesión vencida. Vuelve a entrar al panel.'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Método no permitido'], 405);
$in = body_json();
if (!hash_equals((string)($_SESSION['csrf'] ?? ''), (string)($in['csrf'] ?? ''))) json_out(['ok' => false, 'error' => 'Sesión vencida. Vuelve a cargar el panel.'], 400);
session_write_close();
$camp = (string)($in['campana'] ?? '');
if (!isset(CAMPANAS[$camp])) json_out(['ok' => false, 'error' => 'Campaña desconocida.'], 400);
$pdo = db();
$pdo->exec("CREATE TABLE IF NOT EXISTS envios (
  registro_id INT NOT NULL, campana VARCHAR(20) NOT NULL, ok TINYINT(1) NOT NULL,
  creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (registro_id, campana), INDEX (creado)
) ENGINE=InnoDB");

/** Arma asunto y HTML de la campaña para un registro. */
function mensaje(string $camp, array $r): array {
  return $camp === 'confirmacion'
    ? ['Tu inscripción al 5° Encuentro · código ' . $r['codigo'], correo_confirmacion_html($r, false)]
    : [ASUNTO_RECORDATORIO, correo_recordatorio_html($r)];
}
function pendientes_sql(string $camp): string {
  $base = "FROM registros r WHERE NOT EXISTS (SELECT 1 FROM envios e WHERE e.registro_id=r.id AND e.campana='$camp')";
  return $camp === 'confirmacion' ? "$base AND r.correo_enviado=0" : $base;
}
function estado(PDO $pdo, string $camp): array {
  $q = $pdo->prepare('SELECT SUM(ok=1) enviados, SUM(ok=0) fallidos FROM envios WHERE campana=?'); $q->execute([$camp]); $e = $q->fetch();
  return ['pendientes' => (int)$pdo->query('SELECT COUNT(*) ' . pendientes_sql($camp))->fetchColumn(),
          'enviados' => (int)$e['enviados'], 'fallidos' => (int)$e['fallidos']];
}

$accion = (string)($in['accion'] ?? 'estado');
if ($accion === 'estado') json_out(['ok' => true] + estado($pdo, $camp));

if ($accion === 'prueba') {
  // Datos de ejemplo: la prueba nunca lleva datos de una persona inscrita.
  $to = mb_strtolower(clean((string)($in['correo'] ?? ''), 190));
  if (!filter_var($to, FILTER_VALIDATE_EMAIL)) json_out(['ok' => false, 'error' => 'Escribe un correo válido para la prueba.'], 400);
  $demo = ['codigo' => '5E-PRUEBA', 'nombres' => 'Persona de prueba', 'ajustes' => json_encode(!empty($in['con_ajustes']) ? ['Intérprete de LSC'] : [])];
  [$asunto, $html] = mensaje($camp, $demo);
  json_out(['ok' => true, 'enviado' => send_mail($to, '[Prueba] ' . $asunto, $html, null, 'escarapela.png', false)]);
}

if ($accion === 'reintentar') {
  $q = $pdo->prepare('DELETE FROM envios WHERE campana=? AND ok=0'); $q->execute([$camp]);
  json_out(['ok' => true] + estado($pdo, $camp));
}

if ($accion !== 'enviar') json_out(['ok' => false, 'error' => 'Acción desconocida.'], 400);
if (($in['confirma'] ?? '') !== 'ENVIAR') json_out(['ok' => false, 'error' => 'Escribe ENVIAR para confirmar.'], 400);
// Un solo envío a la vez, aunque el panel quede abierto en dos pestañas.
if (!(int)$pdo->query("SELECT GET_LOCK('dvs_envio_lotes', 0)")->fetchColumn()) json_out(['ok' => true, 'espera' => 1000]);
$marca = sys_get_temp_dir() . '/dvs-envio-ultimo';
$falta = (float)@file_get_contents($marca) + ENVIO_PAUSA - microtime(true);
if ($falta > 0) json_out(['ok' => true, 'espera' => (int)ceil($falta * 1000)]);
$hoy = (int)$pdo->query('SELECT COUNT(*) FROM envios WHERE creado >= CURDATE()')->fetchColumn();
if ($hoy >= (int)(cfg()['envio_max_dia'] ?? 1500)) json_out(['ok' => false, 'error' => 'Se alcanzó el tope de envíos de hoy. Continúa mañana.'], 429);
$r = $pdo->query('SELECT r.* ' . pendientes_sql($camp) . ' ORDER BY r.id LIMIT 1')->fetch();
if (!$r) json_out(['ok' => true, 'terminado' => true] + estado($pdo, $camp));
file_put_contents($marca, (string)microtime(true), LOCK_EX);
[$asunto, $html] = mensaje($camp, $r);
$sent = send_mail($r['correo'], $asunto, $html, null, 'escarapela.png', false);
$pdo->prepare('INSERT INTO envios (registro_id, campana, ok) VALUES (?,?,?) ON DUPLICATE KEY UPDATE ok=VALUES(ok), creado=NOW()')->execute([(int)$r['id'], $camp, $sent ? 1 : 0]);
if ($sent && $camp === 'confirmacion') $pdo->prepare('UPDATE registros SET correo_enviado=1 WHERE id=?')->execute([(int)$r['id']]);
json_out(['ok' => true, 'enviado' => $sent, 'codigo' => $r['codigo']] + estado($pdo, $camp));
