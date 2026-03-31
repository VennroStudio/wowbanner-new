import { API_ENDPOINTS } from '@/shared/constants';
import { apiClient } from '@/shared/api/client';
import type { LoginDto } from '@/shared/types';

export const authApi = {
  login: (data: LoginDto) =>
    apiClient.post(API_ENDPOINTS.AUTH.LOGIN, data),

  refresh: () =>
    apiClient.post(API_ENDPOINTS.AUTH.REFRESH),

  logout: () =>
    apiClient.post(API_ENDPOINTS.AUTH.LOGOUT),

  confirmEmail: (token: string) =>
    apiClient.post(API_ENDPOINTS.AUTH.CONFIRM_EMAIL, { token }),

  requestPasswordReset: (email: string) =>
    apiClient.post(API_ENDPOINTS.AUTH.PASSWORD_RESET, { email }),

  confirmPasswordReset: (token: string, password: string) =>
    apiClient.post(API_ENDPOINTS.AUTH.PASSWORD_RESET_CONFIRM, { token, password }),
};
