import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/api/endpoints';
import type { ApiDataResponse } from '@/shared/api/types';

export const sessionApi = {
  refresh: () =>
    apiClient.post<ApiDataResponse<{ access_token: string }>>(API_ENDPOINTS.AUTH.REFRESH, {}),
  logout: () =>
    apiClient.post(API_ENDPOINTS.AUTH.LOGOUT),
};
