# Skill: Taller Mecánico — Parte III (Entrega Final)

## 1. Alcance

Sistema de Gestión de Taller Mecánico con Laravel 12.x, PHP 8.2+, PostgreSQL, Blade, Bootstrap 5 y arquitectura MVC limpia.

La Parte III integra y cierra el sistema:

1. Autenticación real con login/logout, sesiones seguras y CSRF.
2. Roles `admin` y `mecanico` con permisos diferenciados.
3. Trazabilidad de órdenes mediante `user_id`.
4. Descuento, ajuste y reposición automática de stock con `DB::transaction`.
5. Flujo rápido de estados y fecha real de entrega.
6. Filtros combinables en todos los listados.
7. Dashboard ejecutivo y módulo `/reportes`.
8. Exportación CSV e impresión limpia.
9. Comprobante imprimible de orden.
10. Vistas 403/404 personalizadas.
11. Seeders completos con usuarios y datos variados.

## 2. Stack obligatorio

- Laravel 12.x (cumple “Laravel 11+”), PHP 8.2+.
- PostgreSQL como único motor: `pgsql`, puerto 5432.
- Zona horaria `America/Lima`, locale `es`, faker `es_PE`, moneda `S/`.
- Blade + Bootstrap 5 + Bootstrap Icons por CDN.
- Sin sintaxis exclusiva de MySQL (`AUTO_INCREMENT`, `UNSIGNED`, `ENUM`, backticks, `IFNULL`, `GROUP_CONCAT`).
- Migraciones reversibles e idempotentes.

## 3. Autenticación y roles

- `AuthController` con `showLogin`, `login`, `logout`.
- `LoginRequest` con mensajes en español y rate limiting.
- Regenerar sesión al iniciar; invalidar sesión y regenerar token CSRF al cerrar.
- Todas las rutas del sistema dentro de `Route::middleware('auth')`.
- Solo `/login` (GET y POST) queda para invitados.
- Middleware `EnsureUserHasRole` registrado con alias `role`.
- `users.rol` varchar(20), default `mecanico`, check PostgreSQL `IN ('admin','mecanico')`.
- `User` expone constantes `ROL_ADMIN`, `ROL_MECANICO`, `ROLES`, helpers `esAdmin()`, `esMecanico()` y atributos `rol_label`, `rol_badge`, `iniciales`.
- `ordenes.user_id` nullable, FK a `users` con `nullOnDelete()` e índice.
- Mostrar en sidebar/navbar nombre, badge de rol y botón de cerrar sesión.

### Matriz de permisos

| Acción | Admin | Mecánico |
|---|---|---|
| Dashboard, reportes, CSV e impresión | Sí | Sí |
| Crear/editar clientes, vehículos y órdenes | Sí | Sí |
| Cambiar estado de órdenes | Sí | Sí |
| Ver servicios y repuestos | Sí | Sí |
| Crear/editar servicios y repuestos | Sí | No |
| Reponer stock | Sí | No |
| Eliminar clientes, vehículos, servicios, repuestos u órdenes | Sí | No (403) |

Los botones destructivos y las acciones de administración deben ocultarse o deshabilitarse en Blade cuando el usuario sea mecánico.

## 4. Inventario y órdenes

- Crear orden: `DB::transaction`, bloquear repuestos con `lockForUpdate()`, validar stock, descontar cantidades y guardar pivotes.
- Editar orden: calcular diferencia entre cantidades anteriores y nuevas; descontar aumentos y restituir disminuciones o eliminaciones.
- Eliminar orden: restituir todo el stock antes de borrar.
- Si no hay stock: rollback y mensaje claro con repuesto, stock disponible y cantidad solicitada.
- En edición, el stock disponible para un repuesto es `stock actual + cantidad ya asignada a esa orden`.
- `fecha_entrega_real` se registra al pasar a `Entregado`; se limpia si se retrocede.
- No existe estado “Anulada”: eliminar la orden equivale a anularla y restituir stock.
- Estados: `Pendiente`, `En Proceso`, `Finalizado`, `Entregado`.
- Alertas de stock crítico: `stock <= 5` con badge rojo/ámbar.
- Reposición rápida desde dashboard o inventario, solo admin.

## 5. Filtros y reportes

- Todos los `index` usan `withQueryString()`.
- Clientes: DNI/RUC, nombre o teléfono.
- Vehículos: placa, marca/modelo o propietario.
- Servicios: nombre y rango de precio (`precio_min`, `precio_max`).
- Repuestos: SKU/nombre/marca y filtro “Solo stock bajo (≤ 5)”.
- Órdenes: código/placa, estado, `desde`, `hasta` y botón “Limpiar filtros”.
- `/reportes`: rango de fechas, ingresos de órdenes `Finalizado` y `Entregado`, ticket promedio, atenciones, top servicios, top repuestos e inventario valorizado.
- Los reportes usan `fecha_ingreso` como fecha base.
- Exportación CSV con BOM UTF-8 y separador `;` para Excel en español.
- Impresión con `window.print()` y CSS `@media print` que oculta sidebar, topbar, filtros, botones y paginación.
- Comprobante `/ordenes/{orden}/imprimir` con datos del taller, cliente, vehículo, desglose y total.

## 6. Dashboard y UI

- Dashboard con KPIs: ingresos del mes, órdenes en taller, clientes/vehículos, alertas de stock.
- Tabla de últimas órdenes activas y tabla de repuestos por reponer.
- Navegación cruzada: cliente → vehículos y total gastado; vehículo → historial y kilometraje; orden → vehículo, cliente y comprobante.
- Badges de estado: Pendiente `warning`, En Proceso `info`, Finalizado `success`, Entregado `secondary`.
- Formularios con `@error`, `is-invalid`, mensajes en español y validaciones coherentes.
- Vistas 403/404 personalizadas en Bootstrap 5.

## 7. Seeders

- `UserSeeder` crea:
  - `admin@taller.com` / `admin123` con rol `admin`.
  - `mecanico@taller.com` / `mecanico123` con rol `mecanico`.
- `OrdenSeeder` asigna `user_id` alternando entre ambos usuarios.
- Datos variados en fechas y estados para que filtros y reportes luzcan completos.
- El stock inicial se define en `RepuestoSeeder` y `OrdenSeeder` descuenta por diferencia para mantener idempotencia.
- `migrate:fresh --seed` debe dejar el sistema operativo y coherente.

## 8. Verificación obligatoria

```bash
php artisan migrate:fresh --seed
php artisan route:list --except-vendor
php artisan test
php artisan view:cache
```

Comprobar además:

- Login con ambos usuarios.
- 403 al intentar una acción de admin como mecánico.
- Descuento, ajuste y restitución de stock.
- Fecha real de entrega.
- Filtros, CSV, reporte imprimible y comprobante de orden.
