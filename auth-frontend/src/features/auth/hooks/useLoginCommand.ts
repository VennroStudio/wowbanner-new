import { useMutation, useQueryClient } from '@tanstack/react-query';
import { authApi } from '../api';
import { useAuthStore } from '../store/authStore';
import { userApi } from '@/entities/user';
import { parseJwtId } from '@/shared/utils';
import type { LoginDto } from '../types';

export const useLoginCommand = () => {
  const queryClient = useQueryClient();
  const { setAccessToken, setUser } = useAuthStore();

  return useMutation({
    mutationFn: async (data: LoginDto) => {
      const response = await authApi.login(data);
      const { access_token } = response.data.data;
      
      const userId = parseJwtId(access_token);
      if (!userId) throw new Error('Invalid token');
      
      setAccessToken(access_token);
      const userRes = await userApi.getUser(userId);
      setUser(userRes.data.data);
      return response.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['user'] });
    },
  });
};
