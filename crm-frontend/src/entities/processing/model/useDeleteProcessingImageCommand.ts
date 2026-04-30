import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import { processingKeys } from './query-keys';

export const useDeleteProcessingImageCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (payload: {
      imageId: number | string;
      processingId: number | string;
    }) => processingApi.deleteImage(payload.imageId),
    onSuccess: (_, { processingId }) => {
      queryClient.invalidateQueries({ queryKey: processingKeys.lists() });
      queryClient.invalidateQueries({ queryKey: processingKeys.detail(processingId) });
    },
  });
};
