import { API_URL } from '@/shared/constants';

type ApiFetchFn = (endpoint: string, options?: RequestInit) => Promise<unknown>;

export const authApi = {
  login: (email: string, password: string) =>
    fetch(`${API_URL}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ email, password }),
    }),

  refresh: () =>
    fetch(`${API_URL}/auth/refresh`, {
      method: 'POST',
      credentials: 'include',
    }),

  logout: (apiFetch: ApiFetchFn) =>
    apiFetch('/auth/logout', { method: 'POST' }),

  confirmEmail: (apiFetch: ApiFetchFn, token: string) =>
    apiFetch('/auth/confirm-email', {
      method: 'POST',
      body: JSON.stringify({ token }),
    }),

  requestPasswordReset: (apiFetch: ApiFetchFn, email: string) =>
    apiFetch('/auth/password-reset', {
      method: 'POST',
      body: JSON.stringify({ email }),
    }),

  confirmPasswordReset: (apiFetch: ApiFetchFn, token: string, password: string) =>
    apiFetch('/auth/password-reset/confirm', {
      method: 'POST',
      body: JSON.stringify({ token, password }),
    }),
};
