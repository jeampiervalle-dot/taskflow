function initLogin() {
    document.getElementById("miPopup").style.display = "flex";

    const loginForm = document.querySelector('#loginPanel form');

    loginForm?.addEventListener("submit", () => {
        const userInput = document.querySelector('#loginPanel input[name="email"]');
        const username = userInput?.value.trim() || 'Jean';
        localStorage.setItem('project_final_user', username);
        document.body.classList.add("salir");
    });
}

function cerrarPopup() {
    document.getElementById("miPopup").style.display = "none";
}

const loginPanel = document.getElementById("loginPanel");
const registerPanel = document.getElementById("registerPanel");

function mostrarRegister() {
    loginPanel.classList.remove("active-panel");
    loginPanel.classList.add("hidden-left");
    registerPanel.classList.remove("hidden-right");
    registerPanel.classList.add("active-panel");
}

function mostrarLogin() {
    registerPanel.classList.remove("active-panel");
    registerPanel.classList.add("hidden-right");
    loginPanel.classList.remove("hidden-left");
    loginPanel.classList.add("active-panel");
}

document.addEventListener('DOMContentLoaded', initLogin);

window.cerrarPopup = cerrarPopup;
window.mostrarRegister = mostrarRegister;
window.mostrarLogin = mostrarLogin;
