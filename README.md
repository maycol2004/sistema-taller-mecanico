# Sistema de Gestión de Taller Mecánico — Parte II

Aplicación web desarrollada con **Laravel 12**, **PHP 8.2+**, **PostgreSQL** y **Bootstrap 5** para administrar la operación básica de un taller mecánico: clientes, vehículos, servicios de mano de obra, repuestos y órdenes de servicio.

> **Alcance de esta parte:** CRUD completo de los cinco módulos, cálculo automático de costos y panel de control. No incluye autenticación ni reportes avanzados, que corresponden a la Parte III.

## Módulos implementados

| Módulo | Funcionalidad principal |
| --- | --- |
| **Clientes** | Registro, búsqueda por documento/nombre/correo/teléfono, ficha con vehículos y órdenes, edición y eliminación protegida. |
| **Vehículos** | CRUD con propietario, filtro por cliente, ficha con historial de órdenes y kilometraje. |
| **Servicios** | Catálogo de mano de obra con precio base, duración estimada y métricas de uso. |
| **Repuestos** | Inventario con SKU único, precio unitario, stock y alerta de stock bajo (≤ 5 unidades). |
| **Órdenes** | CRUD transaccional con código automático `OT-AAAA-####`, servicios y repuestos detallados, total recalculado y cambio rápido de estado. |
| **Panel** | KPIs de clientes, vehículos, inventario y órdenes; ingresos del mes, últimas órdenes, distribución por estado y stock bajo. |

### Reglas de negocio

- El **código de orden** se genera automáticamente con correlativo anual: `OT-2026-0001`.
- Cada detalle de la orden guarda `cantidad`, `precio_unitario` y `subtotal` en las tablas pivote `orden_servicio` y `orden_repuesto`.
- El **total de la orden** se recalcula en el servidor a partir de los subtotales; el formulario además muestra el cálculo en vivo con JavaScript.
- Los precios quedan congelados en el detalle al momento de guardar la orden.
- Se valida el **stock disponible** al asignar repuestos, pero en esta parte el stock **no se descuenta automáticamente**.
- No se pueden eliminar clientes con vehículos, vehículos o servicios/repuestos con órdenes asociadas. Las órdenes eliminan sus pivotes en cascada.
- Los errores de integridad se capturan con `QueryException` y se muestran como alertas amigables en español.

### Estados de una orden

`Pendiente` → `En Proceso` → `Finalizado` → `Entregado`

## Requisitos

- PHP 8.2 o superior con extensiones `pdo_pgsql` y `mbstring`.
- Composer.
- PostgreSQL 14+ (probado con PostgreSQL 18 en el puerto `5432`).
- Node.js no es necesario: los estilos y scripts se cargan por CDN.

## Instalación

```bash
# 1. Dependencias
composer install

# 2. Variables de entorno
copy .env.example .env        # Windows
cp .env.example .env          # Linux / macOS
php artisan key:generate

# 3. Crear la base de datos en PostgreSQL
#    CREATE DATABASE taller_mecanico;

# 4. Configurar .env
#    DB_CONNECTION=pgsql
#    DB_HOST=127.0.0.1
#    DB_PORT=5432
#    DB_DATABASE=taller_mecanico
#    DB_USERNAME=postgres
#    DB_PASSWORD=tu_contraseña

# 5. Migrar y sembrar datos de prueba
php artisan migrate:fresh --seed

# 6. Levantar el servidor
php artisan serve
```

Abrir [http://127.0.0.1:8000](http://127.0.0.1:8000). La aplicación no solicita inicio de sesión.

## Datos de prueba

`DatabaseSeeder` genera datos realistas de Lima (Perú):

- 8 clientes (DNI y RUC).
- 10 vehículos.
- 8 servicios de mano de obra.
- 10 repuestos.
- 8 órdenes distribuidas entre `Pendiente`, `En Proceso`, `Finalizado` y `Entregado`, con servicios y repuestos asignados.

Los seeders usan `updateOrCreate` y `sync`, por lo que pueden ejecutarse nuevamente sin duplicar registros.

## Pruebas

```bash
php artisan test
```

Incluye pruebas funcionales de:

- Carga de todas las páginas (panel, listados, fichas y formularios).
- CRUD completo de clientes, vehículos, servicios, repuestos y órdenes.
- Cálculo automático del total de la orden.
- Cambio de estado.
- Bloqueo de eliminación de clientes con vehículos.
- Validación de documento duplicado y stock insuficiente.

Las pruebas usan transacciones sobre la base de datos configurada, por lo que no borran los datos sembrados.

## Estructura relevante

```text
app/
  Http/Controllers/    Dashboard, Cliente, Vehiculo, Servicio, Repuesto, Orden
  Http/Requests/       Validaciones Store/Update con mensajes en español
  Models/              Relaciones Eloquent, scopes, casts y accesores
database/
  migrations/          Esquema PostgreSQL con FKs, índices y CHECK
  seeders/             Datos de prueba idempotentes
resources/views/
  layouts/app.blade.php    Layout maestro con sidebar oscuro y alertas
  dashboard.blade.php      Panel con KPIs
  clientes|vehiculos|servicios|repuestos|ordenes/
  components/          Badges de estado/stock y estados vacíos
routes/web.php         Rutas del panel, recursos y cambio de estado
```

## Detalles técnicos

- Zona horaria `America/Lima`, locale `es` y Faker `es_PE`.
- Montos formateados como `S/ 0.00` mediante `number_format`.
- Paginación con estilos de Bootstrap 5.
- Validaciones centralizadas en `BaseFormRequest`, con atributos y mensajes en español.
- Consultas de búsqueda con `ILIKE` (PostgreSQL) y scopes reutilizables.
- Operaciones de órdenes envueltas en `DB::transaction` y bloqueo del correlativo con `lockForUpdate`.

## Nota sobre la versión de Laravel

El proyecto usa **Laravel 12.73**, que cumple el requisito de “Laravel 11 o superior”. Composer bloquea la rama 11.x por avisos de seguridad de dependencias transitivas, por lo que se optó por la versión estable más reciente compatible con PHP 8.2+.
