const loginForm = document.querySelector('.login-form');
const registerForm = document.querySelector('.register-form');
const showLogin = document.getElementById('show-login');
const showRegister = document.getElementById('show-register');

showLogin.addEventListener('click', (e) => {
    e.preventDefault();
    loginForm.classList.add('active'); // Muestra el Login
    registerForm.classList.remove('active'); // Oculta el Registro
});

showRegister.addEventListener('click', (e) => {
    e.preventDefault();
    registerForm.classList.add('active'); // Muestra el Registro
    loginForm.classList.remove('active'); // Oculta el Login
});
