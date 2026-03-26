import type { ApiFetchFn } from '@/shared/api/client';
import type { RegisterDto } from '../model/types';

export const userApi = {
  register: (apiFetch: ApiFetchFn, data: RegisterDto) =>
    apiFetch('/users/create', {
      method: 'POST',
      body: JSON.stringify(data),
    }),

  getUser: (apiFetch: ApiFetchFn, userId: string | number) =>
    apiFetch(`/users/${userId}`, { method: 'GET' }),
};
