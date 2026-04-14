import { useMutation, useQueryClient } from '@tanstack/react-query';
import { printingApi } from '../api/api';
import type { CreatePrintingBody } from '../api/api';

export const useCreatePrintingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreatePrintingBody) => printingApi.createPrinting(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['printings'] });
    },
  });
};
