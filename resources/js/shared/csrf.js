export const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const buildHeaders = (extra = {}) => ({
    'X-CSRF-TOKEN': csrfToken(),
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json',
    ...extra,
});

export const getJson = async (url) => {
    const res = await fetch(url, {
        method: 'GET',
        headers: buildHeaders(),
        credentials: 'same-origin',
    });
    return res.json();
};

export const postJson = async (url, body = {}) => {
    const res = await fetch(url, {
        method: 'POST',
        headers: buildHeaders({ 'Content-Type': 'application/json' }),
        credentials: 'same-origin',
        body: JSON.stringify(body),
    });
    return res.json();
};

export const postForm = async (url, formData) => {
    const res = await fetch(url, {
        method: 'POST',
        headers: buildHeaders(),
        credentials: 'same-origin',
        body: formData,
    });
    return res.json();
};
