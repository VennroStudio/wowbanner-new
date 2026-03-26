import { authApi } from '../api';
import { API_URL } from '@/shared/constants';

export type ApiFetchFn = (endpoint: string, options?: RequestInit) => Promise<any>;

interface UseApiFetchProps {
  accessToken: string | null;
  setAccessToken: (token: string | null) => void;
  onSessionExpired: () => void;
}

export const useApiFetch = ({
  accessToken,
  setAccessToken,
  onSessionExpired,
}: UseApiFetchProps): ApiFetchFn => {
  const apiFetch = async (endpoint: string, options: RequestInit = {}): Promise<any> => {
    const headers = new Headers(options.headers || {});

    if (accessToken) {
      headers.set('Authorization', `Bearer ${accessToken}`);
    }
    if (!(options.body instanceof FormData)) {
      headers.set('Content-Type', 'application/json');
    }

    const config: RequestInit = {
      ...options,
      headers,
      credentials: 'include',
    };

    let response = await fetch(`${API_URL}${endpoint}`, config);

    // Перехват 401: пытаемся обновить токен
    if (
      response.status === 401 &&
      !endpoint.includes('/auth/refresh') &&
      !endpoint.includes('/auth/login')
    ) {
      try {
        const refreshRes = await authApi.refresh();
        if (refreshRes.ok) {
          const { data } = await refreshRes.json();
          setAccessToken(data.access_token);
          headers.set('Authorization', `Bearer ${data.access_token}`);
          response = await fetch(`${API_URL}${endpoint}`, { ...config, headers });
        } else {
          throw new Error('Refresh failed');
        }
      } catch {
        onSessionExpired();
        throw { error: { message: 'Сессия истекла. Пожалуйста, войдите снова.' } };
      }
    }

    if (response.status === 204) return null;

    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw data;
    return data;
  };

  return apiFetch;
};
