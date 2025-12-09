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
    echo -e "${BLUE}[1/8]${NC} Copiando archivo .env..."
    cp .env.example .env
    echo -e "${GREEN}✓${NC} Archivo .env creado"
else
    echo -e "${BLUE}[1/8]${NC} Archivo .env ya existe"
fi
echo ""

# Instalar dependencias de Composer
echo -e "${BLUE}[2/8]${NC} Instalando dependencias de Composer..."
echo "  - Laravel Framework"
echo "  - Laravel Breeze (Auth)"
echo "  - Livewire"
echo "  - DomPDF (Exportar PDF)"
echo "  - Maatwebsite Excel (Exportar Excel)"
composer install --no-interaction
echo -e "${GREEN}✓${NC} Dependencias de Composer instaladas"
echo ""

# Instalar dependencias de NPM
echo -e "${BLUE}[3/8]${NC} Instalando dependencias de NPM..."
npm install
echo -e "${GREEN}✓${NC} Dependencias de NPM instaladas"
echo ""

# Compilar assets de frontend
echo -e "${BLUE}[4/8]${NC} Compilando assets de frontend..."
npm run build
echo -e "${GREEN}✓${NC} Assets compilados"
echo ""

# Generar key
echo -e "${BLUE}[5/8]${NC} Generando application key..."
php artisan key:generate
echo -e "${GREEN}✓${NC} Application key generada"
echo ""

# Ejecutar migraciones y seeders
echo -e "${BLUE}[6/8]${NC} Ejecutando migraciones y seeders..."
echo "  Seeders incluidos:"
echo "    - UserSeeder (usuarios de prueba)"
echo "    - EventSeeder (eventos de prueba)"
echo "    - EquipoSeeder (equipos de prueba)"
echo "    - EvaluacionSeeder (evaluaciones de prueba)"
echo "    - EntregaSeeder (archivos de prueba)"
php artisan migrate:fresh --seed
echo -e "${GREEN}✓${NC} Base de datos configurada con datos de prueba"
echo ""

# Crear symlink de storage
echo -e "${BLUE}[7/8]${NC} Creando symlink de storage..."
php artisan storage:link
echo -e "${GREEN}✓${NC} Symlink creado"
echo ""

# Iniciar worker de colas
echo -e "${BLUE}[8/8]${NC} Configurando worker de colas..."
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
