import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/api';

export const useDeleteMaterialImageCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ imageId, materialId }: { imageId: number | string; materialId: number | string }) =>
      materialApi.deleteImage(imageId),
    onSuccess: (_, { materialId }) => {
      queryClient.invalidateQueries({ queryKey: ['materials'] });
      queryClient.invalidateQueries({ queryKey: ['material', materialId] });
    },
  });
};
