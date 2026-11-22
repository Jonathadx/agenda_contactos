# Agenda Personal de Contactos (PHP y MySQL)

Aplicación web desarrollada en **PHP (PDO)** y **MySQL** para la gestión personal de contactos con autenticación de usuarios, aislamiento de datos por sesión, categorías, favoritos, búsqueda y ordenamiento, desplegada en un hosting gratuito.

---

## 1. URL Pública

* **Sistema en línea:** [http://agenda-contactos-jb.site.je](http://agenda-contactos-jb.site.je)
* **Panel principal:** [http://agenda-contactos-jb.site.je/dashboard.php](http://agenda-contactos-jb.site.je/dashboard.php)

---

## 2. Capturas de Pantalla

*(Sube tus capturas a una carpeta `capturas/` o a la raíz del repositorio y actualiza el enlace si usas otro nombre de archivo)*

### Panel Principal y Contactos
![Panel Principal de la Agenda](capturas/dashboard.png)

### Gestión / Edición de Contactos
![Gestión de Contactos](capturas/contactos.png)

---

## 3. Tecnologías Utilizadas

* **Backend:** PHP con PDO (consultas preparadas, `password_hash()`, `password_verify()` y manejo de sesiones con `session_start()`)
* **Base de Datos:** MySQL / MariaDB (`utf8mb4_general_ci`)
* **Frontend:** HTML5 y CSS3 (`estilos.css`)
* **Entorno Local:** XAMPP (Apache, MySQL y phpMyAdmin)
* **Hosting Gratuito:** InfinityFree
* **Control de Versiones:** Git y GitHub

---

## 4. Instrucciones para Instalar el Proyecto Localmente

1. **Obtener el código del repositorio:**
   Clona el repositorio o descarga el ZIP y colócalo dentro de la carpeta `htdocs` de XAMPP (por ejemplo, `C:\xampp\htdocs\agenda_contactos`):
   ```bash
   git clone [https://github.com/Jonathadx/agenda_contactos.git](https://github.com/Jonathadx/agenda_contactos.git)
