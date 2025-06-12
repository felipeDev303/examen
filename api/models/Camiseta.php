<?php
// Modelo de Camiseta
// Funcionalidades:
// - CRUD completo (getAll, findById, create, update, delete)
// - Relación con tallas (JOIN con camiseta_tallas y tallas)
// - Lógica de precio final según cliente y ofertas (findByIdForClient)
// - Validaciones de existencia y manejo de errores

require_once __DIR__ . '/../../core/Database.php';

// Modelo de ejemplo para Camiseta
class Camiseta {
    public static function getAll() {
        $db = Database::getConnection();
        $sql = "SELECT c.*, GROUP_CONCAT(t.nombre ORDER BY t.id) AS tallas
                FROM camisetas c
                LEFT JOIN camiseta_tallas ct ON c.id = ct.camiseta_id
                LEFT JOIN tallas t ON ct.talla_id = t.id
                GROUP BY c.id";
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll();
        foreach ($result as &$row) {
            $row['tallas'] = $row['tallas'] ? explode(',', $row['tallas']) : [];
        }
        return $result;
    }

    public static function findById($id) {
        $db = Database::getConnection();
        $sql = "SELECT c.*, GROUP_CONCAT(t.nombre ORDER BY t.id) AS tallas
                FROM camisetas c
                LEFT JOIN camiseta_tallas ct ON c.id = ct.camiseta_id
                LEFT JOIN tallas t ON ct.talla_id = t.id
                WHERE c.id = ?
                GROUP BY c.id";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $row['tallas'] = $row['tallas'] ? explode(',', $row['tallas']) : [];
        }
        return $row;
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO camisetas (codigo_producto, titulo, club, pais, tipo, color, precio, detalles) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['codigo_producto'],
            $data['titulo'],
            $data['club'] ?? null,
            $data['pais'] ?? null,
            $data['tipo'],
            $data['color'] ?? null,
            $data['precio'],
            $data['detalles'] ?? null
        ]);
        return self::findById($db->lastInsertId());
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE camisetas SET codigo_producto=?, titulo=?, club=?, pais=?, tipo=?, color=?, precio=?, detalles=? WHERE id=?");
        $stmt->execute([
            $data['codigo_producto'],
            $data['titulo'],
            $data['club'] ?? null,
            $data['pais'] ?? null,
            $data['tipo'],
            $data['color'] ?? null,
            $data['precio'],
            $data['detalles'] ?? null,
            $id
        ]);
        return self::findById($id);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM camisetas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function findByIdForClient($camisetaId, $clienteId) {
        $db = Database::getConnection();

        // Obtener datos de la camiseta
        $sql = "SELECT c.*, GROUP_CONCAT(t.nombre ORDER BY t.id) AS tallas
                FROM camisetas c
                LEFT JOIN camiseta_tallas ct ON c.id = ct.camiseta_id
                LEFT JOIN tallas t ON ct.talla_id = t.id
                WHERE c.id = ?
                GROUP BY c.id";
        $stmt = $db->prepare($sql);
        $stmt->execute([$camisetaId]);
        $camiseta = $stmt->fetch();
        if (!$camiseta) return null;
        $camiseta['tallas'] = $camiseta['tallas'] ? explode(',', $camiseta['tallas']) : [];

        // Si no se pasa cliente_id, devolver precio base
        if (!$clienteId) {
            $camiseta['precio_final'] = $camiseta['precio'];
            $camiseta['descuento_aplicado'] = 0;
            $camiseta['tipo_precio'] = 'base';
            return $camiseta;
        }

        // Obtener datos del cliente
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$clienteId]);
        $cliente = $stmt->fetch();
        if (!$cliente) {
            $camiseta['precio_final'] = $camiseta['precio'];
            $camiseta['descuento_aplicado'] = 0;
            $camiseta['tipo_precio'] = 'base';
            return $camiseta;
        }

        // Calcular precio con descuento general
        $precio_base = $camiseta['precio'];
        $descuento = floatval($cliente['porcentaje_oferta']);
        $precio_descuento = round($precio_base * (1 - $descuento / 100));

        // Buscar oferta específica
        $stmt = $db->prepare("SELECT precio_oferta FROM ofertas WHERE camiseta_id = ? AND cliente_id = ?");
        $stmt->execute([$camisetaId, $clienteId]);
        $oferta = $stmt->fetch();

        if ($oferta && $cliente['categoria'] === 'Preferencial') {
            $camiseta['precio_final'] = intval($oferta['precio_oferta']);
            $camiseta['descuento_aplicado'] = 0;
            $camiseta['tipo_precio'] = 'oferta';
        } else {
            $camiseta['precio_final'] = $precio_descuento;
            $camiseta['descuento_aplicado'] = $descuento;
            $camiseta['tipo_precio'] = 'descuento_general';
        }
        return $camiseta;
    }
}
