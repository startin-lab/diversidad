<?php
declare(strict_types=1);
// Laboratorio de IA aplicada a procesos de inclusión (9:30–10:30).
// Reparto: al registrar el ingreso, cada persona entra al grupo grande con menos gente (1, 2, 3, 1, 2, 3…).
// Quienes pidieron intérprete de LSC van todos al grupo del intérprete. Al iniciar el taller,
// «Formar células» divide cada grupo SIEMPRE en 5 células, una por reto (90 personas → 30 por grupo → 6 por célula),
// mezclando organizaciones. Quien llega tarde entra a la célula más pequeña de su grupo.
require_once __DIR__ . '/lib.php';

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
    'texto' => 'Reúnete con tu célula: la persona facilitadora de tu grupo les indica dónde. Cada célula trabaja un reto durante las tres fases, con apoyo de Pliego, el agente de IA.'],
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
    'pasos' => ['Elijan una oportunidad de mejora del análisis.', 'Pidan a Pliego una solución sencilla: una guía, una lista de chequeo, un mensaje accesible, un asistente de orientación o una mejora de proceso.', 'Hagan al menos un ajuste para que sea útil y aplicable.', 'Completen la solución y el primer paso en la ficha.', 'Creen la landing de su solución con Pliego y ajústenla con sus palabras.'],
    'prompt' => 'Queremos responder a esta barrera: {barrera} Ayúdanos a diseñar una solución sencilla apoyada en IA que podamos probar en nuestra organización. Describe cómo funcionaría, a quién beneficiaría, qué debemos revisar para evitar sesgos y cuál sería el primer paso para probarla.'],
  'plenaria' => ['n' => 4, 'nombre' => 'Votemos la idea', 'min' => 8,
    'texto' => 'Cada persona vota en su celular por la idea de otra célula de su grupo. La elegida se presenta en un minuto.'],
  'cierre' => ['n' => 5, 'nombre' => 'Cierre', 'min' => 2,
    'texto' => 'Escribe una acción que impulsarás en tu organización. Entre todas y todos armamos el muro final.'],
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
  // Quienes lideran el laboratorio: registran su ingreso, pero no entran al reparto, a las células ni a la votación.
  try { $pdo->exec("ALTER TABLE registros ADD COLUMN IF NOT EXISTS taller_rol VARCHAR(20) NOT NULL DEFAULT ''"); } catch (Throwable $e) {}
  $pdo->exec('CREATE TABLE IF NOT EXISTS taller_kv (k VARCHAR(40) PRIMARY KEY, v VARCHAR(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
  $pdo->exec("CREATE TABLE IF NOT EXISTS taller_fichas (
    grupo TINYINT NOT NULL, celula TINYINT NOT NULL,
    barrera TEXT, hallazgo TEXT, solucion TEXT, prompt TEXT, primer_paso TEXT,
    agente_n INT NOT NULL DEFAULT 0, editado DATETIME NULL,
    PRIMARY KEY (grupo, celula)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  try { $pdo->exec('ALTER TABLE taller_fichas ADD COLUMN IF NOT EXISTS landing MEDIUMTEXT NULL, ADD COLUMN IF NOT EXISTS landing_editado DATETIME NULL'); } catch (Throwable $e) {}
  // Historial: todo lo que la célula escribe, genera, pregunta y vota queda guardado (no se borra al reiniciar).
  $pdo->exec("CREATE TABLE IF NOT EXISTS taller_historial (
    id INT AUTO_INCREMENT PRIMARY KEY, ronda INT NOT NULL DEFAULT 1, grupo TINYINT NOT NULL, celula TINYINT NOT NULL,
    tipo VARCHAR(12) NOT NULL, campo VARCHAR(40) NOT NULL DEFAULT '', valor MEDIUMTEXT, registro_id INT NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX (ronda, grupo, celula), INDEX (tipo)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  // Copia completa de cada ronda antes de reiniciar o borrar las pruebas.
  $pdo->exec("CREATE TABLE IF NOT EXISTS taller_archivo (id INT AUTO_INCREMENT PRIMARY KEY, ronda INT NOT NULL, motivo VARCHAR(60) NOT NULL DEFAULT '', datos LONGTEXT NOT NULL, creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
  // Votación de la puesta en común: una persona, un voto por la idea que presenta su grupo.
  $pdo->exec('CREATE TABLE IF NOT EXISTS taller_votos (registro_id INT PRIMARY KEY, grupo TINYINT NOT NULL, celula TINYINT NOT NULL, creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX (grupo, celula)) ENGINE=InnoDB');
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
    $q = $pdo->prepare('SELECT id, organizacion, ajustes, asistio, taller_grupo, taller_celula, taller_rol FROM registros WHERE id=?'); $q->execute([$id]);
    $r = $q->fetch();
    if (!$r || !(int)$r['asistio']) return [];
    if ($r['taller_rol'] === 'facilitador') {
      if ($r['taller_grupo'] !== null || $r['taller_celula'] !== null) $pdo->prepare('UPDATE registros SET taller_grupo=NULL, taller_celula=NULL WHERE id=?')->execute([$id]);
      return ['facilitador' => true];
    }
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
        // Si el grupo tenía menos de 5 personas al formar, quien llega abre la célula que falta hasta cubrir los 5 retos.
        if ($k < TALLER_MAX_CELULAS && $mejor[0] > 0) $c = $k = $k + 1;
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
  $u = ['grupo' => $g, 'eje' => TALLER_GRUPOS[$g]['eje'], 'pregunta_eje' => TALLER_GRUPOS[$g]['pregunta'], 'celula' => $c ?: null, 'reto' => null];
  if ($c) $u['reto'] = TALLER_GRUPOS[$g]['retos'][($c - 1) % 5];
  return $u;
}

/** Cuántas células abre un grupo según las personas presentes. */
function taller_k(int $n): int { return min(TALLER_MAX_CELULAS, $n); } // siempre 5 retos; menos solo si hay menos de 5 personas

/** Divide cada grupo en células equilibradas. Rehace el reparto de células si ya existía. */
function taller_formar(PDO $pdo): array {
  taller_db();
  // Quien ingresó sin grupo (por ejemplo, marcado a mano antes del módulo) recibe uno primero.
  foreach ($pdo->query("SELECT id FROM registros WHERE asistio=1 AND taller_grupo IS NULL AND taller_rol='' ORDER BY id")->fetchAll(PDO::FETCH_COLUMN) as $id) taller_asignar($pdo, (int)$id);
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
function taller_reiniciar(PDO $pdo, string $motivo = 'reinicio'): void {
  taller_db();
  // Antes de limpiar, se guarda la ronda completa y se abre una nueva: nada de lo escrito se pierde.
  $ronda = taller_ronda($pdo);
  $pdo->prepare('INSERT INTO taller_archivo (ronda, motivo, datos) VALUES (?,?,?)')->execute([$ronda, $motivo, json_encode(taller_exportar($pdo, $ronda), JSON_UNESCAPED_UNICODE)]);
  $pdo->exec('UPDATE registros SET taller_grupo=NULL, taller_celula=NULL');
  $pdo->exec('DELETE FROM taller_kv'); $pdo->exec('DELETE FROM taller_fichas'); $pdo->exec('DELETE FROM taller_votos');
  taller_kv_set($pdo, 'ronda', (string)($ronda + 1));
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
  taller_db();
  // Primero se archiva la ronda de ensayo (con sus fichas y landings); luego se borran las personas ficticias.
  $ronda = taller_ronda($pdo);
  $pdo->prepare('INSERT INTO taller_archivo (ronda, motivo, datos) VALUES (?,?,?)')->execute([$ronda, 'ensayo (personas de prueba)', json_encode(taller_exportar($pdo, $ronda), JSON_UNESCAPED_UNICODE)]);
  $n = $pdo->exec("DELETE FROM registros WHERE codigo LIKE '" . TALLER_PRUEBA . "%'");
  try { $pdo->exec('DELETE e FROM envios e LEFT JOIN registros r ON r.id=e.registro_id WHERE r.id IS NULL'); } catch (Throwable $e) {}
  $pdo->exec('UPDATE registros SET taller_grupo=NULL, taller_celula=NULL');
  $pdo->exec('DELETE FROM taller_kv'); $pdo->exec('DELETE FROM taller_fichas'); $pdo->exec('DELETE FROM taller_votos');
  taller_kv_set($pdo, 'ronda', (string)($ronda + 1));
  foreach ($pdo->query('SELECT id FROM registros WHERE asistio=1 ORDER BY id')->fetchAll(PDO::FETCH_COLUMN) as $id) taller_asignar($pdo, (int)$id);
  return (int)$n;
}

/** Votos por célula de un grupo: [celula => n]. */
function taller_votos(PDO $pdo, int $g): array {
  $q = $pdo->prepare('SELECT v.celula, COUNT(*) n FROM taller_votos v JOIN registros r ON r.id=v.registro_id WHERE v.grupo=? GROUP BY v.celula'); $q->execute([$g]);
  $out = []; foreach ($q->fetchAll() as $x) $out[(int)$x['celula']] = (int)$x['n'];
  return $out;
}

/** Ideas de las células de un grupo para la puesta en común (solo el trabajo de la célula, sin nombres). */
function taller_opciones(PDO $pdo, int $g): array {
  $votos = taller_votos($pdo, $g); $op = [];
  for ($i = 1; $i <= taller_celulas($pdo, $g); $i++) {
    $f = taller_ficha($pdo, $g, $i);
    $lp = taller_landing($pdo, $g, $i);
    $op[] = ['celula' => $i, 'reto' => TALLER_GRUPOS[$g]['retos'][($i - 1) % 5], 'solucion' => $f['solucion'], 'barrera' => $f['barrera'], 'primer_paso' => $f['primer_paso'], 'votos' => $votos[$i] ?? 0,
      'landing' => $lp ? ['titulo' => $lp['titulo'], 'url' => "landing.php?g=$g&c=$i"] : null];
  }
  return $op;
}

/** Landing de una célula (estructura generada por Pliego), o null si aún no existe. */
function taller_landing(PDO $pdo, int $g, int $c): ?array {
  $q = $pdo->prepare('SELECT landing, landing_editado FROM taller_fichas WHERE grupo=? AND celula=?'); $q->execute([$g, $c]);
  $r = $q->fetch(); if (!$r || !$r['landing']) return null;
  $l = json_decode((string)$r['landing'], true); if (!is_array($l)) return null;
  $l['_editado'] = $r['landing_editado']; return $l;
}

/** Ronda en curso (cada reinicio abre una nueva; el evento real queda en la última). */
function taller_ronda(PDO $pdo): int {
  $r = (int)taller_kv($pdo, 'ronda', '0');
  if ($r) return $r;
  $r = 1 + (int)$pdo->query('SELECT COALESCE(MAX(ronda),0) FROM taller_archivo')->fetchColumn();
  taller_kv_set($pdo, 'ronda', (string)$r); return $r;
}

/** Guarda un movimiento en el historial (ficha, landing, chat o voto). */
function taller_registrar(PDO $pdo, int $g, int $c, string $tipo, string $campo, string $valor, ?int $registroId = null): void {
  try { $pdo->prepare('INSERT INTO taller_historial (ronda, grupo, celula, tipo, campo, valor, registro_id) VALUES (?,?,?,?,?,?,?)')->execute([taller_ronda($pdo), $g, $c, $tipo, mb_substr($campo, 0, 40), $valor, $registroId]); }
  catch (Throwable $e) { error_log('historial: ' . $e->getMessage()); }
}

/** Todo el laboratorio de una ronda: por grupo y célula, reto, ficha final, landing, votos e historial (sin nombres). */
function taller_exportar(PDO $pdo, ?int $ronda = null): array {
  taller_db(); $ronda = $ronda ?: taller_ronda($pdo);
  $org = []; foreach ($pdo->query('SELECT id, organizacion FROM registros')->fetchAll() as $x) $org[(int)$x['id']] = $x['organizacion'];
  $hist = $pdo->prepare('SELECT grupo, celula, tipo, campo, valor, registro_id, creado FROM taller_historial WHERE ronda=? ORDER BY id'); $hist->execute([$ronda]);
  $porCel = [];
  foreach ($hist->fetchAll() as $h) {
    $v = $h['tipo'] === 'landing' || $h['tipo'] === 'chat' ? (json_decode((string)$h['valor'], true) ?? $h['valor']) : $h['valor'];
    $porCel[(int)$h['grupo']][(int)$h['celula']][] = ['tipo' => $h['tipo'], 'campo' => $h['campo'], 'valor' => $v, 'organizacion' => $org[(int)$h['registro_id']] ?? null, 'fecha' => $h['creado']];
  }
  $out = ['laboratorio' => 'Laboratorio de IA aplicada a procesos de inclusión · 5° Encuentro Diversidades e Inclusión', 'ronda' => $ronda, 'exportado' => date('c'), 'grupos' => []];
  foreach (TALLER_GRUPOS as $g => $d) {
    $votos = taller_votos($pdo, $g); $dest = (int)taller_kv($pdo, "destacada_$g", '0'); $cels = [];
    $n = max(taller_celulas($pdo, $g), !empty($porCel[$g]) ? max(array_keys($porCel[$g])) : 0);
    for ($c = 1; $c <= $n; $c++) {
      $q = $pdo->prepare('SELECT organizacion FROM registros WHERE asistio=1 AND taller_grupo=? AND taller_celula=?'); $q->execute([$g, $c]);
      $todas = $q->fetchAll(PDO::FETCH_COLUMN); $orgs = array_values(array_unique($todas));
      $l = taller_landing($pdo, $g, $c); if ($l) unset($l['_editado']);
      $cels[] = ['celula' => $c, 'reto' => $d['retos'][($c - 1) % 5], 'personas' => count($todas), 'organizaciones' => $orgs,
        'ficha' => taller_ficha($pdo, $g, $c), 'landing' => $l, 'votos' => $votos[$c] ?? 0, 'presenta' => $dest === $c, 'historial' => $porCel[$g][$c] ?? []];
    }
    $out['grupos'][] = ['grupo' => $g, 'eje' => $d['eje'], 'pregunta' => $d['pregunta'], 'celulas' => $cels];
  }
  return $out;
}

/** Marca o desmarca a una persona como parte del equipo que facilita (sale del reparto y de la votación). */
function taller_facilitar(PDO $pdo, int $id, bool $si): void {
  taller_db();
  $pdo->prepare('UPDATE registros SET taller_rol=?, taller_grupo=IF(?, NULL, taller_grupo), taller_celula=IF(?, NULL, taller_celula) WHERE id=?')->execute([$si ? 'facilitador' : '', $si ? 1 : 0, $si ? 1 : 0, $id]);
  if ($si) $pdo->prepare('DELETE FROM taller_votos WHERE registro_id=?')->execute([$id]);
  else taller_asignar($pdo, $id);
}
