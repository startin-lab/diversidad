# Portal 5° Encuentro Diversidades e Inclusión

**diversidad.startinlab.org** — *Gestión del cambio organizacional para la Inclusión y Equidad*. 11 de noviembre de 2026 · Universidad El Bosque, HUB iEX · Bogotá.

Portal construido por Fundación Startin. Hospedado en Hostinger (PHP 8 + MySQL). El agente **Pliego** corre sobre Microsoft Foundry (proyecto `diversidad-pliego`, implementación `pliego`).

## Estructura
- `index.php`, `app.css`, `app.js`: experiencia por escenas (Inicio · El pliegue · Cifras · Pliego · Inscripción · Evento), sin scroll largo, con panel de accesibilidad (tamaño de texto, alto contraste, lectura fácil, pausar movimiento, voz).
- `api/registro.php`: guarda inscripciones (documento y grupos poblacionales cifrados con AES-256-GCM), genera código `5E-XXXXXX`.
- `api/correo.php`: envía el correo de confirmación con la escarapela adjunta.
- `api/agente.php`: proxy del agente Pliego hacia Foundry (la clave vive solo en el servidor); límites diarios y por IP.
- `api/conocimiento.php`: base de conocimiento verificada (cifras con fuente y fecha) que recibe Pliego.
- `api/muro.php`: muro de compromisos.
- `admin/index.php`: panel privado (inscripciones, ajustes razonables, agregados de autorreconocimiento, exportar CSV, marcar asistencia).
- `verificar.php` (`/v/CODIGO`): verificación pública del QR de la escarapela.
- `ingreso/index.php` + `api/ingreso.php`: mesa de entrada del evento (clave corta para el personal): escanea el QR con la cámara, o valida por código o cédula, y marca la asistencia; avisa si la persona ya ingresó y muestra sus ajustes razonables.
- `assets/fig/`: las nueve figuras de papel (ilustraciones generadas para el evento).

## Despliegue
1. Copiar `config.example.php` a `config.php` y completar credenciales (BD, clave de cifrado, hash de contraseña del panel, endpoint/clave de Foundry).
2. Subir todo a `public_html` del sitio en Hostinger. Las tablas se crean solas en la primera inscripción.
3. `.htaccess` fuerza HTTPS, protege `config.php`, `lib.php` y `conocimiento.php`, y enruta `/v/CODIGO`.

## Datos personales
Las inscripciones incluyen datos sensibles (Ley 1581 de 2012). El documento y la pregunta de autorreconocimiento se guardan cifrados; el panel solo muestra el autorreconocimiento de forma agregada. Conservar `config.php` fuera del repositorio.
