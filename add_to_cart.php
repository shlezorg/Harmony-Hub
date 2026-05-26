<?php
session_start();
include 'connection.php';

header('Content-Type: application/json'); // Use JSON for better error handling

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    $response['message'] = 'Please log in to add items to your cart.';
    echo json_encode($response);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

error_log("add_to_cart: user_id=$user_id, product_id=$product_id, quantity=$quantity");

if ($user_id <= 0 || $product_id <= 0) {
    http_response_code(400);
    $response['message'] = 'Invalid user_id or product_id.';
    echo json_encode($response);
    exit;
}

try {
    // Check if product exists
    $productCheck = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $productCheck->bind_param('i', $product_id);
    $productCheck->execute();
    if ($productCheck->get_result()->num_rows === 0) {
        http_response_code(400);
        $response['message'] = "Product ID $product_id does not exist.";
        echo json_encode($response);
        exit;
    }
    $productCheck->close();

    // Check if item is already in cart
    $check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param('ii', $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQty = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update->bind_param('ii', $newQty, $row['id']);
        if ($update->execute()) {
            $response['success'] = true;
            $response['message'] = 'Updated existing cart item.';
        } else {
            http_response_code(500);
            $response['message'] = 'Error updating cart: ' . $conn->error;
        }
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param('iii', $user_id, $product_id, $quantity);
        if ($insert->execute()) {
            $response['success'] = true;
            $response['message'] = 'Added new cart item.';
        } else {
            http_response_code(500);
            $response['message'] = 'Error adding to cart: ' . $conn->error;
        }
        $insert->close();
    }
    $check->close();
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>