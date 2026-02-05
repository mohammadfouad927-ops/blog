/**
 * utils.js
 * Shared utility / helper functions for the editor
 */

/**
 * Validates whether a given string is a valid http/https URL.
 * Automatically prepends "https://" if no protocol is provided.
 * @param {string} str
 * @returns {boolean}
 */
export function isValidUrl(str) {
    try {
        if (!str.startsWith('http://') && !str.startsWith('https://')) {
            str = 'https://' + str;
        }
        const url = new URL(str);
        return (url.protocol === 'http:' || url.protocol === 'https:') && url.hostname.includes('.');
    } catch {
        return false;
    }
}

/**
 * Normalizes a URL string by prepending "https://" if no protocol exists.
 * @param {string} url
 * @returns {string}
 */
export function normalizeUrl(url) {
    const trimmed = url.trim();
    if (!trimmed.startsWith('http://') && !trimmed.startsWith('https://')) {
        return 'https://' + trimmed;
    }
    return trimmed;
}

/**
 * Calculates the fixed-position (top, left) for the floating toolbar
 * so that it appears above the first line of the current DOM selection.
 * Returns null if no valid selection rect is found.
 *
 * @param {{ height: number, width: number }} toolbar – approximate toolbar dimensions
 * @returns {{ top: number, left: number } | null}
 */
export function getToolbarPosition(toolbar = { height: 48, width: 280 }) {
    const domSelection = window.getSelection();
    if (!domSelection || domSelection.rangeCount === 0) return null;

    const range = domSelection.getRangeAt(0);
    const rects = range.getClientRects();
    if (!rects || rects.length === 0) return null;

    // Use first rect so toolbar always appears above the start of selection
    const first = rects[0];
    if (!first || first.width === 0 || first.height === 0) return null;

    const gap = 8;
    let top  = first.top  + window.pageYOffset - toolbar.height - gap;
    let left = first.left + window.pageXOffset + first.width / 2;

    // Clamp horizontally so toolbar stays inside the viewport
    const halfW  = toolbar.width / 2;
    left = Math.max(halfW + 10, Math.min(left, window.innerWidth - halfW - 10));

    return { top, left };
}
