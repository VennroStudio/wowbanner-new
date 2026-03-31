import React, { useEffect } from 'react';
import { useAuthStore } from '@/features/auth/store/authStore';
import { useRefreshCommand } from '@/features/auth/hooks/useRefreshCommand';
import { getCookie } from '@/shared/api/cookie';

export const AuthInit: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { setIsLoading, isLoading } = useAuthStore();
  const refresh = useRefreshCommand();

  useEffect(() => {
    const initAuth = async () => {
      if (!getCookie('logged_in')) {
        setIsLoading(false);
        return;
      }

      try {
        await refresh.mutateAsync();
      } catch (err) {
        console.error('Silent refresh failed', err);
      } finally {
        setIsLoading(false);
      }
    };

    initAuth();
  }, [setIsLoading]);

  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-slate-50">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return <>{children}</>;
};
