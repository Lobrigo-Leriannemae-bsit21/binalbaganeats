// Checkout page behaviour: zone dropdown, Suki Points, live totals, GCash panel,
// double-submit protection.
const form = document.querySelector('form[data-checkout]');

if (form) {
    const money = (amount) =>
        '₱' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const subtotal = Number(form.dataset.subtotal) || 0;
    const pointValue = Number(form.dataset.pointValue) || 1;
    const minPoints = Number(form.dataset.minPoints) || 0;
    const maxPoints = Number(form.dataset.maxPoints) || 0;

    const zoneSelect = form.querySelector('select[name="delivery_zone_id"]');
    const zoneHint = form.querySelector('[data-zone-hint]');
    const feeDisplay = form.querySelector('[data-fee-display]');
    const totalDisplays = form.querySelectorAll('[data-total-display]');
    const discountRow = form.querySelector('[data-discount-row]');
    const discountDisplay = form.querySelector('[data-discount-display]');
    const pointsToggle = form.querySelector('[data-points-toggle]');
    const pointsPanel = form.querySelector('[data-points-panel]');
    const pointsInput = form.querySelector('input[name="use_points"]');
    const pointsMax = form.querySelector('[data-points-max]');
    const gcashPanel = form.querySelector('[data-gcash-panel]');
    const gcashFields = form.querySelectorAll('[data-gcash-field]');
    const submitButton = form.querySelector('[data-submit-once]');

    const selectedZone = () => zoneSelect?.selectedOptions[0] ?? null;

    const selectedFee = () => {
        const option = selectedZone();
        return option && option.value ? Number(option.dataset.fee) : null;
    };

    const clampPoints = (value) =>
        Math.min(maxPoints, Math.max(minPoints, Math.round(Number(value)) || minPoints));

    const pointsUsed = () => {
        if (!pointsInput || !pointsToggle?.checked) return 0;
        return clampPoints(pointsInput.value);
    };

    const refreshZone = () => {
        const option = selectedZone();
        if (!zoneHint) return;

        zoneHint.textContent = option && option.value
            ? `About ${option.dataset.time} min travel · ${option.dataset.distance}`
            : 'Delivery fees are set per barangay.';
    };

    const refreshTotals = () => {
        const fee = selectedFee();
        const discount = pointsUsed() * pointValue;

        if (feeDisplay) feeDisplay.textContent = fee === null ? 'Select a zone' : money(fee);

        if (discountRow) discountRow.classList.toggle('d-none', discount <= 0);
        if (discountDisplay) discountDisplay.textContent = '−' + money(discount);

        const total = subtotal + (fee ?? 0) - discount;
        totalDisplays.forEach((el) => { el.textContent = money(total); });
    };

    const refreshPoints = () => {
        if (!pointsToggle || !pointsInput || !pointsPanel) return;

        pointsPanel.classList.toggle('d-none', !pointsToggle.checked);

        if (!pointsToggle.checked) {
            pointsInput.value = 0;
        } else {
            // Start from the most the customer may use; keep typed values inside the limits.
            pointsInput.value = clampPoints(Number(pointsInput.value) > 0 ? pointsInput.value : maxPoints);
        }
    };

    const refreshPayment = () => {
        const isGcash = form.querySelector('input[name="payment_method"]:checked')?.value === 'gcash';
        if (gcashPanel) gcashPanel.classList.toggle('d-none', !isGcash);
        gcashFields.forEach((field) => { field.required = isGcash; });
    };

    form.addEventListener('change', (event) => {
        const { name } = event.target;

        if (name === 'delivery_zone_id') { refreshZone(); refreshTotals(); }
        if (name === 'payment_method') refreshPayment();

        if (event.target === pointsToggle) { refreshPoints(); refreshTotals(); }

        if (event.target === pointsInput) {
            pointsInput.value = clampPoints(pointsInput.value);
            refreshTotals();
        }
    });

    form.addEventListener('input', (event) => {
        if (event.target === pointsInput) refreshTotals();
    });

    pointsMax?.addEventListener('click', () => {
        if (!pointsInput) return;
        pointsInput.value = maxPoints;
        refreshTotals();
    });

    // Disable the button once the form passes browser validation and is being sent.
    form.addEventListener('submit', () => {
        if (!submitButton) return;
        submitButton.disabled = true;
        submitButton.textContent = 'Placing order…';
    });

    // If the user comes back with the browser's Back button, make the button usable again.
    window.addEventListener('pageshow', (event) => {
        if (event.persisted && submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = 'Place order';
        }
    });

    refreshZone();
    refreshPoints();
    refreshTotals();
    refreshPayment();
}