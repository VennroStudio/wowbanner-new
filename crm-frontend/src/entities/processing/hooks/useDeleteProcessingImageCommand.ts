import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/api';

export const useDeleteProcessingImageCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({
      imageId,
      processingId,
    }: {
      imageId: number | string;
      processingId: number | string;
    }) => processingApi.deleteImage(imageId),
    onSuccess: (_, { processingId }) => {
      queryClient.invalidateQueries({ queryKey: ['processings'] });
      queryClient.invalidateQueries({ queryKey: ['processing', processingId] });
    },
  });
};
