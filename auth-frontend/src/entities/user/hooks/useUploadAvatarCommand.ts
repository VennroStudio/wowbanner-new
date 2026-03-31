import { useMutation, useQueryClient } from '@tanstack/react-query';
import { userApi } from '../api/api';

export const useUploadAvatarCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async ({ id, file }: { id: number; file: File }) => {
      const response = await userApi.uploadAvatar(id, file);
      return response.data;
    },
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['user', id] });
      queryClient.invalidateQueries({ queryKey: ['session'] });
    },
  });
};
