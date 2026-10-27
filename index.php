<?php $v = '2026.10.22u'; ?><!doctype html>
<html lang="es-CO">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>5° Encuentro Diversidades e Inclusión · Doblar no es romper</title>
<meta name="description" content="Gestión del cambio organizacional para la Inclusión y Equidad. 11 de noviembre de 2026, Universidad El Bosque – HUB iEX, Bogotá. Inscríbete y dobla tu escarapela.">
<meta property="og:title" content="5° Encuentro Diversidades e Inclusión">
<meta property="og:description" content="Gestión del cambio organizacional para la Inclusión y Equidad · 11 nov 2026 · Universidad El Bosque, Bogotá">
<meta property="og:image" content="https://diversidad.startinlab.org/assets/og.jpg">
<meta property="og:type" content="website">
<meta name="theme-color" content="#0C1646">
<link rel="icon" href="favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://use.typekit.net" crossorigin>
<link rel="stylesheet" href="https://use.typekit.net/rcv2qny.css">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&display=swap">
<link rel="stylesheet" href="app.css?v=<?= $v ?>">
</head>
<body>
<a class="skip" href="#escena">Saltar al contenido</a>
<div class="app">
<header class="top">
  <a class="brand" href="#inicio" data-go="inicio" aria-label="5° Encuentro Diversidades e Inclusión, inicio"><span class="n">5°</span><span>Encuentro<br>Diversidades e Inclusión</span></a>
  <nav class="tabs" aria-label="Secciones del portal" role="tablist">
    <a role="tab" href="#inicio" data-go="inicio"><i aria-hidden="true">◇</i>Inicio</a>
    <a role="tab" href="#pliegue" data-go="pliegue"><i aria-hidden="true">▱</i>El pliegue</a>
    <a role="tab" href="#cifras" data-go="cifras"><i aria-hidden="true">◈</i>Cifras</a>
    <a role="tab" href="#pliego" data-go="pliego"><i aria-hidden="true">◆</i>Pliego</a>
    <a role="tab" href="#inscripcion" data-go="inscripcion" class="cta"><i aria-hidden="true">✦</i>Inscríbete</a>
    <a role="tab" href="#evento" data-go="evento"><i aria-hidden="true">◇</i>Evento</a>
  </nav>
  <button class="a11y-toggle" id="a11yBtn" aria-expanded="false" aria-controls="a11yPanel" title="Ajustes de accesibilidad">
    <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2" fill="currentColor"/><path d="M4 8l8 1.5L20 8M12 9.5V15m0 0l-3.5 6M12 15l3.5 6" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg><span>Accesibilidad</span>
  </button>
  <div class="a11y-panel" id="a11yPanel" role="region" aria-label="Ajustes de accesibilidad" hidden>
    <h3>Ajusta el portal a ti</h3>
    <div class="a11y-row"><span id="lblSize">Tamaño de texto</span><div class="seg" role="group" aria-labelledby="lblSize"><button id="sMinus" aria-label="Reducir texto">A−</button><button id="sReset" aria-label="Texto normal">A</button><button id="sPlus" aria-label="Aumentar texto">A+</button></div></div>
    <div class="a11y-row"><span>Alto contraste</span><button class="sw" id="tContrast" aria-pressed="false">No</button></div>
    <div class="a11y-row"><span>Lectura fácil</span><button class="sw" id="tEasy" aria-pressed="false">No</button></div>
    <div class="a11y-row"><span>Pausar movimiento</span><button class="sw" id="tMotion" aria-pressed="false">No</button></div>
    <div class="a11y-row"><span>Leer en voz alta</span><button class="sw" id="tVoice" aria-pressed="false">No</button></div>
    <p class="a11y-note">En el evento habrá intérprete de LSC, subtítulos y otros ajustes: pídelos al inscribirte.</p>
  </div>
</header>

<main id="escena" class="scenes">

<!-- ===== INICIO ===== -->
<section class="scene" id="s-inicio" data-scene="inicio" aria-labelledby="hTitle">
  <canvas id="facets" aria-hidden="true"></canvas>
  <div class="inicio">
    <div class="inicio-copy">
      <div class="edition" aria-hidden="true"><span class="n">5°</span><span class="t"><small>Encuentro</small>Diversidades<br>e Inclusión</span></div>
      <h1 id="hTitle"><b>Gestión</b> del <b>cambio</b> organizacional para la <b>Inclusión</b> y <b>Equidad</b></h1>
      <p class="lede std-only">Una organización, como una hoja de papel, no necesita romperse para cambiar de forma. Necesita pliegues bien hechos: decisiones que reorganizan lo que ya existe para que todas las personas quepan.</p>
      <p class="lede easy-only">Una empresa puede cambiar sin romperse. Como un papel que se dobla. Así todas las personas tienen un lugar.</p>
      <div class="meta">
        <div><b>11 de noviembre de 2026</b><span>8:00 a.m. – 1:00 p.m.</span></div>
        <div><b>Universidad El Bosque · HUB iEX</b><span>Cra. 7b Bis No. 132-28, Bogotá</span></div>
        <div class="countdown" role="timer" aria-live="off" aria-label="Cuenta regresiva para el Encuentro"><span class="cd-title">Faltan</span><div class="cd" id="cd"><div><b id="cdD">—</b><span>días</span></div><i>:</i><div><b id="cdH">—</b><span>horas</span></div><i>:</i><div><b id="cdM">—</b><span>min</span></div><i>:</i><div><b id="cdS">—</b><span>seg</span></div></div></div>
      </div>
      <div class="ctas"><a class="btn btn-primary" href="#inscripcion" data-go="inscripcion">Inscríbete y dobla tu escarapela</a><a class="btn btn-ghost" href="#pliegue" data-go="pliegue">¿Por qué pliegues?</a></div>
    </div>
    <div class="row-wrap">
      <p class="row-hint std-only"><span class="m-only">Desliza para ver a las nueve. </span>Toca una persona: cada una guarda una cifra del país.</p>
      <p class="row-hint easy-only"><span class="m-only">Desliza para ver más. </span>Toca una persona para ver su dato.</p>
      <div class="row" id="row" role="list"></div>
      <div class="floor" aria-hidden="true"></div>
    </div>
  </div>
</section>


<!-- ===== EL PLIEGUE (manifiesto) ===== -->
<section class="scene scroll" id="s-pliegue" data-scene="pliegue" aria-labelledby="hFold" hidden>
  <div class="manif">
    <div class="manif-head">
      <span class="eyebrow">La metáfora del Encuentro</span>
      <h2 id="hFold">Doblar <b>no es romper</b></h2>
      <p class="lede std-only">Un pliegue no le quita papel a la hoja: reorganiza lo que ya está ahí para que sostenga una forma nueva. Así funciona el cambio organizacional para la inclusión y la equidad. Nadie sobra; la estructura se ajusta para que todas las personas quepan.</p>
      <p class="lede easy-only">Doblar un papel no lo daña. Solo cambia su forma. Una empresa también puede cambiar para que todas las personas tengan un lugar.</p>
      <svg class="crane big" viewBox="0 0 120 120" aria-hidden="true"><polygon points="60,18 100,62 60,52" fill="#EE7DB9"/><polygon points="60,18 60,52 20,62" fill="#6A47B8"/><polygon points="20,62 60,52 60,100" fill="#13A79A"/><polygon points="100,62 60,52 60,100" fill="#FFC20E"/><polygon points="60,100 74,86 92,104" fill="#F26A21"/></svg>
    </div>
    <div class="folds">
      <article class="fold-card"><div class="k">1</div><h3>Marcar</h3><p class="std-only">Reconocer dónde está la organización hoy: datos de su gente, barreras, sesgos y obligaciones legales. Sin línea base no hay pliegue.</p><p class="easy-only">Primero miramos cómo está la empresa hoy.</p><small>Herramientas: diagnóstico de talento, cuota de la Ley 2466, mapa de ajustes razonables.</small></article>
      <article class="fold-card"><div class="k">2</div><h3>Doblar</h3><p class="std-only">Ajustar procesos: selección accesible, ajustes razonables, corresponsabilidad en el cuidado y liderazgo diverso. Cada doblez tiene responsable y fecha.</p><p class="easy-only">Después cambiamos lo que hace falta, con una persona responsable y una fecha.</p><small>Herramientas: vacantes accesibles, protocolos contra la discriminación, licencias equivalentes.</small></article>
      <article class="fold-card"><div class="k">3</div><h3>Sostener</h3><p class="std-only">Volver cultura lo que se ajustó: medir, comunicar y celebrar. Una figura de papel se mantiene porque cada pliegue sostiene al siguiente.</p><p class="easy-only">Al final medimos y contamos lo que cambió, para que dure.</p><small>Herramientas: indicadores, muro de compromisos, comunidad del Encuentro.</small></article>
    </div>
    <div class="manif-foot">
      <p><b>Por eso nueve figuras de papel abren el portal:</b> cada una es una persona real del país con una cifra que exige un pliegue distinto. Tócalas en el Inicio, pregúntale a Pliego, y el 11 de noviembre dobla tu compromiso con tu organización.</p>
      <div class="ctas"><a class="btn btn-primary" href="#cifras" data-go="cifras">Ver las cifras</a><a class="btn btn-ghost" href="#inscripcion" data-go="inscripcion">Dobla tu escarapela</a></div>
    </div>
  </div>
</section>

<!-- ===== CIFRAS ===== -->
<section class="scene" id="s-cifras" data-scene="cifras" aria-labelledby="hData" hidden>
  <div class="cifras">
    <aside class="selector">
      <span class="eyebrow">Nueve pliegues, nueve realidades</span>
      <h2 id="hData">Las <b>cifras</b> del país</h2>
      <div class="chips" role="tablist" aria-label="Dimensiones de la diversidad" id="chips"></div>
    </aside>
    <div class="panel" id="panel" role="tabpanel" aria-live="polite"></div>
  </div>
</section>

<!-- ===== PLIEGO ===== -->
<section class="scene" id="s-pliego" data-scene="pliego" aria-labelledby="hAgent" hidden>
  <div class="agent">
    <div class="agent-id">
      <svg class="crane" viewBox="0 0 120 120" aria-hidden="true"><polygon points="60,18 100,62 60,52" fill="#EE7DB9"/><polygon points="60,18 60,52 20,62" fill="#6A47B8"/><polygon points="20,62 60,52 60,100" fill="#13A79A"/><polygon points="100,62 60,52 60,100" fill="#FFC20E"/><polygon points="60,100 74,86 92,104" fill="#F26A21"/></svg>
      <span class="eyebrow">Agente de datos inclusivo</span>
      <h2 id="hAgent">Conversa con <b>Pliego</b></h2>
      <p class="lede std-only">Responde con cifras verificadas y te dice de dónde salen. También te ayuda con el lenguaje: escríbele «discapacitado» o «abuelitos» y te propone una forma respetuosa. Si no tiene un dato, te lo dice.</p>
      <p class="lede easy-only">Pliego responde con datos reales. Si no sabe algo, te lo dice. También te ayuda a decir las cosas con respeto.</p>
      <div class="modes"><button class="sw" id="chatEasy" aria-pressed="false">Lectura fácil</button><button class="sw" id="chatVoice" aria-pressed="false">Leer en voz alta</button></div>
      <p class="fine">Pliego funciona con Microsoft Foundry sobre una base de datos curada por el equipo del Encuentro. No guarda datos personales. Puede equivocarse: verifica antes de citar.</p>
    </div>
    <div class="chat" aria-label="Chat con Pliego">
      <div class="chat-head"><span class="live" aria-hidden="true"></span>Pliego<span class="sp"><button id="newChat">Nuevo chat</button></span></div>
      <div class="log" id="log" role="log" aria-live="polite"></div>
      <div class="sugs" id="sugs"></div>
      <form class="ask" id="askForm" autocomplete="off"><label for="askInput" class="sr">Escribe tu pregunta para Pliego</label><input id="askInput" placeholder="Pregunta por una cifra o una palabra…" maxlength="300"><button type="submit">Enviar</button></form>
    </div>
  </div>
</section>

<!-- ===== INSCRIPCIÓN ===== -->
<section class="scene" id="s-inscripcion" data-scene="inscripcion" aria-labelledby="hReg" hidden>
  <div class="reg">
    <form class="card form" id="regForm" novalidate>
      <span class="eyebrow">Inscripción gratuita</span>
      <h2 id="hReg">Dobla tu <b>escarapela</b></h2>
      <p class="lede small">Seis preguntas. Al final, tu hoja se dobla y se convierte en tu escarapela con código y QR.</p>
      <div class="field"><label for="fName">1. Nombres y apellidos</label><input id="fName" required autocomplete="name" maxlength="120"></div>
      <div class="field"><label for="fDoc">2. Documento de identidad</label><input id="fDoc" required inputmode="numeric" maxlength="20" autocomplete="off"><span class="hint">Se guarda cifrado y solo se usa para validar tu ingreso. No aparece en la escarapela.</span></div>
      <div class="field"><label for="fOrg">3. Institución u organización</label><input id="fOrg" required autocomplete="organization" maxlength="120"></div>
      <div class="field"><label for="fMail">4. Correo electrónico</label><input id="fMail" type="email" required autocomplete="email"><span class="hint">Aquí te llega tu código y tu escarapela.</span></div>
      <fieldset><legend>5. ¿Te identificas con alguno de los siguientes grupos?</legend>
        <span class="hint">Es voluntario y puedes marcar varios. Lo usamos solo de forma agregada para medir la diversidad del Encuentro; se guarda cifrado y nunca aparece en tu escarapela.</span>
        <div class="checks" id="groupPick">
          <label><input type="checkbox" name="grp" value="Persona con discapacidad">Persona con discapacidad</label>
          <label><input type="checkbox" name="grp" value="Pueblos indígenas">Persona perteneciente a pueblos indígenas</label>
          <label><input type="checkbox" name="grp" value="Afrodescendiente, negra, raizal o palenquera">Persona afrodescendiente, negra, raizal o palenquera</label>
          <label><input type="checkbox" name="grp" value="Pueblo Rrom">Persona del pueblo Rrom</label>
          <label><input type="checkbox" name="grp" value="Migrante o refugiada">Persona migrante o refugiada</label>
          <label><input type="checkbox" name="grp" value="LGBTIQ+">Persona LGBTIQ+</label>
          <label><input type="checkbox" name="grp" value="Víctima del conflicto armado">Víctima del conflicto armado</label>
          <label><input type="checkbox" name="grp" value="Otras" id="grpOther">Otras</label>
          <label class="excl"><input type="checkbox" name="grp" value="Ninguno de los anteriores">Ninguno de los anteriores</label>
          <label class="excl"><input type="checkbox" name="grp" value="Prefiero no responder">Prefiero no responder</label>
        </div>
        <div class="field" id="grpOtherWrap" hidden><label for="fGrpOther">¿Cuál?</label><input id="fGrpOther" maxlength="60"></div>
      </fieldset>
      <fieldset><legend>6. ¿Requieres algún ajuste razonable o apoyo para participar plenamente?</legend>
        <div class="checks" role="radiogroup" aria-label="Requiere ajuste razonable"><label><input type="radio" name="ajq" value="si">Sí</label><label><input type="radio" name="ajq" value="no">No</label></div>
        <div id="ajWrap" hidden class="sub">
          <span class="hint">Cuéntanos cuál para tenerlo listo ese día:</span>
          <div class="checks">
            <label><input type="checkbox" name="aj" value="Intérprete de LSC">Intérprete de Lengua de Señas Colombiana</label>
            <label><input type="checkbox" name="aj" value="Movilidad y accesos">Movilidad y accesos</label>
            <label><input type="checkbox" name="aj" value="Subtítulos en vivo">Subtítulos en vivo</label>
            <label><input type="checkbox" name="aj" value="Material en lectura fácil">Material en lectura fácil</label>
            <label><input type="checkbox" name="aj" value="Espacio de calma sensorial">Espacio de calma sensorial</label>
            <label><input type="checkbox" name="aj" value="Acompañante">Asisto con acompañante</label>
            <label><input type="checkbox" name="aj" value="Dieta o alimentación específica">Alimentación específica</label>
          </div>
          <div class="field"><label for="fAjOther">Otro apoyo</label><input id="fAjOther" maxlength="120"></div>
        </div>
      </fieldset>
      <details class="extra"><summary>Personaliza tu escarapela (opcional)</summary>
        <div class="field"><label for="fPron">¿Cómo quieres que te nombremos?</label><input id="fPron" placeholder="Ej.: ella · él · elle" maxlength="40"></div>
        <fieldset><legend>Tu pliegue: ¿con qué dimensión te comprometes?</legend><div class="colors" id="colorPick" role="radiogroup" aria-label="Pliegue"></div></fieldset>
        <div class="field"><label for="fPledge">Mi compromiso para 2027</label><textarea id="fPledge" maxlength="140" placeholder="Ej.: Publicaremos todas nuestras vacantes en formatos accesibles."></textarea></div>
        <div class="field"><label for="fPhoto">Tu foto</label><input id="fPhoto" type="file" accept="image/*"><span class="hint">Solo se usa para dibujar tu escarapela en tu dispositivo; no la subimos.</span>
          <div class="crop" id="cropBox" hidden><canvas id="cropCv" width="240" height="240" aria-label="Encuadre de tu foto: arrastra para mover"></canvas><div class="crop-tools"><label for="cropZoom">Acercar</label><input type="range" id="cropZoom" min="1" max="3" step="0.02" value="1"><button type="button" class="mini" id="cropReset">Centrar</button><span class="hint">Arrastra la foto para encuadrar tu rostro.</span></div></div></div>
      </details>
      <label class="consent"><input type="checkbox" id="fConsent"><span>Autorizo el tratamiento de mis datos para gestionar mi inscripción y los ajustes razonables, según la <a href="https://startin.org.co/privacidad/" target="_blank" rel="noopener">política de tratamiento de datos</a> (Ley 1581 de 2012).</span></label>
      <p class="err" id="regErr" role="alert" hidden></p>
      <button class="btn btn-primary big" type="submit" id="regBtn">Doblar mi escarapela</button>
    </form>
    <div class="studio" aria-live="polite">
      <ol class="steps" id="steps"><li>Hoja</li><li>Marcar</li><li>Doblar</li><li>Sostener</li><li>Escarapela</li></ol>
      <div class="fold-stage" id="foldStage"><div class="sheet" id="sheet"><div class="base"></div><div class="flap f1"></div><div class="flap f2"></div><div class="flap f3"></div><div class="flap f4"></div><div class="seal" id="seal">5°</div></div></div>
      <p class="studio-msg" id="studioMsg">Tu hoja está lista. Responde las seis preguntas y la doblamos juntos.</p>
      <div class="badge-out" id="badgeOut" hidden>
        <img id="badgeImg" alt="">
        <div class="badge-actions"><a class="btn btn-primary" id="dlBtn" download="escarapela.png" href="#">Descargar PNG</a><a class="btn btn-ghost" id="waBtn" href="#" target="_blank" rel="noopener">Compartir por WhatsApp</a></div>
        <p class="fine" id="mailNote"></p>
      </div>
    </div>
  </div>
</section>

<!-- ===== EVENTO ===== -->
<section class="scene scroll" id="s-evento" data-scene="evento" aria-labelledby="hEv" hidden>
  <div class="evento">
    <div class="ev-col">
      <span class="eyebrow">Miércoles 11 de noviembre · 8:00 a.m. – 1:00 p.m.</span>
      <h2 id="hEv">La <b>ruta</b> de la mañana</h2>
      <p class="note">Agenda propuesta, por confirmar con el comité organizador.</p>
      <ol class="agenda">
        <li><time>8:00</time><div><b>Registro y bienvenida</b><span>Entrega de escarapelas y activación de ajustes razonables.</span></div></li>
        <li><time>8:30</time><div><b>Apertura · Doblar no es romper</b><span>Universidad El Bosque y DiversidadES.</span></div></li>
        <li><time>8:50</time><div><b>Marcar</b><span>Lo que dicen los datos y lo que exige la Ley 2466 de 2025.</span></div></li>
        <li><time>9:35</time><div><b>Doblar</b><span>Rediseñar procesos de talento sin dejar a nadie atrás.</span></div></li>
        <li><time>10:20</time><div><b>Sostener</b><span>Cultura, liderazgo y medición para que la inclusión dure.</span></div></li>
        <li><time>11:00</time><div><b>Taller de pliegues</b><span>Mesas de cocreación: cada reto sale con responsable, fecha e indicador.</span></div></li>
        <li><time>12:20</time><div><b>Muro de compromisos</b><span>Cada organización deja su pliegue público.</span></div></li>
        <li><time>12:45</time><div><b>Cierre y foto de comunidad</b><span>Aliados del 5° Encuentro.</span></div></li>
      </ol>
      <h3>Muro de compromisos</h3>
      <form class="wall-form" id="wallForm"><label for="wallIn" class="sr">Tu compromiso</label><input id="wallIn" maxlength="140" placeholder="Mi organización se compromete a…"><input id="wallOrg" maxlength="60" placeholder="Organización"><button class="btn btn-ghost" type="submit">Pegar</button></form>
      <div class="wall" id="wall"></div>
    </div>
    <div class="ev-col">
      <span class="eyebrow">Memoria</span>
      <h2>Cinco ediciones, <b>una comunidad</b></h2>
      <div class="timeline">
        <article class="ed pending"><span class="num">1ª · 2ª · 3ª</span><span class="st">Por documentar</span><p>El comité organizador completará fechas, sedes y temas de las primeras ediciones.</p></article>
        <article class="ed known"><span class="num">4ª</span><span class="st">3 dic 2025 · Auditorio Fundadores, UEB</span><h4>De los desafíos a las soluciones</h4><p>Tres conferencias sobre el marco normativo y mesas de cocreación. Más de 70 participantes: expertos en discapacidad, bienestar, liderazgos LGBTIQ+, academia, talento humano, estudiantes y juristas.</p></article>
        <article class="ed now"><span class="num">5ª</span><span class="st">11 nov 2026 · HUB iEX, UEB</span><h4>Gestión del cambio organizacional para la Inclusión y Equidad</h4><p>Del diagnóstico a la transformación: marcar, doblar y sostener.</p></article>
      </div>
      <h3>Hacen posible el Encuentro</h3>
      <ul class="allies-logos" role="list"><li><img src="assets/aliado-uelbosque.webp?v=t" alt="Universidad El Bosque" loading="lazy" decoding="async"></li><li><img src="assets/aliado-diversidades.webp?v=s" alt="DiversidadES" loading="lazy" decoding="async"></li><li><img src="assets/aliado-centro-diversidad.webp?v=s" alt="Centro de Diversidad, Equidad e Inclusión" loading="lazy" decoding="async"></li><li><img src="assets/aliado-claro.webp?v=s" alt="Claro" loading="lazy" decoding="async"></li><li><img src="assets/aliado-umanos.webp?v=s" alt="Umanos Lab" loading="lazy" decoding="async"></li><li class="wide"><img src="assets/aliado-bancamia.webp?v=s" alt="Bancamía y Fundación BBVA Microfinanzas" loading="lazy" decoding="async"></li><li><img src="assets/aliado-equilatera.webp?v=s" alt="Equilatera, Inclusión y Sostenibilidad" loading="lazy" decoding="async"></li><li><img src="assets/aliado-startin.webp?v=s" alt="Startin" loading="lazy" decoding="async"></li><li><img src="assets/aliado-externado.webp?v=s" alt="Universidad Externado de Colombia, Facultad de Administración de Empresas" loading="lazy" decoding="async"></li><li><img src="assets/aliado-cirec.webp?v=s" alt="Fundación CIREC" loading="lazy" decoding="async"></li><li><img src="assets/aliado-camara.webp?v=s" alt="Cámara de la Diversidad" loading="lazy" decoding="async"></li><li><img src="assets/aliado-pride.webp?v=s" alt="Pride Connection Colombia" loading="lazy" decoding="async"></li><li><img src="assets/aliado-ecr.webp?v=s" alt="Escuela Colombiana de Rehabilitación" loading="lazy" decoding="async"></li><li><img src="assets/aliado-101ideas.webp?v=s" alt="101ideas.co, El orgullo de la edad" loading="lazy" decoding="async"></li><li><img src="assets/aliado-nuestroflow.webp?v=s" alt="Nuestro Flow, Transformación social" loading="lazy" decoding="async"></li><li><img src="assets/aliado-evasadanes.webp?v=s" alt="Asociación Evas &amp; Adanes" loading="lazy" decoding="async"></li></ul>
      <h3>Fuentes de las cifras</h3>
      <ol class="sources">
        <li>DANE, GEIH, población LGBT, jul 2025–jun 2026. <a href="https://www.dane.gov.co/files/operaciones/GEIH/bol-GEIHMLLGBT-jul2025-jun2026.pdf" target="_blank" rel="noopener">Boletín</a></li>
        <li>DANE, GEIH jul–sep 2025, personas con discapacidad, vía <a href="https://radionacional.co/actualidad/salud/dia-internacional-de-las-personas-con-discapacidad-2025-cifras-en-colombia-y-el" target="_blank" rel="noopener">Radio Nacional</a>.</li>
        <li>LEE U. Javeriana (SIMAT 2023, ENCV 2024), vía <a href="https://www.portafolio.co/economia/colombia-tiene-200-334-estudiantes-con-discapacidad-pero-solo-el-2-llega-al-sistema-educativo-formal-495503" target="_blank" rel="noopener">Portafolio</a>.</li>
        <li>Ley 2466 de 2025, vía <a href="https://www.infobae.com/colombia/2026/03/13/por-que-las-empresas-colombianas-deberan-contratar-al-menos-dos-personas-en-situacion-de-discapacidad-por-cada-cien-empleados/" target="_blank" rel="noopener">Infobae</a>.</li>
        <li>Fundación Saldarriaga Concha, <a href="https://www.saldarriagaconcha.org/wp-content/uploads/2025/02/2025-02-Febrero18_ColombiaEnvejece-LasOportunidadesDeUnaSociedadLongeva.pdf" target="_blank" rel="noopener">Colombia envejece</a>; DANE vía <a href="https://www.rtvcnoticias.com/colombia/dane-proyecta-que-en-2036-habra-mas-adultos-mayores-que-ninos-en-colombia" target="_blank" rel="noopener">RTVC</a>.</li>
        <li>LaboUR, U. del Rosario, vía <a href="https://www.valoraanalitik.com/alertan-brechas-laborales-genero-colombia/" target="_blank" rel="noopener">Valora Analitik</a>.</li>
        <li>DANE, CNPV 2018 y estimación NARP 2019, vía <a href="https://www.portafolio.co/economia/poblacion-afro-4-6-millones-535344" target="_blank" rel="noopener">Portafolio</a>; pueblo Rrom vía <a href="https://www.publimetro.co/noticias/2025/11/05/dia-internacional-de-la-lengua-gitana-asi-vive-la-poblacion-rrom-en-colombia/" target="_blank" rel="noopener">Publimetro</a>.</li>
        <li>Migración Colombia vía <a href="https://www.portafolio.co/economia/gobierno/regreso-o-permanencia-el-futuro-incierto-de-los-migrantes-venezolanos-que-viven-hoy-dia-en-colombia-485699" target="_blank" rel="noopener">Portafolio</a> (ene 2026).</li>
        <li>Unidad para las Víctimas, <a href="https://datospaz.unidadvictimas.gov.co/wp-content/uploads/2026/04/Conmemoracion_9ABRIL.pdf" target="_blank" rel="noopener">boletín 9 de abril de 2026</a>.</li>
        <li>McKinsey, Diversity Matters Even More (2023), vía <a href="https://internationalwim.org/iwim-reports/diversity-matters-even-more-the-case-for-holistic-impact/" target="_blank" rel="noopener">IWIM</a>.</li>
        <li>4° Encuentro: <a href="https://www.lasillavacia.com/red-de-expertos/de-los-desafios-a-las-soluciones-encuentro-de-diversidades/" target="_blank" rel="noopener">La Silla Vacía</a> y <a href="https://confidencialnoticias.com/opinion/liderazgos-que-construyen-futuro-en-el-4-o-encuentro-de-diversidades/" target="_blank" rel="noopener">Confidencial Noticias</a>.</li>
      </ol>
      <p class="credit">5° Encuentro Diversidades e Inclusión · Bogotá, 2026 · Portal by <a href="https://startinlab.org" target="_blank" rel="noopener">Startin<span class="ast">*</span>Lab</a></p>
    </div>
  </div>
</section>
</main>

<nav class="dock" aria-label="Secciones (móvil)">
  <a href="#inicio" data-go="inicio"><i aria-hidden="true">◇</i>Inicio</a>
  <a href="#pliegue" data-go="pliegue"><i aria-hidden="true">▱</i>Pliegue</a>
  <a href="#cifras" data-go="cifras"><i aria-hidden="true">◈</i>Cifras</a>
  <a href="#pliego" data-go="pliego"><i aria-hidden="true">◆</i>Pliego</a>
  <a href="#inscripcion" data-go="inscripcion" class="cta"><i aria-hidden="true">✦</i>Inscríbete</a>
  <a href="#evento" data-go="evento"><i aria-hidden="true">◇</i>Evento</a>
</nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>
<script src="app.js?v=<?= $v ?>"></script>
<a class="startin-credit" href="https://startinlab.org/" target="_blank" rel="noopener" aria-label="Hecho por Startin Lab">by <b>Startin*Lab</b></a>
</body>
</html>
