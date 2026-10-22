<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Método no permitido'], 405);

$c = cfg(); $ai = $c['ai'];
$in = body_json();
$msgs = $in['messages'] ?? [];
$easy = !empty($in['facil']);
if (!is_array($msgs) || !count($msgs)) json_out(['ok' => false, 'error' => 'Sin mensajes'], 422);
// Solo las últimas 8 vueltas, saneadas
$hist = [];
foreach (array_slice($msgs, -16) as $m) {
  $role = ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
  $txt = clean((string)($m['content'] ?? ''), 1200);
  if ($txt !== '') $hist[] = ['role' => $role, 'content' => $txt];
}
if (!$hist) json_out(['ok' => false, 'error' => 'Sin mensajes'], 422);
$ultima = end($hist)['content'];

if (empty($ai['api_key']) || str_starts_with($ai['api_key'], '__')) {
  json_out(['ok' => false, 'error' => 'Pliego todavía no está conectado. Mientras tanto, revisa la pestaña Cifras.'], 503);
}

try {
  $pdo = db();
  $dia = date('Y-m-d'); $ip = ip_hash();
  $pdo->prepare('INSERT INTO agente_uso (dia, ip_hash, n) VALUES (?,?,1) ON DUPLICATE KEY UPDATE n=n+1')->execute([$dia, $ip]);
  $q = $pdo->prepare('SELECT n FROM agente_uso WHERE dia=? AND ip_hash=?'); $q->execute([$dia, $ip]);
  if ((int)$q->fetch()['n'] > $ai['ip_limit']) json_out(['ok' => false, 'error' => 'Has hecho muchas preguntas hoy. Mañana seguimos doblando.'], 429);
  $q = $pdo->prepare('SELECT COALESCE(SUM(n),0) t FROM agente_uso WHERE dia=?'); $q->execute([$dia]);
  if ((int)$q->fetch()['t'] > $ai['daily_limit']) json_out(['ok' => false, 'error' => 'Pliego alcanzó su límite diario de conversaciones. Vuelve mañana.'], 429);
} catch (Throwable $e) { /* si la BD falla, seguimos sin límite */ }

$kb = require __DIR__ . '/conocimiento.php';
$system = "Eres Pliego, el agente de datos del 5° Encuentro Diversidades e Inclusión (Bogotá, 11 de noviembre de 2026). Tu trabajo: responder con cifras verificadas sobre diversidad e inclusión en Colombia, ayudar con lenguaje incluyente y orientar sobre el evento y la inscripción.\n\nREGLAS:\n1. Usa SOLO la base de conocimiento de abajo para cifras, fechas y nombres. Si algo no está ahí, dilo con claridad (\"no tengo esa cifra verificada\") y ofrece el dato más cercano que sí tengas. Nunca inventes números, leyes ni fuentes.\n2. Cita la fuente y el año al dar una cifra, de forma breve (ej.: \"DANE, GEIH jul–sep 2025\").\n3. Responde en español de Colombia, con tono cálido, directo y respetuoso. Máximo 120 palabras por respuesta, salvo que pidan detalle. Usa listas cortas cuando ayuden. Sin emojis.\n4. Lenguaje incluyente siempre: persona con discapacidad, personas mayores, personas LGBTIQ+, personas migrantes, pueblos indígenas, población NARP. Si la persona usa un término desaconsejado, responde su pregunta y al final sugiere con amabilidad la forma recomendada.\n5. Cierra, cuando aplique, con UNA acción concreta para la organización (qué puede doblar hoy).\n6. No des asesoría legal ni médica individual; remite a profesionales. No pidas ni guardes datos personales.\n" . ($easy ? "7. MODO LECTURA FÁCIL: frases cortas (máx. 12 palabras), una idea por frase, sin tecnicismos ni siglas sin explicar, máximo 60 palabras.\n" : "") . "\n" . $kb;

$messages = array_merge([['role' => 'system', 'content' => $system]], $hist);
$base = rtrim($ai['endpoint'], '/');
$attempts = [
  [$base . '/openai/v1/chat/completions', ['model' => $ai['deployment'], 'messages' => $messages, 'max_completion_tokens' => 700]],
  [$base . '/openai/deployments/' . rawurlencode($ai['deployment']) . '/chat/completions?api-version=2025-04-01-preview', ['messages' => $messages, 'max_completion_tokens' => 700]],
  [$base . '/openai/deployments/' . rawurlencode($ai['deployment']) . '/chat/completions?api-version=' . $ai['api_version'], ['messages' => $messages, 'max_tokens' => 700, 'temperature' => 0.3]],
];
$res = false; $code = 0; $err = ''; $detalle = [];
foreach ($attempts as [$url, $payload]) {
  $ch = curl_init($url);
  curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 50,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'api-key: ' . $ai['api_key']]]);
  $res = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
  if ($res !== false && $code < 400) break;
  $detalle[] = "HTTP $code $err " . substr((string)$res, 0, 200);
}
if ($res === false || $code >= 400) {
  error_log('agente: ' . implode(' | ', $detalle));
  json_out(['ok' => false, 'error' => 'Pliego no pudo responder ahora. Intenta de nuevo en un momento.', 'detalle' => $detalle], 502);
}
$j = json_decode($res, true);
$answer = trim((string)($j['choices'][0]['message']['content'] ?? ''));
if ($answer === '') json_out(['ok' => false, 'error' => 'Respuesta vacía.'], 502);
try { db()->prepare('INSERT INTO agente_log (pregunta, respuesta, tokens) VALUES (?,?,?)')->execute([mb_substr($ultima, 0, 400), $answer, (int)($j['usage']['total_tokens'] ?? 0)]); } catch (Throwable $e) {}
json_out(['ok' => true, 'answer' => $answer]);
