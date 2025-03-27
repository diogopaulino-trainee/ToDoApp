/**
 * Returns the initials of a full name.
 * @param {string} fullName - The full name to get initials from
 * @returns {string} The initials of the full name
 */
export function getInitials(fullName?: string): string {
    if (!fullName) return '';

    const names = fullName.trim().split(' ');

    if (names.length === 0) return '';
    if (names.length === 1) return names[0].charAt(0).toUpperCase();

    return `${names[0].charAt(0)}${names[names.length - 1].charAt(0)}`.toUpperCase();
}

/**
 * Returns the initials of a full name.
 * @param {string} fullName - The full name to get initials from
 * @returns {string} The initials of the full name
 */
export function useInitials() {
    return { getInitials };
}
