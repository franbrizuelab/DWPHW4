# README: To-Do List Application

## Student Information
- Student ID: 112550176
- Name: 孫傅康

## System Structure
This To-Do List application is built with PHP, MySQL, HTML, CSS, and JavaScript. The application allows users to register, log in, and manage their tasks and categories with a modern, dynamic user interface.

### File Structure
```
HW4_112550176_孫傅康/
├── assets/
│   ├── moon.svg
│   ├── pencil.svg
│   ├── plus.svg
│   ├── sun.svg
│   └── trash.svg
├── css/
│   └── style.css
├── js/
│   └── script.js
├── config/
│   └── database.php
├── api.php
├── index.php
├── register.php
├── logout.php
├── dashboard.php
├── hw4.sql
└── readme.md
```

### Key Files and Directories
-   **`dashboard.php`**: The main application page where users manage their tasks and categories.
-   **`index.php` / `register.php`**: Handle user login and registration.
-   **`api.php`**: A dedicated backend endpoint to handle background AJAX requests, such as deleting items, ensuring smooth UI updates without page reloads.
-   **`assets/`**: Contains all SVG icons used throughout the application for a clean, vectorized look.
-   **`css/style.css`**: Main stylesheet, including all variables for theme colors, animations, and responsive design.
-   **`js/script.js`**: Handles all client-side interactivity, including theme switching, pop-up animations, and smooth deletion.
-   **`config/database.php`**: Contains the database connection configuration.
-   **`hw4.sql`**: The SQL file to set up the initial database schema and sample data.


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

### Dynamic UX Features
The application has been updated with several modern, dynamic features to enhance the user experience:

-   **Animated Theme Switching:**
    -   Users can toggle between light and dark themes.
    -   The switch is accompanied by a smooth, expanding circle animation originating from the theme button.
    -   All page elements transition their colors gradually for a seamless effect.
    -   Animation speeds can be configured via CSS variables in `style.css`.

-   **Interactive "Add" Forms:**
    -   The forms for adding new tasks and categories are now interactive pop-ups.
    -   They are triggered by new circular "plus" buttons located in the section headers.
    -   The forms appear with a smooth fade-in and scale-up animation, overlaid on the current content without causing page displacement.
    -   Pop-ups can be closed with either the dedicated "x" button or by pressing the `Escape` key.

-   **Icon-based Actions & Hover Effects:**
    -   The text-based "Edit" and "Delete" buttons in the lists have been replaced with modern pencil and trash can SVG icons.
    -   These icon buttons provide visual feedback with a subtle highlight effect on hover.

-   **Animated Error Handling:**
    -   When the server detects an error (e.g., creating a duplicate category or submitting a task with an empty field), a custom animated pop-up appears.
    -   The pop-up is centered, and the background smoothly dims and blurs to draw focus to the message.
    *   Crucially, the user's input in the form is preserved after the error, so they do not have to type everything again.

-   **Smooth Deletion:**
    -   Deleting a task or category no longer requires a page reload.
    -   Items are removed from the list with a smooth fade-out animation, providing a better and faster user experience.

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