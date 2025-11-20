document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('darkModeToggle');
    if (!toggleBtn) return;

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        document.documentElement.classList.add('dark-mode');
        document.documentElement.setAttribute('data-theme', 'dark');
        toggleBtn.innerHTML = '<i class="bi bi-sun-fill"></i>';
    } else {
        document.body.classList.remove('dark-mode');
        document.documentElement.classList.remove('dark-mode');
        document.documentElement.setAttribute('data-theme', 'light');
        toggleBtn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
    }

    toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode'); 
        document.documentElement.classList.toggle('dark-mode'); 
        
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            document.documentElement.setAttribute('data-theme', 'dark');
            toggleBtn.innerHTML = '<i class="bi bi-sun-fill"></i>';
        } else {
            localStorage.setItem('theme', 'light');
            document.documentElement.setAttribute('data-theme', 'light');
            toggleBtn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        }
    });
});
