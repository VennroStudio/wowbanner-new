import { useMutation } from '@tanstack/react-query';
import { authApi } from '../api';

export const useRequestResetCommand = () => {
  return useMutation({
    mutationFn: async (email: string) => {
      const response = await authApi.requestPasswordReset(email);
      return response.data;
    },
  });
};
