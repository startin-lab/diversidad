<?php
declare(strict_types=1);
// Pliego arma la landing de la solución de una célula: reto, ficha, cómo funciona, mockup, riesgos y primer paso.
// Devuelve una estructura (JSON) que taller/landing.php pinta con la marca del Encuentro; nunca HTML del modelo.
require __DIR__ . '/taller_lib.php';
@set_time_limit(120);
const LANDING_COSTO = 3; // una landing cuenta como 3 consultas de la célula

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Método no permitido'], 405);
$in = body_json();
$code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string)($in['c'] ?? '')));
$ai = cfg()['ai'];
if (empty($ai['api_key']) || str_starts_with($ai['api_key'], '__')) json_out(['ok' => false, 'error' => 'Pliego no está conectado.'], 503);

try {
  $pdo = taller_db();
  $q = $pdo->prepare('SELECT taller_grupo, taller_celula FROM registros WHERE codigo=?'); $q->execute([$code]);
  $p = $q->fetch();
  if (!$p || !(int)$p['taller_celula']) json_out(['ok' => false, 'error' => 'La landing se activa cuando se forme tu célula.'], 409);
  $u = taller_ubicacion((int)$p['taller_grupo'], (int)$p['taller_celula']);
  $g = (int)$u['grupo']; $c = (int)$u['celula'];
  // Evita dos generaciones al tiempo de la misma célula (doble toque o dos celulares).
  if (!(int)$pdo->query("SELECT GET_LOCK('dvs_landing_{$g}_{$c}', 0)")->fetchColumn()) json_out(['ok' => false, 'error' => 'Pliego ya está armando la landing de tu célula. Espera unos segundos.'], 409);
  $pdo->prepare('INSERT INTO taller_fichas (grupo, celula, agente_n) VALUES (?,?,?) ON DUPLICATE KEY UPDATE agente_n=agente_n+?')->execute([$g, $c, LANDING_COSTO, LANDING_COSTO]);
  $q = $pdo->prepare('SELECT agente_n FROM taller_fichas WHERE grupo=? AND celula=?'); $q->execute([$g, $c]);
  if ((int)$q->fetchColumn() > TALLER_TOPE_AGENTE) json_out(['ok' => false, 'error' => 'Tu célula ya usó todas sus consultas a Pliego.'], 429);
  $ficha = taller_ficha($pdo, $g, $c);
  $previa = taller_landing($pdo, $g, $c);
} catch (Throwable $e) { error_log('landing: ' . $e->getMessage()); json_out(['ok' => false, 'error' => 'Servicio no disponible.'], 500); }

$instr = clean((string)($in['instruccion'] ?? ''), 600);
$chat = '';
foreach (array_slice(is_array($in['contexto'] ?? null) ? $in['contexto'] : [], -8) as $m) {
  $rol = ($m['role'] ?? '') === 'user' ? 'Célula' : 'Pliego';
  $chat .= "$rol: " . clean((string)($m['content'] ?? ''), 900) . "\n";
}
$fichaTxt = '';
foreach (TALLER_CAMPOS as $k => [$tit]) $fichaTxt .= "- $tit: " . (trim($ficha[$k]) !== '' ? trim($ficha[$k]) : '(vacío)') . "\n";

$esquema = <<<TXT
Responde SOLO un objeto JSON válido, en español de Colombia, con esta forma exacta:
{
 "titulo": "nombre corto y memorable de la solución (máx. 6 palabras)",
 "lema": "una frase que explique el valor (máx. 18 palabras)",
 "problema": "el problema y la barrera, en 2 o 3 frases claras",
 "para_quien": "a quiénes beneficia (sin asumir características de las personas)",
 "hallazgos": ["3 hallazgos o causas, cada uno en una frase"],
 "solucion": "qué proponen y cómo usa la IA, en 2 o 3 frases",
 "como_funciona": [{"paso": "título corto", "detalle": "una frase"}],
 "mockup": {"tipo": "chat | checklist | formulario | tablero | guia", "app": "nombre de la pantalla", "elementos": ["4 a 6 textos cortos de la pantalla"], "mensajes": [{"de": "persona | asistente", "texto": "frase corta"}]},
 "riesgos": ["2 o 3 sesgos o riesgos a vigilar y cómo mitigarlos"],
 "primer_paso": "acción concreta para los próximos 30 días",
 "indicador": "cómo sabrán que funciona (una métrica simple)",
 "prompt": "un prompt reutilizable para que la empresa siga trabajando esta solución"
}
Reglas: "como_funciona" con 3 o 4 pasos. "mensajes" solo si el tipo es chat (3 o 4 mensajes); si no, lista vacía. No inventes cifras, leyes ni nombres de empresas. Usa lenguaje incluyente y claro. Si la ficha está incompleta, propón con lo que hay y escríbelo como hipótesis a validar.
TXT;
$sistema = "Eres Pliego, el copiloto del Laboratorio de IA aplicada a procesos de inclusión (5° Encuentro Diversidades e Inclusión, Bogotá). Vas a convertir el trabajo de una célula en una landing visual de su solución, para que la presenten y la sigan ajustando.\n\n$esquema";
$usuario = "GRUPO {$g}: {$u['eje']}. {$u['pregunta_eje']}\nCÉLULA {$c}. RETO: {$u['reto']}\n\nFICHA DE LA CÉLULA:\n$fichaTxt"
  . ($chat !== '' ? "\nCONVERSACIÓN RECIENTE CON PLIEGO:\n$chat" : '')
  . ($previa ? "\nLANDING ACTUAL (ajústala, no empieces de cero):\n" . json_encode(array_diff_key($previa, ['_editado' => 1]), JSON_UNESCAPED_UNICODE) : '')
  . ($instr !== '' ? "\nCAMBIOS QUE PIDE LA CÉLULA: $instr" : '');

$messages = [['role' => 'system', 'content' => $sistema], ['role' => 'user', 'content' => $usuario]];
$base = rtrim($ai['endpoint'], '/');
$attempts = [
  [$base . '/openai/v1/chat/completions', ['model' => $ai['deployment'], 'messages' => $messages, 'max_completion_tokens' => 3500, 'reasoning_effort' => 'low', 'response_format' => ['type' => 'json_object']]],
  [$base . '/openai/v1/chat/completions', ['model' => $ai['deployment'], 'messages' => $messages, 'max_completion_tokens' => 3500, 'response_format' => ['type' => 'json_object']]],
  [$base . '/openai/deployments/' . rawurlencode($ai['deployment']) . '/chat/completions?api-version=2025-04-01-preview', ['messages' => $messages, 'max_completion_tokens' => 3500]],
];
$res = false; $code = 0; $detalle = [];
foreach ($attempts as [$url, $payload]) {
  $ch = curl_init($url);
  curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 90,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'api-key: ' . $ai['api_key']]]);
  $res = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); unset($ch);
  if ($res !== false && $code < 400) break;
  $detalle[] = "HTTP $code " . substr((string)$res, 0, 160);
}
if ($res === false || $code >= 400) { error_log('landing: ' . implode(' | ', $detalle)); json_out(['ok' => false, 'error' => 'Pliego no pudo armar la landing ahora. Intenta de nuevo.'], 502); }
$j = json_decode((string)$res, true);
$txt = trim((string)($j['choices'][0]['message']['content'] ?? ''));
if (preg_match('/\{.*\}/s', $txt, $m)) $txt = $m[0];
$d = json_decode($txt, true);
if (!is_array($d)) json_out(['ok' => false, 'error' => 'Pliego no armó bien la landing. Intenta de nuevo.'], 502);

// Limpieza: solo texto plano, largos acotados y la forma esperada.
$t = fn($v, $n) => clean(strip_tags((string)(is_scalar($v) ? $v : '')), $n);
$lista = fn($v, $n, $max) => array_values(array_filter(array_map(fn($x) => $t($x, $n), array_slice(is_array($v) ? $v : [], 0, $max)), fn($x) => $x !== ''));
$tipos = ['chat', 'checklist', 'formulario', 'tablero', 'guia'];
$mk = is_array($d['mockup'] ?? null) ? $d['mockup'] : [];
$tipo = strtolower($t($mk['tipo'] ?? '', 20)); if (!in_array($tipo, $tipos, true)) $tipo = 'guia';
$landing = [
  'titulo' => $t($d['titulo'] ?? '', 80) ?: 'Nuestra solución',
  'lema' => $t($d['lema'] ?? '', 200),
  'problema' => $t($d['problema'] ?? '', 600),
  'para_quien' => $t($d['para_quien'] ?? '', 300),
  'hallazgos' => $lista($d['hallazgos'] ?? [], 260, 4),
  'solucion' => $t($d['solucion'] ?? '', 700),
  'como_funciona' => array_values(array_filter(array_map(fn($x) => is_array($x) ? ['paso' => $t($x['paso'] ?? '', 60), 'detalle' => $t($x['detalle'] ?? '', 220)] : null, array_slice(is_array($d['como_funciona'] ?? null) ? $d['como_funciona'] : [], 0, 5)))),
  'mockup' => ['tipo' => $tipo, 'app' => $t($mk['app'] ?? '', 50) ?: 'Nuestra herramienta', 'elementos' => $lista($mk['elementos'] ?? [], 90, 6),
    'mensajes' => array_values(array_filter(array_map(fn($x) => is_array($x) ? ['de' => (($x['de'] ?? '') === 'asistente' ? 'asistente' : 'persona'), 'texto' => $t($x['texto'] ?? '', 200)] : null, array_slice(is_array($mk['mensajes'] ?? null) ? $mk['mensajes'] : [], 0, 5))))],
  'riesgos' => $lista($d['riesgos'] ?? [], 260, 3),
  'primer_paso' => $t($d['primer_paso'] ?? '', 300),
  'indicador' => $t($d['indicador'] ?? '', 260),
  'prompt' => $t($d['prompt'] ?? '', 1200),
  'grupo' => $g, 'celula' => $c, 'eje' => $u['eje'], 'reto' => $u['reto'],
];
try {
  $pdo->prepare('UPDATE taller_fichas SET landing=?, landing_editado=NOW() WHERE grupo=? AND celula=?')->execute([json_encode($landing, JSON_UNESCAPED_UNICODE), $g, $c]);
  // Si la ficha tenía vacíos la solución, el prompt o el primer paso, se completan con la landing.
  foreach (['solucion' => $landing['solucion'], 'prompt' => $landing['prompt'], 'primer_paso' => $landing['primer_paso']] as $k => $v)
    if (trim($ficha[$k]) === '' && $v !== '') $pdo->prepare("UPDATE taller_fichas SET $k=? WHERE grupo=? AND celula=?")->execute([$v, $g, $c]);
  $pdo->prepare('INSERT INTO agente_log (pregunta, respuesta, tokens) VALUES (?,?,?)')->execute(['[landing] grupo ' . $g . ' célula ' . $c . ($instr ? ': ' . mb_substr($instr, 0, 300) : ''), mb_substr($landing['titulo'] . ' — ' . $landing['lema'], 0, 1000), (int)($j['usage']['total_tokens'] ?? 0)]);
} catch (Throwable $e) { error_log('landing guardar: ' . $e->getMessage()); }
json_out(['ok' => true, 'url' => 'landing.php?g=' . $g . '&c=' . $c, 'landing' => $landing]);
