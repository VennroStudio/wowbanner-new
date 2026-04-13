import axios from 'axios';
import { API_URL, AUTH_URL } from '@/shared/constants';
import { getAccessToken } from './accessToken';

export const apiClient = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
  },
});

apiClient.interceptors.request.use((config) => {
  const token = getAccessToken();
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    // If we catch a 401 Unauthorized across the CRM, redirect to Auth
    if (error.response?.status === 401) {
      window.location.href = AUTH_URL;
    }
    return Promise.reject(error);
  }
);

export { API_URL, AUTH_URL };
export { getCookie } from './cookie';
