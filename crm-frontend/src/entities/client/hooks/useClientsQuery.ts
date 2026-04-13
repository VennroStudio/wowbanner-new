import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/api';
import type { GetClientsParams } from '../model/types';

export const useClientsQuery = (params?: GetClientsParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';

  return useQuery({
    queryKey: ['clients', { page, perPage, search }] as const,
    queryFn: () => clientApi.getClients({ page, perPage, search }),
    placeholderData: (previousData) => previousData,
  });
};
