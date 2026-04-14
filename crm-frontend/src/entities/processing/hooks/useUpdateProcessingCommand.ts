import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/api';
import type { UpdateProcessingBody } from '../api/api';

export const useUpdateProcessingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateProcessingBody }) =>
      processingApi.updateProcessing(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['processings'] });
      queryClient.invalidateQueries({ queryKey: ['processing', id] });
    },
  });
};
