import React, { useState, useEffect } from 'react';
import type { User } from '@/entities/user';
import { AuthContext } from './authContext';
import { parseJwtId } from '@/shared/utils';
import { API_URL } from '@/shared/constants';
import { getCookie } from '@/shared/api/client';

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [accessToken, setAccessToken] = useState<string | null>(null);
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  const apiFetch = async (endpoint: string, options: RequestInit = {}): Promise<unknown> => {
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
        const refreshRes = await fetch(`${API_URL}/auth/refresh`, {
          method: 'POST',
          credentials: 'include',
        });
        if (refreshRes.ok) {
          const { data } = await refreshRes.json();
          setAccessToken(data.access_token);
          headers.set('Authorization', `Bearer ${data.access_token}`);
          response = await fetch(`${API_URL}${endpoint}`, { ...config, headers });
        } else {
          throw new Error('Refresh failed');
        }
      } catch {
        setAccessToken(null);
        setUser(null);
        throw { error: { message: 'Сессия истекла. Пожалуйста, войдите снова.' } };
      }
    }

    if (response.status === 204) return null;

    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw data;
    return data;
  };

  const fetchUserProfile = async (token: string) => {
    const userId = parseJwtId(token);
    if (!userId) throw new Error('Invalid token structure');
    setAccessToken(token);
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const res = await apiFetch(`/users/${userId}`, { method: 'GET' }) as any;
    setUser(res.data);
  };

  const login = async (token: string) => {
    try {
      await fetchUserProfile(token);
    } catch (err) {
      setAccessToken(null);
      throw err;
    }
  };

  const logout = async () => {
    try {
      await apiFetch('/auth/logout', { method: 'POST' });
    } catch {
      // ignore
    }
    setAccessToken(null);
    setUser(null);
  };

  // Тихий рефреш при загрузке приложения
  useEffect(() => {
    const initAuth = async () => {
      // Если сигнальной куки нет (ставится бэкендом) — мы гость, запрос не делаем
      if (!getCookie('logged_in')) {
        setIsLoading(false);
        return;
      }

      try {
        const res = await fetch(`${API_URL}/auth/refresh`, {
          method: 'POST',
          credentials: 'include',
        });
        if (res.ok) {
          const { data } = await res.json();
          await fetchUserProfile(data.access_token);
        }
      } catch {
        console.log('Silent refresh failed — user not logged in');
      } finally {
        setIsLoading(false);
      }
    };
    initAuth();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <AuthContext.Provider value={{ user, isAuthenticated: !!user, isLoading, login, logout, apiFetch }}>
      {children}
    </AuthContext.Provider>
  );
};
