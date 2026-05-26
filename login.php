<?php
session_start();
include 'connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // ADMIN LOGIN
    if ($email === "admin@admin.com" && $password === "admin") {

        $_SESSION['admin'] = true;
        $_SESSION['user_name'] = "Administrator";

        header("Location: admin.html");
        exit();
    }

    // NORMAL USER LOGIN
    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");

        if (!$stmt) {
            echo "Error preparing query: " . $conn->error;
            $conn->close();
            exit();
        }

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $user = $result->fetch_assoc();

                // VERIFY PASSWORD
                if (password_verify($password, $user['password'])) {

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];

                    header("Location: HH.html");
                    exit();

                } else {
                    echo "Invalid password.";
                }

            } else {
                echo "No user found with that email.";
            }

        } else {
            echo "Error executing query: " . $stmt->error;
        }

        $stmt->close();

    } else {
        echo "Email and password are required.";
    }
}

$conn->close();
?>