import { API_URL, API_ENDPOINTS } from '@/shared/constants';
import type { LoginDto } from './types';
import type { ApiFetchFn } from '@/shared/api/client';

export const authApi = {
  login: (apiFetch: ApiFetchFn, data: LoginDto) =>
    apiFetch(API_ENDPOINTS.AUTH.LOGIN, {
      method: 'POST',
      body: JSON.stringify(data),
    }),

  refresh: () =>
    fetch(`${API_URL}${API_ENDPOINTS.AUTH.REFRESH}`, {
      method: 'POST',
      credentials: 'include',
    }),

  logout: (apiFetch: ApiFetchFn) =>
    apiFetch(API_ENDPOINTS.AUTH.LOGOUT, { method: 'POST' }),

  confirmEmail: (apiFetch: ApiFetchFn, token: string) =>
    apiFetch(API_ENDPOINTS.AUTH.CONFIRM_EMAIL, {
      method: 'POST',
      body: JSON.stringify({ token }),
    }),

  requestPasswordReset: (apiFetch: ApiFetchFn, email: string) =>
    apiFetch(API_ENDPOINTS.AUTH.PASSWORD_RESET, {
      method: 'POST',
      body: JSON.stringify({ email }),
    }),

  confirmPasswordReset: (apiFetch: ApiFetchFn, token: string, password: string) =>
    apiFetch(API_ENDPOINTS.AUTH.PASSWORD_RESET_CONFIRM, {
      method: 'POST',
      body: JSON.stringify({ token, password }),
    }),
};
