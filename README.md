<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Template Voyager Example

## Instalación
```
composer install
cp .env.example .env
php artisan example:install
sudo chmod -R 775 storage bootstrap/cache
chown -R www-data storage bootstrap/cache
```

## Versión de Laravel
Laravel Framework 10.0.0

## Requisistos
- php >= 8.1
- Extenciones **php-mbstring php-intl php-dom php-gd php-xml php-zip php-curl php-fpm php-mysql**


## Dockerfile
Crear en la Raiz del proyecto los siguientes archivos:
Dockerfile
unit.json

Ejecutar.
```
docker build -t example .
docker run -e DB_DATABASE=example -e DB_HOST=host.docker.internal -p 8000:8000 -t example
```
Ejemplo
```
docker run  -e DB_CONNECTION=mysql -e DB_HOST=host.docker.internal -e DB_PORT=3306 -e DB_DATABASE=example -e DB_USERNAME=root -e DB_CONNECTION_SOLUCION_DIGITAL=mysql -e DB_HOST_SOLUCION_DIGITAL=host.docker.internal -e DB_PORT_SOLUCION_DIGITAL=3306 -e DB_DATABASE_SOLUCION_DIGITAL=soluciondigital -e DB_USERNAME_SOLUCION_DIGITAL=root -p 8000:8000 -t example
```
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Template Voyager Example

## Instalación
```
composer install
cp .env.example .env
php artisan example:install
sudo chmod -R 775 storage bootstrap/cache
chown -R www-data storage bootstrap/cache
```

## Versión de Laravel
Laravel Framework 10.0.0

## Requisistos
- php >= 8.1
- Extenciones **php-mbstring php-intl php-dom php-gd php-xml php-zip php-curl php-fpm php-mysql**


## Dockerfile
Crear en la Raiz del proyecto los siguientes archivos:
Dockerfile
unit.json

Ejecutar.
```
docker build -t example .
docker run -e DB_DATABASE=example -e DB_HOST=host.docker.internal -p 8000:8000 -t example
```
Ejemplo
```
docker run  -e DB_CONNECTION=mysql -e DB_HOST=host.docker.internal -e DB_PORT=3306 -e DB_DATABASE=example -e DB_USERNAME=root -e DB_CONNECTION_SOLUCION_DIGITAL=mysql -e DB_HOST_SOLUCION_DIGITAL=host.docker.internal -e DB_PORT_SOLUCION_DIGITAL=3306 -e DB_DATABASE_SOLUCION_DIGITAL=soluciondigital -e DB_USERNAME_SOLUCION_DIGITAL=root -p 8000:8000 -t example
```


## Configuración de Nginx (nginx.conf o tu sitio)
```sh
client_max_body_size 300M;
client_body_timeout 300s;
client_header_timeout 300s;
keepalive_timeout 300s;
send_timeout 300s;
fastcgi_read_timeout 300s;
fastcgi_send_timeout 300s;
fastcgi_connect_timeout 300s;
proxy_read_timeout 300s;
```
## Configuración de PHP (php.ini)
```sh
upload_max_filesize = 300M
post_max_size = 300M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```
## Configuración específica para tu sitio (en server block)
```sh
server {
    listen 80;
    server_name tu-dominio.com;
    
    # Configuración para uploads grandes
    client_max_body_size 300M;
    client_body_timeout 300s;
    client_header_timeout 300s;
    
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_read_timeout 300s;
        fastcgi_send_timeout 300s;
        fastcgi_connect_timeout 300s;
    }
    
    # O si usas PHP-FPM en puerto
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_read_timeout 300s;
    }
}
```

## PERMISO DE EJECUCIÓN EN PRODUCCIÓN
```sh
#!/bin/bash
PROJECT_PATH="/var/www/production/example"
WEB_USER="www-data"

echo "=== Configurando permisos seguros para Laravel ==="

# 1. Establecer propietario correcto
sudo chown -R $WEB_USER:$WEB_USER $PROJECT_PATH
echo "✓ Propietario establecido a www-data"

# 2. Permisos base SEGUROS
sudo find $PROJECT_PATH -type f -exec chmod 644 {} \;
sudo find $PROJECT_PATH -type d -exec chmod 755 {} \;
echo "✓ Permisos base aplicados"

# 3. Dar permisos de escritura SOLO donde se necesitan
sudo chmod -R 775 $PROJECT_PATH/storage $PROJECT_PATH/bootstrap/cache
echo "✓ Permisos de escritura en storage y bootstrap/cache"

# 4. Archivos específicos ejecutables
sudo chmod +x $PROJECT_PATH/artisan
[ -f "$PROJECT_PATH/deploy.sh" ] && sudo chmod +x $PROJECT_PATH/deploy.sh
echo "✓ Archivos ejecutables configurados"

# 5. Proteger archivos sensibles EXTRA
[ -f "$PROJECT_PATH/.env" ] && sudo chmod 640 $PROJECT_PATH/.env
[ -f "$PROJECT_PATH/.env.example" ] && sudo chmod 640 $PROJECT_PATH/.env.example
sudo chmod 600 $PROJECT_PATH/storage/oauth-*.key 2>/dev/null || true
echo "✓ Archivos sensibles protegidos"

# 6. Limpiar cache de Laravel
sudo -u $WEB_USER php $PROJECT_PATH/artisan cache:clear > /dev/null 2>&1
sudo -u $WEB_USER php $PROJECT_PATH/artisan config:clear > /dev/null 2>&1
echo "✓ Cache de Laravel limpiado"

# 7. Verificación de seguridad
echo ""
echo "=== VERIFICACIÓN DE SEGURIDAD ==="
echo "Archivos sensibles:"
ls -la $PROJECT_PATH/.env $PROJECT_PATH/.env.example 2>/dev/null || echo "No encontrados"
echo ""
echo "Permisos de storage:"
ls -ld $PROJECT_PATH/storage $PROJECT_PATH/bootstrap/cache
echo ""
echo "Archivos con permisos peligrosos (debería estar vacío):"
sudo find $PROJECT_PATH -type f -perm /o=w ! -path "*/storage/*" ! -path "*/bootstrap/cache/*" | head -5
```"# farmacia" 
"# farmacia" 
