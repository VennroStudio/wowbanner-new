import { useQuery } from '@tanstack/react-query';
import { printingApi } from '../api/api';
import type { GetPrintingsParams } from '../model/types';

export const usePrintingsQuery = (params?: GetPrintingsParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';

  return useQuery({
    queryKey: ['printings', { page, perPage, search }] as const,
    queryFn: () => printingApi.getPrintings({ page, perPage, search }),
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
