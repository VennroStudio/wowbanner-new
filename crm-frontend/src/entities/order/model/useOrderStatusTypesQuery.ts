import { useQuery } from '@tanstack/react-query';
import { orderApi } from '../api/order.api';
import { orderKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

export const useOrderStatusTypesQuery = () =>
  useQuery({
    queryKey: orderKeys.statusTypes(),
    queryFn: () => orderApi.getStatusTypes(),
    staleTime: STALE_MS,
  });
