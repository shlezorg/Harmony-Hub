<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "music_site";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please log in to view this blog post.']);
        exit;
    }

    if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        echo json_encode(['success' => false, 'message' => 'Invalid blog post ID.']);
        exit;
    }

    $blogId = $_GET['id'];
    $stmt = $conn->prepare("SELECT id, title, content, image, date_posted FROM blogs WHERE id = :id");
    $stmt->execute(['id' => $blogId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Blog post not found.']);
        exit;
    }

    echo json_encode(['success' => true, 'data' => $post]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>