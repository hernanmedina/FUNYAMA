# FUNYAMA

Plataforma web de administración de cursos y oferta de servicios educativos de la Fundación YAMA.

## Descripción

FUNYAMA es una plataforma de aprendizaje en línea construida con Laravel. Permite a los administradores gestionar cursos, estudiantes, eventos, certificados, inscripciones y contenido (blog/noticias); a los estudiantes inscribirse en cursos, seguir su progreso y consultar sus certificados; y a los instructores enseñar y calificar a los estudiantes.

## Tecnologías

- **Backend:** PHP 8.2+ · Laravel 12
- **Autenticación:** Laravel Fortify + Jetstream
- **Frontend:** Livewire 3 + Blade + Tailwind CSS 3
- **Build:** Vite
- **Base de datos:** MySQL
- **Exportación:** maatwebsite/excel (Excel / CSV)
- **Tests:** PHPUnit 11

## Roles de usuario

| Rol | Descripción |
|-----|-------------|
| `admin` | Gestiona cursos, estudiantes, eventos, certificados, solicitudes, pagos y contenido |
| `estu` (Estudiante) | Se inscribe en cursos, sigue su progreso y consulta sus certificados |
| `instructor` | Enseña y califica a los estudiantes en los cursos asignados |

## Arquitectura

- Las **rutas** en `routes/web.php` montan componentes **Livewire** (full-page), protegidas por el middleware de roles (`RoleMiddleware`) registrado en `bootstrap/app.php`.
- La **autorización** se gestiona mediante **Laravel Policies** (`app/Policies/`), registradas en `App\Providers\AuthServiceProvider`.
- La **lógica de negocio** está extraída a **Actions** (`app/Actions/`) y **Services** (`app/Services/`).
- Las **exportaciones** usan `FromQuery` (streaming) para no cargar todos los datos en memoria (`app/Exports/`).

La arquitectura está documentada con diagramas **C4** y de **componentes** en el directorio `mardown/`.

## Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 20 y npm
- MySQL

## Instalación

```bash
# 1. Instalar dependencias
composer install
npm install

# 2. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 3. Configurar la base de datos en .env y migrar
php artisan migrate

# 4. Compilar los assets
npm run build
```

## Uso en desarrollo

```bash
composer run dev
```

Este comando levanta el servidor, la cola, los logs (Pail) y Vite en modo desarrollo.

## Tests

```bash
php artisan test --compact
```

## Despliegue

El proyecto se despliega en **Azure App Service** mediante el workflow de GitHub Actions (`.github/workflows/main_funyama.yml`).
