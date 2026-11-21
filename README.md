# Agenda Personal de Contactos (PHP y MySQL)

Aplicación web desarrollada en **PHP (PDO)** y **MySQL** para la gestión segura de contactos personales con autenticación de usuarios, aislamiento de datos por sesión, validación de formularios y búsqueda instantánea.

## Características Principales

* **Autenticación y Seguridad:**
  * Registro e inicio de sesión de usuarios con contraseñas cifradas mediante `password_hash()` y verificadas con `password_verify()`.
  * Control estricto de sesiones (`session_start()`) en todas las rutas protegidas.
  * Prevención de inyección SQL mediante consultas preparadas con **PDO**.
  * Validación de campos y formato de correo electrónico (`FILTER_VALIDATE_EMAIL`) en el servidor.
  * Eliminación segura de registros mediante peticiones `POST` en lugar de `GET`.
* **Gestión de Contactos (CRUD Completo):**
  * Creación, consulta, edición y eliminación de contactos vinculados exclusivamente al `id_usuario` autenticado.
* **Funcionalidades Adicionales:**
  * Buscador instantáneo en tiempo real (JavaScript) por nombre, teléfono, correo o ciudad.
  * Panel de estadísticas rápidas (total de contactos y ciudades registradas).

## Requisitos Previos

* Servidor local con **PHP 8.0 o superior** y **MySQL / MariaDB** (ej. **XAMPP**, WAMP o Laragon).
* Navegador web moderno.

## Instrucciones de Instalación

1. **Clonar o descargar el repositorio:**
   Descarga los archivos del proyecto y copia la carpeta `agenda_contactos` dentro del directorio raíz de tu servidor web:
   * En XAMPP (Windows): `C:\xampp\htdocs\agenda_contactos`

2. **Iniciar servicios:**
   Abre el panel de control de **XAMPP** e inicia los módulos **Apache** y **MySQL**.

3. **Crear e importar la base de datos:**
   * Abre tu navegador y accede a `http://localhost/phpmyadmin`.
   * Crea una nueva base de datos llamada `agenda_db` (cotejamiento `utf8mb4_general_ci`).
   * Selecciona la base de datos `agenda_db`, ve a la pestaña **Importar**, selecciona el archivo `agenda_db.sql` incluido en este repositorio y haz clic en **Continuar**.

4. **Verificar conexión a la base de datos:**
   Abre el archivo `conexion.php` y confirma que las credenciales coincidan con tu servidor local (por defecto en XAMPP: usuario `root` y contraseña vacía `""`).

5. **Ejecutar la aplicación:**
   Abre tu navegador e ingresa a:
   ```text
   http://localhost/agenda_contactos/index.php
