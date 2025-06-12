<?php
require_once __DIR__ . '/../models/Talla.php';

class TallaController {
    private static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($data);
        exit;
    }

    public static function index() {
        self::jsonResponse(Talla::getAll());
    }

    public static function show($id) {
        $talla = Talla::findById($id);
        if ($talla) {
            self::jsonResponse($talla);
        } else {
            self::jsonResponse(["error" => "Talla no encontrada"], 404);
        }
    }

    public static function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['nombre'])) {
            self::jsonResponse(["error" => "nombre requerido"], 400);
        }
        $nueva = Talla::create($input['nombre']);
        self::jsonResponse($nueva, 201);
    }

    public static function destroy($id) {
        $ok = Talla::delete($id);
        if ($ok) {
            self::jsonResponse(null, 204);
        } else {
            self::jsonResponse(["error" => "No se pudo eliminar la talla"], 404);
        }
    }

    public static function addTallaToCamiseta($camiseta_id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['talla_id'])) {
            self::jsonResponse(["error" => "talla_id requerido"], 400);
        }
        $ok = Talla::addTallaToCamiseta($camiseta_id, $input['talla_id']);
        if ($ok) {
            self::jsonResponse(["message" => "Talla asociada"]);
        } else {
            self::jsonResponse(["error" => "No se pudo asociar"], 400);
        }
    }

    public static function removeTallaFromCamiseta($camiseta_id, $talla_id) {
        $ok = Talla::removeTallaFromCamiseta($camiseta_id, $talla_id);
        if ($ok) {
            self::jsonResponse(null, 204);
        } else {
            self::jsonResponse(["error" => "No se pudo desasociar"], 400);
        }
    }
}
