// Central place for the platform currency. Change these three to switch currency.
const CURRENCY_LOCALE = 'id-ID';
const CURRENCY_SYMBOL = 'Rp';
const CURRENCY_FRACTION_DIGITS = 2;

/**
 * Format a money value (string or number) as a display currency string,
 * e.g. formatCurrency('100000.00') -> "Rp 100.000,00".
 */
export function formatCurrency(value: string | number): string {
    const amount = new Intl.NumberFormat(CURRENCY_LOCALE, {
        minimumFractionDigits: CURRENCY_FRACTION_DIGITS,
        maximumFractionDigits: CURRENCY_FRACTION_DIGITS,
    }).format(Number(value));

    return `${CURRENCY_SYMBOL} ${amount}`;
}
