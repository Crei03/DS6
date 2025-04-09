# Proyecto FormoAntro - Sistema de Gestión de Empleados

## Descripción General
FormoAntro es un sistema de gestión de empleados desarrollado para una pequeña empresa. El sistema proporciona funcionalidades CRUD (Crear, Leer, Actualizar y Eliminar) para la administración de empleados, incluyendo un sistema de autenticación de usuarios con diferentes niveles de acceso.

## Funcionalidades Principales
- **Autenticación de Usuarios**: Sistema de login seguro con diferentes roles (Administrador y Empleado)
- **Gestión de Empleados**: 
  - Ver lista de empleados
  - Agregar nuevos empleados
  - Actualizar datos de empleados existentes
  - Eliminar empleados del sistema

## Estructura del Proyecto
```
FormoAntro/
├── config/           # Configuraciones de la aplicación y conexión a la BD
├── modules/          # Módulos funcionales del sistema
│   ├── auth/         # Módulo de autenticación
│   ├── employees/    # Módulo de gestión de empleados
│   ├── admin/        # Funcionalidades exclusivas para administradores
│   └── user/         # Funcionalidades para usuarios regulares
├── assets/           # Recursos estáticos (CSS, JS, imágenes)
├── includes/         # Archivos compartidos (headers, footers, etc.)
├── lib/              # Bibliotecas de utilidades
├── index.php         # Punto de entrada principal
├── main.php          # Controlador principal
└── bd.sql            # Estructura de la base de datos
```

## Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx)
- XAMPP (para desarrollo local)

## Base de Datos
El sistema utiliza una base de datos relacional con las siguientes tablas principales:
- **empleados**: Almacena la información completa de cada empleado
- **usuarios**: Gestiona las credenciales y permisos de acceso
- **departamento**: Catálogo de departamentos de la empresa
- **cargo**: Posiciones disponibles dentro de la empresa
- **provincia/distrito/corregimiento**: Información de ubicación geográfica

## Instalación y Configuración
1. Clone el repositorio en su directorio web local (ej. `c:\xampp\htdocs\`)
2. Importe el archivo `bd.sql` en su servidor MySQL para crear la estructura de la base de datos
3. Configure los parámetros de conexión en el archivo de configuración
4. Acceda a la aplicación a través de su navegador web (ej. `http://localhost/DS6/`)

## Roles y Permisos
El sistema cuenta con dos tipos de usuarios:

### Administrador
- Acceso completo al sistema
- Puede gestionar todos los empleados (crear, ver, editar, eliminar)
- Administra usuarios y permisos

### Empleado
- Acceso restringido
- Solo puede ver y editar su propia información

## Guía de Uso

### Autenticación
1. Acceda a la página de inicio
2. Ingrese sus credenciales (usuario y contraseña)
3. El sistema lo redirigirá al panel correspondiente según su rol

### Gestión de Empleados
1. Navegar a la sección "Empleados"
2. Utilizar las opciones disponibles para administrar registros:
   - **Ver**: Listar todos los empleados o buscar por diferentes criterios
   - **Agregar**: Completar el formulario con los datos del nuevo empleado
   - **Editar**: Seleccionar un empleado y modificar sus datos
   - **Eliminar**: Seleccionar un empleado y confirmar su eliminación

## Diagrama Entidad-Relación
La base de datos está diseñada con las siguientes relaciones principales:
- Un empleado pertenece a un departamento
- Un empleado tiene un cargo específico
- Un usuario está asociado a un empleado
- Un empleado tiene datos de ubicación (provincia, distrito, corregimiento)

## En Desarrollo
- Módulo de reportes y estadísticas
- Exportación de datos en diferentes formatos
- Panel de control con métricas clave
- Sistema de notificaciones

## Contacto y Soporte
Para reportar problemas o solicitar asistencia, contacte al equipo de desarrollo.

---
© 2023 FormoAntro. Todos los derechos reservados.