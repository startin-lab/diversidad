<?php
declare(strict_types=1);
// Laboratorio de IA aplicada a procesos de inclusión (9:30–10:30).
// Reparto: al registrar el ingreso, cada persona entra al grupo grande con menos gente (1, 2, 3, 1, 2, 3…).
// Quienes pidieron intérprete de LSC van todos al grupo del intérprete. Al iniciar el taller,
// «Formar células» divide cada grupo en células de máximo 9 personas (hasta 5 por grupo), mezclando organizaciones.
require_once __DIR__ . '/lib.php';

const TALLER_MAX_CELULA = 9;
const TALLER_MAX_CELULAS = 5;
const TALLER_TOPE_AGENTE = 60; // consultas a Pliego por célula

const TALLER_GRUPOS = [
  1 => ['eje' => 'Talento y desarrollo', 'pregunta' => '¿Cómo hacemos más equitativos los procesos de talento?', 'retos' => [
    'Revisar sesgos en selección y filtros de hojas de vida.',
    'Mejorar la inclusión en la inducción de nuevos colaboradores.',
    'Identificar barreras en formación y desarrollo profesional.',
    'Revisar criterios de evaluación de desempeño.',
    'Promover oportunidades de crecimiento sin sesgos.']],
  2 => ['eje' => 'Accesibilidad y participación', 'pregunta' => '¿Cómo hacemos que todas las personas puedan acceder, participar y aprender?', 'retos' => [
    'Hacer más accesibles los canales y herramientas digitales.',
    'Mejorar la comunicación interna con lenguaje claro e inclusivo.',
    'Diseñar reuniones en las que todas las personas puedan participar.',
    'Adaptar experiencias de capacitación a diferentes necesidades.',
    'Revisar protocolos de emergencia con enfoque diferencial.']],
  3 => ['eje' => 'Cultura y experiencia', 'pregunta' => '¿Cómo construimos entornos y experiencias más respetuosas e inclusivas?', 'retos' => [
    'Fortalecer la orientación y difusión de rutas frente al acoso laboral y acoso sexual laboral.',
    'Mejorar la atención a clientes con distintas necesidades de comunicación.',
    'Promover una cultura de respeto y sentido de pertenencia.',
    'Identificar barreras en la experiencia cotidiana de los colaboradores.',
    'Incorporar criterios de diversidad en campañas y comunicaciones.']],
];

// Fases del documento metodológico (60 minutos).
const TALLER_FASES = [
  'bienvenida' => ['n' => 0, 'nombre' => 'Bienvenida', 'min' => 5,
    'texto' => 'Ubica tu célula en la mesa de tu grupo. Cada célula trabaja un reto durante las tres fases, con apoyo de Pliego, el agente de IA.'],
  'diagnostico' => ['n' => 1, 'nombre' => 'Diagnóstico', 'min' => 12, 'campo' => 'barrera',
    'pregunta' => '¿Qué está impidiendo que todas las personas puedan participar, acceder o desarrollarse en igualdad de condiciones?',
    'pasos' => ['Lean el reto que les correspondió.', 'Conversen: ¿qué barrera concreta puede existir?, ¿a quién podría afectar?, ¿en qué momento del proceso aparece?', 'Escriban una barrera en la ficha y en un post-it. Una idea por nota.', 'La persona delegada pega el post-it en el backing Diagnóstico, en el espacio de su grupo y célula.'],
    'prompt' => 'Ayúdanos a identificar barreras de inclusión relacionadas con este reto: {reto} Sugiere preguntas que deberíamos hacernos para reconocer barreras que quizás no estamos viendo. No inventes datos ni asumas características de las personas.'],
  'analisis' => ['n' => 2, 'nombre' => 'Análisis', 'min' => 15, 'campo' => 'hallazgo',
    'pregunta' => '¿Por qué ocurre esta barrera y qué debemos revisar antes de proponer una solución?',
    'pasos' => ['Copien la barrera que identificaron.', 'Consulten a Pliego para explorar causas, consecuencias y riesgos de sesgo.', 'Decidan qué les sirve, qué deben validar y qué podría estar equivocado.', 'Registren el hallazgo más importante y una pregunta que necesitan resolver.'],
    'prompt' => 'Esta es la barrera que identificamos: {barrera} Ayúdanos a analizar posibles causas, a quién podría afectar y qué sesgos o riesgos debemos considerar. Propón tres oportunidades de mejora y señala qué información debemos validar con las personas involucradas.'],
  'experimentacion' => ['n' => 3, 'nombre' => 'Experimentación', 'min' => 18, 'campo' => 'solucion',
    'pregunta' => '¿Qué podemos crear o mejorar con IA para empezar a resolver esta barrera?',
    'pasos' => ['Elijan una oportunidad de mejora del análisis.', 'Pidan a Pliego una solución sencilla: una guía, una lista de chequeo, un mensaje accesible, un asistente de orientación o una mejora de proceso.', 'Hagan al menos un ajuste para que sea útil y aplicable.', 'Completen la solución, el prompt reutilizable y el primer paso.'],
    'prompt' => 'Queremos responder a esta barrera: {barrera} Ayúdanos a diseñar una solución sencilla apoyada en IA que podamos probar en nuestra organización. Describe cómo funcionaría, a quién beneficiaría, qué debemos revisar para evitar sesgos y cuál sería el primer paso para probarla.'],
  'plenaria' => ['n' => 4, 'nombre' => 'Puesta en común', 'min' => 8,
    'texto' => 'Cada grupo elige una idea destacada y la comparte en un minuto.'],
  'cierre' => ['n' => 5, 'nombre' => 'Cierre', 'min' => 2,
    'texto' => 'Registra una acción que podrías impulsar en tu organización en el Muro de compromisos.'],
];

const TALLER_CAMPOS = [
  'barrera' => ['Barrera priorizada', '¿Qué está dificultando la inclusión?'],
  'hallazgo' => ['Hallazgo del análisis', '¿Qué nos ayudó a comprender la IA y qué debemos validar?'],
  'solucion' => ['Solución propuesta', '¿Qué podríamos probar? ¿A quién ayuda? ¿Cómo sabríamos si funciona?'],
  'prompt' => ['Prompt reutilizable', '¿Qué instrucción puede adaptar la empresa?'],
  'primer_paso' => ['Primer paso', '¿Qué acción concreta podemos iniciar en los próximos 30 días?'],
];

function taller_db(): PDO {
  static $listo = false;
  $pdo = db();
  if ($listo) return $pdo;
  try { $pdo->exec('ALTER TABLE registros ADD COLUMN IF NOT EXISTS taller_grupo TINYINT NULL, ADD COLUMN IF NOT EXISTS taller_celula TINYINT NULL'); } catch (Throwable $e) {}
  $pdo->exec('CREATE TABLE IF NOT EXISTS taller_kv (k VARCHAR(40) PRIMARY KEY, v VARCHAR(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
  $pdo->exec("CREATE TABLE IF NOT EXISTS taller_fichas (
    grupo TINYINT NOT NULL, celula TINYINT NOT NULL,
    barrera TEXT, hallazgo TEXT, solucion TEXT, prompt TEXT, primer_paso TEXT,
    agente_n INT NOT NULL DEFAULT 0, editado DATETIME NULL,
    PRIMARY KEY (grupo, celula)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  $listo = true;
  return $pdo;
}

function taller_kv(PDO $pdo, string $k, ?string $def = null): ?string {
  $q = $pdo->prepare('SELECT v FROM taller_kv WHERE k=?'); $q->execute([$k]);
  $v = $q->fetchColumn();
  return $v === false ? $def : (string)$v;
}
function taller_kv_set(PDO $pdo, string $k, ?string $v): void {
  if ($v === null) { $pdo->prepare('DELETE FROM taller_kv WHERE k=?')->execute([$k]); return; }
  $pdo->prepare('INSERT INTO taller_kv (k, v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)')->execute([$k, $v]);
}

function taller_grupo_lsc(): int { $g = (int)(cfg()['taller_grupo_lsc'] ?? 2); return isset(TALLER_GRUPOS[$g]) ? $g : 2; }
function taller_es_lsc(?string $ajustesJson): bool {
  foreach (json_decode($ajustesJson ?: '[]', true) ?: [] as $a) if (preg_match('/LSC|se[ñn]as/iu', (string)$a)) return true;
  return false;
}
function taller_org(string $o): string {
  $o = mb_strtolower(trim($o));
  $o = strtr($o, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
  return preg_replace('/\b(s\.?a\.?s?|ltda|sas|fundacion|corporacion|universidad)\b|[^a-z0-9]/u', '', $o) ?: 'x';
}
function taller_formado(PDO $pdo): bool { return taller_kv($pdo, 'formado') === '1'; }
function taller_celulas(PDO $pdo, int $g): int { return (int)taller_kv($pdo, "celulas_$g", '0'); }

function taller_lock(PDO $pdo): void { $pdo->query("SELECT GET_LOCK('dvs_taller', 10)"); }
function taller_unlock(PDO $pdo): void { $pdo->query("SELECT RELEASE_LOCK('dvs_taller')"); }

/** Asigna grupo (y célula, si ya se formaron) a una persona que acaba de ingresar. Devuelve su ubicación. */
function taller_asignar(PDO $pdo, int $id): array {
  taller_db();
  taller_lock($pdo);
  try {
    $q = $pdo->prepare('SELECT id, organizacion, ajustes, asistio, taller_grupo, taller_celula FROM registros WHERE id=?'); $q->execute([$id]);
    $r = $q->fetch();
    if (!$r || !(int)$r['asistio']) return [];
    $g = (int)$r['taller_grupo']; $c = (int)$r['taller_celula'];
    $org = taller_org((string)$r['organizacion']);
    $lsc = taller_es_lsc($r['ajustes']);
    if (!$g) {
      if ($lsc) $g = taller_grupo_lsc();
      else {
        $mejor = null;
        foreach (array_keys(TALLER_GRUPOS) as $gg) {
          [$n, $misma] = taller_conteo($pdo, $gg, null, $org);
          $clave = [$n, $misma, $gg];
          if ($mejor === null || $clave < $mejor) $mejor = $clave;
        }
        $g = $mejor[2];
      }
      $pdo->prepare('UPDATE registros SET taller_grupo=? WHERE id=?')->execute([$g, $id]);
    }
    if (!$c && taller_formado($pdo)) {
      $k = max(1, taller_celulas($pdo, $g));
      $cl = ($lsc && $g === taller_grupo_lsc()) ? (int)taller_kv($pdo, 'celula_lsc', '0') : 0;
      if ($cl) $c = $cl;
      else {
        $mejor = null;
        for ($cc = 1; $cc <= $k; $cc++) {
          [$n, $misma] = taller_conteo($pdo, $g, $cc, $org);
          $clave = [$n, $misma, $cc];
          if ($mejor === null || $clave < $mejor) $mejor = $clave;
        }
        $c = $mejor[2];
        // Si todas las células ya llegaron a 10 (una más del máximo), quien llega tarde abre una nueva.
        if ($mejor[0] > TALLER_MAX_CELULA && $k < TALLER_MAX_CELULAS) $c = $k = $k + 1;
        if ($lsc && $g === taller_grupo_lsc()) taller_kv_set($pdo, 'celula_lsc', (string)$c);
      }
      if ($k > taller_celulas($pdo, $g)) taller_kv_set($pdo, "celulas_$g", (string)$k);
      $pdo->prepare('UPDATE registros SET taller_celula=? WHERE id=?')->execute([$c, $id]);
    }
  } finally { taller_unlock($pdo); }
  return taller_ubicacion($g, $c);
}

/** Personas presentes en un grupo (o célula) y cuántas son de la misma organización. */
function taller_conteo(PDO $pdo, int $g, ?int $c, string $org): array {
  $sql = 'SELECT organizacion FROM registros WHERE asistio=1 AND taller_grupo=?' . ($c ? ' AND taller_celula=?' : '');
  $q = $pdo->prepare($sql); $q->execute($c ? [$g, $c] : [$g]);
  $n = 0; $misma = 0;
  foreach ($q->fetchAll(PDO::FETCH_COLUMN) as $o) { $n++; if (taller_org((string)$o) === $org) $misma++; }
  return [$n, $misma];
}

function taller_ubicacion(int $g, int $c): array {
  if (!$g || !isset(TALLER_GRUPOS[$g])) return [];
  $u = ['grupo' => $g, 'mesa' => $g, 'eje' => TALLER_GRUPOS[$g]['eje'], 'pregunta_eje' => TALLER_GRUPOS[$g]['pregunta'], 'celula' => $c ?: null, 'reto' => null];
  if ($c) $u['reto'] = TALLER_GRUPOS[$g]['retos'][($c - 1) % 5];
  return $u;
}

/** Cuántas células abre un grupo según las personas presentes. */
function taller_k(int $n): int { return $n ? min(TALLER_MAX_CELULAS, max(1, (int)ceil($n / TALLER_MAX_CELULA))) : 0; }

/** Divide cada grupo en células equilibradas. Rehace el reparto de células si ya existía. */
function taller_formar(PDO $pdo): array {
  taller_db();
  // Quien ingresó sin grupo (por ejemplo, marcado a mano antes del módulo) recibe uno primero.
  foreach ($pdo->query('SELECT id FROM registros WHERE asistio=1 AND taller_grupo IS NULL ORDER BY id')->fetchAll(PDO::FETCH_COLUMN) as $id) taller_asignar($pdo, (int)$id);
  taller_lock($pdo);
  $res = [];
  try {
    $pdo->beginTransaction();
    $pdo->exec('UPDATE registros SET taller_celula=NULL');
    foreach (array_keys(TALLER_GRUPOS) as $g) {
      $q = $pdo->prepare('SELECT id, organizacion, ajustes FROM registros WHERE asistio=1 AND taller_grupo=? ORDER BY id'); $q->execute([$g]);
      $gente = $q->fetchAll();
      $k = taller_k(count($gente));
      $cel = array_fill(1, max(1, $k), []);
      $lscCel = 0;
      // 1) Personas con intérprete de LSC: juntas en la célula 1 de su grupo.
      $resto = [];
      foreach ($gente as $p) {
        if ($k && taller_es_lsc($p['ajustes'])) { $cel[1][] = $p; $lscCel = 1; } else $resto[] = $p;
      }
      // 2) Orden intercalado por organización, para que una misma empresa quede repartida.
      $cubetas = [];
      foreach ($resto as $p) $cubetas[taller_org((string)$p['organizacion'])][] = $p;
      usort($cubetas, fn($a, $b) => count($b) <=> count($a));
      $orden = [];
      while ($cubetas) { foreach ($cubetas as $i => &$cb) { $orden[] = array_shift($cb); if (!$cb) unset($cubetas[$i]); } unset($cb); }
      // 3) Cada persona a la célula con menos gente; si empatan, la que tenga menos de su organización.
      foreach ($orden as $p) {
        $o = taller_org((string)$p['organizacion']); $mejor = null;
        foreach ($cel as $i => $miembros) {
          $misma = count(array_filter($miembros, fn($m) => taller_org((string)$m['organizacion']) === $o));
          $clave = [count($miembros), $misma, $i];
          if ($mejor === null || $clave < $mejor) $mejor = $clave;
        }
        $cel[$mejor[2]][] = $p;
      }
      $up = $pdo->prepare('UPDATE registros SET taller_celula=? WHERE id=?');
      foreach ($cel as $i => $miembros) foreach ($miembros as $p) $up->execute([$i, (int)$p['id']]);
      taller_kv_set($pdo, "celulas_$g", (string)$k);
      if ($g === taller_grupo_lsc()) taller_kv_set($pdo, 'celula_lsc', $lscCel ? '1' : null);
      $res[$g] = array_map('count', $k ? $cel : []);
    }
    taller_kv_set($pdo, 'formado', '1');
    $pdo->commit();
  } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
  finally { taller_unlock($pdo); }
  return $res;
}

/** Fase en curso y segundos restantes (el reloj lo lleva el servidor para que todos vean lo mismo). */
function taller_estado(PDO $pdo): array {
  taller_db();
  $f = taller_kv($pdo, 'fase', '');
  $ini = (int)taller_kv($pdo, 'fase_inicio', '0');
  $e = ['formado' => taller_formado($pdo), 'fase' => null, 'ahora' => time()];
  if ($f && isset(TALLER_FASES[$f])) {
    $d = TALLER_FASES[$f];
    $fin = $ini + $d['min'] * 60;
    $e['fase'] = ['id' => $f, 'n' => $d['n'], 'nombre' => $d['nombre'], 'min' => $d['min'], 'inicio' => $ini, 'fin' => $fin, 'restante' => max(0, $fin - time()),
      'pregunta' => $d['pregunta'] ?? null, 'texto' => $d['texto'] ?? null, 'pasos' => $d['pasos'] ?? [], 'campo' => $d['campo'] ?? null];
  }
  $e['fases'] = array_map(fn($k, $d) => ['id' => $k, 'nombre' => $d['nombre'], 'min' => $d['min']], array_keys(TALLER_FASES), TALLER_FASES);
  return $e;
}

function taller_ficha(PDO $pdo, int $g, int $c): array {
  $q = $pdo->prepare('SELECT * FROM taller_fichas WHERE grupo=? AND celula=?'); $q->execute([$g, $c]);
  $f = $q->fetch() ?: [];
  $out = [];
  foreach (array_keys(TALLER_CAMPOS) as $k) $out[$k] = (string)($f[$k] ?? '');
  return $out;
}

function taller_prompt(string $fase, array $u, array $ficha): string {
  $p = TALLER_FASES[$fase]['prompt'] ?? '';
  $barrera = trim($ficha['barrera'] ?? '') ?: '[escriban aquí la barrera que identificaron]';
  return strtr($p, ['{reto}' => (string)($u['reto'] ?? ''), '{barrera}' => $barrera]);
}

// ---- Modo prueba: 50 personas ficticias para ensayar el taller sin tocar a los inscritos reales. ----
const TALLER_PRUEBA = '5E-PRB';

function taller_prueba_n(PDO $pdo): int {
  return (int)$pdo->query("SELECT COUNT(*) FROM registros WHERE codigo LIKE '" . TALLER_PRUEBA . "%'")->fetchColumn();
}

/** Vuelve a empezar: sin células, fases ni fichas; quienes ya ingresaron reciben grupo de nuevo. */
function taller_reiniciar(PDO $pdo): void {
  taller_db();
  $pdo->exec('UPDATE registros SET taller_grupo=NULL, taller_celula=NULL');
  $pdo->exec('DELETE FROM taller_kv'); $pdo->exec('DELETE FROM taller_fichas');
  foreach ($pdo->query('SELECT id FROM registros WHERE asistio=1 ORDER BY id')->fetchAll(PDO::FETCH_COLUMN) as $id) taller_asignar($pdo, (int)$id);
}

/** Crea 30 personas de prueba (sin correo real) y registra el ingreso de las primeras 24. */
function taller_prueba_crear(PDO $pdo): void {
  taller_db();
  if (taller_prueba_n($pdo)) return;
  $orgs = ['Organización Prueba A', 'Organización Prueba A', 'Organización Prueba A', 'Organización Prueba B', 'Organización Prueba B', 'Organización Prueba C', 'Organización Prueba D', 'Organización Prueba E'];
  $ins = $pdo->prepare('INSERT INTO registros (codigo, nombres, documento_enc, documento_hash, organizacion, correo, grupos_enc, requiere_ajuste, ajustes, consentimiento, correo_enviado) VALUES (?,?,?,?,?,?,?,?,?,1,1)');
  for ($i = 1; $i <= 50; $i++) {
    $aj = in_array($i, [5, 17, 33], true) ? ['Intérprete de LSC'] : [];
    $ins->execute([sprintf('%s%03d', TALLER_PRUEBA, $i), sprintf('Persona de prueba %02d', $i), encrypt_str('0'), hash('sha256', random_bytes(16)),
      $orgs[$i % count($orgs)], sprintf('prueba%02d@prueba.invalid', $i), encrypt_str('[]'), $aj ? 1 : 0, json_encode($aj, JSON_UNESCAPED_UNICODE)]);
  }
  $q = $pdo->prepare("SELECT id FROM registros WHERE codigo LIKE ? ORDER BY codigo LIMIT 44"); $q->execute([TALLER_PRUEBA . '%']);
  foreach ($q->fetchAll(PDO::FETCH_COLUMN) as $id) { $pdo->prepare('UPDATE registros SET asistio=1 WHERE id=?')->execute([(int)$id]); taller_asignar($pdo, (int)$id); }
}

/** Borra las personas de prueba y deja el taller en cero. */
function taller_prueba_borrar(PDO $pdo): int {
  $n = $pdo->exec("DELETE FROM registros WHERE codigo LIKE '" . TALLER_PRUEBA . "%'");
  try { $pdo->exec('DELETE e FROM envios e LEFT JOIN registros r ON r.id=e.registro_id WHERE r.id IS NULL'); } catch (Throwable $e) {}
  taller_reiniciar($pdo);
  return (int)$n;
}
