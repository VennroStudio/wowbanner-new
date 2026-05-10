import { useMutation, useQueryClient } from '@tanstack/react-query';
import { orderApi } from '../api/order.api';
import type { CreateOrderBody } from '../model/types';
import { orderKeys } from './query-keys';

export const useCreateOrderCommand = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (body: CreateOrderBody) => orderApi.createOrder(body),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: orderKeys.lists() });
    },
  });
};
