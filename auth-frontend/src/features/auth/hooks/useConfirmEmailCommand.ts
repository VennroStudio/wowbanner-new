import { useMutation } from '@tanstack/react-query';
import { authApi } from '../api';

export const useConfirmEmailCommand = () => {
  return useMutation({
    mutationFn: async (token: string) => {
      const response = await authApi.confirmEmail(token);
      return response.data;
    },
  });
};
