<?php
/*
 * ARQUITECTURA DEL PROYECTO TodoCamisetas
 * 
 * Estructura de carpetas:
 * 
 * /api
 *   /controllers   -> Controladores: lógica de negocio y manejo de requests.
 *   /models        -> Modelos: acceso a datos y lógica de persistencia.
 *   /routes        -> Definición de rutas y mapeo a controladores.
 *   index.php      -> Punto de entrada principal de la API.
 * /core
 *   Database.php   -> Clase para conexión a la base de datos.
 *   Request.php    -> Clase para manejo de solicitudes HTTP.
 *   Router.php     -> Clase para enrutamiento (si se usa).
 * /config
 *   database.php   -> Configuración de conexión a la base de datos.
 * /docs, /swagger  -> Documentación y especificación OpenAPI.
 * 
 * Descripción de componentes:
 * - Controladores: Reciben la solicitud, validan datos y llaman a los modelos.
 * - Modelos: Realizan operaciones CRUD sobre la base de datos.
 * - Rutas: Definen los endpoints y asocian rutas HTTP a métodos de controladores.
 * - Core: Utilidades generales como conexión a BD y manejo de requests.
 * - Config: Parámetros de configuración.
 * 
 * Relación de entidades:
 * - Cliente (1) <-> (N) Camiseta (relación por compras/ofertas)
 * - Camiseta (N) <-> (N) Talla (relación muchos a muchos)
 * - Ofertas: tabla pivote para precios especiales por cliente/camiseta.
 */

// Controlador de ejemplo para Camiseta

class CamisetaController {
    // ...lógica de negocio...
}
