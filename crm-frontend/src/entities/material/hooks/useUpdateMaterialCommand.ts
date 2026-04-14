import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/api';
import type { UpdateMaterialBody } from '../api/api';

export const useUpdateMaterialCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateMaterialBody }) =>
      materialApi.updateMaterial(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['materials'] });
      queryClient.invalidateQueries({ queryKey: ['material', id] });
    },
  });
};
