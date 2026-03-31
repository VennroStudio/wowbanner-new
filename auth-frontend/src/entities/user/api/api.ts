import { API_ENDPOINTS } from '@/shared/constants';
import { apiClient } from '@/shared/api/client';
import type { User } from '../model/types';
import type { ApiResponse } from '@/shared/types';

export const userApi = {
  getUser: (userId: number) =>
    apiClient.get<ApiResponse<User>>(API_ENDPOINTS.USERS.BY_ID(userId)),

  updateUser: (userId: number, data: Partial<User>) =>
    apiClient.put<ApiResponse<User>>(API_ENDPOINTS.USERS.BY_ID(userId), data),

  register: (data: { firstName: string; lastName: string; email: string }) =>
    apiClient.post(API_ENDPOINTS.USERS.CREATE, data),

  deleteUser: (userId: number) =>
    apiClient.delete(API_ENDPOINTS.USERS.BY_ID(userId)),

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
