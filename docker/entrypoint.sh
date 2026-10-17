#!/bin/sh
set -e

# Si es el servicio worker, ejecutar el consumidor de colas
if [ "$PROCESS_TYPE" = "worker" ]; then
    exec php artisan queue:work --sleep=3 --tries=3 --max-time=3600
fi

# Crear directorios necesarios (sobre el disco persistente)
mkdir -p storage/app/public \
         storage/framework/cache \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Enlace simbólico public/storage para archivos subidos
php artisan storage:link --force || true

# Aplicar migraciones pendientes (idempotente)
php artisan migrate --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Arrancar PHP-FPM + Nginx
exec supervisord -c /etc/supervisord.conf
