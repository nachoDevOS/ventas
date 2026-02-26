# Guía del Proyecto Laravel Voyager

Este proyecto utiliza **Laravel** con el panel de administración **Voyager**. 

## Estructura y Reglas
- **BREAD:** La lógica de las vistas del administrador se maneja a través de BREAD en la base de datos.
- **Modelos:** Los modelos principales están en `app/Models/`.
- **Controladores Voyager:** Si hay controladores personalizados para Voyager, se encuentran en `app/Http/Controllers/Voyager/`.
- **Estilo de código:** Sigue las convenciones de Laravel (PSR-12). Usa `artisan` para generar migraciones y modelos.

## Comandos Frecuentes
- Levantar servidor: `php artisan serve`
- Limpiar caché: `php artisan cache:clear && php artisan config:clear`
- Ver rutas: `php artisan route:list`

## Notas Importantes
- No modifiques directamente los archivos en `vendor/tcg/voyager`. Si necesitas cambiar algo, sobreescribe la vista o el controlador en la carpeta `app` o `resources`.
- El archivo `.claudeignore` excluye carpetas pesadas para optimizar el contexto.