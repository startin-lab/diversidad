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
      <p style="font-size:34px;font-weight:900;letter-spacing:.04em;margin:0 0 14px">' . $codigo . '</p>
      <p>Preséntalo, o el código QR de tu escarapela adjunta, en la mesa de registro.</p>
      <table style="width:100%;border-collapse:collapse;margin:16px 0;font-size:15px">
        <tr><td style="padding:8px 0;color:#4B537F;width:110px">Cuándo</td><td style="padding:8px 0;font-weight:700">Miércoles 11 de noviembre de 2026 · 8:00 a.m. – 1:00 p.m.</td></tr>
        <tr><td style="padding:8px 0;color:#4B537F">Dónde</td><td style="padding:8px 0;font-weight:700">Universidad El Bosque – HUB iEX<br><span style="font-weight:400">Cra. 7b Bis No. 132-28, Bogotá</span></td></tr>
      </table>' . $ajTxt . '
      <p style="margin-top:20px"><a href="' . $site . '" style="display:inline-block;background:#F26A21;color:#fff;text-decoration:none;font-weight:800;padding:12px 20px;border-radius:999px">Volver al portal</a></p>
      <p style="font-size:12px;color:#4B537F;margin-top:24px">Tus datos se usan solo para gestionar tu participación en el Encuentro, según la <a href="https://startin.org.co/privacidad/" style="color:#4B537F">política de tratamiento de datos</a>. Dudas: hola@startin.org.co.</p>
    </div>
    <p style="text-align:center;font-size:12px;color:#8A90B8;margin-top:14px">Portal construido por Fundación Startin · startin.org.co</p>
  </div></body></html>';
  $png = null;
  if ($badge && preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', $badge, $m) && strlen($m[1]) < 2000000) $png = $m[1];
  $sent = send_mail($correo, "Tu inscripción al 5° Encuentro · código $codigo", $html, $png, "escarapela-$codigo.png");
  $pdo->prepare('UPDATE registros SET correo_enviado=? WHERE id=?')->execute([$sent ? 1 : 0, (int)$r['id']]);
  json_out(['ok' => true, 'enviado' => $sent]);
} catch (Throwable $e) { error_log('correo: ' . $e->getMessage()); json_out(['ok' => false, 'error' => 'No se pudo enviar el correo.'], 500); }
