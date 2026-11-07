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

// Modo taller: Pliego acompaña a una célula del Laboratorio de IA. El tope es por célula y no por IP,
// porque en el auditorio muchas personas comparten la misma red.
$taller = ($in['modo'] ?? '') === 'taller';
$tu = [];
if ($taller) {
  require_once __DIR__ . '/taller_lib.php';
  $code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($in['c'] ?? '')));
  try {
    $pdo = taller_db();
    $q = $pdo->prepare('SELECT taller_grupo, taller_celula FROM registros WHERE codigo=?'); $q->execute([$code]);
    $p = $q->fetch();
    if (!$p || !(int)$p['taller_celula']) json_out(['ok' => false, 'error' => 'Pliego se activa cuando se forme tu célula.'], 409);
    $tu = taller_ubicacion((int)$p['taller_grupo'], (int)$p['taller_celula']);
    $pdo->prepare('INSERT INTO taller_fichas (grupo, celula, agente_n) VALUES (?,?,1) ON DUPLICATE KEY UPDATE agente_n=agente_n+1')->execute([$tu['grupo'], $tu['celula']]);
    $q = $pdo->prepare('SELECT agente_n FROM taller_fichas WHERE grupo=? AND celula=?'); $q->execute([$tu['grupo'], $tu['celula']]);
    if ((int)$q->fetchColumn() > TALLER_TOPE_AGENTE) json_out(['ok' => false, 'error' => 'Tu célula ya usó todas sus consultas a Pliego. Sigan con lo que tienen: ¡van muy bien!'], 429);
    $fase = taller_estado($pdo)['fase'];
    $tu['fase'] = $fase ? $fase['nombre'] : 'antes de empezar';
    $tu['ficha'] = taller_ficha($pdo, (int)$tu['grupo'], (int)$tu['celula']);
  } catch (Throwable $e) { error_log('agente taller: ' . $e->getMessage()); json_out(['ok' => false, 'error' => 'Servicio no disponible.'], 500); }
}

try {
  $pdo = db();
  $dia = date('Y-m-d'); $ip = $taller ? hash('sha256', "taller|{$tu['grupo']}|{$tu['celula']}") : ip_hash();
  $pdo->prepare('INSERT INTO agente_uso (dia, ip_hash, n) VALUES (?,?,1) ON DUPLICATE KEY UPDATE n=n+1')->execute([$dia, $ip]);
  $q = $pdo->prepare('SELECT n FROM agente_uso WHERE dia=? AND ip_hash=?'); $q->execute([$dia, $ip]);
  if (!$taller && (int)$q->fetch()['n'] > $ai['ip_limit']) json_out(['ok' => false, 'error' => 'Has hecho muchas preguntas hoy. Mañana seguimos doblando.'], 429);
  $q = $pdo->prepare('SELECT COALESCE(SUM(n),0) t FROM agente_uso WHERE dia=?'); $q->execute([$dia]);
  if ((int)$q->fetch()['t'] > $ai['daily_limit']) json_out(['ok' => false, 'error' => 'Pliego alcanzó su límite diario de conversaciones. Vuelve mañana.'], 429);
} catch (Throwable $e) { /* si la BD falla, seguimos sin límite */ }

$kb = require __DIR__ . '/conocimiento.php';
$system = "Eres Pliego, el agente de datos del 5° Encuentro Diversidades e Inclusión (Bogotá, 11 de noviembre de 2026). Tu trabajo: responder con cifras verificadas sobre diversidad e inclusión en Colombia, ayudar con lenguaje incluyente y orientar sobre el evento y la inscripción.\n\nREGLAS:\n1. Usa SOLO la base de conocimiento de abajo para cifras, fechas y nombres. Si algo no está ahí, dilo con claridad (\"no tengo esa cifra verificada\") y ofrece el dato más cercano que sí tengas. Nunca inventes números, leyes ni fuentes.\n2. Cita la fuente y el año al dar una cifra, de forma breve (ej.: \"DANE, GEIH jul–sep 2025\").\n3. Responde en español de Colombia, con tono cálido, directo y respetuoso. Máximo 120 palabras por respuesta, salvo que pidan detalle. Usa listas cortas cuando ayuden. Sin emojis.\n4. Lenguaje incluyente siempre: persona con discapacidad, personas mayores, personas LGBTIQ+, personas migrantes, pueblos indígenas, población NARP. Si la persona usa un término desaconsejado, responde su pregunta y al final sugiere con amabilidad la forma recomendada.\n5. Cierra, cuando aplique, con UNA acción concreta para la organización (qué puede doblar hoy).\n6. No des asesoría legal ni médica individual; remite a profesionales. No pidas ni guardes datos personales.\n" . ($easy ? "7. MODO LECTURA FÁCIL: frases cortas (máx. 12 palabras), una idea por frase, sin tecnicismos ni siglas sin explicar, máximo 60 palabras.\n" : "") . "\n" . $kb;

if ($taller) {
  $system = "Eres Pliego, el copiloto del Laboratorio de IA aplicada a procesos de inclusión en las organizaciones (5° Encuentro Diversidades e Inclusión, Bogotá, 11 de noviembre de 2026). Acompañas a una célula de unas ocho personas de distintas organizaciones.\n\nREGLAS:\n1. No tomas decisiones por las personas: ayudas a formular preguntas, analizar situaciones, proponer alternativas y construir borradores (guías, listas de chequeo, mensajes accesibles, asistentes de orientación, mejoras de proceso).\n2. Nunca inventes datos, cifras, leyes ni fuentes. Para cifras usa solo la base de conocimiento de abajo y cita fuente y año; si no está, dilo.\n3. Distingue hechos de hipótesis. Señala qué información deben validar con las personas involucradas y cuándo se requiere criterio humano.\n4. No asumas características de las personas ni presupongas necesidades individuales. Lenguaje incluyente siempre.\n5. Español de Colombia, claro y cálido. Máximo 220 palabras, con listas cortas. Sin emojis.\n6. En Análisis y Experimentación, cierra con un prompt reutilizable que la empresa pueda adaptar, escrito entre comillas.\n7. No pidas ni guardes datos personales; si alguien comparte un caso real, sugiere usar un ejemplo ficticio.\n8. Mantente en el reto de la célula; si preguntan otra cosa del evento, responde breve y vuelve al reto.\n\n\n\n" . $kb . "\n\nCONTEXTO DE LA CÉLULA\n- Grupo {$tu['grupo']}: {$tu['eje']}. {$tu['pregunta_eje']}\n- Célula {$tu['celula']}. Reto: {$tu['reto']}\n- Fase en curso: {$tu['fase']}. Las fases son Diagnóstico (identificar una barrera concreta), Análisis (causas, sesgos, oportunidades y qué validar) y Experimentación (una solución sencilla apoyada en IA, un prompt reutilizable y un primer paso para los próximos 30 días).\n" . $kb;
}
if ($taller) {
  // Lo que la célula ya escribió en su ficha: Pliego lo usa sin pedir que lo copien.
  $ft = "\n\nLO QUE LA CÉLULA YA ESCRIBIÓ EN SU FICHA (úsalo como contexto; no pidas que lo copien ni lo repitan):\n";
  foreach (TALLER_CAMPOS as $k => [$tit]) $ft .= "- $tit: " . (trim($tu['ficha'][$k] ?? '') !== '' ? mb_substr(trim($tu['ficha'][$k]), 0, 900) : '(aún vacío)') . "\n";
  $ft .= "\nSi piden una landing, una página o un mockup de su solución, diles que usen el botón «Crear nuestra landing» de la pestaña Fase (en Experimentación): arma una página visual con su reto, su ficha y un mockup.\n";
  $system .= $ft;
}
$messages = array_merge([['role' => 'system', 'content' => $system]], $hist);
$base = rtrim($ai['endpoint'], '/');
$attempts = [
  [$base . '/openai/v1/chat/completions', ['model' => $ai['deployment'], 'messages' => $messages, 'max_completion_tokens' => $taller ? 1100 : 700]],
  [$base . '/openai/deployments/' . rawurlencode($ai['deployment']) . '/chat/completions?api-version=2025-04-01-preview', ['messages' => $messages, 'max_completion_tokens' => $taller ? 1100 : 700]],
  [$base . '/openai/deployments/' . rawurlencode($ai['deployment']) . '/chat/completions?api-version=' . $ai['api_version'], ['messages' => $messages, 'max_tokens' => $taller ? 1100 : 700, 'temperature' => 0.3]],
];
$res = false; $code = 0; $err = ''; $detalle = [];
foreach ($attempts as [$url, $payload]) {
  $ch = curl_init($url);
  curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 50,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'api-key: ' . $ai['api_key']]]);
  $res = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); unset($ch);
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
