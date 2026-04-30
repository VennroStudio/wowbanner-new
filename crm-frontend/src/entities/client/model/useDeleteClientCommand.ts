import { useMutation, useQueryClient } from '@tanstack/react-query';
import { clientApi } from '../api/client.api';
import { clientKeys } from './query-keys';

export const useDeleteClientCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => clientApi.deleteClient(id),
    onSuccess: (_, id) => {
      queryClient.invalidateQueries({ queryKey: clientKeys.lists() });
      queryClient.invalidateQueries({ queryKey: clientKeys.detail(id) });
    },
  });
};
