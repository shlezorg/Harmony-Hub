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
        echo json_encode(['success' => false, 'message' => 'Please log in to view blogs.']);
        exit;
    }

    $page = isset($_GET['page']) ? max(1, filter_var($_GET['page'], FILTER_VALIDATE_INT)) : 1;
    $perPage = 5; // Posts per page
    $offset = ($page - 1) * $perPage;

    $stmt = $conn->prepare("SELECT id, title, excerpt, image, date_posted FROM blogs ORDER BY date_posted DESC LIMIT :offset, :perPage");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT COUNT(*) FROM blogs");
    $totalPosts = $stmt->fetchColumn();
    $totalPages = ceil($totalPosts / $perPage);

    echo json_encode([
        'success' => true,
        'data' => $blogs,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPosts' => $totalPosts
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>