# Archivos modificados de gestionConsorcios

Estos archivos deben copiarse al repo/servidor de gestionConsorcios.
Cada archivo va en la ruta indicada dentro de la carpeta gestionConsorcios.

## Archivos incluidos:

### Nuevos (crear):
- `app/action/dashboard_data.php` - API del dashboard
- `app/action/listar_cobranzas.php` - API de cobranzas
- `app/data/expensas_data.php` - Datos para PDF de expensas

### Modificados (reemplazar):
- `app/layout/layout.php` - Layout principal corregido
- `app/views/index-view.php` - Dashboard con graficos
- `app/views/expensas_cobranzas-view.php` - Cobranzas y deuda
- `app/views/expensas_resumen-view.php` - Resumen de expensas
- `app/views/expensas_pdf-view.php` - Generador de PDF

## Instrucciones:
1. Descarga esta rama como ZIP desde GitHub
2. Copia cada archivo a la carpeta correspondiente en tu servidor
3. Asegurate de crear la carpeta `app/data/` si no existe
