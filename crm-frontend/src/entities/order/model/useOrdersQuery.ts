import { useQuery } from '@tanstack/react-query';
import { orderApi } from '../api/order.api';
import type { GetOrdersParams } from './types';
import { orderKeys } from './query-keys';

export const useOrdersQuery = (params?: GetOrdersParams) => {
  const normalizedParams = {
    page: params?.page || 1,
    perPage: params?.perPage || 20,
    search: params?.search || '',
    dateFrom: params?.dateFrom || '',
    dateTo: params?.dateTo || '',
    printId: params?.printId || 0,
    materialId: params?.materialId || 0,
    optionId: params?.optionId || 0,
    docs: params?.docs || 0,
    managerId: params?.managerId || 0,
    designerId: params?.designerId || 0,
    statusType: params?.statusType || 0,
    storageType: params?.storageType || 0,
    serviceType: params?.serviceType || 0,
    archived: params?.archived ?? false,
    deleted: params?.deleted ?? false,
  };

  return useQuery({
    queryKey: orderKeys.list(normalizedParams),
    queryFn: () => orderApi.getOrders(params),
  });
};
