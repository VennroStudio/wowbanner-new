import { apiClient } from '@/shared/api/client';
import { API_ENDPOINTS } from '@/shared/api/endpoints';
import type { ApiDataResponse } from '@/shared/api/types';
import type { User, UserSelectOption } from '../model/types';

export const userApi = {
  getUser: (id: string | number) =>
    apiClient.get<ApiDataResponse<User>>(API_ENDPOINTS.USERS.BY_ID(id)),

  getUserSelectOptions: async (role?: number): Promise<UserSelectOption[]> => {
    const { data } = await apiClient.get<ApiDataResponse<UserSelectOption[]>>(API_ENDPOINTS.USERS.SELECT, {
      params: { role },
    });
    return data.data;
  },
};
