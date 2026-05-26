<?php
include 'connection.php';
$user_id = 1; // or use logged-in user id

$sql = "SELECT c.quantity, p.name, p.image, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<table>';
echo '<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>';
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    echo '<tr>';
    echo '<td><img src="'.htmlspecialchars($row['image']).'">'.htmlspecialchars($row['name']).'</td>';
    echo '<td>$'.number_format($row['price'],2).'</td>';
    echo '<td>'.$row['quantity'].'</td>';
    echo '<td>$'.number_format($subtotal,2).'</td>';
    echo '</tr>';
}
echo '</table>';
?>
