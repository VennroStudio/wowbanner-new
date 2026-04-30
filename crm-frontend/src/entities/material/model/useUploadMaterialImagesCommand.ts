import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/material.api';
import { materialKeys } from './query-keys';

export const useUploadMaterialImagesCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({
      materialId,
      files,
      imageAlts,
    }: {
      materialId: number | string;
      files: File[];
      imageAlts: string[];
    }) => materialApi.uploadImages(materialId, files, imageAlts),
    onSuccess: (_, { materialId }) => {
      queryClient.invalidateQueries({ queryKey: materialKeys.lists() });
      queryClient.invalidateQueries({ queryKey: materialKeys.detail(materialId) });
    },
  });
};
