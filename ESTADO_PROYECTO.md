# Contexto del Proyecto "Pagos Pilar" para Cascade (IA)

## Estado Actual
- **Frontend/Backend:** PHP, JS (jQuery, AJAX), Bootstrap, SweetAlert2, Chart.js.
- **Base de Datos:** MySQL / MariaDB (contraseña root: `IT2017petro`, base de datos: `impuestos`). El archivo `mysql-dump/init.sql` contiene el volcado inicial.
- **Infraestructura:** Todo corre sobre Docker (`docker-compose.yml` con servicios `web`, `db`, `phpmyadmin` y `whatsapp`).

## Últimas integraciones realizadas:
1. **Telegram Bot:** Implementado en `ajax/notificar.php`. Funciona correctamente para notificar deudas masivamente.
2. **WhatsApp Bot (Microservicio):** Se creó un contenedor de Node.js en `/whatsapp-bot` usando `whatsapp-web.js` y `puppeteer` (con Chromium). Expone un endpoint en el puerto `3000` (`/send`).
3. **Gráficos de Historial:** Se ajustó el endpoint `historialImpuestoUsuario.php` para que filtre por año y retorne los 12 meses (incluso los nulos), para que Chart.js dibuje la línea correctamente sin unir meses saltados.
4. **ABM de Impuestos (Administrar Gastos):** Se agregó un modal para listar, editar y eliminar todas las facturas cargadas globalmente. También se inyectaron las funciones globales en JS para editar y eliminar desde el panel principal.

## Instrucciones para levantar en un nuevo equipo:
1. Instalar Docker y Docker Compose.
2. Abrir la terminal en la raíz del proyecto y ejecutar: `docker compose up -d --build`.
   - La base de datos se restaurará automáticamente desde `mysql-dump/init.sql` si el volumen es nuevo.
3. Revisar los logs del bot de WhatsApp (`docker logs -f whatsapp_bot_imp`) para escanear el código QR si la sesión caducó.
