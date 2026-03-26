import type { ApiFetchFn } from '@/shared/api/client';
import type { RegisterDto } from '../model/types';
import { API_ENDPOINTS } from '@/shared/constants';

export const userApi = {
  register: (apiFetch: ApiFetchFn, data: RegisterDto) =>
    apiFetch(API_ENDPOINTS.USERS.CREATE, {
      method: 'POST',
      body: JSON.stringify(data),
    }),

  getUser: (apiFetch: ApiFetchFn, userId: string | number) =>
    apiFetch(API_ENDPOINTS.USERS.BY_ID(userId), { method: 'GET' }),

  uploadAvatar: (apiFetch: ApiFetchFn, userId: string | number, file: File) => {
    const formData = new FormData();
    formData.append('avatar', file);
    return apiFetch(API_ENDPOINTS.USERS.AVATAR(userId), {
      method: 'POST',
      body: formData,
    });
  },

  deleteAvatar: (apiFetch: ApiFetchFn, userId: string | number) =>
    apiFetch(API_ENDPOINTS.USERS.AVATAR(userId), { method: 'DELETE' }),
};
