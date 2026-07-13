/**
 * The funded portion of a loan as a percentage (0-100), clamped so it never
 * exceeds 100 even if the funded amount overshoots the requested amount.
 */
export function fundedPercentage(
    amountFunded: string | number,
    amountRequested: string | number,
): number {
    const requested = Number(amountRequested);

    return requested > 0
        ? Math.min((Number(amountFunded) / requested) * 100, 100)
        : 0;
}
