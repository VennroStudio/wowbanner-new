import { useMutation, useQueryClient } from '@tanstack/react-query';
import { authApi } from '../api';
import { useAuthStore } from '../store/authStore';

export const useLogoutCommand = () => {
  const queryClient = useQueryClient();
  const { logout: logoutStore } = useAuthStore();

  return useMutation({
    mutationFn: async () => {
      try {
        await authApi.logout();
      } finally {
        logoutStore();
        queryClient.clear();
      }
    },
  });
};
