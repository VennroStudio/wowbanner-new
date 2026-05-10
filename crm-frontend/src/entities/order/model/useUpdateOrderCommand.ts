import { useMutation, useQueryClient } from '@tanstack/react-query';
import { orderApi } from '../api/order.api';
import type { UpdateOrderBody } from '../model/types';
import { orderKeys } from './query-keys';

export const useUpdateOrderCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, body }: { id: number | string; body: UpdateOrderBody }) =>
      orderApi.updateOrder(id, body),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: orderKeys.lists() });
      queryClient.invalidateQueries({ queryKey: orderKeys.detail(id) });
    },
  });
};
