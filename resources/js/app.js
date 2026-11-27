// Bootstrap CSS must load first so our brand overrides in app.css win.
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/app.css';
import '../css/customer.css';
import '../css/catalog.css';
import '../css/orders.css';
import '../css/suki.css';
import '../css/account.css';
import '../css/portal.css';

import * as bootstrap from 'bootstrap';

import './checkout';

// Makes modals, dropdowns, tooltips, etc. available to inline scripts.
window.bootstrap = bootstrap;

// Show / hide password buttons (used by <x-form.input type="password">)
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-toggle-password]');
    if (!button) return;

    const input = document.getElementById(button.dataset.togglePassword);
    if (!input) return;

    const reveal = input.type === 'password';
    input.type = reveal ? 'text' : 'password';
    button.querySelector('i').className = reveal ? 'bi bi-eye-slash' : 'bi bi-eye';
});

// Quantity steppers (used by <x-quantity-input>)
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-qty-minus], [data-qty-plus]');
    if (!button) return;

    const input = button.closest('[data-qty]')?.querySelector('input[type="number"]');
    if (!input) return;

    const min = Number(input.min || 1);
    const max = Number(input.max || 99);
    const current = Number(input.value) || min;
    const next = button.hasAttribute('data-qty-plus')
        ? Math.min(max, current + 1)
        : Math.max(min, current - 1);

    if (next !== current) {
        input.value = next;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
});

// Keep typed quantities inside their limits, then auto-submit forms marked data-autosubmit.
document.addEventListener('change', (event) => {
    const target = event.target;

    if (target.matches?.('.be-qty input[type="number"]')) {
        const min = Number(target.min);
        const max = Number(target.max);
        const value = Math.round(Number(target.value)) || min;
        target.value = Math.min(max, Math.max(min, value));
    }

    const form = target.closest?.('form[data-autosubmit]');
    if (form) form.requestSubmit();
});

// Ask before submitting forms marked data-confirm="Are you sure?"
document.addEventListener('submit', (event) => {
    const message = event.target.dataset?.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});