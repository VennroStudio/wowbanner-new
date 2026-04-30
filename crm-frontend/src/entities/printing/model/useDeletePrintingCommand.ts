import { useMutation, useQueryClient } from '@tanstack/react-query';
import { printingApi } from '../api/printing.api';
import { printingKeys } from './query-keys';

export const useDeletePrintingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => printingApi.deletePrinting(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: printingKeys.lists() });
    },
  });
};
