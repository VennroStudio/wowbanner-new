import { API_ENDPOINTS } from '@/shared/constants';
import { apiClient } from '@/shared/api/client';
import type { User } from '../model/types';
import type { ApiResponse } from '@/shared/types';

export type UpdateUserBody = {
  firstName: string;
  lastName: string;
  email: string;
};

export type UsersListParams = {
  page?: number;
  perPage?: number;
  search?: string;
};

export const userApi = {
  getUsers: (params?: UsersListParams) =>
    apiClient.get<ApiResponse<{ count: number; items: User[] }>>(API_ENDPOINTS.USERS.LIST, {
      params: {
        page: params?.page ?? 1,
        perPage: params?.perPage ?? 20,
        ...(params?.search ? { search: params.search } : {}),
      },
    }),

  getUser: (userId: number) =>
    apiClient.get<ApiResponse<User>>(API_ENDPOINTS.USERS.BY_ID(userId)),

  updateUser: (userId: number, data: UpdateUserBody) =>
    apiClient.patch(API_ENDPOINTS.USERS.UPDATE(userId), data),

  register: (data: { firstName: string; lastName: string; email: string }) =>
    apiClient.post(API_ENDPOINTS.USERS.CREATE, data),

  deleteUser: (userId: number) => apiClient.delete(API_ENDPOINTS.USERS.DELETE(userId)),

  uploadAvatar: (userId: number, file: File) => {
    const formData = new FormData();
    formData.append('avatar', file);
    return apiClient.post(API_ENDPOINTS.USERS.AVATAR(userId), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
  },

  deleteAvatar: (userId: number) =>
    apiClient.delete(API_ENDPOINTS.USERS.AVATAR(userId)),
};
