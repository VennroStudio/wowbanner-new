import { useQuery } from '@tanstack/react-query';
import { useAuthStore } from '@/features/auth';
import { userApi } from '@/entities/user';

export const useSessionQuery = () => {
  const { user, accessToken, setUser } = useAuthStore();

  return useQuery({
    queryKey: ['session'],
    queryFn: async () => {
      if (!accessToken || !user?.id) return null;
      const response = await userApi.getUser(user.id);
      const userData = response.data.data;
      setUser(userData);
      return userData;
    },
    enabled: !!accessToken && !!user?.id,
    staleTime: 1000 * 60 * 5, // 5 minutes
  });
};
