document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("container");
    const registerBtn = document.getElementById("register");
    const loginBtn = document.getElementById("login");
    if (registerBtn && loginBtn && container) {
        registerBtn.addEventListener("click", () => {
            container.classList.add("active");
        });
        loginBtn.addEventListener("click", () => {
            container.classList.remove("active");
        });
    } else {
        console.error("Error: One or more elements not found!");
    }
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    togglePassword.addEventListener("click", () => {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
        // Toggle the icon
        togglePassword.classList.toggle("fa-eye");
        togglePassword.classList.toggle("fa-eye-slash");
    });
});

