<?php
// config/database.php
// Database connection configuration

 $host = 'localhost';
 $dbname = 'todo_list';
 $username = 'cvml';
 $password = 'dwpcvml2025';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>