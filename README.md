# **TodoCamisetas API**

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white) ![Postman](https://img.shields.io/badge/Postman-FF6C37?style=for-the-badge&logo=postman&logoColor=white) ![OpenAPI](https://img.shields.io/badge/OpenAPI-6BA539?style=for-the-badge&logo=openapi-initiative&logoColor=white)

API RESTful desarrollada en PHP puro para la gestión de inventario y clientes B2B de **TodoCamisetas**, un proveedor mayorista de camisetas de fútbol. Esta API sirve como la columna vertebral del sistema, manejando productos, clientes, precios y ofertas especiales.

## Índice

- [Arquitectura del Sistema](#arquitectura-del-sistema)
- [Características Principales](#características-principales)
- [Modelo de Datos](#modelo-de-datos)
- [Guía de Instalación y Uso](#guía-de-instalación-y-uso)
  - [Requisitos Previos](#requisitos-previos)
  - [Configuración](#configuración)
  - [Pruebas con Postman y cURL](#pruebas-con-postman-y-curl)
- [Documentación de la API](#documentación-de-la-api)
- [Futuras Mejoras y Características](#futuras-mejoras-y-características)
- [Recomendaciones para el Frontend](#recomendaciones-para-el-frontend)

---

## Arquitectura del Sistema

La API sigue una arquitectura de software limpia y desacoplada, inspirada en el patrón **Modelo-Vista-Controlador (MVC)**, pero adaptada para una API sin vistas directas. El objetivo es la separación de responsabilidades para facilitar el mantenimiento y la escalabilidad.

- **Punto de Entrada Único (Front Controller):** Todas las solicitudes son redirigidas a `api/index.php` gracias a un archivo `.htaccess`. Este script inicializa la aplicación, procesa la URI y despacha la solicitud al router.
- **Router (`/core/Router.php`):** Componente central que mapea las rutas de la API (ej. `/camisetas/{id}`) y los métodos HTTP (`GET`, `POST`) a la acción correspondiente en un controlador. Utiliza expresiones regulares para un enrutamiento flexible y potente.
- **Controladores (`/api/controllers`):** Orquestan el flujo de la solicitud. Reciben la petición, validan los datos de entrada (si es necesario), invocan a los modelos para interactuar con la base de datos y finalmente, construyen y devuelven la respuesta en formato JSON.
- **Modelos (`/api/models`):** Encapsulan toda la lógica de acceso y manipulación de datos. Son la única capa que interactúa directamente con la base de datos, realizando operaciones CRUD y aplicando reglas de negocio complejas (como el cálculo de precios finales).
- **Configuración (`/config`):** Archivos de configuración centralizados (ej. `database.php`) para desacoplar parámetros sensibles o variables del entorno del código de la aplicación.

```
/
├── api/
│   ├── controllers/      # Lógica de negocio
│   ├── models/           # Acceso a datos
│   └── index.php         # Punto de entrada y Router
├── config/               # Configuración de BD
├── core/                 # Clases base (Router, Database)
├── .htaccess             # Reglas de reescritura de URL
└── swagger.yaml          # Especificación OpenAPI
```

## Características Principales

- [x] **CRUD Completo:** Gestión total sobre los recursos de `Camisetas`, `Clientes` y `Tallas`.
- [x] **Relaciones Complejas:** Implementación de una relación muchos a muchos entre camisetas y tallas a través de una tabla pivote.
- [x] **Lógica de Precios Dinámica:** El precio final de un producto se calcula en tiempo real basándose en la categoría del cliente, descuentos generales y ofertas específicas por producto.
- [x] **Validaciones de Negocio:** Reglas de integridad como la prevención de eliminar clientes con ofertas activas.
- [x] **API RESTful Pura:** Construida sin frameworks para un control total y un rendimiento óptimo.
- [x] **Documentación Profesional:** Especificación completa y detallada bajo el estándar OpenAPI 3.0.

## Modelo de Datos

El sistema se basa en un modelo relacional que conecta clientes, productos y ofertas.

- **`clientes`**: Almacena los datos de los clientes B2B, incluyendo su categoría y descuento general.
- **`camisetas`**: Catálogo de productos.
- **`tallas`**: Tabla normalizada para las tallas disponibles.
- **`camiseta_tallas`**: Tabla pivote que gestiona la relación N:M entre camisetas y tallas.
- **`ofertas`**: Tabla clave que vincula un `cliente`, una `camiseta` y un `precio_oferta` especial, permitiendo una gestión de precios granular.

---

## Guía de Instalación y Uso

### Requisitos Previos

- Un entorno de servidor local como [XAMPP](https://www.apachefriends.org/index.html) (Apache, MySQL, PHP).
- PHP 7.4 o superior.
- Un cliente API como [Postman](https://www.postman.com/downloads/).
- Base de datos y tablas creadas según el script SQL proporcionado.

### Configuración

1.  Clona o descarga este repositorio en tu carpeta de proyectos web (ej. `C:\xampp\htdocs\examen`).
2.  Verifica que las credenciales de la base de datos en `config/database.php` sean correctas.
3.  Asegúrate de que el archivo `.htaccess` esté presente en la carpeta `/api` para habilitar las URLs amigables.
4.  Inicia los servicios de Apache y MySQL desde el panel de control de XAMPP.

### Pruebas con Postman y cURL

La API estará disponible en `http://localhost/examen/api/`.

**Usando Postman:**
La forma más sencilla es importar el archivo `swagger.yaml` directamente en Postman. Esto generará automáticamente una colección con todos los endpoints, sus parámetros y cuerpos de ejemplo listos para usar.

**Usando cURL:**

- **Listar todas las camisetas:**
  ```bash
  curl http://localhost/examen/api/camisetas
  ```
- **Crear un nuevo cliente:**
  ```bash
  curl -X POST -H "Content-Type: application/json" -d '{"nombre_comercial":"90minutos","rut":"12345678-9","categoria":"Preferencial"}' http://localhost/examen/api/clientes
  ```
- **Obtener precio final de una camiseta para un cliente:**
  ```bash
  curl http://localhost/examen/api/camisetas/1?cliente_id=1
  ```

---

## Documentación de la API

La documentación completa de todos los endpoints, incluyendo métodos, parámetros, cuerpos de solicitud y esquemas de respuesta, está disponible en el archivo `swagger.yaml`.

Puedes visualizar esta documentación de forma interactiva usando herramientas como el [Swagger Editor](https://editor.swagger.io/) o [Swagger UI](https://swagger.io/tools/swagger-ui/).

---

## Futuras Mejoras y Características

La API actual es una base sólida. Las próximas iteraciones podrían incluir:

- **Autenticación y Autorización:** Implementar un sistema de seguridad basado en **JWT (JSON Web Tokens)**.
  - Un endpoint `/login` que devuelva un token.
  - Middleware para proteger rutas, asegurando que solo usuarios autenticados puedan realizar ciertas acciones (ej. solo un admin puede crear camisetas).
- **Paginación:** Para endpoints que devuelven listas largas (como `/camisetas`), implementar paginación con parámetros `?page=1&limit=20` para mejorar el rendimiento.
- **Búsqueda y Filtrado Avanzado:** Permitir filtrar camisetas por club, país, tipo o rango de precios (ej. `/camisetas?club=Real+Madrid&precio_max=50000`).
- **Gestión de Pedidos:** Añadir un nuevo recurso `/pedidos` para que los clientes puedan crear, ver y gestionar sus órdenes de compra.
- **Roles y Permisos:** Introducir un sistema de roles (ej. `admin`, `cliente`) para un control de acceso más granular.

## Recomendaciones para el Frontend

Para el equipo de frontend que consumirá esta API, se recomienda:

- **Framework Moderno:** Utilizar un framework de JavaScript como **React, Vue o Svelte** para construir una Single-Page Application (SPA) dinámica y reactiva.
- **Gestión de Estado Global:** Implementar una librería de gestión de estado (como Redux, Pinia o Zustand) para manejar datos compartidos en la aplicación, como la información del usuario autenticado o el carrito de compras.
- **Llamadas Asíncronas:** Usar `fetch` o librerías como `axios` para manejar las llamadas a la API de forma asíncrona, gestionando estados de carga (`loading`) y errores de forma elegante en la UI.
- **Interfaz de Usuario:**
  - **Dashboard de Cliente:** Una vez implementado el login, cada cliente debería tener un dashboard donde pueda ver su historial de pedidos y precios personalizados.
  - **Catálogo de Productos:** Una vista de catálogo con filtros dinámicos que consuma el endpoint `/camisetas` y sus futuras mejoras de filtrado.
  - **Página de Detalles del Producto:** Una vista que consuma `GET /camisetas/{id}?cliente_id={id_cliente}` para mostrar el precio final correcto al usuario logueado.
- **Manejo de Tokens:** Al implementar la autenticación JWT, el frontend deberá almacenar el token de forma segura (en `localStorage` o `sessionStorage`) y enviarlo en la cabecera `Authorization: Bearer <token>` en cada solicitud a rutas protegidas.
