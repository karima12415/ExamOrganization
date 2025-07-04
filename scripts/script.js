let btns = document.querySelectorAll('[data-btn]');

btns.forEach(btn => {
    btn.addEventListener('click',function(e){
        e.preventDefault();
        let id = document.querySelector(btn.getAttribute('data-target'));
        id.classList.toggle('active')
    })
    
});

let links = document.querySelectorAll('.sidebar ul li a');
let pages = document.querySelectorAll('.page');

links.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();

        // Remove active class from all links and pages
        links.forEach(item => item.classList.remove('active'));
        pages.forEach(page => page.classList.remove('active'));

        // Add active class to clicked link
        this.classList.add('active');

        // Show the corresponding section
        let targetId = this.getAttribute('href').substring(1);
        document.getElementById(targetId).classList.add('active');
    });
});

document.getElementById('dark-mode-toggle').addEventListener('click', function () {
    document.body.classList.toggle('dark-mode');

    // Store the user’s preference in local storage
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
});

// Apply saved theme on page load
window.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
});


//oldpassword
document.addEventListener('DOMContentLoaded', function () {
    const toggleOldPassword = document.getElementById('toggleOldPassword');
    const oldPasswordInput = document.getElementById('oldPassword');

    if (toggleOldPassword) {
        toggleOldPassword.addEventListener('click', function () {
            const type = oldPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            oldPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
});
//newpassword
document.addEventListener('DOMContentLoaded', function () {
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const newPasswordInput = document.getElementById('newPassword');

    if (toggleNewPassword) {
        toggleNewPassword.addEventListener('click', function () {
            const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            newPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
});

