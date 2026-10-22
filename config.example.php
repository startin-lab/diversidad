<?php
// Configuración del portal diversidad.startinlab.org
// Este archivo NUNCA debe ser público: está fuera del alcance de la web por .htaccess.

return [
  // Base de datos MySQL (Hostinger)
  'db' => [
    'host' => 'localhost',
    'name' => 'u274113549_diversidad',
    'user' => 'u274113549_diversidad',
    'pass' => '__DB_PASS__',
  ],
  // Clave de cifrado (AES-256-GCM) para documento y grupo poblacional. 64 hex = 32 bytes.
  'enc_key_hex' => '__ENC_KEY__',
  // Panel privado
  'admin_pass_hash' => '__ADMIN_HASH__',
  'puerta_pass_hash' => '__PUERTA_HASH__',
  'session_name' => 'dvs_admin',
  // Correo
  'mail_from' => 'no-reply@diversidad.startinlab.org',
  'mail_from_name' => '5° Encuentro Diversidades e Inclusión',
  'mail_reply_to' => 'hola@startin.org.co',
  'mail_bcc' => 'portales@startin.org.co',
  // Agente Pliego (Microsoft Foundry / Azure OpenAI)
  'ai' => [
    'endpoint' => '__AI_ENDPOINT__',   // p. ej. https://xxxx.openai.azure.com
    'deployment' => '__AI_DEPLOYMENT__',
    'api_key' => '__AI_KEY__',
    'api_version' => '2024-10-21',
    'daily_limit' => 1500,   // mensajes por día (todo el portal)
    'ip_limit' => 40,        // mensajes por IP por día
  ],
  'site_url' => 'https://diversidad.startinlab.org',
];
