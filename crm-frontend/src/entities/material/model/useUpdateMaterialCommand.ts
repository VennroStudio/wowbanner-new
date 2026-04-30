import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/material.api';
import type { UpdateMaterialBody } from '../api/material.api';
import { materialKeys } from './query-keys';

export const useUpdateMaterialCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateMaterialBody }) =>
      materialApi.updateMaterial(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: materialKeys.lists() });
      queryClient.invalidateQueries({ queryKey: materialKeys.detail(id) });
    },
  });
};
