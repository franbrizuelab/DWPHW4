<?php
// index.php - Login page
session_start();
require_once 'config/database.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Retrieve form data if available from a previous error
$form_data = [];
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']); // Clear it after retrieving
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Prepare statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT id, username, password, theme FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['theme'] = $user['theme'];
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Invalid username or password"; // Store error in session
        $_SESSION['form_data'] = $_POST; // Save form data
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?php echo $_SESSION['theme'] ?? 'light'; ?>">
    <div id="flash-popup-backdrop" class="flash-popup-backdrop"></div>
    <div id="flash-popup" class="flash-popup">
        <p id="flash-popup-message"></p>
    </div>

    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<script>const flashMessage = ' . json_encode($_SESSION['error_message']) . ';</script>';
        unset($_SESSION['error_message']);
    }
    ?>
    <div class="container">
        <div class="login-form">
            <h1>To-Do List Login</h1>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="off">
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>