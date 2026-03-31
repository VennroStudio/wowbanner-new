import { useAuthStore } from '@/features/auth';
import { useLoginCommand } from '@/features/auth';
import { useLogoutCommand } from '@/features/auth';
import { useRefreshCommand } from '@/features/auth';
import { useSessionQuery } from '@/features/auth';

export const useAuth = () => {
  const store = useAuthStore();
  const login = useLoginCommand();
  const logout = useLogoutCommand();
  const refresh = useRefreshCommand();
  const session = useSessionQuery();

  const isAdmin = store.user?.role?.id === 1;

  return {
    ...store,
    login,
    logout,
    refresh,
    session,
    isAdmin,
  };
};
