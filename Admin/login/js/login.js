function showLogin() {
    document.getElementById('login').classList.remove('hidden');
    document.getElementById('register').classList.add('hidden');
    document.getElementById('forgotPassword').classList.add('hidden');
}

function showRegister() {
    document.getElementById('register').classList.remove('hidden');
    document.getElementById('login').classList.add('hidden');
    document.getElementById('forgotPassword').classList.add('hidden');
}

function showForgotPassword() {
    document.getElementById('forgotPassword').classList.remove('hidden');
    document.getElementById('login').classList.add('hidden');
    document.getElementById('register').classList.add('hidden');
}
