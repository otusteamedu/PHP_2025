document.addEventListener('DOMContentLoaded', () => {
    const from = document.querySelector('input[name="date_from"]');
    const to = document.querySelector('input[name="date_to"]');

    if (!from || !to) return;

    const sync = () => {
        if (from.value) to.min = from.value;
        if (to.value) from.max = to.value;

        if (from.value && to.value && from.value > to.value) {
            to.value = '';
        }
    };

    from.addEventListener('change', sync);
    to.addEventListener('change', sync);
    sync();
});
