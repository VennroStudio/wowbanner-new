import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/constants';
import type { User } from '../model/types';

export const userApi = {
  getUser: (id: string | number) => 
    apiClient.get<{ data: User }>(API_ENDPOINTS.USERS.BY_ID(id)),
};

export const authApi = {
  refresh: () => 
    apiClient.post<{ data: { access_token: string } }>(API_ENDPOINTS.AUTH.REFRESH, {}),
};
