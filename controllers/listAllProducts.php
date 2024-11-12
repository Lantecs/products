<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/database/database.php';
require_once __DIR__ . '/../src/class/user.php';
require_once __DIR__ . '/../src/class/product.php';

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$jwt_secret_key = $_ENV['JWT_SECRET'];

try {
    $database = new Database();
    $db = $database->connect();
    echo json_encode(['message' => 'Database connection successful']);
} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}


// Get the Authorization header from the request
$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Authorization header missing']);
    exit();
} else {
    echo json_encode(['message' => 'Authorization header received']);
}



$authHeader = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authHeader);

try {
    // Validate and decode JWT
    $decoded = JWT::decode($token, new Key($jwt_secret_key, 'HS256'));
    $user_id = $decoded->data->id;

    $product = new Product($db);
    $products = $product->getAllProducts();

    if ($products) {
        echo json_encode($products);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No products found']);
    }



} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['message' => 'Access denied', 'error' => $e->getMessage()]);
}


