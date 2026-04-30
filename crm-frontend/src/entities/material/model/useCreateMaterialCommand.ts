import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/material.api';
import type { CreateMaterialBody } from '../api/material.api';
import { materialKeys } from './query-keys';

export const useCreateMaterialCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateMaterialBody) => materialApi.createMaterial(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: materialKeys.lists() });
    },
  });
};
