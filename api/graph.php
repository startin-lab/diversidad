<?php
declare(strict_types=1);

/** Token de aplicación de Microsoft Graph (se reutiliza ~50 min en un archivo de caché fuera del web root). */
function graph_token(): ?string {
  $g = cfg()['graph'] ?? [];
  if (empty($g['tenant']) || empty($g['client_id']) || empty($g['client_secret'])) return null;
  $f = sys_get_temp_dir() . '/dvs-graph-token-' . substr(sha1($g['client_id']), 0, 8) . '.json';
  $c = @json_decode((string)@file_get_contents($f), true);
  if (is_array($c) && ($c['exp'] ?? 0) > time() + 120) return $c['token'];
  $ch = curl_init('https://login.microsoftonline.com/' . rawurlencode($g['tenant']) . '/oauth2/v2.0/token');
  curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
    CURLOPT_POSTFIELDS => http_build_query(['client_id' => $g['client_id'], 'client_secret' => $g['client_secret'], 'scope' => 'https://graph.microsoft.com/.default', 'grant_type' => 'client_credentials'])]);
  $r = json_decode((string)curl_exec($ch), true); curl_close($ch);
  if (empty($r['access_token'])) { error_log('graph token: ' . ($r['error'] ?? 'sin respuesta')); return null; }
  @file_put_contents($f, json_encode(['token' => $r['access_token'], 'exp' => time() + (int)($r['expires_in'] ?? 3600)]), LOCK_EX);
  @chmod($f, 0600);
  return $r['access_token'];
}

/** Envía por Microsoft 365 (Graph sendMail) desde un buzón de startinlab.org: sale firmado con DKIM del dominio. */
function send_graph(string $to, string $subject, string $html, ?string $pngB64, string $pngName, bool $copia = true): bool {
  $c = cfg(); $g = $c['graph'];
  for ($try = 0; $try < 3; $try++) {
    $tok = graph_token();
    if (!$tok) { @unlink(sys_get_temp_dir() . '/dvs-graph-token-' . substr(sha1($g['client_id']), 0, 8) . '.json'); usleep(400000); continue; }
    $msg = ['subject' => $subject, 'body' => ['contentType' => 'HTML', 'content' => $html],
      'toRecipients' => [['emailAddress' => ['address' => $to]]]];
    if (!empty($c['mail_reply_to'])) $msg['replyTo'] = [['emailAddress' => ['address' => $c['mail_reply_to']]]];
    if ($copia && !empty($c['mail_bcc'])) $msg['bccRecipients'] = [['emailAddress' => ['address' => $c['mail_bcc']]]];
    if ($pngB64) $msg['attachments'] = [['@odata.type' => '#microsoft.graph.fileAttachment', 'name' => $pngName, 'contentType' => 'image/png', 'contentBytes' => $pngB64]];
    $ch = curl_init('https://graph.microsoft.com/v1.0/users/' . rawurlencode($c['mail_from']) . '/sendMail');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 25,
      CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tok, 'Content-Type: application/json'],
      CURLOPT_POSTFIELDS => json_encode(['message' => $msg, 'saveToSentItems' => true], JSON_UNESCAPED_UNICODE)]);
    $resp = curl_exec($ch); $st = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($st === 202) return true;
    error_log("graph sendMail $st: " . substr((string)$resp, 0, 200));
    if ($st === 401) @unlink(sys_get_temp_dir() . '/dvs-graph-token-' . substr(sha1($g['client_id']), 0, 8) . '.json');
    elseif ($st !== 429 && $st < 500) return false;
    usleep(($try + 1) * 600000);
  }
  return false;
}
