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
    printIds: [...(params?.printIds ?? [])].sort((left, right) => left - right),
    materialId: params?.materialId || 0,
    optionId: params?.optionId || 0,
    docs: params?.docs || 0,
    managerId: params?.managerId || 0,
    designerId: params?.designerId || 0,
    statusTypes: [...(params?.statusTypes ?? [])].sort((left, right) => left - right),
    storageType: params?.storageType || 0,
    serviceType: params?.serviceType || 0,
    archived: params?.archived ?? false,
    deleted: params?.deleted ?? false,
  };

  const requestParams: GetOrdersParams = {
    page: normalizedParams.page,
    perPage: normalizedParams.perPage,
    search: normalizedParams.search || undefined,
    dateFrom: normalizedParams.dateFrom || undefined,
    dateTo: normalizedParams.dateTo || undefined,
    printIds: normalizedParams.printIds.length > 0 ? normalizedParams.printIds : undefined,
    materialId: normalizedParams.materialId || undefined,
    optionId: normalizedParams.optionId || undefined,
    docs: normalizedParams.docs || undefined,
    managerId: normalizedParams.managerId || undefined,
    designerId: normalizedParams.designerId || undefined,
    statusTypes: normalizedParams.statusTypes.length > 0 ? normalizedParams.statusTypes : undefined,
    storageType: normalizedParams.storageType || undefined,
    serviceType: normalizedParams.serviceType || undefined,
    archived: normalizedParams.archived ? true : undefined,
    deleted: normalizedParams.deleted ? true : undefined,
  };

  return useQuery({
    queryKey: orderKeys.list(normalizedParams),
    queryFn: () => orderApi.getOrders(requestParams),
  });
};
