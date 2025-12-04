// js/script.js
// Client-side functionality for To-Do List Application

document.addEventListener('DOMContentLoaded', function() {
    // Task Edit Modal
    const editTaskModal = document.getElementById('edit-task-modal');
    const editTaskBtns = document.querySelectorAll('.edit-btn');
    const editTaskClose = editTaskModal.querySelector('.close');
    
    // Category Edit Modal
    const editCategoryModal = document.getElementById('edit-category-modal');
    const editCategoryBtns = document.querySelectorAll('.edit-category-btn');
    const editCategoryClose = editCategoryModal.querySelector('.close');
    
    // Edit Task Modal Functionality
    editTaskBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const taskName = this.getAttribute('data-task-name');
            const deadline = this.getAttribute('data-deadline');
            const categoryId = this.getAttribute('data-category-id');
            
            document.getElementById('edit-task-id').value = taskId;
            document.getElementById('edit-task-name').value = taskName;
            document.getElementById('edit-deadline').value = deadline;
            document.getElementById('edit-category-id').value = categoryId;
            
            editTaskModal.style.display = 'block';
        });
    });
    
    editTaskClose.addEventListener('click', function() {
        editTaskModal.style.display = 'none';
    });
    
    // Edit Category Modal Functionality
    editCategoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            
            document.getElementById('edit-category-id').value = categoryId;
            document.getElementById('edit-category-name').value = categoryName;
            
            editCategoryModal.style.display = 'block';
        });
    });
    
    editCategoryClose.addEventListener('click', function() {
        editCategoryModal.style.display = 'none';
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === editTaskModal) {
            editTaskModal.style.display = 'none';
        }
        if (event.target === editCategoryModal) {
            editCategoryModal.style.display = 'none';
        }
    });
    
    // Confirm before deleting
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Set today's date as minimum for deadline
    const deadlineInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    deadlineInputs.forEach(input => {
        input.setAttribute('min', today);
    });

    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    themeToggle.addEventListener('click', (event) => {
        event.preventDefault();

        const isDark = document.body.classList.contains('dark');
        const newTheme = isDark ? 'light' : 'dark';
        
        // Toggle theme immediately to start transitions
        document.body.classList.toggle('dark');
        const isDarkMode = document.body.classList.contains('dark');
        themeIcon.src = isDarkMode ? 'assets/sun.svg' : 'assets/moon.svg';

        // 1. Create the circle
        const circle = document.createElement('div');
        circle.classList.add('theme-transition-circle');

        // 2. Set color
        const newThemeBg = newTheme === 'dark' ? '#2c3e50' : '#f5f5f5';
        circle.style.backgroundColor = newThemeBg;

        // 3. Set position and size
        const clickX = event.clientX;
        const clickY = event.clientY;

        const screenW = window.innerWidth;
        const screenH = window.innerHeight;
        const radius = Math.max(
            Math.hypot(clickX, clickY),
            Math.hypot(screenW - clickX, clickY),
            Math.hypot(clickX, screenH - clickY),
            Math.hypot(screenW - clickX, screenH - clickY)
        );
        
        circle.style.width = `${radius * 2}px`;
        circle.style.height = `${radius * 2}px`;
        circle.style.left = `${clickX - radius}px`;
        circle.style.top = `${clickY - radius}px`;

        // 4. Add to DOM and animate
        document.body.appendChild(circle);

        requestAnimationFrame(() => {
            circle.style.transform = 'scale(1)';
            circle.style.opacity = '0';
        });

        // 5. After animation, cleanup
        circle.addEventListener('transitionend', () => {
            fetch('update_theme.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ theme: newTheme })
            });
            
            circle.remove();
        }, { once: true });
    });
});