import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/entities/user';

export const useConfirmResetCommand = () => {
  return useMutation({
    mutationFn: async ({ token, password }: { token: string; password: string }) => {
      const response = await authApi.confirmPasswordReset(token, password);
      return response.data;
    },
  });
};
