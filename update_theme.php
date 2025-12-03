<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $theme = $data['theme'];
    $user_id = $_SESSION['user_id'];

    if ($theme === 'light' || $theme === 'dark') {
        $stmt = $pdo->prepare('UPDATE users SET theme = ? WHERE id = ?');
        $stmt->execute([$theme, $user_id]);
        $_SESSION['theme'] = $theme;
        echo json_encode(['success' => true]);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'Invalid theme']);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}
