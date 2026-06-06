window.onload = function () {

    document.getElementById("miPopup").style.display = "flex";

    const btnLogin = document.getElementById("btn-logindash");

    btnLogin.addEventListener("click", () => {

        const userInput = document.querySelector('#loginPanel input[name="email"]');
        const username = userInput?.value.trim() || 'Jean';

        localStorage.setItem('project_final_user', username);

        document.body.classList.add("salir");

        // NO usar preventDefault()
        // NO usar window.location.href
        // Laravel se encargará de redirigir al dashboard

    });

};

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