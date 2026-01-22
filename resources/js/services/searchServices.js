const searchCache = {}
const CACHE_TTL = 5 * 60 * 1000;
let controller;
export async function searchUsers(query) {
    let now = Date.now();

    if (controller) {
        controller.abort(); // cancel old request
    }

    if (searchCache[query] && now - searchCache[query].time < CACHE_TTL) {
        return searchCache[query].data;
    }

    controller = new AbortController();

    const res = await fetch(`/blog/search?q=${encodeURIComponent(query)}`,
        {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: controller.signal
        })
    const data = await res.json();
    searchCache[query] =
        {
            data: data.users || [],
            time: now
        };
    return searchCache[query].data;

}
