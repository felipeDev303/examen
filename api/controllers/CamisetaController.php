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
 *
 * FUNCIONALIDADES IMPLEMENTADAS:
 * - CRUD completo para camisetas, clientes y tallas.
 * - Relación muchos a muchos entre camisetas y tallas.
 * - Lógica de precios finales según cliente y ofertas.
 * - Validaciones para evitar eliminar clientes con ofertas activas.
 * - Enrutamiento en PHP puro usando expresiones regulares.
 * - Documentación OpenAPI (swagger.yaml) para todos los endpoints.
 * - Respuestas JSON y cabeceras correctas para consumo por frontend.
 */
 
// Controlador de ejemplo para Camiseta
require_once __DIR__ . '/../models/Camiseta.php';

class CamisetaController {
    private static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($data);
        exit;
    }

    public static function index() {
        self::jsonResponse(Camiseta::getAll());
    }

    public static function show($id) {
        $cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : null;
        if ($cliente_id) {
            $camiseta = Camiseta::findByIdForClient($id, $cliente_id);
        } else {
            $camiseta = Camiseta::findById($id);
        }
        if ($camiseta) {
            self::jsonResponse($camiseta);
        } else {
            self::jsonResponse(["error" => "Camiseta no encontrada"], 404);
        }
    }

    public static function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            self::jsonResponse(["error" => "Datos de entrada inválidos"], 400);
        }
        $nueva = Camiseta::create($input);
        self::jsonResponse($nueva, 201);
    }

    public static function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            self::jsonResponse(["error" => "Datos de entrada inválidos"], 400);
        }
        $actualizada = Camiseta::update($id, $input);
        if ($actualizada) {
            self::jsonResponse($actualizada);
        } else {
            self::jsonResponse(["error" => "Camiseta no encontrada para actualizar"], 404);
        }
    }

    public static function destroy($id) {
        $ok = Camiseta::delete($id);
        if ($ok) {
            self::jsonResponse(null, 204);
        } else {
            self::jsonResponse(["error" => "No se pudo eliminar la camiseta"], 404);
        }
    }
}
