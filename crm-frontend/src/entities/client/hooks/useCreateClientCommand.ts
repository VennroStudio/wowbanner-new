import { useMutation, useQueryClient } from '@tanstack/react-query';
import { clientApi } from '../api/api';
import type { CreateClientBody } from '../api/api';

export const useCreateClientCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateClientBody) => clientApi.createClient(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['clients'] });
    },
  });
};
