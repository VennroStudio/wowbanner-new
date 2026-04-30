import { useMutation, useQueryClient } from '@tanstack/react-query';
import { clientApi } from '../api/client.api';
import type { UpdateClientBody } from '../api/client.api';
import { clientKeys } from './query-keys';

export const useUpdateClientCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateClientBody }) =>
      clientApi.updateClient(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: clientKeys.lists() });
      queryClient.invalidateQueries({ queryKey: clientKeys.detail(id) });
    },
  });
};
