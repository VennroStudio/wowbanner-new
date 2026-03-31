import { useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi } from '../api/api';
import type { User } from '../model/types';

export const useUpdateUserCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async ({ id, data }: { id: number; data: Partial<User> }) => {
      const response = await userApi.updateUser(id, data);
      return response.data;
    },
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['user', id] });
      queryClient.invalidateQueries({ queryKey: ['session'] });
    },
  });
};
