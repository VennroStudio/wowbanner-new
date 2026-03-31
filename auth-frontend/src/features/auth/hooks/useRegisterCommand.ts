import { useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi } from '@/entities/user';
import type { RegisterDto } from '@/entities/user';

export const useRegisterCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: RegisterDto) => {
      const response = await userApi.register(data);
      return response.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] });
    },
  });
};
