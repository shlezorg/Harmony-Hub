<?php
include 'connection.php';

$result = $conn->query("SELECT id, name, image, price FROM products");

while ($row = $result->fetch_assoc()) {
    echo '<div class="pro">';
    echo '<img src="'.htmlspecialchars($row['image']).'">';
    echo '<h5>'.htmlspecialchars($row['name']).'</h5>';
    echo '<h4>$'.number_format($row['price'],2).'</h4>';
    echo '<a href="#" class="add-to-cart" data-product-id="'.$row['id'].'">Add to cart</a>';
    echo '</div>';
}
?>
