import { useMutation, useQueryClient } from '@tanstack/react-query';
import { printingApi } from '../api/printing.api';
import type { UpdatePrintingBody } from '../api/printing.api';
import { printingKeys } from './query-keys';

export const useUpdatePrintingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdatePrintingBody }) =>
      printingApi.updatePrinting(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: printingKeys.lists() });
      queryClient.invalidateQueries({ queryKey: printingKeys.detail(id) });
    },
  });
};
