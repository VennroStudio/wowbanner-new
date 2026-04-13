import { useMutation, useQueryClient } from '@tanstack/react-query';
import { clientApi } from '../api/api';
import type { UpdateClientBody } from '../api/api';

export const useUpdateClientCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateClientBody }) =>
      clientApi.updateClient(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['clients'] });
      queryClient.invalidateQueries({ queryKey: ['client', id] });
    },
  });
};
