import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/api/endpoints';
import type { ApiDataResponse } from '@/shared/api/types';
import type { User } from '../model/types';

export const userApi = {
  getUser: (id: string | number) => 
    apiClient.get<ApiDataResponse<User>>(API_ENDPOINTS.USERS.BY_ID(id)),
};
