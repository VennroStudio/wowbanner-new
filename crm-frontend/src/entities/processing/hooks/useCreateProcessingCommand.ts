import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/api';
import type { CreateProcessingBody } from '../api/api';

export const useCreateProcessingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateProcessingBody) => processingApi.createProcessing(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['processings'] });
    },
  });
};
