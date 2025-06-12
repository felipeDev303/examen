<?php
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/controllers/CamisetaController.php';
require_once __DIR__ . '/controllers/ClienteController.php';
require_once __DIR__ . '/controllers/TallaController.php';

// Ajuste de base path para el enrutamiento correcto
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/examen/api';
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
    if ($uri === '') $uri = '/';
}

// Rutas de Camisetas
Router::add('GET', '#^/camisetas/?$#', ['CamisetaController', 'index']);
Router::add('POST', '#^/camisetas/?$#', ['CamisetaController', 'store']);
Router::add('GET', '#^/camisetas/(\d+)/?$#', ['CamisetaController', 'show']);
Router::add('PUT', '#^/camisetas/(\d+)/?$#', ['CamisetaController', 'update']);
Router::add('DELETE', '#^/camisetas/(\d+)/?$#', ['CamisetaController', 'destroy']);

// Rutas de Clientes
Router::add('GET', '#^/clientes/?$#', ['ClienteController', 'index']);
Router::add('POST', '#^/clientes/?$#', ['ClienteController', 'store']);
Router::add('GET', '#^/clientes/(\d+)/?$#', ['ClienteController', 'show']);
Router::add('PUT', '#^/clientes/(\d+)/?$#', ['ClienteController', 'update']);
Router::add('DELETE', '#^/clientes/(\d+)/?$#', ['ClienteController', 'destroy']);

// Rutas de Tallas
Router::add('GET', '#^/tallas/?$#', ['TallaController', 'index']);
Router::add('POST', '#^/tallas/?$#', ['TallaController', 'store']);
Router::add('DELETE', '#^/tallas/(\d+)/?$#', ['TallaController', 'destroy']);

// Rutas de Asociación Camiseta-Talla
Router::add('POST', '#^/camisetas/(\d+)/tallas/?$#', ['TallaController', 'addTallaToCamiseta']);
Router::add('DELETE', '#^/camisetas/(\d+)/tallas/(\d+)/?$#', ['TallaController', 'removeTallaFromCamiseta']);

// Ejecutar el router con la URI procesada
Router::dispatch($uri);