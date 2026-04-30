import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/material.api';
import { materialKeys } from './query-keys';

export const useUpdateMaterialImageAltCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (payload: { imageId: number | string; alt: string; materialId: number | string }) =>
      materialApi.updateImageAlt(payload.imageId, payload.alt),
    onSuccess: (_, { materialId }) => {
      queryClient.invalidateQueries({ queryKey: materialKeys.lists() });
      queryClient.invalidateQueries({ queryKey: materialKeys.detail(materialId) });
    },
  });
};
