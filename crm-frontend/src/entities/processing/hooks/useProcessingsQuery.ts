import { useQuery } from '@tanstack/react-query';
import { processingApi } from '../api/api';
import type { GetProcessingsParams } from '../model/types';

export const useProcessingsQuery = (params?: GetProcessingsParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';

  return useQuery({
    queryKey: ['processings', { page, perPage, search }] as const,
    queryFn: () => processingApi.getProcessings({ page, perPage, search }),
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
