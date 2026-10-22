<?php
declare(strict_types=1);

function cfg(): array { static $c; if (!$c) { $c = require __DIR__ . '/../config.php'; } return $c; }

function db(): PDO {
  static $pdo;
  if ($pdo) return $pdo;
  $c = cfg()['db'];
  $pdo = new PDO("mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4", $c['user'], $c['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  $pdo->exec("CREATE TABLE IF NOT EXISTS registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(12) NOT NULL UNIQUE,
    nombres VARCHAR(120) NOT NULL,
    documento_enc TEXT NOT NULL,
    documento_hash CHAR(64) NOT NULL,
    organizacion VARCHAR(120) NOT NULL,
    correo VARCHAR(190) NOT NULL,
    pronombre VARCHAR(40) DEFAULT '',
    grupos_enc TEXT NOT NULL,
    requiere_ajuste TINYINT(1) NOT NULL DEFAULT 0,
    ajustes TEXT,
    pliegue VARCHAR(40) DEFAULT '',
    compromiso VARCHAR(200) DEFAULT '',
    consentimiento TINYINT(1) NOT NULL DEFAULT 1,
    asistio TINYINT(1) NOT NULL DEFAULT 0,
    correo_enviado TINYINT(1) NOT NULL DEFAULT 0,
    ip_hash CHAR(64) DEFAULT '',
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (correo), INDEX (documento_hash), INDEX (creado)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  $pdo->exec("CREATE TABLE IF NOT EXISTS compromisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    texto VARCHAR(200) NOT NULL,
    organizacion VARCHAR(120) NOT NULL,
    pliegue VARCHAR(40) DEFAULT '',
    aprobado TINYINT(1) NOT NULL DEFAULT 1,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  $pdo->exec("CREATE TABLE IF NOT EXISTS agente_uso (
    dia DATE NOT NULL, ip_hash CHAR(64) NOT NULL, n INT NOT NULL DEFAULT 0,
    PRIMARY KEY (dia, ip_hash)
  ) ENGINE=InnoDB");
  $pdo->exec("CREATE TABLE IF NOT EXISTS agente_log (
    id INT AUTO_INCREMENT PRIMARY KEY, creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pregunta VARCHAR(400), respuesta TEXT, tokens INT DEFAULT 0
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  return $pdo;
}

function enc_key(): string { return hex2bin(cfg()['enc_key_hex']); }
function encrypt_str(string $plain): string {
  $iv = random_bytes(12); $tag = '';
  $ct = openssl_encrypt($plain, 'aes-256-gcm', enc_key(), OPENSSL_RAW_DATA, $iv, $tag);
  return base64_encode($iv . $tag . $ct);
}
function decrypt_str(string $b64): string {
  $raw = base64_decode($b64); if ($raw === false || strlen($raw) < 28) return '';
  $iv = substr($raw, 0, 12); $tag = substr($raw, 12, 16); $ct = substr($raw, 28);
  $p = openssl_decrypt($ct, 'aes-256-gcm', enc_key(), OPENSSL_RAW_DATA, $iv, $tag);
  return $p === false ? '' : $p;
}
function json_out($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
function body_json(): array {
  $raw = file_get_contents('php://input');
  $j = json_decode($raw ?: '', true);
  return is_array($j) ? $j : [];
}
function ip_hash(): string { return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|dvs'); }
function clean(string $s, int $max): string {
  $s = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $s));
  return mb_substr($s, 0, $max);
}
function gen_code(PDO $pdo): string {
  $A = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  for ($i = 0; $i < 20; $i++) {
    $c = '5E-'; for ($k = 0; $k < 6; $k++) $c .= $A[random_int(0, 31)];
    $q = $pdo->prepare('SELECT 1 FROM registros WHERE codigo=?'); $q->execute([$c]);
    if (!$q->fetch()) return $c;
  }
  throw new RuntimeException('No se pudo generar un código único');
}
function admin_logged(): bool {
  if (session_status() !== PHP_SESSION_ACTIVE) { session_name(cfg()['session_name']); session_start(); }
  return !empty($_SESSION['admin']);
}
/** Personal de la mesa de entrada (clave corta) o administración. */
function puerta_logged(): bool {
  if (session_status() !== PHP_SESSION_ACTIVE) { session_name(cfg()['session_name']); session_start(); }
  return !empty($_SESSION['admin']) || !empty($_SESSION['puerta']);
}

/** Envía un correo HTML con adjunto opcional (PNG en base64). */
function send_mail(string $to, string $subject, string $html, ?string $pngB64 = null, string $pngName = 'escarapela.png'): bool {
  $c = cfg();
  $boundary = 'b' . bin2hex(random_bytes(12));
  $from = $c['mail_from']; $fromName = '=?UTF-8?B?' . base64_encode($c['mail_from_name']) . '?=';
  $headers = "From: $fromName <$from>\r\nReply-To: {$c['mail_reply_to']}\r\n";
  if (!empty($c['mail_bcc'])) $headers .= "Bcc: {$c['mail_bcc']}\r\n";
  $headers .= "MIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
  $body = "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "\r\n";
  if ($pngB64) {
    $body .= "--$boundary\r\nContent-Type: image/png; name=\"$pngName\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$pngName\"\r\n\r\n" . chunk_split($pngB64) . "\r\n";
  }
  $body .= "--$boundary--";
  $subj = '=?UTF-8?B?' . base64_encode($subject) . '?=';
  return @mail($to, $subj, $body, $headers, '-f' . $from);
}
