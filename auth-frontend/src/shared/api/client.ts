import { API_URL } from '@/shared/constants';

export type ApiFetchFn = (endpoint: string, options?: RequestInit) => Promise<any>;

let apiFetchInstance: ApiFetchFn | null = null;

export const setApiFetch = (fn: ApiFetchFn) => {
  apiFetchInstance = fn;
};

export const apiClient = async (endpoint: string, options?: RequestInit): Promise<unknown> => {
  if (!apiFetchInstance) {
    throw new Error('API client not initialized. Call setApiFetch() first.');
  }
  return apiFetchInstance(endpoint, options);
};

export const getCookie = (name: string): string | undefined => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop()?.split(';').shift();
};

export { API_URL };
