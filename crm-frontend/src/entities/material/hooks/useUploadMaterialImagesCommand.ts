import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/api';

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
      queryClient.invalidateQueries({ queryKey: ['materials'] });
      queryClient.invalidateQueries({ queryKey: ['material', materialId] });
    },
  });
};
