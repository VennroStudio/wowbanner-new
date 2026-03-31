import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/entities/user';
import { useAuthStore } from '@/features/auth';
import { userApi } from '@/entities/user';
import { parseJwtId } from '@/shared/utils';

export const useRefreshCommand = () => {
  const { setAccessToken, setUser } = useAuthStore();

  return useMutation({
    mutationFn: async () => {
      const response = await authApi.refresh();
      const { access_token } = response.data.data;
      
      const userId = parseJwtId(access_token);
      if (!userId) throw new Error('Invalid token');
      
      setAccessToken(access_token);
      const userRes = await userApi.getUser(userId);
      setUser(userRes.data.data);
      return access_token;
    },
  });
};
