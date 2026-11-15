# Agenda Personal de Contactos con Autenticación en PHP

Aplicación web desarrollada en PHP y MySQL que permite a los usuarios registrarse, iniciar sesión de forma segura y administrar una agenda de contactos personal e independiente con funciones avanzadas de filtrado y estadísticas.

## ✨ Características Principales
- **Sistema de Autenticación Seguro:** Registro de nuevos usuarios con validación de correo único, cifrado de contraseñas mediante `password_hash()` y verificación con `password_verify()`.
- **Gestión de Sesiones:** Control estricto de acceso mediante variables de sesión para proteger el panel principal y las páginas de edición/eliminación.
- **CRUD Completo de Contactos:** Permite registrar, consultar, editar y eliminar contactos de manera intuitiva.
- **Aislamiento de Datos por Usuario:** Cada usuario accede exclusivamente a su propia información. Se implementó una capa de seguridad estricta para impedir el acceso no autorizado a contactos ajenos mediante la manipulación manual de la URL.
- **Buscador en Tiempo Real (Plus Frontend):** Barra de búsqueda interactiva con JavaScript que filtra los contactos al instante sin necesidad de recargar la página.
- **Panel de Estadísticas Rápidas (Plus Backend):** Tarjetas informativas en el panel principal que calculan en tiempo real el total de contactos y las ciudades diferentes mediante consultas agregadas de SQL (`COUNT` y `DISTINCT`).

## 🛠️ Tecnologías Utilizadas
- **Backend:** PHP (con uso de PDO para consultas preparadas y seguras).
- **Base de Datos:** MySQL / MariaDB.
- **Frontend:** HTML5 semántico, CSS3 (Diseño responsivo con hoja de estilos externa y excelente contraste) y JavaScript.
- **Entorno de desarrollo:** XAMPP (Apache).

## 📋 Requisitos para su ejecución
- Servidor local compatible con PHP (versión 8.0 o superior recomendada).
- Gestor de base de datos MySQL (como phpMyAdmin incluido en XAMPP).

## 🚀 Pasos para instalar y ejecutar la aplicación
1. Clonar o descargar este repositorio en la carpeta `htdocs` de tu servidor local (por ejemplo, `C:/xampp/htdocs/agenda_contactos/`).
2. Encender los servicios de **Apache** y **MySQL** desde el panel de control de XAMPP.
3. Ingresar a `http://localhost/phpmyadmin/` y crear una nueva base de datos con el nombre: `agenda_db`.
4. Importar o ejecutar el script SQL con las tablas `usuarios` y `contactos`.
5. Abrir el navegador web y acceder a la URL:
   `http://localhost/agenda_contactos/`