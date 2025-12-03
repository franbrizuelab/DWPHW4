-- hw4.sql
-- Database creation and initial data for To-Do List Application

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS todo_list;
USE todo_list;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category (name, user_id)
);

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    deadline DATE,
    category_id INT,
    is_completed BOOLEAN DEFAULT 0,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),  -- password: password
('demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');   -- password: password

-- Get user IDs
SET @admin_id = (SELECT id FROM users WHERE username = 'admin');
SET @demo_id = (SELECT id FROM users WHERE username = 'demo');

-- Insert default categories for each user
INSERT INTO categories (name, user_id) VALUES 
('none', @admin_id),
('Work', @admin_id),
('Personal', @admin_id),
('none', @demo_id),
('Work', @demo_id),
('Personal', @demo_id);

-- Get category IDs
SET @admin_none = (SELECT id FROM categories WHERE name = 'none' AND user_id = @admin_id);
SET @admin_work = (SELECT id FROM categories WHERE name = 'Work' AND user_id = @admin_id);
SET @admin_personal = (SELECT id FROM categories WHERE name = 'Personal' AND user_id = @admin_id);
SET @demo_none = (SELECT id FROM categories WHERE name = 'none' AND user_id = @demo_id);
SET @demo_work = (SELECT id FROM categories WHERE name = 'Work' AND user_id = @demo_id);
SET @demo_personal = (SELECT id FROM categories WHERE name = 'Personal' AND user_id = @demo_id);

-- Insert sample tasks for admin user
INSERT INTO tasks (name, deadline, category_id, is_completed, user_id) VALUES 
('Complete project documentation', '2023-12-15', @admin_work, 0, @admin_id),
('Review pull requests', '2023-12-10', @admin_work, 1, @admin_id),
('Buy groceries', '2023-12-08', @admin_personal, 0, @admin_id),
('Call mom', '2023-12-07', @admin_none, 0, @admin_id),
('Schedule dentist appointment', '2023-12-20', @admin_personal, 0, @admin_id);

-- Insert sample tasks for demo user
INSERT INTO tasks (name, deadline, category_id, is_completed, user_id) VALUES 
('Prepare presentation', '2023-12-12', @demo_work, 0, @demo_id),
('Fix bug in authentication', '2023-12-09', @demo_work, 1, @demo_id),
('Pay utility bills', '2023-12-05', @demo_personal, 0, @demo_id),
('Read new book', '2023-12-25', @demo_none, 0, @demo_id),
('Plan weekend trip', '2023-12-18', @demo_personal, 0, @demo_id);