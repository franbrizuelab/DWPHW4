<?php
// dashboard.php - Main to-do list page
session_start();
require_once 'config/database.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Retrieve form data if available from a previous error
$form_data = [];
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']); // Clear it after retrieving
}

// Handle task operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new task
    if (isset($_POST['add_task'])) {
        $task_name = $_POST['task_name'];
        $deadline = $_POST['deadline'];
        $category_id = $_POST['category_id'];

        if (empty($deadline)) {
            $_SESSION['error_message'] = "Deadline is empty!";
            $_SESSION['form_data'] = $_POST;
        } else {
            $stmt = $pdo->prepare("INSERT INTO tasks (name, deadline, category_id, user_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$task_name, $deadline, $category_id, $user_id]);
        }
    }
    
    // Update task
    if (isset($_POST['update_task'])) {
        $task_id = $_POST['task_id'];
        $task_name = $_POST['task_name'];
        $deadline = $_POST['deadline'];
        $category_id = $_POST['category_id'];
        
        $stmt = $pdo->prepare("UPDATE tasks SET name = ?, deadline = ?, category_id = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$task_name, $deadline, $category_id, $task_id, $user_id]);
    }
    
    // Delete task
    if (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$task_id, $user_id]);
    }
    
    // Toggle task completion
    if (isset($_POST['toggle_task'])) {
        $task_id = $_POST['task_id'];
        $is_completed = $_POST['is_completed'] ? 0 : 1; // Toggle value
        
        $stmt = $pdo->prepare("UPDATE tasks SET is_completed = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$is_completed, $task_id, $user_id]);
    }
    
    // Add new category
    if (isset($_POST['add_category'])) {
        $category_name = $_POST['category_name'];

        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ? AND user_id = ?");
        $stmt_check->execute([$category_name, $user_id]);
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            $_SESSION['error_message'] = "Category '$category_name' already exists!";
            $_SESSION['form_data'] = $_POST;
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (?, ?)");
            $stmt->execute([$category_name, $user_id]);
        }
    }
    
    // Update category
    if (isset($_POST['update_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];
        
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$category_name, $category_id, $user_id]);
    }
    
    // Delete category and its tasks
    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        
        $stmt_tasks = $pdo->prepare("DELETE FROM tasks WHERE category_id = ? AND user_id = ?");
        $stmt_tasks->execute([$category_id, $user_id]);
        
        $stmt_cat = $pdo->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
        $stmt_cat->execute([$category_id, $user_id]);
    }
    
    // For all non-AJAX POST requests, redirect to prevent form resubmission
    header("Location: dashboard.php");
    exit();
}

// Get categories
 $stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
 $stmt->execute([$user_id]);
 $categories = $stmt->fetchAll();

// Get tasks with category names
 $stmt = $pdo->prepare("
    SELECT t.id, t.name, t.deadline, t.is_completed, t.category_id, c.name as category_name
    FROM tasks t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
    ORDER BY t.is_completed ASC, t.deadline ASC
");
 $stmt->execute([$user_id]);
 $tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Dashboard</title>
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
        <header>
            <h1>To-Do List</h1>
            <div class="user-info">
                Welcome, <?php echo htmlspecialchars($username); ?> | <a href="logout.php">Logout</a>
                <button id="theme-toggle" class="icon-btn">
                    <img id="theme-icon" src="assets/<?php echo ($_SESSION['theme'] ?? 'light') === 'light' ? 'moon.svg' : 'sun.svg'; ?>" alt="Toggle Theme">
                </button>
            </div>
        </header>
        
        <main>
            <section class="task-section">
                <div class="section-header">
                    <h2>Tasks</h2>
                    <button id="show-add-task-form" class="add-btn icon-btn">
                        <img src="assets/plus.svg" alt="Add Task">
                    </button>
                </div>

                <!-- Add Task Form (now a popup) -->
                <div id="add-task-container" class="add-form-container">
                    <div class="add-form">
                        <button class="close-form-btn">&times;</button>
                        <form action="dashboard.php" method="post">
                            <h3>Add New Task</h3>
                            <div class="form-group">
                                <label for="task_name">Task Name</label>
                                <input type="text" id="task_name" name="task_name" required autocomplete="off" value="<?php echo htmlspecialchars($form_data['task_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="deadline">Deadline</label>
                                <input type="date" id="deadline" name="deadline" value="<?php echo htmlspecialchars($form_data['deadline'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select id="category_id" name="category_id">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"
                                            <?php echo ((isset($form_data['category_id']) && $form_data['category_id'] == $category['id'])) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="add_task" class="btn">Add Task</button>
                        </form>
                    </div>
                </div>
                
                <!-- Tasks List -->
                <div class="tasks-list">
                    <?php if (empty($tasks)): ?>
                        <p>No tasks yet. Add your first task!</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Completed</th>
                                    <th>Task Name</th>
                                    <th>Deadline</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tasks as $task): ?>
                                    <tr class="<?php echo $task['is_completed'] ? 'completed' : ''; ?>">
                                        <td>
                                            <form action="dashboard.php" method="post" class="inline-form">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <input type="hidden" name="is_completed" value="<?php echo $task['is_completed']; ?>">
                                                <button type="submit" name="toggle_task" class="checkbox-btn">
                                                    <?php echo $task['is_completed'] ? '✓' : ''; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <?php if ($task['is_completed']): ?>
                                                <s><?php echo htmlspecialchars($task['name']); ?></s>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($task['name']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $task['deadline'] ? date('M d, Y', strtotime($task['deadline'])) : 'No deadline'; ?></td>
                                        <td><?php echo htmlspecialchars($task['category_name']); ?></td>
                                        <td>
                                            <button class="edit-btn" data-task-id="<?php echo $task['id']; ?>" 
                                                    data-task-name="<?php echo htmlspecialchars($task['name']); ?>"
                                                    data-deadline="<?php echo $task['deadline']; ?>"
                                                    data-category-id="<?php echo $task['category_id']; ?>">Edit</button>
                                            
                                            <form action="dashboard.php" method="post" class="inline-form">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" name="delete_task" class="delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- Edit Task Modal -->
                <div id="edit-task-modal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3>Edit Task</h3>
                        <form action="dashboard.php" method="post">
                            <input type="hidden" id="edit-task-id" name="task_id">
                            <div class="form-group">
                                <label for="edit-task-name">Task Name</label>
                                <input type="text" id="edit-task-name" name="task_name" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="edit-deadline">Deadline</label>
                                <input type="date" id="edit-deadline" name="deadline">
                            </div>
                            <div class="form-group">
                                <label for="edit-category-id">Category</label>
                                <select id="edit-category-id" name="category_id">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="update_task" class="btn">Update Task</button>
                        </form>
                    </div>
                </div>
            </section>
            
            <section class="category-section">
                <div class="section-header">
                    <h2>Categories</h2>
                    <button id="show-add-category-form" class="add-btn icon-btn">
                        <img src="assets/plus.svg" alt="Add Category">
                    </button>
                </div>

                <!-- Add Category Form (now a popup) -->
                <div id="add-category-container" class="add-form-container">
                    <div class="add-form">
                        <button class="close-form-btn">&times;</button>
                        <form action="dashboard.php" method="post">
                            <h3>Add New Category</h3>
                            <div class="form-group">
                                <label for="category_name">Category Name</label>
                                <input type="text" id="category_name" name="category_name" required autocomplete="off" value="<?php echo htmlspecialchars($form_data['category_name'] ?? ''); ?>">
                            </div>
                            <button type="submit" name="add_category" class="btn">Add Category</button>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="categories-list">
                    <?php if (empty($categories)): ?>
                        <p>No categories yet. Add your first category!</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                                        <td>
                                            <?php if ($category['name'] !== 'none'): ?>
                                                <button class="edit-category-btn" data-category-id="<?php echo $category['id']; ?>" 
                                                        data-category-name="<?php echo htmlspecialchars($category['name']); ?>">Edit</button>
                                                
                                                <form action="dashboard.php" method="post" class="inline-form">
                                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                    <button type="submit" name="delete_category" class="delete-btn">Delete</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="default-category">Default category (cannot be modified)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- Edit Category Modal -->
                <div id="edit-category-modal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3>Edit Category</h3>
                        <form action="dashboard.php" method="post">
                            <input type="hidden" id="edit-category-id" name="category_id">
                            <div class="form-group">
                                <label for="edit-category-name">Category Name</label>
                                <input type="text" id="edit-category-name" name="category_name" required autocomplete="off">
                            </div>
                            <button type="submit" name="update_category" class="btn">Update Category</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>