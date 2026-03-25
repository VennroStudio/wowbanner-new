import React, { useState, useEffect, createContext, useContext } from 'react';
import type { User } from '../types';
import { parseJwtId } from '../utils/jwt';

const API_URL = import.meta.env.VITE_API_URL;

interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (accessToken: string) => Promise<void>;
  logout: () => Promise<void>;
  apiFetch: (endpoint: string, options?: RequestInit) => Promise<any>;
}

const getCookie = (name: string) => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop()?.split(';').shift();
};

const AuthContext = createContext<AuthContextType | null>(null);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [accessToken, setAccessToken] = useState<string | null>(null);
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  const apiFetch = async (endpoint: string, options: RequestInit = {}) => {
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
    const res = await apiFetch(`/users/${userId}`, { method: 'GET' });
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
  }, []);

  return (
    <AuthContext.Provider value={{ user, isAuthenticated: !!user, isLoading, login, logout, apiFetch }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
};
