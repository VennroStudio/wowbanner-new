import { useMutation, useQueryClient } from '@tanstack/react-query';
import { processingApi } from '../api/api';

export const useDeleteProcessingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => processingApi.deleteProcessing(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['processings'] });
    },
  });
};
