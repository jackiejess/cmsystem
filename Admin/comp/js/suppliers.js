// scripts.js
document.addEventListener('DOMContentLoaded', () => {
    const logoutButton = document.getElementById('logout');
    
    logoutButton.addEventListener('click', () => {
        // Perform logout action, e.g., redirect to login page or clear session
        window.location.href = 'admin_login.html';
    });
});
