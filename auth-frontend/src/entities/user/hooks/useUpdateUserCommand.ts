import { useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi, type UpdateUserBody } from '../api/api';

export const useUpdateUserCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async ({ id, data }: { id: number; data: UpdateUserBody }) => {
      const response = await userApi.updateUser(id, data);
      return response.data;
    },
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['user', id] });
      queryClient.invalidateQueries({ queryKey: ['session'] });
      queryClient.invalidateQueries({ queryKey: ['users'] });
    },
  });
};
