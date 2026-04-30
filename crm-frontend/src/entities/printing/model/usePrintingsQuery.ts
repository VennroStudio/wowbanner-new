import { useQuery } from '@tanstack/react-query';
import { printingApi } from '../api/printing.api';
import type { GetPrintingsParams } from './types';
import { printingKeys } from './query-keys';

export const usePrintingsQuery = (params?: GetPrintingsParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';
  const queryParams = { page, perPage, search };

  return useQuery({
    queryKey: printingKeys.list(queryParams),
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
