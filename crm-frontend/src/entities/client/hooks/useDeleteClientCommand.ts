import { useMutation, useQueryClient } from '@tanstack/react-query';
import { clientApi } from '../api/api';

export const useDeleteClientCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => clientApi.deleteClient(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['clients'] });
    },
  });
};
