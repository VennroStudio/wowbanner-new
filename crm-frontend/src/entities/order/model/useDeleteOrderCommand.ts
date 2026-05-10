import { useMutation, useQueryClient } from '@tanstack/react-query';
import { orderApi } from '../api/order.api';
import { orderKeys } from './query-keys';

export const useDeleteOrderCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number | string) => orderApi.deleteOrder(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: orderKeys.lists() });
    },
  });
};
