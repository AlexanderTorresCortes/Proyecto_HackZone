# 📧 Sistema de Notificaciones de Evaluación - HackZone

## 🎯 Descripción General

Este sistema notifica automáticamente a todos los integrantes de un equipo cuando su proyecto es calificado por un juez. Utiliza una arquitectura escalable con **Jobs en cola**, **Observers** y **Mailables** para garantizar que el proceso no bloquee la plataforma.

---

## 🏗️ Arquitectura del Sistema

### Flujo de Funcionamiento

```
1. Juez registra calificación → Evaluacion creada en BD
                ↓
2. EvaluacionObserver detecta el evento created()
                ↓
3. Observer despacha NotificarEquipoCalificado Job a la cola
                ↓
4. Job obtiene todos los miembros del equipo
                ↓
5. Job envía correo personalizado a cada miembro
                ↓
6. Miembros reciben notificación con detalles de la evaluación
```

---

## 📁 Componentes del Sistema

### 1. **Mailable**: `ProyectoCalificadoMail`
**Ubicación**: `app/Mail/ProyectoCalificadoMail.php`

- Implementa `ShouldQueue` para envío asíncrono
- Recibe la evaluación y el miembro del equipo
- Calcula automáticamente la puntuación total
- Vista asociada: `emails.proyecto-calificado`

**Propiedades disponibles en la vista**:
- `$evaluacion` - Objeto de la evaluación
- `$miembro` - Usuario que recibirá el correo
- `$equipo` - Equipo evaluado
- `$evento` - Evento del que forma parte
- `$juez` - Juez que realizó la evaluación
- `$puntuacionTotal` - Puntuación calculada

---

### 2. **Job**: `NotificarEquipoCalificado`
**Ubicación**: `app/Jobs/NotificarEquipoCalificado.php`

**Características**:
- ✅ Ejecuta en segundo plano (no bloquea la aplicación)
- ✅ Cola dedicada: `notifications`
- ✅ 3 intentos automáticos si falla
- ✅ Timeout de 60 segundos
- ✅ Logs detallados de cada envío
- ✅ Manejo de errores con `failed()`

**Proceso**:
1. Obtiene el equipo de la evaluación
2. Recolecta líder + miembros del equipo
3. Elimina duplicados
4. Envía correo personalizado a cada miembro
5. Registra logs de éxito/error

---

### 3. **Observer**: `EvaluacionObserver`
**Ubicación**: `app/Observers/EvaluacionObserver.php`

**Eventos observados**:
- `created()` - Cuando se registra nueva calificación ✅
- `updated()` - Cuando se actualiza calificación (comentado por defecto)
- `deleted()` - Cuando se elimina calificación (solo log)

**Delay estratégico**: 5 segundos antes de ejecutar para asegurar que la transacción de BD se completó.

---

### 4. **Vista Email**: `proyecto-calificado.blade.php`
**Ubicación**: `resources/views/emails/proyecto-calificado.blade.php`

**Diseño**:
- 🎨 Estilo coherente con `bienvenida.blade.php`
- 📱 Responsive (desktop y móvil)
- 🌈 Gradiente verde (tema de éxito)
- ✨ Animaciones sutiles

**Contenido del email**:
- Saludo personalizado
- Puntuación total destacada
- Detalles de la evaluación (equipo, evento, juez, fecha)
- Desglose por criterios
- Comentarios del juez (si existen)
- Botón CTA para ver ranking completo
- Footer con información legal

---

## ⚙️ Configuración Necesaria

### 1. **Configurar Queue Driver** (`.env`)
```env
QUEUE_CONNECTION=database
# o
QUEUE_CONNECTION=redis  # Recomendado para producción
```

### 2. **Crear tabla de jobs** (si usas database)
```bash
php artisan queue:table
php artisan migrate
```

### 3. **Configurar Email** (`.env`)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hackzone.com
MAIL_FROM_NAME="HackZone"
```

---

## 🚀 Ejecutar el Sistema

### Modo Desarrollo (Local)
```bash
# Opción 1: Procesar colas manualmente
php artisan queue:work --queue=notifications

# Opción 2: Supervisor (recomendado)
# Ver sección de Supervisor más abajo
```

### Modo Producción
```bash
# Con múltiples workers
php artisan queue:work --queue=notifications --tries=3 --timeout=90

# Con Supervisor (recomendado)
# Ver sección de Supervisor más abajo
```

---

## 🔍 Supervisar y Monitorear

### Ver Jobs en Cola
```bash
# Ver jobs pendientes
php artisan queue:monitor notifications

# Ver jobs fallidos
php artisan queue:failed
```

### Reintentar Jobs Fallidos
```bash
# Reintentar todos los jobs fallidos
php artisan queue:retry all

# Reintentar un job específico
php artisan queue:retry {job-id}
```

### Limpiar Jobs Fallidos
```bash
php artisan queue:flush
```

### Logs del Sistema
Los logs se guardan en `storage/logs/laravel.log`:
- ✅ Evaluación creada
- ✅ Job despachado
- ✅ Correos enviados exitosamente
- ❌ Errores de envío

---

## 📊 Supervisor (Producción Recomendada)

### Instalar Supervisor (Linux)
```bash
sudo apt-get install supervisor
```

### Configuración (`/etc/supervisor/conf.d/hackzone-worker.conf`)
```ini
[program:hackzone-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/a/tu/proyecto/artisan queue:work --queue=notifications --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=3
redirect_stderr=true
stdout_logfile=/ruta/a/tu/proyecto/storage/logs/worker.log
stopwaitsecs=3600
```

### Comandos Supervisor
```bash
# Recargar configuración
sudo supervisorctl reread
sudo supervisorctl update

# Iniciar workers
sudo supervisorctl start hackzone-worker:*

# Detener workers
sudo supervisorctl stop hackzone-worker:*

# Ver estado
sudo supervisorctl status
```

---

## 🧪 Testing Manual

### Probar el envío de correos
```php
// En tinker: php artisan tinker

use App\Models\Evaluacion;
use App\Jobs\NotificarEquipoCalificado;

// Obtener una evaluación de prueba
$evaluacion = Evaluacion::first();

// Despachar job manualmente
NotificarEquipoCalificado::dispatch($evaluacion);

// Procesar inmediatamente (sin cola)
php artisan queue:work --once
```

---

## 🎨 Personalización del Email

### Modificar el diseño
Edita: `resources/views/emails/proyecto-calificado.blade.php`

### Cambiar colores del tema
```css
/* Header gradient */
background: linear-gradient(135deg, #10b981 0%, #059669 100%);

/* Botón CTA */
background: linear-gradient(135deg, #10b981 0%, #059669 100%);
```

### Agregar más información
```php
// En ProyectoCalificadoMail.php - constructor
$this->datosAdicionales = [
    'posicion_ranking' => $evaluacion->equipo->getPosicionRanking(),
    'total_equipos' => $evento->equiposInscritos->count(),
];
```

---

## 📈 Optimizaciones

### 1. **Rate Limiting** (Evitar spam)
```php
// En NotificarEquipoCalificado.php
use Illuminate\Support\Facades\RateLimiter;

public function handle(): void
{
    foreach ($miembros as $miembro) {
        RateLimiter::attempt(
            'send-email:' . $miembro->id,
            $perMinute = 5,
            function() use ($miembro) {
                Mail::to($miembro->email)->send(...);
            }
        );
    }
}
```

### 2. **Batch Processing** (Procesar en lotes)
```php
// Agrupar múltiples evaluaciones
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

Bus::batch([
    new NotificarEquipoCalificado($evaluacion1),
    new NotificarEquipoCalificado($evaluacion2),
])->dispatch();
```

---

## ❓ Troubleshooting

### ❌ Los correos no se envían

**Verificar**:
```bash
# 1. ¿El worker está corriendo?
php artisan queue:work --queue=notifications

# 2. ¿Hay jobs fallidos?
php artisan queue:failed

# 3. ¿La configuración de email es correcta?
php artisan tinker
Mail::raw('Test', function($msg) { $msg->to('tu@email.com'); });
```

### ❌ Jobs se quedan en "processing"

**Solución**:
```bash
# Reiniciar workers
php artisan queue:restart

# Si usas Supervisor
sudo supervisorctl restart hackzone-worker:*
```

### ❌ Errores de permisos

**Solución**:
```bash
# Dar permisos a storage
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

---

## 🎯 Mejoras Futuras

- [ ] Notificaciones in-app (además de email)
- [ ] Notificaciones push (Firebase/Pusher)
- [ ] Resumen semanal de evaluaciones
- [ ] Dashboard de estadísticas de notificaciones
- [ ] A/B testing de plantillas de email
- [ ] Preferencias de notificación por usuario

---

## 📚 Recursos Adicionales

- [Laravel Queues Documentation](https://laravel.com/docs/queues)
- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Laravel Observers Documentation](https://laravel.com/docs/eloquent#observers)
- [Supervisor Documentation](http://supervisord.org/)

---

**Desarrollado con ❤️ para HackZone**
