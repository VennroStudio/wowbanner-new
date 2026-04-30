import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/processing.api';
import { processingKeys } from './query-keys';

export const useDeleteProcessingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => processingApi.deleteProcessing(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: processingKeys.lists() });
    },
  });
};
