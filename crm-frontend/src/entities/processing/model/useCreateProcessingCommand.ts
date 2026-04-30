import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import type { CreateProcessingBody } from '../api/processing.api';
import { processingKeys } from './query-keys';

export const useCreateProcessingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateProcessingBody) => processingApi.createProcessing(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: processingKeys.lists() });
    },
  });
};
