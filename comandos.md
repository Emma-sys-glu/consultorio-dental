## Arrancar proyecto local

### 1. Entrar a la carpeta del proyecto

```bash
cd "/run/media/ernesto/OS/Users/ernes/Documents/University/8.-𝓢𝓮𝓶𝓮𝓼𝓽𝓻𝓮 8/Arquitectura Back-End/consultorio-dental"
```

### 2. Levantar PostgreSQL con Docker

```bash
docker compose up -d
```

Verificar contenedores:

```bash
docker ps
```

Contenedores esperados:

```text
postgres_consultorio_1 -> puerto 5434
postgres_consultorio_2 -> puerto 5433
```

### 3. Instalar dependencias

```bash
composer install
npm install
```

### 4. Configurar archivo .env

Si no existe `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Configuracion local esperada para PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5434
DB_DATABASE=consultorio_dental
DB_USERNAME=admin
DB_PASSWORD=admin123
```

### 5. Crear tablas

```bash
php artisan migrate
```

Si quieres reiniciar la base y volver a crear todo:

```bash
php artisan migrate:fresh
```

### 6. Restaurar backup de base de datos

Si quieres cargar los datos del archivo `consultorio_backup.sql`:

```bash
cat consultorio_backup.sql | docker exec -i postgres_consultorio_1 psql -U admin -d consultorio_dental
```

### 7. Compilar vistas/assets

Para produccion o cuando quieras dejar assets compilados:

```bash
npm run build
```

Crear enlace publico para archivos subidos, por ejemplo PDFs de expedientes:

```bash
php artisan storage:link
```

Para desarrollo con Vite:

```bash
npm run dev
```

### 8. Limpiar caches de Laravel

```bash
php artisan optimize:clear
```

### 9. Arrancar servidor local Laravel

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Abrir:

```text
http://127.0.0.1:8000
```

### 10. Credenciales locales

Si usaste el backup:

```text
Correo: admin@dentaltec.com
Contraseña: admin123
```

Si usaste el usuario creado localmente anteriormente:

```text
Correo: admin@consultorio.com
Contraseña: password
```

### 11. Verificaciones utiles

Ver rutas:

```bash
php artisan route:list
```

Ver estado de Laravel:

```bash
php artisan about
```

Probar conexion a PostgreSQL desde Docker:

```bash
docker exec -it postgres_consultorio_1 psql -U admin -d consultorio_dental
```

Dentro de `psql`, listar tablas:

```sql
\dt
```

Salir de `psql`:

```sql
\q
```

### 12. Apagar Docker

```bash
docker compose down
```

Apagar Docker y borrar volumenes de base de datos:

```bash
docker compose down -v
```

Ojo: `down -v` borra los datos guardados en los volumenes.

## Actualizar contenido del repo en servidor 

git pull origin main

## Entrar al servidor en consola  (para hacer cambios etc)
ssh -i ~/.ssh/id_ed25519 ernestogomez2211@34.72.247.59


## Para probar el server 
URL: http://34.72.247.59:8000
Correo: admin@dentaltec.com
Contraseña: admin123

## Login 
Correo: admin@dentaltec.com
Contraseña: admin123

## Cuando se suabn cambios al git hay que ejecutar en el server 
cd ~/proyectos/consultorio-dental
git pull origin main
composer install
npm install
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link

### Si se agregaron nuevas tablas 
php artisan migrate



## Para que clones mi BD 
cat consultorio_backup.sql | docker exec -i postgres_consultorio_1 psql -U admin -d consultorio_dental

### Explicacion 
Servidor Laravel: Google Cloud Debian
Base de datos: PostgreSQL en Docker local
Contenedor principal: postgres_consultorio_1
Puerto local BD: 5434
Puerto túnel en servidor: 15434
Conexión: SSH reverse tunnel


## Contraseñas Azure 
adminUsername=ernesto adminPassword='DentalTec2026!*' location=mexicocentral  


Tunel Azure 
ssh -N -R 5434:127.0.0.1:5434 ernesto@dentaltec-g7yabej47nuye.mexicocentral.cloudapp.azure.com

## Actualizar server 
cd /var/www/consultorio-dental
git pull origin main
composer install
npm install
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
sudo chown -R www-data:www-data storage bootstrap/cache


## Levante 

Levantar BD en docker 
cd /var/www/consultorio-dental && sudo docker compose -f docker-compose-replica.yml up -d

Levantar servidor larabel 
cd /var/www/consultorio-dental && php artisan serve --host=0.0.0.0 --port=8000


Todo junto 
cd /var/www/consultorio-dental && sudo docker compose -f docker-compose-replica.yml up -d && php artisan serve --host=0.0.0.0 --port=8000


## Prueba Alta Disponibilidad 
Terminal 1 — Watchdog en tiempo real

sudo journalctl -u db-watchdog.service -f

Terminal 2 — Estado de los contenedores (se refresca cada 2s)

watch -n 2 'sudo docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"'


Terminal 3 — La réplica siendo promovida (consulta continua)

watch -n 2 'sudo docker exec -e PGPASSWORD=admin123 postgres_ha_replica \
  psql -h 127.0.0.1 -U admin -d consultorio_dental \
  -tAc "SELECT CASE WHEN pg_is_in_recovery() THEN '"'"'REPLICA (solo lectura)'"'"' ELSE '"'"'PRIMARIO (acepta escrituras)'"'"' END AS estado;"'


  
Terminal 4 — Aquí ejecutas el golpe
Paso 1 — Confirmar que todo está bien:


sudo docker exec -e PGPASSWORD=admin123 postgres_ha_principal \
  psql -h 127.0.0.1 -U admin -d consultorio_dental \
  -c "SELECT 'Principal vivo' AS estado, COUNT(*) AS pacientes FROM pacientes;"
Paso 2 — Caer el principal:


sudo docker stop postgres_ha_principal
Paso 3 — Esperar ~15s y verificar que la réplica ya acepta escrituras:


sudo docker exec -e PGPASSWORD=admin123 postgres_ha_replica \
  psql -h 127.0.0.1 -U admin -d consultorio_dental \
  -c "INSERT INTO inventarios(nombre,categoria,cantidad,stock_minimo,precio_unitario,created_at,updated_at) VALUES('Prueba HA','Test',1,1,1,now(),now()) RETURNING id, nombre, created_at;"
Paso 4 — Ver el log del watchdog guardado:


cat /var/log/db-watchdog.log