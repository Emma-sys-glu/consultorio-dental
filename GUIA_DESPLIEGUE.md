# Guía de Despliegue — Sistema DentalTec
### Receta de cocina para levantar el proyecto desde cero

---

## Índice

1. [Requisitos Previos](#1-requisitos-previos)
2. [Clonar el Proyecto](#2-clonar-el-proyecto)
3. [Variables de Entorno (.env)](#3-variables-de-entorno-env)
4. [Levantar Infraestructura con Docker](#4-levantar-infraestructura-con-docker)
5. [Instalar y Configurar Laravel](#5-instalar-y-configurar-laravel)
6. [Migraciones y Datos de Prueba](#6-migraciones-y-datos-de-prueba)
7. [Configuración para Producción](#7-configuración-para-producción-opcional)
8. [Verificar que Todo Funciona](#8-verificar-que-todo-funciona)
9. [Credenciales de Acceso por Defecto](#9-credenciales-de-acceso-por-defecto)
10. [Comandos de Operación Diaria](#10-comandos-de-operación-diaria)
11. [Solución de Problemas Frecuentes](#11-solución-de-problemas-frecuentes)

---

## 1. Requisitos Previos

Instala los siguientes programas en el orden indicado antes de continuar.

### 1.1 Docker y Docker Compose

```bash
# Ubuntu / Debian
sudo apt update
sudo apt install -y docker.io docker-compose-plugin

# Agregar tu usuario al grupo docker (evita usar sudo en cada comando)
sudo usermod -aG docker $USER
newgrp docker

# Verificar instalación
docker --version          # Docker version 24.x o superior
docker compose version    # Docker Compose version v2.x o superior
```

### 1.2 PHP 8.3 o superior

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.4 php8.4-cli php8.4-fpm \
    php8.4-pgsql php8.4-mbstring php8.4-xml php8.4-curl \
    php8.4-zip php8.4-bcmath php8.4-gd php8.4-tokenizer \
    php8.4-ctype php8.4-fileinfo php8.4-sqlite3

# Verificar
php --version   # PHP 8.4.x
```

> **Extensiones obligatorias:** `pdo_pgsql`, `pgsql`, `mbstring`, `xml`, `curl`,
> `zip`, `bcmath`, `gd`, `openssl`, `tokenizer`, `ctype`, `json`, `fileinfo`, `sqlite3`

### 1.3 Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verificar
composer --version   # Composer version 2.7.x
```

### 1.4 Git

```bash
sudo apt install -y git
git --version   # git version 2.x
```

### 1.5 PostgreSQL Client (opcional, para diagnóstico)

```bash
sudo apt install -y postgresql-client
psql --version   # psql 16.x
```

### 1.6 Node.js (solo si modificas frontend)

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
node --version   # v18.x
```

### Resumen de versiones mínimas requeridas

| Programa | Versión mínima |
|---|---|
| PHP | 8.3 |
| Composer | 2.0 |
| Docker | 24.0 |
| Docker Compose | 2.0 (plugin) |
| PostgreSQL client | 16.0 (opcional) |

---

## 2. Clonar el Proyecto

```bash
# Clona el repositorio en la carpeta deseada
git clone <URL_DEL_REPOSITORIO> consultorio-dental
cd consultorio-dental
```

---

## 3. Variables de Entorno (.env)

Copia el archivo de ejemplo y edítalo:

```bash
cp .env.example .env
```

Luego abre `.env` y establece exactamente estos valores:

```env
# ── Aplicación ────────────────────────────────────────────────────────────────
APP_NAME=DentalTec
APP_ENV=local
APP_KEY=                          # Se genera en el paso 5.2
APP_DEBUG=true
APP_URL=http://localhost:8000     # Cambiar a tu dominio real en producción

# ── Base de datos ─────────────────────────────────────────────────────────────
# Conexión principal (puerto 5444 del contenedor Docker)
DB_CONNECTION=pgsql_principal

DB_PRIMARY_HOST=127.0.0.1
DB_PRIMARY_PORT=5444

DB_BACKUP_HOST=127.0.0.1
DB_BACKUP_PORT=5443

DB_DATABASE=consultorio_dental
DB_USERNAME=admin
DB_PASSWORD=admin123

# ── Caché, Sesión y Colas ────────────────────────────────────────────────────
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database

# ── Correo (puede dejarse como array para desarrollo) ─────────────────────────
MAIL_MAILER=array

# ── PWA / Notificaciones Push (VAPID) ────────────────────────────────────────
# Genera claves nuevas con: php artisan tinker
# >>> \Minishlink\WebPush\VAPID::createVapidKeys()
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
VAPID_SUBJECT=mailto:tu_email@dominio.com
```

> **Nota importante sobre las claves VAPID:** Cada instalación debe generar sus
> propias claves. Ver el paso 5.5.

---

## 4. Levantar Infraestructura con Docker

El archivo `docker-compose-replica.yml` levanta **dos contenedores PostgreSQL** configurados en modo maestro-réplica para alta disponibilidad.

### 4.1 Levantar los contenedores

```bash
# Desde la raíz del proyecto
sudo docker compose -f docker-compose-replica.yml up -d
```

Esto crea:

| Contenedor | Rol | Puerto local | Puerto interno |
|---|---|---|---|
| `postgres_ha_principal` | Maestro (lecturas y escrituras) | `5444` | `5432` |
| `postgres_ha_replica` | Réplica (solo lecturas) | `5443` | `5432` |

### 4.2 Verificar que los contenedores están sanos

```bash
sudo docker compose -f docker-compose-replica.yml ps
```

Espera hasta que ambos muestren `healthy` en la columna STATUS (puede tomar 30-60 segundos):

```
NAMES                   STATUS                    PORTS
postgres_ha_replica     Up X minutes (healthy)    0.0.0.0:5443->5432/tcp
postgres_ha_principal   Up X minutes (healthy)    0.0.0.0:5444->5432/tcp
```

### 4.3 Probar la conexión directa a la base de datos

```bash
# Probar el principal
pg_isready -h 127.0.0.1 -p 5444 -U admin
# Resultado esperado: 127.0.0.1:5444 - accepting connections

# Probar la réplica
pg_isready -h 127.0.0.1 -p 5443 -U admin
# Resultado esperado: 127.0.0.1:5443 - accepting connections
```

### 4.4 Credenciales de la base de datos (Docker)

| Parámetro | Valor |
|---|---|
| Host principal | `127.0.0.1:5444` |
| Host réplica | `127.0.0.1:5443` |
| Base de datos | `consultorio_dental` |
| Usuario | `admin` |
| Contraseña | `admin123` |
| Usuario replicación | `replicador` |
| Contraseña replicación | `replica123` |

---

## 5. Instalar y Configurar Laravel

Ejecuta los siguientes comandos **en orden**. Cada uno debe completarse sin errores antes de continuar.

### 5.1 Instalar dependencias PHP

```bash
composer install
```

> Si estás en producción usa: `composer install --optimize-autoloader --no-dev`

### 5.2 Generar la clave de la aplicación

```bash
php artisan key:generate
```

Esto escribe `APP_KEY=base64:...` automáticamente en tu `.env`.

### 5.3 Limpiar caché inicial

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 5.4 Verificar la conexión a la base de datos

```bash
php artisan db:show
```

Debe mostrar las tablas disponibles y confirmar conexión a PostgreSQL sin errores.

### 5.5 Generar claves VAPID para notificaciones push

```bash
php artisan tinker --execute="\$k = \Minishlink\WebPush\VAPID::createVapidKeys(); echo 'VAPID_PUBLIC_KEY=' . \$k['publicKey'] . PHP_EOL; echo 'VAPID_PRIVATE_KEY=' . \$k['privateKey'] . PHP_EOL;"
```

Copia las dos líneas que imprime y pégalas en tu `.env` reemplazando los valores vacíos de `VAPID_PUBLIC_KEY` y `VAPID_PRIVATE_KEY`.

---

## 6. Migraciones y Datos de Prueba

### 6.1 Ejecutar las migraciones

Crea todas las tablas en la base de datos:

```bash
php artisan migrate
```

Debe mostrar una lista de migraciones aplicadas sin errores. Tablas que se crean:

- `users`, `cache`, `jobs`, `job_batches`, `failed_jobs`
- `pacientes`, `dentistas`, `citas`
- `personal_access_tokens`
- `expedientes`, `expediente_documentos`
- `tratamientos`, `recetas`, `inventarios`
- `notificaciones`, `push_subscriptions`

### 6.2 Sembrar los datos de prueba

Carga ~3,000 registros de datos realistas para probar el sistema:

```bash
php artisan db:seed
```

El proceso corre en este orden (respetando dependencias de claves foráneas):

```
1. PacienteSeeder     →  200 pacientes con datos clínicos reales
2. DentistaSeeder     →   50 dentistas con especialidades y horarios
3. UserSeeder         →  254 usuarios (1 admin + 3 recepcionistas + 50 dentistas + 200 pacientes)
4. CitaSeeder         → 1000 citas distribuidas entre pacientes y dentistas
5. ExpedienteSeeder   →  200 expedientes (uno por paciente)
6. TratamientoSeeder  → 2000 tratamientos con estados variados
7. RecetaSeeder       → 1500 recetas médicas
8. InventarioSeeder   →   33 productos dentales reales en 6 categorías
9. NotificacionSeeder →  500 notificaciones de distintos tipos
```

> El seeding completo tarda aproximadamente **2-4 minutos** dependiendo del hardware.

### 6.3 Opción rápida: migrar y sembrar en un solo comando

```bash
php artisan migrate:fresh --seed
```

> **Advertencia:** `migrate:fresh` **borra todas las tablas** antes de recrearlas.
> Úsalo solo en desarrollo, nunca en producción con datos reales.

---

## 7. Configuración para Producción (opcional)

Si vas a desplegar en un servidor con dominio real, sigue estos pasos adicionales.

### 7.1 Actualizar el .env para producción

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

### 7.2 Instalar y configurar Apache

```bash
sudo apt install -y apache2 libapache2-mod-php8.4
sudo a2enmod rewrite
sudo a2enmod ssl

# Crear el VirtualHost
sudo nano /etc/apache2/sites-available/dentaltec.conf
```

Contenido del VirtualHost:

```apache
<VirtualHost *:80>
    ServerName tu-dominio.com
    DocumentRoot /var/www/consultorio-dental/public

    <Directory /var/www/consultorio-dental/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dentaltec_error.log
    CustomLog ${APACHE_LOG_DIR}/dentaltec_access.log combined
</VirtualHost>
```

```bash
sudo a2ensite dentaltec.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

### 7.3 Instalar SSL con Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d tu-dominio.com
```

Certbot crea automáticamente el VirtualHost HTTPS y configura la renovación automática.

### 7.4 Permisos de carpetas

```bash
sudo chown -R www-data:www-data /var/www/consultorio-dental
sudo chmod -R 755 /var/www/consultorio-dental
sudo chmod -R 775 /var/www/consultorio-dental/storage
sudo chmod -R 775 /var/www/consultorio-dental/bootstrap/cache
```

### 7.5 Optimizar Laravel para producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7.6 Configurar el Scheduler (tareas automáticas)

Agrega esta línea al crontab del servidor para que los jobs diarios se ejecuten:

```bash
crontab -e
```

```cron
* * * * * cd /var/www/consultorio-dental && php artisan schedule:run >> /dev/null 2>&1
```

Esto ejecuta cada minuto el scheduler de Laravel, que a su vez corre:
- `08:00` — `RecordatorioCitasJob` (recordatorios de citas del día siguiente)
- `08:05` — `RevisarInventarioJob` (alertas de stock bajo y caducidad)

### 7.7 Watchdog de Alta Disponibilidad (opcional)

Si quieres que el sistema promueva automáticamente la réplica cuando el principal falla:

```bash
# Crear el script watchdog
sudo tee /usr/local/bin/db-watchdog.sh > /dev/null << 'EOF'
#!/bin/bash
FAILURES=0
while true; do
    if ! docker exec postgres_ha_principal pg_isready -h 127.0.0.1 -U admin > /dev/null 2>&1; then
        FAILURES=$((FAILURES + 1))
        if [ $FAILURES -ge 3 ]; then
            docker exec postgres_ha_replica \
                /opt/bitnami/postgresql/bin/pg_ctl promote \
                -D /bitnami/postgresql/data
            FAILURES=0
        fi
    else
        FAILURES=0
    fi
    sleep 5
done
EOF
sudo chmod +x /usr/local/bin/db-watchdog.sh

# Crear el servicio systemd
sudo tee /etc/systemd/system/db-watchdog.service > /dev/null << 'EOF'
[Unit]
Description=Database HA Watchdog
After=docker.service
Requires=docker.service

[Service]
ExecStart=/usr/local/bin/db-watchdog.sh
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable db-watchdog
sudo systemctl start db-watchdog
```

---

## 8. Verificar que Todo Funciona

Ejecuta esta lista de verificaciones en orden:

```bash
# 1. Contenedores de base de datos
sudo docker compose -f docker-compose-replica.yml ps
# Esperado: ambos en estado "healthy"

# 2. Conexión desde Laravel
php artisan db:show
# Esperado: lista de tablas sin errores

# 3. Todos los tests automatizados (69 tests)
php artisan test
# Esperado: OK (69 tests, 132 assertions)

# 4. Levantar el servidor de desarrollo
php artisan serve
# Abre en el navegador: http://localhost:8000
# Inicia sesión con admin@dentaltec.com / admin123

# 5. Verificar tareas programadas
php artisan schedule:list
# Esperado: 2 jobs listados con su próxima ejecución

# 6. Verificar push notifications (si VAPID está configurado)
php artisan push:test
# Esperado: "Suscripciones push en BD: X"
```

---

## 9. Credenciales de Acceso por Defecto

Todas estas cuentas se crean automáticamente al ejecutar `php artisan db:seed`.

### Administrador

| Campo | Valor |
|---|---|
| Email | `admin@dentaltec.com` |
| Contraseña | `admin123` |
| Rol | `administrador` |
| Dashboard | `/home` (estadísticas globales + calendario) |
| Acceso | Todo el sistema: pacientes, dentistas, citas, inventario, recetas, expedientes |

### Recepcionistas

| Email | Contraseña | Nombre |
|---|---|---|
| `recepcion1@dentaltec.com` | `recepcion123` | María García |
| `recepcion2@dentaltec.com` | `recepcion123` | Ana López |
| `recepcion3@dentaltec.com` | `recepcion123` | Laura Martínez |

Dashboard: `/home` · Acceso: Citas, Pacientes, Inventario

### Dentistas (50 cuentas)

| Patrón de email | Contraseña | Ejemplo |
|---|---|---|
| `dentista_{id}@dentaltec.com` | `dentista123` | `dentista_1@dentaltec.com` |

donde `{id}` es el ID del registro en la tabla `dentistas` (del 1 al 50).

Dashboard: `/home-dentista` · Acceso: Sus Citas, Expedientes, Tratamientos, Recetas

Para ver todos los dentistas y sus emails exactos:

```bash
php artisan tinker --execute="App\Models\User::where('rol','dentista')->select('email','name')->get()->each(fn(\$u)=>print(\$u->email.' | '.\$u->name.PHP_EOL));"
```

### Pacientes (200 cuentas)

| Patrón de email | Contraseña | Ejemplo |
|---|---|---|
| `paciente_{id}@dentaltec.com` | `paciente123` | `paciente_1@dentaltec.com` |

donde `{id}` es el ID del registro en la tabla `pacientes` (del 1 al 200).

Dashboard: `/home-paciente` · Acceso: Sus Citas, Sus Recetas, Sus Notificaciones

### Resumen rápido para primera prueba

```
┌────────────────────────────────────────────────────────┐
│  ROL            EMAIL                   CONTRASEÑA      │
├────────────────────────────────────────────────────────┤
│  Administrador  admin@dentaltec.com     admin123        │
│  Recepcionista  recepcion1@dentaltec.com recepcion123   │
│  Dentista       dentista_1@dentaltec.com dentista123    │
│  Paciente       paciente_1@dentaltec.com paciente123    │
└────────────────────────────────────────────────────────┘
```

---

## 10. Comandos de Operación Diaria

### Base de datos

```bash
# Ver estado de los contenedores
sudo docker compose -f docker-compose-replica.yml ps

# Detener la base de datos
sudo docker compose -f docker-compose-replica.yml down

# Levantar la base de datos
sudo docker compose -f docker-compose-replica.yml up -d

# Ver logs del contenedor principal
sudo docker logs postgres_ha_principal --tail=50

# Verificar replicación activa
sudo docker exec postgres_ha_principal psql -U admin -c "SELECT * FROM pg_stat_replication;"
```

### Laravel

```bash
# Limpiar toda la caché (usar cuando se cambian archivos de config)
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear

# Reconstruir caché (usar en producción)
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Ver todas las rutas registradas
php artisan route:list

# Ejecutar los tests
php artisan test --testdox

# Disparar los jobs manualmente (sin esperar el cron)
php artisan queue:work --once

# Ver logs de la aplicación (últimas 50 líneas)
tail -50 storage/logs/laravel.log
```

### Seeders

```bash
# Limpiar la BD y volver a sembrar (solo desarrollo)
php artisan migrate:fresh --seed

# Sembrar solo un seeder específico
php artisan db:seed --class=InventarioSeeder
```

---

## 11. Solución de Problemas Frecuentes

### Error: "Connection refused" en puerto 5444

```bash
# Los contenedores no están corriendo
sudo docker compose -f docker-compose-replica.yml up -d

# Verificar que el puerto está escuchando
ss -tlnp | grep 5444
```

### Error: "SQLSTATE[08006] connection to server failed"

```bash
# Verificar estado del contenedor
sudo docker ps | grep postgres

# Si el contenedor está caído, revisar logs
sudo docker logs postgres_ha_principal

# Reiniciar contenedores
sudo docker compose -f docker-compose-replica.yml restart
```

### Error: "The stream or file storage/logs/laravel.log could not be opened"

```bash
sudo chown -R www-data:www-data storage/ bootstrap/cache/
sudo chmod -R 775 storage/ bootstrap/cache/
```

### Error: "No application encryption key has been specified"

```bash
php artisan key:generate
```

### Las migraciones fallan con "already exists"

```bash
# Solo en desarrollo (borra todo)
php artisan migrate:fresh
```

### Push notifications no llegan (AbortError en el navegador)

```bash
# Las claves VAPID son inválidas o antiguas. Generar nuevas:
php artisan tinker --execute="\$k = \Minishlink\WebPush\VAPID::createVapidKeys(); echo 'VAPID_PUBLIC_KEY=' . \$k['publicKey'] . PHP_EOL; echo 'VAPID_PRIVATE_KEY=' . \$k['privateKey'] . PHP_EOL;"

# Actualizar .env con las nuevas claves y limpiar caché
php artisan config:clear && php artisan config:cache

# En el navegador: DevTools → Application → Service Workers → Unregister
# Luego recargar la página
```

### La réplica no sincroniza después de una prueba de failover

```bash
# Eliminar el volumen de la réplica y resincronizar desde el principal
sudo docker compose -f docker-compose-replica.yml down
sudo docker volume rm consultorio-dental_postgres_ha_replica_data
sudo docker compose -f docker-compose-replica.yml up -d
```

---

*Guía generada el 2026-06-03 · Sistema DentalTec v1.0 · Laravel 13 · PHP 8.4 · PostgreSQL 16*
