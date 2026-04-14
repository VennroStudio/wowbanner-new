import { useQuery } from '@tanstack/react-query';
import { materialApi } from '../api/api';
import type { GetMaterialsParams } from '../model/types';

export const useMaterialsQuery = (params?: GetMaterialsParams) => {
  const page = params?.page ?? 1;
  const perPage = params?.perPage ?? 20;
  const search = params?.search ?? '';

  return useQuery({
    queryKey: ['materials', { page, perPage, search }] as const,
    queryFn: () => materialApi.getMaterials({ page, perPage, search }),
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
