import { useQuery } from '@tanstack/react-query';
import { orderApi } from '../api/order.api';
import { orderKeys } from './query-keys';

const STALE_MS = 1000 * 60 * 60;

export const useOrderPaymentTypesQuery = () =>
  useQuery({
    queryKey: orderKeys.paymentTypes(),
    queryFn: () => orderApi.getPaymentTypes(),
    staleTime: STALE_MS,
  });
