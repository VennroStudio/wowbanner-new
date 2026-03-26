import { useState, useEffect, useCallback } from 'react';
import type { User } from '@/entities/user';
import { userApi } from '@/entities/user';
import { getCookie } from '@/shared/api/client';
import { parseJwtId } from '@/shared/utils';
import { authApi } from '../api';
import type { AuthContextType } from '../types';
import { useApiFetch } from './useApiFetch';

export const useAuthProvider = (): AuthContextType => {
  const [accessToken, setAccessToken] = useState<string | null>(null);
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  const onSessionExpired = useCallback(() => {
    setAccessToken(null);
    setUser(null);
  }, []);

  const apiFetch = useApiFetch({
    accessToken,
    setAccessToken,
    onSessionExpired,
  });

  const fetchUserProfile = useCallback(
    async (token: string) => {
      const userId = parseJwtId(token);
      if (!userId) throw new Error('Invalid token structure');
      setAccessToken(token);
      const res = await userApi.getUser(apiFetch, userId);
      setUser(res.data);
    },
    [apiFetch],
  );

  const login = useCallback(
    async (token: string) => {
      try {
        await fetchUserProfile(token);
      } catch (err) {
        setAccessToken(null);
        throw err;
      }
    },
    [fetchUserProfile],
  );

  const logout = useCallback(async () => {
    try {
      await authApi.logout(apiFetch);
    } catch {
      // ignore
    }
    setAccessToken(null);
    setUser(null);
  }, [apiFetch]);

  // Тихий рефреш при загрузке приложения
  useEffect(() => {
    const initAuth = async () => {
      if (!getCookie('logged_in')) {
        setIsLoading(false);
        return;
      }

      try {
        const res = await authApi.refresh();
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

  return {
    user,
    isAuthenticated: !!user,
    isAdmin: user?.role?.id === 1,
    isLoading,
    login,
    logout,
    apiFetch,
  };
};
