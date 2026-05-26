<?php
session_start();
include 'connection.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: Method not allowed";
    exit;
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    http_response_code(400);
    echo "Error: Invalid user ID";
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($user_id <= 0) {
    http_response_code(401);
    echo "Error: Please log in to update cart";
    exit;
}

if ($product_id <= 0 || $quantity <= 0) {
    http_response_code(400);
    echo "Error: Invalid product ID or quantity";
    exit;
}

$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
$stmt->bind_param('iii', $quantity, $user_id, $product_id);
if ($stmt->execute()) {
    echo "Success";
} else {
    http_response_code(500);
    echo "Error: Failed to update quantity - " . $conn->error;
}

$stmt->close();
$conn->close();
?>