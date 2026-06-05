# Comandos rapidos DentalTec

Chuleta para el dia de la prueba. Copia y pega segun lo que necesites.

## 1. Revisar que estoy en el proyecto

```bash
pwd
git branch
git log -1 --oneline
```

## 2. Descargar cambios de main

```bash
git fetch origin
git checkout main
git pull origin main
```

## 3. Instalar dependencias

```bash
composer install
npm install
npm run build
php artisan storage:link
```

## 4. Limpiar cache de Laravel

```bash
php artisan optimize:clear
```

## 5. Base de datos

Contenedores:

- Principal HA: `postgres_ha_principal` en puerto `5444`
- Replica HA: `postgres_ha_replica` en puerto `5443`

Ver estado de contenedores:

```bash
sudo docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
```

Probar conexion desde Laravel:

```bash
php artisan db:show
```

Entrar a PostgreSQL:

```bash
sudo docker exec -it postgres_ha_principal psql -U admin -d consultorio_dental
sudo docker exec -it postgres_ha_replica psql -U admin -d consultorio_dental
```

## 6. Migraciones y datos de prueba

Actualizar tablas sin borrar datos:

```bash
php artisan migrate
```

Borrar y cargar todo desde cero (3000 registros por tabla):

```bash
php artisan db:seed --force
```

Sembrar una tabla especifica:

```bash
php artisan db:seed --class=InventarioSeeder --force
php artisan db:seed --class=PacienteSeeder --force
```

## 7. Levantar Laravel

Todo rapido:

```bash
composer install && php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=8000
```

Solo el servidor:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Detener:

```bash
pkill -f "artisan serve"
```

## 8. Credenciales

```text
Administrador
  correo:    admin@dentaltec.com
  password:  admin123

Recepcionista
  correo:    recepcion1@dentaltec.com
  password:  recepcion123

Dentista
  correo:    dentista1@dentaltec.com
  password:  dentista123

Paciente
  correo:    paciente1@dentaltec.com
  password:  paciente123
```

Ver usuarios por rol:

```bash
php artisan tinker --execute="App\Models\User::whereIn('rol',['administrador','recepcionista'])->get(['rol','email','name'])->each(fn(\$u)=>print(\$u->rol.' | '.\$u->email.PHP_EOL));"
```

## 9. Verificaciones rapidas

```bash
php artisan route:list
php artisan test
php artisan schedule:list
```

## 10. Alta disponibilidad PostgreSQL

Levantar HA:

```bash
sudo docker compose -f docker-compose-replica.yml up -d
```

Ver estado:

```bash
sudo docker compose -f docker-compose-replica.yml ps
```

Ver logs:

```bash
sudo docker logs postgres_ha_principal --tail=50
sudo docker logs postgres_ha_replica --tail=50
```

Ver si la replica esta conectada al principal:

```bash
sudo docker exec postgres_ha_principal psql -U admin -d consultorio_dental -c "SELECT * FROM pg_stat_replication;"
```

Ver si un contenedor esta como replica:

```bash
sudo docker exec postgres_ha_replica psql -U admin -d consultorio_dental -c "SELECT pg_is_in_recovery();"
```

Resultado `t` = replica activa. Resultado `f` = ya es principal.

## 11. Probar failover

Terminal 1 — ver watchdog:

```bash
journalctl -u db-watchdog -f
```

Terminal 2 — ver contenedores en vivo:

```bash
watch -n 2 'sudo docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"'
```

Terminal 3 — ver si la replica cambio a principal:

```bash
watch -n 2 'sudo docker exec -e PGPASSWORD=admin123 postgres_ha_replica psql -h 127.0.0.1 -U admin -d consultorio_dental -tAc "SELECT CASE WHEN pg_is_in_recovery() THEN '\''REPLICA'\'' ELSE '\''PRINCIPAL'\'' END;"'
```

Terminal 4 — apagar el principal:

```bash
sudo docker stop postgres_ha_principal
```

Esperar 15-25 segundos. Si `pg_is_in_recovery()` devuelve `f`, la replica ya es principal.

Probar escritura en la replica promovida:

```bash
sudo docker exec -e PGPASSWORD=admin123 postgres_ha_replica psql -h 127.0.0.1 -U admin -d consultorio_dental -c "INSERT INTO inventarios(nombre,categoria,cantidad,stock_minimo,precio_unitario,created_at,updated_at) VALUES('Prueba HA','Test',1,1,1,now(),now()) RETURNING id, nombre;"
```

## 12. Reiniciar alta disponibilidad limpia

Borra los volumenes. Solo usar con datos de prueba.

```bash
sudo systemctl stop db-watchdog
sudo docker compose -f docker-compose-replica.yml down -v
sudo docker compose -f docker-compose-replica.yml up -d
sudo systemctl start db-watchdog
```

## 13. Watchdog

Crear o actualizar script:

```bash
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
```

Crear o actualizar servicio:

```bash
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
sudo systemctl restart db-watchdog
```

Ver estado:

```bash
sudo systemctl status db-watchdog
journalctl -u db-watchdog -f
```

## 14. Notificaciones push

Ver estado general del sistema:

```bash
php artisan notif:estado
```

Probar alerta de stock bajo (envia push a admin y recepcionistas):

```bash
php artisan notif:inventario
```

Probar recordatorio de cita 1 dia antes:

```bash
php artisan notif:cita paciente1@dentaltec.com
```

Push de prueba a un usuario especifico:

```bash
php artisan push:test admin@dentaltec.com
php artisan push:test recepcion1@dentaltec.com
php artisan push:test paciente1@dentaltec.com
```

Push de prueba a todos los suscritos:

```bash
php artisan push:test
```

Ejecucion automatica (cron activo):

```text
08:00 diario  — recordatorio cita 1 dia antes   → pacientes
08:05 diario  — alerta de inventario             → admin y recepcionistas
```

## 15. Arranque automatico al encender la VM

Todos los servicios estan configurados para iniciar solos cuando la VM arranca.
No hay que hacer nada manual. El orden de arranque es:

```
docker.service
  └── dentaltec-db.service   (levanta los contenedores PostgreSQL HA)
        └── db-watchdog.service  (monitorea failover)
apache2.service              (sirve la aplicacion Laravel)
cron.service                 (ejecuta el scheduler de notificaciones)
```

Verificar que todo este corriendo:

```bash
sudo systemctl status dentaltec-db db-watchdog apache2 --no-pager
sudo docker ps --format "table {{.Names}}\t{{.Status}}"
```

Si algun servicio no arranco:

```bash
sudo systemctl start dentaltec-db
sudo systemctl start db-watchdog
```

Ver que servicios arrancan al inicio:

```bash
sudo systemctl list-unit-files --state=enabled | grep -E "apache|docker|watchdog|dentaltec|cron"
```

## 16. Servidor remoto

Entrar:

```bash
ssh ernesto@dentaltec-g7yabej47nuye.mexicocentral.cloudapp.azure.com
cd /var/www/consultorio-dental
```

Actualizar y reiniciar:

```bash
git pull origin main
composer install
npm run build
php artisan optimize:clear
sudo chown -R www-data:www-data storage bootstrap/cache
```

Levantar todo:

```bash
sudo docker compose -f docker-compose-replica.yml up -d
php artisan serve --host=0.0.0.0 --port=8000
```

Detener:

```bash
pkill -f "artisan serve"
sudo systemctl stop db-watchdog
sudo docker compose -f docker-compose-replica.yml down
```
