import { useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi } from '../api/api';

export const useDeleteAvatarCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (id: number) => {
      const response = await userApi.deleteAvatar(id);
      return response.data;
    },
    onSuccess: (_, id) => {
      queryClient.invalidateQueries({ queryKey: ['user', id] });
      queryClient.invalidateQueries({ queryKey: ['session'] });
    },
  });
};
