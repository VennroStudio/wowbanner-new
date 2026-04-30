import React, { useEffect } from 'react';
import { useRefreshSession, useSessionStore } from '@/entities/session';
import { getCookie } from '@/shared/api/client';
import { AUTH_URL } from '@/shared/config/env';

export const AuthInit: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { setIsLoading, isLoading } = useSessionStore();
  const { mutateAsync: refreshSession } = useRefreshSession();

  useEffect(() => {
    const initAuth = async () => {
      if (!getCookie('logged_in')) {
        setIsLoading(false);
        // Если пользователь не залогинен - редирект на страницу авторизации
        window.location.href = AUTH_URL;
        return;
      }

      try {
        await refreshSession();
      } catch (err) {
        console.error('Silent refresh failed', err);
        // В случае ошибки (токен стух/неверный) тоже редирект на страницу авторизации
        window.location.href = AUTH_URL;
      } finally {
        setIsLoading(false);
      }
    };

    initAuth();
  }, [refreshSession, setIsLoading]);

  // Пока грузится первоначальная сессия - показываем спиннер, а не белый экран или куски интерфейса
  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-slate-50">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return <>{children}</>;
};
