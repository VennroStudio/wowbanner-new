import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/material.api';
import { materialKeys } from './query-keys';

export const useDeleteMaterialImageCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (payload: { imageId: number | string; materialId: number | string }) =>
      materialApi.deleteImage(payload.imageId),
    onSuccess: (_, { materialId }) => {
      queryClient.invalidateQueries({ queryKey: materialKeys.lists() });
      queryClient.invalidateQueries({ queryKey: materialKeys.detail(materialId) });
    },
  });
};
