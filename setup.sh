#!/bin/bash

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "================================================"
echo "   HACKZONE - Script de Inicialización"
echo "================================================"
echo ""

# Copiar .env si no existe
if [ ! -f .env ]; then
    echo -e "${BLUE}[1/6]${NC} Copiando archivo .env..."
    cp .env.example .env
    echo -e "${GREEN}✓${NC} Archivo .env creado"
else
    echo -e "${BLUE}[1/6]${NC} Archivo .env ya existe"
fi
echo ""

# Instalar dependencias
echo -e "${BLUE}[2/6]${NC} Instalando dependencias de Composer..."
composer install
echo -e "${GREEN}✓${NC} Dependencias instaladas"
echo ""

# Generar key
echo -e "${BLUE}[3/6]${NC} Generando application key..."
php artisan key:generate
echo -e "${GREEN}✓${NC} Application key generada"
echo ""

# Ejecutar migraciones y seeders
echo -e "${BLUE}[4/6]${NC} Ejecutando migraciones y seeders..."
php artisan migrate:fresh --seed
echo -e "${GREEN}✓${NC} Base de datos configurada"
echo ""

# Crear symlink de storage
echo -e "${BLUE}[5/6]${NC} Creando symlink de storage..."
php artisan storage:link
echo -e "${GREEN}✓${NC} Symlink creado"
echo ""

# Iniciar worker de colas
echo -e "${BLUE}[6/6]${NC} Configurando worker de colas..."
echo ""
echo "================================================"
echo "   IMPORTANTE: Worker de Colas"
echo "================================================"
echo ""
echo "El worker de colas es necesario para:"
echo "  - Enviar correos de notificación"
echo "  - Procesar evaluaciones"
echo "  - Enviar emails de bienvenida"
echo ""
echo "Opciones para ejecutar el worker:"
echo ""
echo "  1) En esta terminal (bloqueará la terminal):"
echo "     php artisan queue:work --queue=notifications"
echo ""
echo "  2) En segundo plano (recomendado):"
echo "     php artisan queue:work --queue=notifications > /dev/null 2>&1 &"
echo ""
echo "  3) Con Supervisor (producción):"
echo "     Ver NOTIFICACIONES_EVALUACION.md"
echo ""

read -p "¿Deseas iniciar el worker en segundo plano ahora? (s/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[SsYy]$ ]]; then
    echo ""
    echo "Iniciando worker en segundo plano..."
    nohup php artisan queue:work --queue=notifications --tries=3 --timeout=90 > storage/logs/queue-worker.log 2>&1 &
    WORKER_PID=$!
    echo -e "${GREEN}✓${NC} Worker iniciado (PID: $WORKER_PID)"
    echo "  Logs: storage/logs/queue-worker.log"
    echo "  Para detenerlo: kill $WORKER_PID"
else
    echo ""
    echo -e "${YELLOW}Worker NO iniciado.${NC} Recuerda ejecutarlo manualmente:"
    echo "  php artisan queue:work --queue=notifications"
fi

echo ""
echo "================================================"
echo "   SETUP COMPLETADO"
echo "================================================"
echo ""
echo "Puedes iniciar el servidor con:"
echo "  php artisan serve"
echo ""
echo "O configurar en Apache/Nginx"
echo ""
