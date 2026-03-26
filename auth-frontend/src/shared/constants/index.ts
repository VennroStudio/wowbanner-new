export const API_URL = import.meta.env.VITE_API_URL;

export const ROUTES = {
  LOGIN: '/login',
  REGISTER: '/register',
  FORGOT_PASSWORD: '/forgot-password',
  RESET_PASSWORD: '/password-reset-confirm',
  VERIFY_EMAIL: '/email-verification',
  HOME: '/',
} as const;
