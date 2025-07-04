document.addEventListener('DOMContentLoaded', function() {
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle'); 
    const userIdInput = document.getElementById('userId'); 
    let isEditing = false;
    const deleteBtn = document.getElementById('deleteBtn');

    //  opne wind 
    addUserBtn.addEventListener('click', function() {
        isEditing = false;
        modalTitle.textContent = 'Add a new user';
        userForm.reset();
        userIdInput.value = '';
        deleteBtn.style.display = 'none';
        oldPasswordGroup.style.display='none';
        newPasswordGroup.style.display='none';
        document.getElementById('passwordGroup').style.display = 'block';
        addUserModal.style.display = 'flex';  

    });
    // close wind
    cancelBtn.addEventListener('click', function() {
        addUserModal.style.display = 'none';
    });

    //  enter out to close   
    addUserModal.addEventListener('click', function(e) {
        if (e.target === addUserModal) {
            addUserModal.style.display = 'none';
        }
    });

    //  updat card  
    document.querySelectorAll('.user-card').forEach(card => {
        card.addEventListener('click', function() {
            isEditing = true;
            modalTitle.textContent = 'Edit User';
            userIdInput.value = card.dataset.id;
            document.getElementById('name').value = card.dataset.name;
            document.getElementById('email').value = card.dataset.email;
            document.getElementById('password').required = false; 
            document.getElementById('passwordGroup').style.display = 'none';
            document.getElementById('oldPasswordGroup').style.display = 'block';
            document.getElementById('newPasswordGroup').style.display = 'block';
            document.getElementById('oldPassword').required = false;
            document.getElementById('newPassword').required = false;
                deleteBtn.style.display = 'inline-block';
                addUserModal.style.display = 'flex';
            }); 
    });
   
    // add or updat
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(userForm);
        const targetUrl = isEditing ? 'update_organizer.php' : 'add_organizer.php';
        fetch(targetUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('An error occurred ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while connecting to the server');
        });
    }); 
    deleteBtn.addEventListener('click', function () {
    const userId = userIdInput.value;
    if (confirm('Are you sure you want to delete this user?')) {
        fetch('delete_organizer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(userId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('failed to delete user' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while connecting to the server, check inputs');
        });
    }
});
});
document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
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