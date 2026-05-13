# ParkManager Lite

Aplicacion Laravel 10 para operacion completa de parqueadero con autenticacion, roles, auditoria, tarifas funcionales, pagos pendientes y experiencia movil optimizada.

## Stack

- PHP 8.1+
- Laravel 10
- MySQL

## Modulos

- Login con usuario o correo y contrasena
- Dashboard operativo
- Gestion unificada de entrada y salida
- Pagos pendientes
- Reportes administrativos
- Configuracion funcional de tarifas
- Auditoria completa
- Gestion de usuarios con roles `admin` y `operario`

## Base de datos incluida

El proyecto ya trae migraciones y seeder para:

- `sites`
- `users` con username, roles y estado
- `tariff_profiles`
- `parking_tickets`
- `payments`
- `audit_logs`

## Comandos para correr

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

## Si ya existe `.env`

```bash
composer dump-autoload
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan serve
```

## Credenciales demo

- `admin` / `password`
- `operador` / `password`
- `operador@sede.com` / `password`

## Configuracion MySQL

1. Crea una base de datos vacia llamada `parkmanager`.
2. Usa esta configuracion en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parkmanager
DB_USERNAME=root
DB_PASSWORD=123Alejandro.
```

3. Ejecuta:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

## Importante

- Si vienes de una version anterior del proyecto, usa `php artisan migrate:fresh --seed` para recrear toda la base correctamente.
- Si Composer te mantiene paquetes viejos, corre `composer update` antes de migrar.
