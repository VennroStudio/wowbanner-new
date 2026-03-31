import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/entities/user';

export const useRequestResetCommand = () => {
  return useMutation({
    mutationFn: async (email: string) => {
      const response = await authApi.requestPasswordReset(email);
      return response.data;
    },
  });
};
