import type { ApiResponse } from '../types';

const BASE = '/api';
const TOKEN_KEY = 'hd_token';

// ─── Token helpers ────────────────────────────────────────────────────────────

export const getToken = (): string | null => localStorage.getItem(TOKEN_KEY);
export const setToken = (t: string): void  => localStorage.setItem(TOKEN_KEY, t);
export const clearToken = (): void         => localStorage.removeItem(TOKEN_KEY);

// ─── Core request ─────────────────────────────────────────────────────────────

async function request<T>(
    endpoint: string,
    options: RequestInit = {},
): Promise<ApiResponse<T>> {
    const token = getToken();

    const headers: Record<string, string> = {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        ...(options.headers as Record<string, string>),
    };

    if (token) headers['Authorization'] = `Bearer ${token}`;

    const res = await fetch(`${BASE}${endpoint}`, { ...options, headers });
    const json: ApiResponse<T> = await res.json();

    if (!res.ok) {
        throw new Error(json.message ?? 'Request failed');
    }

    return json;
}

// ─── HTTP verbs ───────────────────────────────────────────────────────────────

const api = {
    get: <T>(endpoint: string) =>
        request<T>(endpoint),

    post: <T>(endpoint: string, body: unknown) =>
        request<T>(endpoint, {
            method: 'POST',
            body: JSON.stringify(body),
        }),

    patch: <T>(endpoint: string, body: unknown) =>
        request<T>(endpoint, {
            method: 'PATCH',
            body: JSON.stringify(body),
        }),

    delete: <T>(endpoint: string) =>
        request<T>(endpoint, { method: 'DELETE' }),
};

export default api;
