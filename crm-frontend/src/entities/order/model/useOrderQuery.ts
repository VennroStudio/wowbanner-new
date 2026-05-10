import { useQuery, type UseQueryOptions } from '@tanstack/react-query';
import { orderApi } from '../api/order.api';
import { orderKeys } from './query-keys';

type OrderResponse = Awaited<ReturnType<typeof orderApi.getOrder>>;

export const useOrderQuery = (
  id: number | string,
  options?: Pick<UseQueryOptions<OrderResponse>, 'enabled'>,
) => {
  return useQuery({
    queryKey: orderKeys.detail(id),
    queryFn: () => orderApi.getOrder(id),
    enabled: options?.enabled ?? true,
  });
};
