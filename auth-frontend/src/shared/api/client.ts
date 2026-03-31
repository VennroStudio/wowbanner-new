import axios from 'axios';
import { API_URL } from '@/shared/constants';
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

export { API_URL };
export { getCookie } from './cookie';
