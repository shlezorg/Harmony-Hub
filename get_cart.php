<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => [], 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    $response['message'] = 'Please log in to view your cart.';
    echo json_encode($response);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    $sql = "SELECT c.quantity, p.id as product_id, p.name, p.image, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = [
            'product_id' => $row['product_id'],
            'name' => $row['name'],
            'image' => $row['image'],
            'price' => $row['price'],
            'quantity' => $row['quantity']
        ];
    }
    $response['success'] = true;
    $response['data'] = $cart_items;
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>