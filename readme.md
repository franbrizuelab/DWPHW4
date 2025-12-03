# README: To-Do List Application

## Student Information
- Student ID: 112550176
- Name: 孫傅康

## System Structure
This To-Do List application is built with PHP, MySQL, HTML, CSS, and JavaScript. The application allows users to register, log in, and manage their tasks and categories.

### File Structure
HW4_112550176_孫傅康/
├── css/
│   └── style.css
├── js/
│   └── script.js
├── config/
│   └── database.php
├── index.php (login page)
├── register.php
├── logout.php
├── dashboard.php (main to-do list page)
├── hw4.sql
└── README.mds


## How to Run the Project

### Prerequisites
- XAMPP/WAMP/LAMP server with PHP and MySQL
- Web browser (Google Chrome recommended)

### Installation Steps
1. Extract the compressed file `HW4_112550176_孫傅康.zip` to your web server's root directory (e.g., `htdocs` for XAMPP).
2. Start your Apache and MySQL servers.
3. Open phpMyAdmin and create a new database named `todo_list`.
4. Import the `hw4.sql` file into the `todo_list` database. This will create the necessary tables and populate them with sample data.
5. Access the application by navigating to `http://localhost/HW4_112550176_孫傅康/` in your web browser.

### Database Configuration
The application uses the following MySQL credentials:
- Username: cvml
- Password: dwpcvml2025

If you need to change these credentials, modify them in the `config/database.php` file.

## How Login and Registration Work

### Registration
1. New users can register by clicking the "Register here" link on the login page.
2. Users must provide a username and password (minimum 6 characters).
3. The system validates the input and checks for duplicate usernames.
4. Upon successful registration, a default "none" category is created for the user.
5. Users are automatically logged in after registration and redirected to the dashboard.

### Login
1. Existing users can log in using their username and password.
2. Passwords are securely hashed using PHP's `password_hash()` function.
3. Upon successful authentication, users are redirected to their dashboard.
4. If authentication fails, an error message is displayed.

### Logout
1. A logout button is available on the dashboard.
2. Clicking this button destroys the session and redirects the user back to the login page.

## Database Schema

The application uses three main tables:

### Users Table
- `id`: Primary key, auto-increment
- `username`: Unique username for each user
- `password`: Hashed password
- `created_at`: Timestamp of account creation

### Categories Table
- `id`: Primary key, auto-increment
- `name`: Category name
- `user_id`: Foreign key referencing the users table
- `created_at`: Timestamp of category creation

### Tasks Table
- `id`: Primary key, auto-increment
- `name`: Task description
- `deadline`: Task due date
- `category_id`: Foreign key referencing the categories table
- `is_completed`: Boolean flag indicating task completion status
- `user_id`: Foreign key referencing the users table
- `created_at`: Timestamp of task creation

## Features

### Task Management
- **Add Task**: Users can add new tasks with a name, deadline, and category.
- **Edit Task**: Users can modify task names, deadlines, and categories.
- **Delete Task**: Users can remove tasks from their list.
- **Mark as Complete**: Users can mark tasks as complete using a checkbox.
- **Persistent Storage**: All task changes are immediately saved to the database.

### Category Management
- **Add Category**: Users can create new categories to organize their tasks.
- **Edit Category**: Users can modify category names (except for the default "none" category).
- **Delete Category**: Users can remove categories and all tasks associated with them (except for the default "none" category).
- **Default Category**: A "none" category is automatically created for each user and cannot be modified or deleted.

### User Interface
- **Responsive Design**: The application adapts to different screen sizes.
- **Modal Dialogs**: Edit operations use modal dialogs for a better user experience.
- **Visual Feedback**: Completed tasks are visually distinguished.
- **Confirmation Dialogs**: Delete operations require confirmation to prevent accidental deletions.

## Security Considerations
- Passwords are securely hashed using PHP's `password_hash()` function.
- SQL injection is prevented using prepared statements.
- Session management ensures users can only access their own data.
- Input validation prevents malicious data submission.

## Known Issues and Future Improvements
- Task priorities could be added for better organization.
- Email notifications for approaching deadlines would enhance functionality.
- Task sharing between users could be implemented for collaborative projects.
- Mobile app version for better accessibility on mobile devices.