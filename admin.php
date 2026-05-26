<?php
include 'connection.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $result = $conn->query("SELECT id, name, image, price FROM products");
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode($products);
} elseif ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['name']) || !isset($data['price']) || !isset($data['image'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing required fields"]);
        exit;
    }
    $name = $conn->real_escape_string($data['name']);
    $image = $conn->real_escape_string($data['image']);
    $price = floatval($data['price']);
    $query = "INSERT INTO products (name, image, price) VALUES ('$name', '$image', $price)";
    if ($conn->query($query)) {
        echo json_encode(["message" => "Product added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error adding product: " . $conn->error]);
    }
} elseif ($method == 'DELETE') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing product ID"]);
        exit;
    }
    $id = $conn->real_escape_string($_GET['id']);
    $query = "DELETE FROM products WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(["message" => "Product deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error deleting product: " . $conn->error]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}

$conn->close();
?>