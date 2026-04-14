import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/api';

export const useDeleteMaterialCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => materialApi.deleteMaterial(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['materials'] });
    },
  });
};
