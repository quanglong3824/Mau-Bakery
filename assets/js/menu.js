// Helpers
function updateQueryString(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    if (key !== 'p') url.searchParams.set('p', 1); // Reset to page 1 on filter change
    return url.toString();
}
