import { useMutation, useQueryClient } from '@tanstack/react-query';
import { printingApi } from '../api/api';
import type { UpdatePrintingBody } from '../api/api';

export const useUpdatePrintingCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdatePrintingBody }) =>
      printingApi.updatePrinting(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['printings'] });
      queryClient.invalidateQueries({ queryKey: ['printing', id] });
    },
  });
};
