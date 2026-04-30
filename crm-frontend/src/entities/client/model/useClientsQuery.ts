import { useQuery } from '@tanstack/react-query';
import { clientApi } from '../api/client.api';
import type { GetClientsParams } from './types';
import { clientKeys } from './query-keys';

export const useClientsQuery = (params?: GetClientsParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';
  const queryParams = { page, perPage, search };

  return useQuery({
    queryKey: clientKeys.list(queryParams),
    queryFn: () => clientApi.getClients({ page, perPage, search }),
    placeholderData: (previousData, previousQuery) => {
      if (!previousQuery) return undefined;

      const prevParams = previousQuery.queryKey[1] as {
        page: number;
        perPage: number;
        search: string;
      };

      if (prevParams.search !== search || prevParams.perPage !== perPage) return undefined;

      return previousData;
    },
  });
};
