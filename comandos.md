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