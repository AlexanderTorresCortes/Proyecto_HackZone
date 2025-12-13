@echo off
echo ================================================
echo   HACKZONE - Script de Inicializacion
echo ================================================
echo.

REM Copiar .env si no existe
if not exist .env (
    echo [1/6] Copiando archivo .env...
    copy .env.example .env
    echo ✓ Archivo .env creado
) else (
    echo [1/6] Archivo .env ya existe
)
echo.

REM Instalar dependencias
echo [2/6] Instalando dependencias de Composer...
call composer install
echo ✓ Dependencias instaladas
echo.

REM Generar key
echo [3/6] Generando application key...
call php artisan key:generate
echo ✓ Application key generada
echo.

REM Ejecutar migraciones y seeders
echo [4/6] Ejecutando migraciones y seeders...
call php artisan migrate:fresh --seed
echo ✓ Base de datos configurada
echo.

REM Crear symlink de storage
echo [5/6] Creando symlink de storage...
call php artisan storage:link
echo ✓ Symlink creado
echo.

REM Iniciar worker de colas en segundo plano
echo [6/6] Iniciando worker de colas...
echo.
echo ================================================
echo   IMPORTANTE: Worker de Colas
echo ================================================
echo.
echo El worker de colas es necesario para:
echo - Enviar correos de notificacion
echo - Procesar evaluaciones
echo - Enviar emails de bienvenida
echo.
echo Opcion 1: Ejecutar en esta ventana (bloqueara la terminal)
echo   php artisan queue:work --queue=notifications
echo.
echo Opcion 2: Ejecutar en una nueva ventana
echo   start cmd /k php artisan queue:work --queue=notifications
echo.
echo ¿Deseas iniciar el worker automaticamente? (S/N)
set /p RESPUESTA=

if /i "%RESPUESTA%"=="S" (
    echo.
    echo Iniciando worker en nueva ventana...
    start "HackZone Queue Worker" cmd /k "php artisan queue:work --queue=notifications --tries=3 --timeout=90"
    echo ✓ Worker iniciado en nueva ventana
) else (
    echo.
    echo Worker NO iniciado. Recuerda ejecutarlo manualmente:
    echo   php artisan queue:work --queue=notifications
)

echo.
echo ================================================
echo   SETUP COMPLETADO
echo ================================================
echo.
echo Puedes iniciar el servidor con:
echo   php artisan serve
echo.
echo O usar XAMPP/WAMP directamente
echo.
pause
