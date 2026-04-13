import { useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi, type AdminUpdateUserBody } from '../api/api';

export const useAdminUpdateUserCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async ({ id, data }: { id: number; data: AdminUpdateUserBody }) => {
      const response = await userApi.adminUpdateUser(id, data);
      return response.data;
    },
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['user', id] });
      queryClient.invalidateQueries({ queryKey: ['session'] });
      queryClient.invalidateQueries({ queryKey: ['users'] });
    },
  });
};
