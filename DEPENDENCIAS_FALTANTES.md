# 📦 Instalar Dependencias Faltantes

## Paquetes Necesarios

Ejecuta estos comandos en tu terminal:

```bash
# 1. Laravel Excel (para exportar a Excel)
composer require maatwebsite/excel

# 2. DomPDF (para exportar a PDF)
composer require barryvdh/dompdf

# O instalar ambos a la vez:
composer require maatwebsite/excel barryvdh/dompdf
```

## Verificar Instalación

```bash
# Verificar que los paquetes estén instalados
composer show | grep -E "maatwebsite|barryvdh"

# Debería mostrar:
# barryvdh/laravel-dompdf
# maatwebsite/excel
```

## Configuración (Opcional)

Si quieres publicar la configuración:

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

Después de instalar, las exportaciones de Excel y PDF funcionarán correctamente.
