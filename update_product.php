<?php
include 'connection.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    if (!isset($_GET['id']) || !isset($data['name']) || !isset($data['price']) || !isset($data['image'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing required fields or ID"]);
        exit;
    }
    $id = $conn->real_escape_string($_GET['id']);
    $name = $conn->real_escape_string($data['name']);
    $image = $conn->real_escape_string($data['image']);
    $price = floatval($data['price']);
    $query = "UPDATE products SET name = '$name', image = '$image', price = $price WHERE id = $id";
    if ($conn->query($query)) {
        echo json_encode(["message" => "Product updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error updating product: " . $conn->error]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}

$conn->close();
?>