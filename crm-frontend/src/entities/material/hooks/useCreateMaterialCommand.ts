import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/api';
import type { CreateMaterialBody } from '../api/api';

export const useCreateMaterialCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateMaterialBody) => materialApi.createMaterial(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['materials'] });
    },
  });
};
