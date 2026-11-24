document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('portalSidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }
});

function showPrototypeToast(message, type = 'success') {
    const container = document.createElement('div');
    container.className = 'position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1080';
    container.innerHTML = `
        <div class="toast show align-items-center text-bg-${type === 'error' ? 'danger' : 'success'} border-0 shadow" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-medium">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    document.body.appendChild(container);
    setTimeout(() => container.remove(), 3500);
}