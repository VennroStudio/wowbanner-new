import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import { processingKeys } from './query-keys';

export const useUploadProcessingImagesCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({
      processingId,
      files,
      imageAlts,
    }: {
      processingId: number | string;
      files: File[];
      imageAlts: string[];
    }) => processingApi.uploadImages(processingId, files, imageAlts),
    onSuccess: (_, { processingId }) => {
      queryClient.invalidateQueries({ queryKey: processingKeys.lists() });
      queryClient.invalidateQueries({ queryKey: processingKeys.detail(processingId) });
    },
  });
};
