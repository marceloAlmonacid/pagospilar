# Contexto del Proyecto "Pagos Pilar" para Cascade (IA)

## Estado Actual
- **Frontend/Backend:** PHP, JS (jQuery, AJAX), Bootstrap, SweetAlert2, Chart.js.
- **Base de Datos:** MySQL / MariaDB (contraseña root: `IT2017petro`, base de datos: `impuestos`). El archivo `mysql-dump/init.sql` contiene el volcado inicial.
- **Infraestructura (Producción):** Preparado para correr detrás de un **Nginx Proxy Manager**. Los puertos no están expuestos a internet por seguridad; el tráfico se enruta a través de la red de docker llamada `red-proxy`.
- **Integración y Despliegue Continuo (CI/CD):** Configurado con GitHub Actions. Cualquier push a `master` se despliega automáticamente en el VPS.

## Últimas integraciones realizadas:
1. **Telegram Bot:** Implementado en `ajax/notificar.php`. Funciona correctamente para notificar deudas masivamente.
2. **WhatsApp Bot (Microservicio):** Se creó un contenedor de Node.js en `/whatsapp-bot` usando `whatsapp-web.js` y `puppeteer` (con Chromium). Expone un endpoint en el puerto `3000` (`/send`).
3. **Gráficos de Historial:** Ajustado en `historialImpuestoUsuario.php` para Chart.js.
4. **ABM de Impuestos (Administrar Gastos):** Modal para listar, editar y eliminar todas las facturas cargadas globalmente.

## Instrucciones para levantar desde Cero (En un Nuevo Servidor/PC):

**Paso 1: Crear la red y el Nginx Proxy Manager (Requisito previo)**
Como la app está blindada, primero debemos levantar el proxy que gestiona las conexiones.
```bash
docker network create red-proxy
mkdir -p ~/nginx-proxy && cd ~/nginx-proxy
cat << 'YML' > docker-compose.yml
version: '3.8'
services:
  app:
    image: 'jc21/nginx-proxy-manager:latest'
    restart: unless-stopped
    ports:
      - '80:80'
      - '81:81'
      - '443:443'
    volumes:
      - ./data:/data
      - ./letsencrypt:/etc/letsencrypt
    networks:
      - red-proxy
networks:
  red-proxy:
    external: true
YML
docker compose up -d
```

**Paso 2: Levantar Pagos Pilar**
```bash
git clone https://github.com/marceloAlmonacid/pagospilar.git
cd pagospilar
docker compose up -d --build
```
- La base de datos se restaurará automáticamente desde `mysql-dump/init.sql`.
- El bot de WhatsApp generará la sesión. Ejecuta `docker logs -f whatsapp_bot_imp` para escanear el QR.

**Paso 3: Enrutar desde el panel web**
- Entrar a `http://IP_DEL_SERVIDOR:81`
- Crear credenciales nuevas de Admin.
- En *Proxy Hosts*, crear un registro apuntando tu dominio hacia el contenedor `php_apache_imp` (puerto 80).
