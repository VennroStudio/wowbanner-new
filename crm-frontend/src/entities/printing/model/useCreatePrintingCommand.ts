import { useMutation, useQueryClient } from '@tanstack/react-query';
import { printingApi } from '../api/printing.api';
import type { CreatePrintingBody } from '../api/printing.api';
import { printingKeys } from './query-keys';

export const useCreatePrintingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreatePrintingBody) => printingApi.createPrinting(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: printingKeys.lists() });
    },
  });
};
