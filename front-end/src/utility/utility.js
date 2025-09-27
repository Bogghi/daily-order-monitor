export function formatPrice(value) {
    let euros = Math.floor(value / 100);
    let cents = value % 100;

    return `${euros},${cents.toString().padStart(2, '0')} €`;
}