# Portal de monitoreo

Portal PHP vanilla para consultar datos sincronizados desde la instalacion local. Incluye login sencillo, vistas moviles, filtros, paginacion y administracion basica de usuarios.

## Instalacion

1. Ejecuta `database.sql` en MySQL. Esto crea las tablas `portal_tickets`, `portal_events` y `portal_users`.
2. Ajusta `config.php` con tus credenciales de base de datos.
3. Ingresa con el usuario inicial:

```text
Usuario: admin
Clave: 123Alejandro.
```

4. Ajusta el mismo token en:

```env
PORTAL_SYNC_TOKEN="cambia-este-token"
```

y en `public/portal/config.php`.

5. En la plataforma local configura:

```env
PORTAL_SYNC_URL="https://ingedev94.com/portalricardo/sync.php"
PORTAL_SYNC_TOKEN="cambia-este-token"
```

## Archivos principales

- `index.php`: mini app del portal con dashboard, registros, tarifas, graficas y usuarios.
- `login.php` / `logout.php`: acceso sencillo al portal.
- `sync.php`: endpoint que recibe tickets.
- `database.sql`: base de datos sencilla del portal.
- `config.php`: conexion y token.
