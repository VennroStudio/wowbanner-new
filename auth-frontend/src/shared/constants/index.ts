export const API_URL = import.meta.env.VITE_API_URL;

export const ROUTES = {
  REGISTER: '/register',
  FORGOT_PASSWORD: '/forgot-password',
  RESET_PASSWORD: '/password-reset-confirm',
  VERIFY_EMAIL: '/email-verification',
  HOME: '/',
  ADMIN_USERS: '/admin/users',
} as const;

export const API_ENDPOINTS = {
  AUTH: {
    LOGIN: '/auth/login',
    REFRESH: '/auth/refresh',
    LOGOUT: '/auth/logout',
    CONFIRM_EMAIL: '/auth/confirm-email',
    PASSWORD_RESET: '/auth/password-reset',
    PASSWORD_RESET_CONFIRM: '/auth/password-reset/confirm',
  },
  USERS: {
    LIST: '/users',
    CREATE: '/users/create',
    BASE: '/users',
    BY_ID: (id: string | number) => `/users/${id}`,
    UPDATE: (id: string | number) => `/users/update/${id}`,
    DELETE: (id: string | number) => `/users/delete/${id}`,
    AVATAR: (id: string | number) => `/users/${id}/avatar`,
  },
} as const;
