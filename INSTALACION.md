# 🚀 Guía de Instalación - HackZone

## 📋 Requisitos Previos

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js y npm (opcional, para assets)

---

## ⚡ Instalación Rápida (Recomendada)

### Windows
```bash
# Ejecutar script de instalación
setup.bat
```

### Linux/Mac
```bash
# Dar permisos de ejecución
chmod +x setup.sh

# Ejecutar script
./setup.sh
```

**El script automáticamente hará:**
1. ✅ Copiar `.env` (si no existe)
2. ✅ Instalar dependencias de Composer
3. ✅ Generar application key
4. ✅ Ejecutar migraciones y seeders
5. ✅ Crear symlink de storage
6. ✅ Ofrecer iniciar worker de colas

---

## 🔧 Instalación Manual

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/hackzone.git
cd hackzone
```

### 2. Copiar archivo de configuración
```bash
cp .env.example .env
```

### 3. Configurar base de datos en `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hackzone
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Instalar dependencias
```bash
composer install
```

### 5. Generar application key
```bash
php artisan key:generate
```

### 6. Ejecutar migraciones y seeders
```bash
php artisan migrate:fresh --seed
```

### 7. Crear symlink de storage
```bash
php artisan storage:link
```

### 8. ⚠️ IMPORTANTE: Iniciar worker de colas
```bash
# Opción 1: En una terminal separada
php artisan queue:work --queue=notifications

# Opción 2: En segundo plano (Linux/Mac)
php artisan queue:work --queue=notifications > /dev/null 2>&1 &

# Opción 3: En nueva ventana (Windows)
start cmd /k php artisan queue:work --queue=notifications
```

### 9. Iniciar servidor
```bash
php artisan serve
```

Visita: `http://localhost:8000`

---

## 📧 Configuración de Correos (Importante)

### Para desarrollo local (Mailtrap)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hackzone.com
MAIL_FROM_NAME="HackZone"
```

### Para producción (Gmail)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hackzone.com
MAIL_FROM_NAME="HackZone"
```

**Nota**: Para Gmail, necesitas generar una "Contraseña de aplicación" en tu cuenta de Google.

---

## 🔄 Sistema de Colas (MUY IMPORTANTE)

### ¿Por qué necesito el worker?

El worker de colas procesa:
- ✉️ **Correos de notificación** cuando un juez califica
- 👋 **Emails de bienvenida** a nuevos usuarios
- 📊 **Notificaciones de evaluación** a equipos

### Verificar que el worker esté corriendo

```bash
# Ver procesos
php artisan queue:monitor notifications

# Ver jobs en cola
php artisan queue:work --queue=notifications --once

# Ver jobs fallidos
php artisan queue:failed
```

### ⚠️ Si olvidaste iniciar el worker

Los correos NO se enviarán hasta que inicies el worker:

```bash
php artisan queue:work --queue=notifications
```

---

## 🎯 Datos de Prueba (Seeders)

Después de ejecutar `php artisan migrate:fresh --seed` tendrás:

### Usuarios de Prueba

| Rol | Email | Password |
|-----|-------|----------|
| Administrador | `admin@hackzone.com` | `password` |
| Juez | `juez@hackzone.com` | `password` |
| Usuario | `usuario@hackzone.com` | `password` |

### Datos Incluidos
- ✅ 50 usuarios de prueba
- ✅ 3 eventos (NASA Hackathon, Tech Tournament, Coding Marathon)
- ✅ 20 equipos
- ✅ Criterios de evaluación
- ✅ Jueces asignados a eventos

---

## 🧪 Verificar Instalación

### 1. Verificar base de datos
```bash
php artisan migrate:status
```

### 2. Verificar tablas de colas
```bash
# Deberías ver: jobs, failed_jobs
php artisan db:show --table=jobs
```

### 3. Probar envío de correos
```bash
php artisan tinker

# En tinker:
Mail::raw('Test email', function($msg) {
    $msg->to('tu-email@example.com')->subject('Test HackZone');
});
```

### 4. Verificar observer de evaluaciones
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

---

## 🐛 Solución de Problemas

### Error: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Error: "SQLSTATE[HY000] [1045] Access denied"
- Verifica credenciales en `.env`
- Asegúrate que MySQL esté corriendo
- Crea la base de datos manualmente:
  ```sql
  CREATE DATABASE hackzone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

### Error: "Class 'App\Observers\EvaluacionObserver' not found"
```bash
composer dump-autoload
php artisan optimize:clear
```

### Los correos no se envían
```bash
# 1. Verificar worker
php artisan queue:work --queue=notifications

# 2. Ver jobs fallidos
php artisan queue:failed

# 3. Reintentar
php artisan queue:retry all
```

### Error de permisos en storage
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (ejecutar como administrador)
icacls storage /grant Users:F /t
```

---

## 📚 Documentación Adicional

- [Sistema de Notificaciones](NOTIFICACIONES_EVALUACION.md)
- [Arquitectura del Proyecto](docs/arquitectura.md)
- [API Documentation](docs/api.md)

---

## 🚀 Despliegue en Producción

### Con Supervisor (Recomendado)

Crear archivo: `/etc/supervisor/conf.d/hackzone-worker.conf`

```ini
[program:hackzone-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --queue=notifications --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=3
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/worker.log
```

Comandos:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start hackzone-worker:*
```

---

## 📞 Soporte

¿Problemas con la instalación?

1. Revisa los logs: `storage/logs/laravel.log`
2. Ejecuta: `php artisan optimize:clear`
3. Consulta: [NOTIFICACIONES_EVALUACION.md](NOTIFICACIONES_EVALUACION.md)

---

**Desarrollado con ❤️ para HackZone**
