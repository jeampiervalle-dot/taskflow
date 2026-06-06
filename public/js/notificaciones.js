function closeToast(id) {
    const t = document.getElementById(id);
    if (t) t.classList.add('hidden');
}

setTimeout(() => {
    const t = document.getElementById('systemToast');
    if (t) t.classList.add('hidden');
}, 4000);
