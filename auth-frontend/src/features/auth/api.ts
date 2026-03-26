import { API_URL } from '@/shared/constants';
import type { LoginDto } from './types';
import type { ApiFetchFn } from '@/shared/api/client';

export const authApi = {
  login: (apiFetch: ApiFetchFn, data: LoginDto) =>
    apiFetch('/auth/login', {
      method: 'POST',
      body: JSON.stringify(data),
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
