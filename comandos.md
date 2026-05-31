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


## COntraseñas Azure 
adminUsername=ernesto adminPassword='DentalTec2026!*' location=mexicocentral  


Tunel Azure 
ssh -N -R 15434:127.0.0.1:5434 ernesto@dentaltec-g7yabej47nuye.mexicocentral.cloudapp.azure.com


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