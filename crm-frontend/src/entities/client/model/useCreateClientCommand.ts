import { useMutation, useQueryClient } from '@tanstack/react-query';
import { clientApi } from '../api/client.api';
import type { CreateClientBody } from '../api/client.api';
import { clientKeys } from './query-keys';

export const useCreateClientCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateClientBody) => clientApi.createClient(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: clientKeys.lists() });
    },
  });
};
