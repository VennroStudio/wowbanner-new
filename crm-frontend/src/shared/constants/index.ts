export const API_URL = import.meta.env.VITE_API_URL;
export const AUTH_URL = import.meta.env.VITE_AUTH_URL;

export const API_ENDPOINTS = {
  AUTH: {
    REFRESH: '/auth/refresh',
  },
  USERS: {
    BY_ID: (id: string | number) => `/users/${id}`,
  },
} as const;
