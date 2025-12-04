<?php
session_start();
require_once 'config/database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => 'Invalid action'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $success = $stmt->execute([$task_id, $user_id]);
        if ($success) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => $stmt->errorInfo()];
        }
    }

    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        $pdo->prepare("DELETE FROM tasks WHERE category_id = ? AND user_id = ?")->execute([$category_id, $user_id]);
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
        $success = $stmt->execute([$category_id, $user_id]);
        if ($success) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => $stmt->errorInfo()];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>