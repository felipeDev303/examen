<?php
require_once __DIR__ . '/../../core/Database.php';

class Talla {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM tallas");
        return $stmt->fetchAll();
    }

    public static function findById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM tallas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($nombre) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO tallas (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
        return self::findById($db->lastInsertId());
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM tallas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Métodos para la tabla pivote camiseta_tallas
    public static function addTallaToCamiseta($camiseta_id, $talla_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT IGNORE INTO camiseta_tallas (camiseta_id, talla_id) VALUES (?, ?)");
        return $stmt->execute([$camiseta_id, $talla_id]);
    }

    public static function removeTallaFromCamiseta($camiseta_id, $talla_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM camiseta_tallas WHERE camiseta_id = ? AND talla_id = ?");
        return $stmt->execute([$camiseta_id, $talla_id]);
    }

    public static function getTallasByCamiseta($camiseta_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT t.* FROM tallas t INNER JOIN camiseta_tallas ct ON t.id = ct.talla_id WHERE ct.camiseta_id = ?");
        $stmt->execute([$camiseta_id]);
        return $stmt->fetchAll();
    }
}
