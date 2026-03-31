import { useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi } from '../api/api';

export const useDeleteUserCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (id: number) => {
      const response = await userApi.deleteUser(id);
      return response.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] });
    },
  });
};
