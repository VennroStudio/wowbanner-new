import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import type { UpdateProcessingBody } from '../api/processing.api';
import { processingKeys } from './query-keys';

export const useUpdateProcessingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateProcessingBody }) =>
      processingApi.updateProcessing(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: processingKeys.lists() });
      queryClient.invalidateQueries({ queryKey: processingKeys.detail(id) });
    },
  });
};
