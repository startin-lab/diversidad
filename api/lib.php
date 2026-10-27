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

/** HTML del correo de confirmación (lo usan api/correo.php y el panel para reenviar). */
function correo_confirmacion_html(array $r, bool $conEscarapela = true): string {
  $codigo = $r['codigo'];
  $ajustes = json_decode($r['ajustes'] ?: '[]', true) ?: [];
  $site = cfg()['site_url'];
  $primer = htmlspecialchars(explode(' ', $r['nombres'])[0], ENT_QUOTES, 'UTF-8');
  $ajTxt = $ajustes ? '<p style="margin:12px 0;padding:12px 14px;background:#FFF4D6;border-radius:10px;color:#4A3A00"><b>Ajustes razonables registrados:</b> ' . htmlspecialchars(implode(', ', $ajustes), ENT_QUOTES, 'UTF-8') . '. El equipo organizador los tendrá listos para ti.</p>' : '';
  $html = '<!doctype html><html lang="es"><body style="margin:0;background:#F4F3FA;font-family:Segoe UI,Helvetica,Arial,sans-serif;color:#0C1646">
  <div style="max-width:560px;margin:0 auto;padding:28px 16px">
    <div style="background:#0C1646;color:#fff;border-radius:16px 16px 0 0;padding:26px 28px">
      <div style="font-size:44px;font-weight:900;color:#FFC20E;line-height:1">5°</div>
      <div style="font-size:18px;font-weight:800;margin-top:4px">Encuentro Diversidades e Inclusión</div>
      <div style="font-size:14px;opacity:.85;margin-top:4px">Gestión del cambio organizacional para la Inclusión y Equidad</div>
    </div>
    <div style="background:#fff;padding:26px 28px;border-radius:0 0 16px 16px">
      <p style="font-size:17px">Hola, ' . $primer . '. Tu inscripción quedó registrada.</p>
      <p style="margin:18px 0 6px;font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#4B537F;font-weight:700">Tu código de registro</p>
      <p style="font-size:34px;font-weight:900;letter-spacing:.04em;margin:0 0 14px">' . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . '</p>
      <p>' . ($conEscarapela ? 'Preséntalo, o el código QR de tu escarapela adjunta, en la mesa de registro.' : 'Preséntalo en la mesa de registro. Si necesitas tu escarapela, vuelve a inscribirte en el portal con tu mismo documento y correo: se genera de nuevo con el mismo código.') . '</p>
      <table style="width:100%;border-collapse:collapse;margin:16px 0;font-size:15px">
        <tr><td style="padding:8px 0;color:#4B537F;width:110px">Cuándo</td><td style="padding:8px 0;font-weight:700">Miércoles 11 de noviembre de 2026 · 8:00 a.m. – 1:00 p.m.</td></tr>
        <tr><td style="padding:8px 0;color:#4B537F">Dónde</td><td style="padding:8px 0;font-weight:700">Universidad El Bosque – HUB iEX<br><span style="font-weight:400">Cra. 7b Bis No. 132-28, Bogotá</span></td></tr>
      </table>' . $ajTxt . '
      <p style="margin-top:20px"><a href="' . $site . '" style="display:inline-block;background:#F26A21;color:#fff;text-decoration:none;font-weight:800;padding:12px 20px;border-radius:999px">Volver al portal</a></p>
      <p style="font-size:12px;color:#4B537F;margin-top:24px">Tus datos se usan solo para gestionar tu participación en el Encuentro, según la <a href="https://startin.org.co/privacidad/" style="color:#4B537F">política de tratamiento de datos</a>. Dudas: hola@startin.org.co.</p>
    </div>
    <p style="text-align:center;font-size:12px;color:#8A90B8;margin-top:14px">by <a href="https://startinlab.org" style="color:#8A90B8;text-decoration:none">Startin<span style="color:#E01CC0">*</span>Lab</a> · <a href="https://startinlab.org" style="color:#8A90B8">startinlab.org</a></p>
  </div></body></html>';
  return $html;
}
