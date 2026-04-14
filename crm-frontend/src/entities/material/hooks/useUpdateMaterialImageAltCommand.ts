import { useMutation, useQueryClient } from '@tanstack/react-query';
import { materialApi } from '../api/api';

export const useUpdateMaterialImageAltCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ imageId, alt, materialId }: { imageId: number | string; alt: string; materialId: number | string }) =>
      materialApi.updateImageAlt(imageId, alt),
    onSuccess: (_, { materialId }) => {
      queryClient.invalidateQueries({ queryKey: ['materials'] });
      queryClient.invalidateQueries({ queryKey: ['material', materialId] });
    },
  });
};
